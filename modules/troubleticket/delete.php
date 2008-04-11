<?php // by denny@esa-box.de

switch($_GET["step"]) {
	default:
    include_once('modules/troubleticket/search.inc.php');	
	break;

	case 2:
		$tt_id = $_GET["ttid"];
		$func->question($lang['troubleticket']['q_unlink'], "index.php?mod=troubleticket&action=delete&step=3&ttid=$tt_id", "index.php?mod=troubleticket&action=delete");
	break;

    case 3:
		$tt_id = $_GET["ttid"];
		$del_ticket = $db->query("DELETE FROM {$config["tables"]["troubleticket"]} WHERE ttid = '$tt_id'");
		$db->query("DELETE FROM {$config["tables"]["infobox"]} WHERE id_in_class = '$tt_id' AND class = 'troubleticket'");
		if ($del_ticket) $func->confirmation($lang['troubleticket']['unlink_confirm'],"index.php?mod=troubleticket&action=delete");
		else $func->error($lang['troubleticket']['err_unlink'], "index.php?mod=troubleticket&action=delete");
	break;
}
?>
