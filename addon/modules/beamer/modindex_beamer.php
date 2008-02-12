<?php
	// Here is, where you define, which page should be loaded according to submitted action

	switch ($vars["action"]) {
	
		default:
			if (($auth["type"] >= 2) or (($auth["userid"] == $_GET["userid"]) && $cfg['user_self_details_change'])) {
			include ("modules/beamer/adminmsg.php");
			} else {
			include ("modules/beamer/usermsg.php");
			}
		break;

		case "inhalt_sort";
			include("modules/beamer/inhalt_sort.php");
		break;

		case "history";
			include("modules/beamer/history.php");
		break;

		case "delblacklist":
			include("modules/beamer/delblacklist.php");
		break;
		
		case "addblacklist":
			include("modules/beamer/addblacklist.php");
		break;

		case "adminmsg":
			include("modules/beamer/adminmsg.php");
		break;

		case "delmsg":
			include("modules/beamer/delmsg.php");
		break;

		case "usermsg":
			include("modules/beamer/usermsg.php");
		break;

		case "inhalt":
			include("modules/beamer/inhalt.php");
		break;

		case "beamer":
			include("modules/beamer/beamer.php");
		break;

		case "start":
			include("modules/beamer/start.php");
		break;

		case "show":
			include("modules/beamer/show.php");
		break;
	}
?>