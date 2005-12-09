<?php

function authorized($mod, $action) {
	global $menu, $auth, $func, $db, $config;

	switch ($menu["requirement"]) {
		case 1:
			if ($auth["login"]) return 1;
			else $func->error("NO_LOGIN", "");
		break;
		case 2:
			if ($auth["type"] > 1) return 1;
			else $func->error("ACCESS_DENIED", "");
		break;
		case 3:
			if ($auth["type"] > 2) return 1;
			else $func->error("ACCESS_DENIED", "");
		break;
		case 4:
			if ($auth["type"] < 2) return 1;
			else $func->error("ACCESS_DENIED", "");
		break;
		case 5:
			if (!$auth["login"]) return 1;
			else $func->error("ACCESS_DENIED", "");
		break;
		default:
			return 1;
		break;
	}
	return 0;
}


// Set Mod = "Home", if none selected
if (($_GET['mod'] == "") || (!$func->check_var($_GET['mod'], "string", 0, 50))) {
	($_GET["templ"] == "install")? $mod = "install" : $mod = "home";
} else $mod = $_GET['mod'];

//// Load Lang-File
// 1) Include "de"
// 2) Overwrite with $language
if (file_exists("modules/{$mod}/language/{$mod}_lang_de.php")) include_once("modules/{$mod}/language/{$mod}_lang_de.php");
if ($language != "de" and file_exists("modules/{$mod}/language/{$mod}_lang_{$language}.php")) include_once("modules/{$mod}/language/{$mod}_lang_{$language}.php");

// Reset $auth["type"], if no permission to Mod
if ($found_adm and $auth["type"] > 1) {
	$permission = $db->query_first("SELECT 1 AS found FROM {$config["tables"]["user_permissions"]} WHERE (module = '$mod')");
	if ($permission["found"]) {
		$permission = $db->query_first("SELECT 1 AS found FROM {$config["tables"]["user_permissions"]} WHERE (module = '$mod') AND (userid = '{$auth['userid']}')");
		if (!$permission["found"]) {
			$auth["type"] = 1;
			$_SESSION["auth"]["type"] = 1;
		}
	}
}


switch ($mod) {
	case "logout": $func->confirmation('Sie wurden erfolgreich aus dem Intranet ausgeloggt', "");
	break;

	case "datalost": $func->confirmation('Bitte wenden Sie sich an die Organisatoren. Und seien Sie nett und geduldig :)', "");
	break;

	case "install":
		include("modules/install/modindex_install.php");
	break;

	default:
		// If module is deactivated reset $mod
		$module = $db->query_first("SELECT * FROM {$config["tables"]["modules"]} WHERE name = '$mod'");
		if (!$module["active"]) $mod = "deactivated";

		switch ($mod) {
			case "deactivated": $func->error("DEACTIVATED", "");
			break;

			default:
				//// Load Mod-Config
				// 1) Search $_GET["action"] in DB
				// 2) Try to find modindex_$mod.php (will be removed in the future)
				// 3) Search file named $_GET["action"] in the Mod-Directory
				// 4) Search "default"-Entry in DB
				// 5) Error: "Not Found"
				$menu = $db->query_first("SELECT * FROM {$config["tables"]["menu"]} WHERE (module = '$mod') and (action = '{$_GET['action']}')");
				if ($menu["file"] != "") {
					if (authorized($mod, $menu["file"])) include_once("modules/{$mod}/{$menu["file"]}.php");
				} elseif (file_exists("modules/$mod/modindex_$mod.php")) include_once("modules/$mod/modindex_$mod.php");
				elseif (file_exists("modules/$mod/{$_GET["action"]}.php")) {
					if (authorized($mod, $_GET["action"])) include_once("modules/{$mod}/{$_GET["action"]}.php");
				} else {
					$menu = $db->query_first("SELECT * FROM {$config["tables"]["menu"]} WHERE (module = '$mod') and (action = 'default')");
					if ($menu["file"] != "") {
						if (authorized($mod, $menu["file"])) include_once("modules/{$mod}/{$menu["file"]}.php");
					} else $func->error("NOT_FOUND", "");
				}

				if ($mod == "info") $modindex_info = New modindex_info();
			break;
		}
	break;
}
?>
