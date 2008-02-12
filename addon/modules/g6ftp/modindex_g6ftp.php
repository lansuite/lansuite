<?
switch($_GET["action"]) {
	default:									
		include("modules/g6ftp/show.php");
break;
	case "show2":	include("modules/g6ftp/show2.php");
break;
	case "show3":	include("modules/g6ftp/show3.php");
break;
	case "show4":	include("modules/g6ftp/show4.php");
break;
	case "show5":	include("modules/g6ftp/show5.php");
}
?>