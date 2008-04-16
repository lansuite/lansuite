<?php

$install->TryCreateDB(1);
$db->connect();


if ($_GET["quest"]){
	switch ($_GET["step"]){
		case 2: // Rewrite specific table
			$func->question(str_replace("%NAME%", $_GET["table"], t('Sind Sie sicher, dass Sie die Datenbank des Moduls <b>\'%NAME%\'</b> zurücksetzen möchten? Dies löscht unwiderruflich alle Daten, die in diesem Modul bereits geschrieben wurden!')), "index.php?mod=install&action=db&step=2&table={$_GET["table"]}&quest=0", "index.php?mod=install&action=db");
		break;

		case 3: // Rewrite all tables
			$func->question(str_replace("%NAME%", $config["database"]["database"], t('Sind Sie sicher, dass Sie <b>\'alle Tabellen\'</b> zurücksetzen möchten? Dies löscht unwiderruflich alle Datenbankeinträge und Lansuite wird komplett auf den Ausgangszustand zurückgesetzt!')), "index.php?mod=install&action=db&step=3&quest=0", "index.php?mod=install&action=db");
		break;

		case 4: // Rewrite configs
			$func->question(t('Sind Sie sicher, dass Sie <b>\'alle Konfigurationen\'</b> zurücksetzen möchten? Damit gehen alle Ihre Moduleinstellungen verloren!'), "index.php?mod=install&action=db&step=4&quest=0", "index.php?mod=install&action=db");
		break;
/*
// Muss f�r die Multipartyfunktion angepasst werden
		case 5: // Reset Users Signonstatus
			$func->question(t('Sind Sie sicher, dass Sie den Status der Benutzer zurücksetzen möchten? Damit ist kein Benutzer mehr zur aktuellen Party angemeldet. Außerdem wird der Bezahltstatus aller Benutzer auf \'Nicht Bezahlt\' gesetzt.'), "index.php?mod=install&action=db&step=5&quest=0", "index.php?mod=install&action=db");
		break;
*/
		case 6: // Rewrite Config
			$func->question(t('Sind Sie sicher, dass Sie die Modultabelle zurücksetzen möchten? Dadurch sind nur noch die Standardmodule aktiviert.'), "index.php?mod=install&action=db&step=6&quest=0", "index.php?mod=install&action=db");
		break;

		case 7: // Reset Module DBs
			$func->question(t('Sind Sie sicher, dass Sie die Datenbank dieses Moduls zurücksetzen möchten? Dies löscht unwiderruflich alle Daten, die in diesem Modul bereits geschrieben wurden!'), "index.php?mod=install&action=db&step=7&module={$_GET["module"]}&quest=0", "index.php?mod=install&action=db");
		break;
	}

} else {
	// Action Switch
	switch ($_GET["step"]){
		case 2: // Rewrite specific table
			$db->query("DROP TABLE {$_GET["table"]}");
		break;

		case 4: // Rewrite configs
			$db->query("DROP TABLE {$config["tables"]["config"]}");
			$db->query("DROP TABLE {$config["tables"]["config_selections"]}");
		break;
/*
		// Muss f�r die Multipartyfunktion angepasst werden
		case 5: // Reset Users Signonstatus
			$db->query("UPDATE {$config["tables"]["user"]} SET signon = 0, paid = 0");
			$signonnstatus_out = t('Status zurückgesetzt');
		break;
*/
		case 6: // Rewrite Configs
			$db->query("DROP TABLE {$config["tables"]["modules"]}");
		break;

		case 7: // Reset Module DBs
			$install->WriteTableFromXMLFile($_GET["module"], 1);
		break;		
	}


	$dsp->NewContent(t('Datenbank-Initialisierung'), t('<br><b>Ihre Datenbank-Struktur wurde soeben automatisch auf den neusten Stand gebracht</b>. Zusätzlich können Sie unterhalb einzelne Modul-Datenbanken zurücksetzen'));

	// Scan the modules-dir for mod_settings/db.xml-File, read data, compare with db and create/update DB, if neccessary
	$install->CreateNewTables(1);
	// Read table-names from DB an save them in $config['tables']
	$install->SetTableNames();

	// Insert PLZs from modules/install/db_insert_locations.sql in DB, if not exist
	$install->InsertPLZs();
	// Insert modules-settings from mod_settings/module.xml in DB, if not exist
	$install->InsertModules(0);
	// Insert menus from mod_settings/menu.xml in DB, if not exist
	$install->InsertMenus(0);
	// Insert translations of DB-items
#	$install->InsertTranslations();

	$dsp->AddBackButton("index.php?mod=install", "install/db");
	$dsp->AddContent();

}

?>
