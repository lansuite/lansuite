<? 
switch($_GET["action"]) {
	default:	
	case "vote":
	case "show":
	case "history":
	case "best_of":
		include("modules/lansintv/lansinplayer.php");
	break;
	
	case "upload":
		include("modules/lansintv/lansinplayer_upload.php");
	break;

	case "edit":
		include("modules/lansintv/lansinplayer_edit.php");
	break;

	case "remote":
		include("modules/lansintv/lansinplayer_remote.php");
	break;

	case "show":
		include("modules/lansintv/lansinplayer.php");
	break;

	case "blacklist":
		include("modules/lansintv/lansinplayer_blacklist.php");
	break;

	case "stats":
		include("modules/lansintv/lansinplayer_stats.php");
	break;

	case "search":
		include("modules/lansintv/search.php");
	break;

	// Admin Only
	case "setup":
		($auth["type"] >= 2)? include("modules/lansintv/lansinplayer_setup.php")
		: $func->error("ACCESS_DENIED", "");
	break;
}
?>