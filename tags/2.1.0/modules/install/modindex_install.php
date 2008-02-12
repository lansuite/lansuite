<?php
switch($_GET["action"]) {

	case "env_check":
		include ("modules/install/envcheck.php");
	break;

	case "dbmenu":
		include ("modules/install/dbmenu.php");
	break;
	
	case "wizard":
		include ("modules/install/wizard.php");
	break;

	case "ls_conf":
		include ("modules/install/ls_conf.php");
	break;

	case "db":
		include ("modules/install/db.php");
	break;

	case "adminaccount":
		include ("modules/install/adminaccount.php");
	break;

	case "modules":
		include ("modules/install/modules.php");
	break;

	case "menu":
		include ("modules/install/menu.php");
	break;

	case "settings":
		include ("modules/install/settings.php");
	break;

	case "import":
		include ("modules/install/import.php");
	break;

	case "export":
		include ("modules/install/export.php");
	break;
	
	default:
		include ("modules/install/index.php");
	break;
}
?>
