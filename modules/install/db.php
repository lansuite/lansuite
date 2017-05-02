<?php

include_once('modules/install/class_install.php');
$install = new Install();

$install->TryCreateDB(1);
$db->connect();


if ($_GET["quest"]) {
    switch ($_GET["step"]) {
        case 2: // Rewrite specific table
            $func->question(str_replace("%NAME%", $_GET["table"], t('Bist du sicher, dass du die Datenbank des Moduls <b>\'%NAME%\'</b> zurücksetzen möchtest? Dies löscht unwiderruflich alle Daten, die in diesem Modul bereits geschrieben wurden!')), "index.php?mod=install&action=db&step=2&table={$_GET["table"]}&quest=0", "index.php?mod=install&action=db");
            break;

        case 3: // Rewrite all tables
            $func->question(str_replace("%NAME%", $config["database"]["database"], t('Bist du sicher, dass du <b>\'alle Tabellen\'</b> zurücksetzen möchtest? Dies löscht unwiderruflich alle Datenbankeinträge und Lansuite wird komplett auf den Ausgangszustand zurückgesetzt!')), "index.php?mod=install&action=db&step=3&quest=0", "index.php?mod=install&action=db");
            break;

        case 4: // Rewrite configs
            $func->question(t('Bist du sicher, dass du <b>\'alle Konfigurationen\'</b> zurÃ¼cksetzen möchtest? Damit gehen alle deine Moduleinstellungen verloren!'), "index.php?mod=install&action=db&step=4&quest=0", "index.php?mod=install&action=db");
            break;
/*
// Muss für die Multipartyfunktion angepasst werden
        case 5: // Reset Users Signonstatus
            $func->question(t('Bist du sicher, dass du den Status der Benutzer zurücksetzen möchtest? Damit ist kein Benutzer mehr zur aktuellen Party angemeldet. Außerdem wird der Bezahltstatus aller Benutzer auf \'Nicht Bezahlt\' gesetzt.'), "index.php?mod=install&action=db&step=5&quest=0", "index.php?mod=install&action=db");
        break;
*/
        case 6: // Rewrite Config
            $func->question(t('Bist du sicher, dass du die Modultabelle zurücksetzen möchtest? Dadurch sind nur noch die Standardmodule aktiviert.'), "index.php?mod=install&action=db&step=6&quest=0", "index.php?mod=install&action=db");
            break;

        case 7: // Reset Module DBs
            $func->question(t('Bist du sicher, dass du die Datenbank dieses Moduls zurücksetzen möchtest? Dies löscht unwiderruflich alle Daten, die in diesem Modul bereits geschrieben wurden!'), "index.php?mod=install&action=db&step=7&module={$_GET["module"]}&quest=0", "index.php?mod=install&action=db");
            break;
    }
} else {
    // Action Switch
    switch ($_GET["step"]) {
        case 2: // Rewrite specific table
            $db->qry("DROP TABLE %plain%", $_GET["table"]);
            break;

        case 4: // Rewrite configs
            $db->qry("DROP TABLE %prefix%config");
            $db->qry("DROP TABLE %prefix%config_selections");
            break;
/*
        // Muss für die Multipartyfunktion angepasst werden
        case 5: // Reset Users Signonstatus
            $db->qry("UPDATE %prefix%user SET signon = 0, paid = 0");
            $signonnstatus_out = t('Status zurückgesetzt');
        break;
*/
        case 6: // Rewrite Configs
            $db->qry("DROP TABLE %prefix%modules");
            break;

        case 7: // Reset Module DBs
            $install->WriteTableFromXMLFile($_GET["module"], 1);
            break;
    }


    $dsp->NewContent(t('Datenbank-Initialisierung'), t('<br><b>Deine Datenbank-Struktur wurde soeben automatisch auf den neusten Stand gebracht</b>. Zusätzlich kannst du unterhalb einzelne Modul-Datenbanken zurücksetzen'));
    $install->CreateNewTables(1);
    $install->InsertPLZs();
#	$install->InsertTranslations();

  // Delete Log eintries which indicate a broken DB-Structure, for they are most likely fixed by now
    $db->qry_first('DELETE FROM %prefix%log WHERE type = 3 AND description LIKE \'%Unknown column%\'');

    $dsp->AddBackButton("index.php?mod=install", "install/db");
    $dsp->AddContent();
}
