<?php

$install->TryCreateDB(1);
$db->connect();


if ($_GET["quest"]){
	switch ($_GET["step"]){
		case 2: // Rewrite specific table
			$func->question(str_replace("%NAME%", $_GET["table"], $lang["install"]["db_rewrite_quest"]), "install.php?mod=install&action=db&step=2&table={$_GET["table"]}&quest=0", "install.php?mod=install&action=db");
		break;

		case 3: // Rewrite all tables
			$func->question(str_replace("%NAME%", $config["database"]["database"], $lang["install"]["db_rewrite_all_quest"]), "install.php?mod=install&action=db&step=3&quest=0", "install.php?mod=install&action=db");
		break;

		case 4: // Rewrite configs
			$func->question($lang["install"]["db_rewrite_config_quest"], "install.php?mod=install&action=db&step=4&quest=0", "install.php?mod=install&action=db");
		break;
/*
// Muss für die Multipartyfunktion angepasst werden
		case 5: // Reset Users Signonstatus
			$func->question($lang["install"]["db_reset_user_quest"], "install.php?mod=install&action=db&step=5&quest=0", "install.php?mod=install&action=db");
		break;
*/
		case 6: // Rewrite Config
			$func->question($lang["install"]["db_rewrite_modules_quest"], "install.php?mod=install&action=db&step=6&quest=0", "install.php?mod=install&action=db");
		break;

		case 7: // Reset Module DBs
			$func->question($lang["install"]["db_rewrite_this_module_quest"], "install.php?mod=install&action=db&step=7&module={$_GET["module"]}&quest=0", "install.php?mod=install&action=db");
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
		// Muss für die Multipartyfunktion angepasst werden
		case 5: // Reset Users Signonstatus
			$db->query("UPDATE {$config["tables"]["user"]} SET signon = 0, paid = 0");
			$signonnstatus_out = $lang["install"]["db_signonstatus_rewritten"];
		break;
*/
		case 6: // Rewrite Configs
			$db->query("DROP TABLE {$config["tables"]["modules"]}");
		break;

		case 7: // Reset Module DBs
			$install->WriteTableFromXMLFile($_GET["module"], 1);
		break;		
	}


	$dsp->NewContent($lang["install"]["db_caption"], $lang["install"]["db_subcaption"]);

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
	$install->InsertTranslations();

	$dsp->AddBackButton("install.php?mod=install", "install/db"); 
	$dsp->AddContent();

}

?>
