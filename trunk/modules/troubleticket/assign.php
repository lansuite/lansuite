<?php
switch($_GET["step"]) {
	default:
	default:
		switch ($auth["type"]) {
			default:
				$sql = "AND (status = '0')";
			break;
			case 2:
				$sql = "AND (status > '0' AND target_userid = '0')";
			break;
			case 3:
				 $sql = "AND (status > '0')";
			break;
		}

		$mastersearch = new MasterSearch($vars, "index.php?mod=troubleticket&action=assign", "index.php?mod=troubleticket&action=assign&step=2&ttid=", $sql);
		$mastersearch->LoadConfig($lang['troubleticket']['modulname'],$lang['troubleticket']['ms_search_ticket'],$lang['troubleticket']['ms_ticket_result']);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();

		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;


	case 2:
		$mastersearch = new MasterSearch($vars, "index.php?mod=troubleticket&action=assign&step=2&ttid={$_GET["ttid"]}", "index.php?mod=troubleticket&action=assign&step=3&ttid={$_GET["ttid"]}&userid=", " AND type > 1 GROUP BY u.userid");
		$mastersearch->LoadConfig("users",$lang['troubleticket']['ms_search_user'],$lang['troubleticket']['ms_user_result']);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();

		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
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

		// Wenn Update erfolgreich folgende Funktionen ausführen
		if ($assign_ticket) {
			// Infobox Messages erstellen bzw. ggf. löschen
			$db->query("DELETE FROM {$config["tables"]["infobox"]} WHERE id_in_class = '$tt_id' AND class = 'troubleticket'");
			$func->setainfo(str_replace("%TTCaption%",$tt_caption,$lang['troubleticket']['user_assign']),$t_userid,1,"troubleticket",$tt_id);
			// Bestätigung ausgeben
			$func->confirmation($lang['troubleticket']['assign_confirm'], "index.php?mod=troubleticket&action=assign");

		} else $func->error($lang['troubleticket']['err_assign'],"index.php?mod=troubleticket&action=assign");
	break;
}
?>
