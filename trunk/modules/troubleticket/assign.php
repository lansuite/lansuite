<?php
switch($_GET["step"]) {
	default:
    include_once('modules/troubleticket/search.inc.php');	
	break;


	case 2:
    include_once('modules/usrmgr/search_main.inc.php');
    
    $ms2->query['where'] .= "u.type > 1";
    if ($auth['type'] >= 2) $ms2->AddIconField('assign', 'index.php?mod=troubleticket&action=assign&step=3&ttid='.$_GET['ttid'] .'&userid=', 'Assign');
    
    $ms2->PrintSearch('index.php?mod=troubleticket&action=assign&step=2&ttid='. $_GET['ttid'], 'u.userid');
	break;

	case 3:
		// Variabeln zuweisen
		$tt_id = $_GET["ttid"];
		$t_userid = $_GET["userid"];
		$zeit = time();

		// aktuelles Ticket laden
		$get_ticket = $db->query_first("SELECT target_userid, caption FROM {$config["tables"]["troubleticket"]} WHERE ttid = '$tt_id'");
		$tt_caption = $get_ticket["caption"];
		$target_userid_old = $get_ticket["target_userid"];

		// Zuweisen, Status setzen, Comment setzen, Zeiten setzen, assign_by setzen, old_target_user setzen
		$assign_ticket = $db->query("UPDATE {$config["tables"]["troubleticket"]} SET target_userid = '$t_userid',
			 target_userid_old = '$target_userid_old',
			 status = '2',
			 publiccomment = '',
			 verified = '". time() ."',
			 assignby_userid = '{$auth["userid"]}'
			 WHERE ttid = '$tt_id'");

		// Wenn Update erfolgreich folgende Funktionen ausf�hren
		if ($assign_ticket) {
			// Infobox Messages erstellen bzw. ggf. l�schen
			$db->query("DELETE FROM {$config["tables"]["infobox"]} WHERE id_in_class = '$tt_id' AND class = 'troubleticket'");
			$func->setainfo(str_replace("%TTCaption%",$tt_caption,$lang['troubleticket']['user_assign']),$t_userid,1,"troubleticket",$tt_id);
			// Best�tigung ausgeben
			$func->confirmation($lang['troubleticket']['assign_confirm'], "index.php?mod=troubleticket&action=assign");

		} else $func->error($lang['troubleticket']['err_assign'],"index.php?mod=troubleticket&action=assign");
	break;
}
?>
