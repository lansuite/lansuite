<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.1
*	Filename: 			delete.php
*	Module: 			Tournamentsystem
*	Main editor: 		jochen@orgapage.de
*	Last change: 		24.05.2004 17:58
*	Description: 		deletes tournaments
*	Remarks: 			
*
**************************************************************************/

$step 		= $vars["step"];
$tournamentid 	= $vars["tournamentid"];

switch($step) {

	default:
		$mastersearch = new MasterSearch( $vars, "index.php?mod=tournament2&action=delete", "index.php?mod=tournament2&action=delete&step=2&tournamentid=", "");
		$mastersearch->LoadConfig("tournament", $lang["tourney"]["t_del_ms_caption"], $lang["tourney"]["t_del_ms_subcaption"]);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;

	case 2:
		$row = $db->query_first_rows("SELECT status, name FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = '$tournamentid'");

		if($row['number'] == 0) $func->error($lang["tourney"]["t_not_exist"], "");
		else {
			$text = str_replace("%T%", $row["name"], $lang["tourney"]["t_del_confirm"]);		
			$question_text["open"] = $text;

			$question_text["process"] = $text . HTML_NEWLINE . HTML_NEWLINE . HTML_FONT_ERROR . "{$lang["tourney"]["t_del_confirm_process"]}" . HTML_FONT_END;

			$question_text["closed"] = $text . HTML_NEWLINE . HTML_NEWLINE . HTML_FONT_ERROR . "{$lang["tourney"]["t_del_confirm_closed"]}" . HTML_FONT_END;

			$func->question($question_text[$row["status"]], "index.php?mod=tournament2&action=delete&step=3&tournamentid=".$tournamentid , "index.php?mod=tournament2&action=delete");
		} // else number
	break;

	case 3:
		$row = $db->query_first_rows("SELECT name FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = '$tournamentid'");

		if($row["number"] == 0) $func->error($lang["tourney"]["t_not_exist"], "index.php?mod=tournament2&action=delete");
		else {
			$db->query("DELETE FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = '$tournamentid'");
			$db->query("DELETE FROM {$config["tables"]["t2_teams"]} WHERE tournamentid = '$tournamentid'");
			$db->query("DELETE FROM {$config["tables"]["t2_teammembers"]} WHERE tournamentid = '$tournamentid'");
			$db->query("DELETE FROM {$config["tables"]["t2_games"]} WHERE tournamentid = '$tournamentid'");
	
			$func->confirmation(str_replace("%T%", $row["name"], $lang["tourney"]["t_del_success"]), "index.php?mod=tournament2&action=delete");
			$func->log_event(str_replace("%T%", $row["name"], $lang["tourney"]["t_del_log"]), 1, $lang["tourney"]["log_t_manage"]);
		} // else
	break;
}//step
?>
