<?
switch ($_GET["action"]) {
	default:
	case "list":
		include ("modules/equipment/list.php");
	break;

	case "order":
	case "go_new":
		include ("modules/equipment/order.php");
	break;

	case "calendar":
		include ("modules/equipment/calendar.php");
	break;

	case "history":
		include ("modules/equipment/history.php");
	break;

	case "admin":
		if ($auth["type"] > 0) include ("modules/equipment/admin.php");
		else $func->error("ACCESS_DENIED", "");
	break;
}
?>