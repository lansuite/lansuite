<?php
// This could run a long time, so avoid running into a timeout
ini_set('max_execution_time', 0);
set_time_limit(0);

// And now continue with the fun stuff...
$importXml = new \LanSuite\XML();
$installImport = new \LanSuite\Module\Install\Import($importXml);
$install = new \LanSuite\Module\Install\Install($installImport);

$install->TryCreateDB(1);
$db->connect();

if ($_GET["quest"]) {
    switch ($_GET["step"]) {
        // Rewrite specific table
        case 2:
            $func->question(str_replace("%NAME%", $_GET["table"], t('Bist du sicher, dass du die Datenbank des Moduls <b>\'%NAME%\'</b> zurücksetzen möchtest? Dies löscht unwiderruflich alle Daten, die in diesem Modul bereits geschrieben wurden!')), "index.php?mod=install&action=db&step=2&table={$_GET["table"]}&quest=0", "index.php?mod=install&action=db");
            break;

        // Rewrite all tables
        case 3:
            $func->question(str_replace("%NAME%", $config["database"]["database"], t('Bist du sicher, dass du <b>\'alle Tabellen\'</b> zurücksetzen möchtest? Dies löscht unwiderruflich alle Datenbankeinträge und Lansuite wird komplett auf den Ausgangszustand zurückgesetzt!')), "index.php?mod=install&action=db&step=3&quest=0", "index.php?mod=install&action=db");
            break;

        // Rewrite configs
        case 4:
            $func->question(t('Bist du sicher, dass du <b>\'alle Konfigurationen\'</b> zurÃ¼cksetzen möchtest? Damit gehen alle deine Moduleinstellungen verloren!'), "index.php?mod=install&action=db&step=4&quest=0", "index.php?mod=install&action=db");
            break;

        // Rewrite Config
        case 6:
            $func->question(t('Bist du sicher, dass du die Modultabelle zurücksetzen möchtest? Dadurch sind nur noch die Standardmodule aktiviert.'), "index.php?mod=install&action=db&step=6&quest=0", "index.php?mod=install&action=db");
            break;

        // Reset Module DBs
        case 7:
            $func->question(t('Bist du sicher, dass du die Datenbank dieses Moduls zurücksetzen möchtest? Dies löscht unwiderruflich alle Daten, die in diesem Modul bereits geschrieben wurden!'), "index.php?mod=install&action=db&step=7&module={$_GET["module"]}&quest=0", "index.php?mod=install&action=db");
            break;
    }
} else {
    // Action Switch
    switch ($_GET["step"]) {
        // Rewrite specific table
        case 2:
            $db->qry("DROP TABLE %plain%", $_GET["table"]);
            break;

        // Rewrite configs
        case 4:
            $db->qry("DROP TABLE %prefix%config");
            $db->qry("DROP TABLE %prefix%config_selections");
            break;

        // Rewrite Configs
        case 6:
            $db->qry("DROP TABLE %prefix%modules");
            break;

        // Reset Module DBs
        case 7:
            $install->WriteTableFromXMLFile($_GET["module"], 1);
            break;
    }

    $dsp->NewContent(t('Datenbank-Initialisierung'), t('<br><b>Deine Datenbank-Struktur wurde soeben automatisch auf den neusten Stand gebracht</b>. Zusätzlich kannst du unterhalb einzelne Modul-Datenbanken zurücksetzen'));
    $install->CreateNewTables(1);
    $install->InsertPLZs();

    // Delete Log eintries which indicate a broken DB-Structure, for they are most likely fixed by now
    $db->qry_first('DELETE FROM %prefix%log WHERE type = 3 AND description LIKE \'%Unknown column%\'');

    $dsp->AddBackButton("index.php?mod=install", "install/db");
}
