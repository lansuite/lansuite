<?php // 11.08.2003 16:17 webmaster@netec.org
// Rewrite 22.06.2004 jochen@lansuite.de
// Rewrite 11.03.2005 Genesis(marco@chuchi.tv)

$action = $_GET['action'];

switch($action) {
	case guestlist:
		include("modules/signon/guestlist.php");
	break;

	case usermap:
		include("modules/signon/usermap.php");
	break;

	case config:
		include("modules/signon/config.php");
	break;

	default:
		if($cfg['signon_multiparty'] == "0"){
			$user = $db->query_first("SELECT * FROM {$config["tables"]["party_user"]} WHERE user_id = '{$auth['userid']}' AND party_id = '{$party->party_id}'");
			
			$currenttime = time();
			if ($db->num_rows($user) > 0) {
				$func->information($lang['signon']['allready'], "index.php?mod=news");

			} elseif($_SESSION['party_info']['s_startdate'] >= $currenttime) {
				$func->information(HTML_NEWLINE . "{$lang['signon']['signon_start']}:" . HTML_NEWLINE . HTML_NEWLINE . "<strong>". $func->unixstamp2date($_SESSION['party_info']['s_startdate'], "daydatetime"). "</strong>", "");

			} elseif($_SESSION['party_info']['s_enddate'] <= $currenttime) {
				$func->information( HTML_NEWLINE . "{$lang['signon']['signon_closed']}:" . HTML_NEWLINE . HTML_NEWLINE . "<strong>". $func->unixstamp2date($_SESSION['party_info']['s_enddate'],"daydatetime"). "</strong>", "");

			} else {
				include("modules/signon/add.php");
			}
			
		}else{
			include("modules/signon/show_party.php");
		}
		break;
	case "httpinforequest":
		include("modules/signon/httpinforequest.php");		
	break;
	
	case add:
		include("modules/signon/add.php");
	break;
	
	// ADMIN FUNCTION
	case edit_party:	
		if($_SESSION["auth"]["type"] >= 2) {
			include("modules/signon/edit_party.php");	
		}else {
			$func->error("ACCESS_DENIED", "");
		}
	break;
	
	case add_party:	
		if($_SESSION["auth"]["type"] >= 2) {
			include("modules/signon/add_party.php");	
		}else {
			$func->error("ACCESS_DENIED", "");
		}
	break;
	
	case price:	
		if($_SESSION["auth"]["type"] >= 2) {
			include("modules/signon/price.php");	
		}else {
			$func->error("ACCESS_DENIED", "");  
		}
	break;
	
		
}
?>
