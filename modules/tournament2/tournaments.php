<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.1
*	Filename: 			add.php
*	Module: 			Tournamentsystem
*	Main editor: 		jochen@one-network.org
*	Last change: 		20.04.2004
*	Description: 		Adds tournaments
*	Remarks: 		
*
**************************************************************************/

$step = $vars["step"];
$tournamentid 	= $vars["tournamentid"];
$action 	= $vars["action"];

$_POST["tournament_name"] 	= $func->db2text($_POST["tournament_name"]);
$_POST["tournament_game"] 	= $func->db2text($_POST["tournament_game"]);
$_POST["tournament_version"] 	= $func->db2text($_POST["tournament_version"]);

$startzeit["day"] = $_POST["startzeit_value_day"];
$startzeit["month"] = $_POST["startzeit_value_month"];
$startzeit["year"] = $_POST["startzeit_value_year"];
$startzeit["hour"] = $_POST["startzeit_value_hours"];
$startzeit["min"] = $_POST["startzeit_value_minutes"];

if (($action == "change") && ($step == "")){
	$mastersearch = new MasterSearch( $vars, "index.php?mod=tournament2&action=change", "index.php?mod=tournament2&action=change&step=1&tournamentid=", "");
	$mastersearch->LoadConfig("tournament", $lang["tourney"]["t_add_ms_caption"], $lang["tourney"]["t_add_ms_subcaption"]);
	$mastersearch->PrintForm();
	$mastersearch->Search();
	$mastersearch->PrintResult();

	$templ['index']['info']['content'] .= $mastersearch->GetReturn();
} else {
	
switch($step) {
	case 1:
		$tournament = $db->query_first_rows("SELECT * FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = '$tournamentid'");

		if ($tournament['number'] == 0 and $action == "change") $func->error($lang["tourney"]["t_add_err_tnotexist"],"index.php?mod=tournament2&action=$action");
		else {
			$_POST["tournament_name"] 	= $tournament["name"];
			$_POST["tournament_game"] 	= $tournament["game"];
			$_POST["tournament_version"] = $tournament["version"];

			$_POST["tournament_mode"]	= $tournament["mode"];
			$_POST["tournament_teams"]	= $tournament["maxteams"];
			$_POST["tournament_teammembers"]	= $tournament["teamplayer"];

			$_POST["tournament_age"]		= $tournament["over18"];
			$_POST["tournament_groupid"]	= $tournament["groupid"];
			$_POST["tournament_coins"]	= $tournament["coins"];

			$_POST["tournament_wwcl_gameid"]	= $tournament["wwcl_gameid"];
			$_POST["tournament_ngl_gamename"]	= $tournament["ngl_gamename"];

			$start_date_unix 	= getdate($tournament["starttime"]);
			$startzeit['day'] = $start_date_unix[mday];
			$startzeit['month'] = $start_date_unix[mon];
			$startzeit["year"] = $start_date_unix[year];
			$startzeit["hour"] = $start_date_unix[hours];
			$startzeit['min'] = $start_date_unix[minutes];

			$_POST["tournament_game_duration"] = $tournament["game_duration"];
			$_POST["tournament_max_games"] = $tournament["max_games"];
			$_POST["tournament_break_duration"] = $tournament["break_duration"];
			$_POST["tournament_defwin_on_time_exceed"] = $tournament["defwin_on_time_exceed"];
			$_POST["tournament_blind_draw"] = $tournament["blind_draw"];

			$_POST["tournament_icon"]	= $tournament["icon"];
			$_POST["tournament_rules_ext"]	= $tournament["rules_ext"];
			$_POST["tournament_comment"]	= $func->db2text($tournament["comment"]);
			$_POST["tournament_mapcycle"]	= $func->db2text($tournament["mapcycle"]);
		}
	break;


	case 2:
		$old_tournament = $db->query_first("SELECT status, mode
 					FROM {$config["tables"]["tournament_tournaments"]} 
					WHERE tournamentid = '$tournamentid'");

		if (($old_tournament['status'] != "open") && ($old_tournament['mode'] != $_POST["tournament_mode"]) && ($old_tournament['mode'] != "")) {
			if (($old_tournament['mode'] == "single") || ($old_tournament['mode'] == "double")){
				if (($_POST["tournament_mode"] != "single") && ($_POST["tournament_mode"] != "double")){
					$tournament_mode_error = $lang["tourney"]["t_add_err_chgsedeonly"];
					$step = 1;
				}
			} else {
				$tournament_mode_error = $lang["tourney"]["t_add_err_chgsedeonly2"];
				$step = 1;
			}
		}
		if ($_POST["tournament_mode"] != "single" and $_POST["tournament_mode"] != "double") {
			if ($_POST["tournament_ngl_gamename"]){
				$tournament_ngl_gamename_error = $lang["tourney"]["t_add_err_ngl"];
				$step = 1;
			}
			if ($_POST["tournament_lgz_gamename"]){
				$tournament_lgz_gamename_error = $lang["tourney"]["t_add_err_lgz"];
				$step = 1;
			}
			if ($_POST["tournament_wwcl_gameid"] != 0){
				$tournament_wwcl_gameid_error = $lang["tourney"]["t_add_err_wwcl"];
				$step = 1;
			}
		}
		if ($_POST["tournament_name"] == "") {
			$tournament_name_error = $lang["tourney"]["t_add_err_noname"];
			$step = 1;
		}
		if ($_POST["tournament_game"] == "") {
			$tournament_game_error = $lang["tourney"]["t_add_err_nogame"];
			$step = 1;
		}
		$check_date = checkdate($startzeit["month"], $startzeit["day"], $startzeit["year"]);
		if (!$check_date){
			$startzeit_error = $lang["tourney"]["t_add_err_date"];
			$step = 1;
		} else {
			$starttime = $func->date2unixstamp($startzeit["year"], $startzeit["month"], $startzeit["day"], $startzeit["hour"], $startzeit["min"], 0);
			$timestamp = time();
			if (($action == "add") && ($starttime < $timestamp)) {
				$startzeit_error = $lang["tourney"]["t_add_err_date_past"];
				$step = 1;
			}
		}

		if (strlen($_POST["tournament_comment"]) > 5000) {
			$tournament_comment_error = $lang["tourney"]["t_add_err_comment"];
			$step = 1;
		}
		break;
} // error switch

switch($step) {
	default:
		$_SESSION['add_blocker_tournament'] = FALSE;
		
		$dsp->NewContent($lang["tourney"]["t_add_caption"], $lang["tourney"]["t_add_subcaption"]);
		$dsp->SetForm("index.php?mod=tournament2&action=$action&step=2&tournamentid=$tournamentid");
		$dsp->AddTextFieldRow("tournament_name", $lang["tourney"]["details_name"], $_POST["tournament_name"], $tournament_name_error);
		$dsp->AddTextFieldRow("tournament_game", $lang["tourney"]["details_game"], $_POST["tournament_game"], $tournament_game_error);
		$dsp->AddTextFieldRow("tournament_version", $lang["tourney"]["details_version"], $_POST["tournament_version"], "", "", 1);

		$dsp->AddSingleRow("<b>". $lang["tourney"]["t_add_mode"] ."</b>");

		// Player per Team
		$t_array = array();
		if($_POST["tournament_teammembers"] == "") $_POST["tournament_teammembers"] = 1;		
		for($i = 1; $i <= 20; $i++) {
			($_POST["tournament_teammembers"] == $i) ? $selected = "selected" : $selected = "";
			array_push ($t_array, "<option $selected value=\"$i\">$i</option>");
		}
		$dsp->AddDropDownFieldRow("tournament_teammembers", $lang["tourney"]["t_add_playerperteam"], $t_array, "");

		// Teams
		$t_array = array();
		if($_POST["tournament_teams"] == "") $_POST["tournament_teams"] = 1024;		
		for($i = 8; $i <= 1024; $i*=2) {
			($_POST["tournament_teams"] == $i) ? $selected = "selected" : $selected = "";
			array_push ($t_array, "<option $selected value=\"$i\">$i</option>");
		}
		$dsp->AddDropDownFieldRow("tournament_teams", $lang["tourney"]["t_add_maxteamanz"], $t_array, "");

		// Mode
		$t_array = array();
		if ($_POST["tournament_mode"] == "") $_POST["tournament_mode"] = "double";
		($_POST["tournament_mode"] == "single") ? $selected = "selected" : $selected = "";
		array_push ($t_array, "<option $selected value=\"single\">{$lang["tourney"]["se"]}</option>");
		($_POST["tournament_mode"] == "double") ? $selected = "selected" : $selected = "";
		array_push ($t_array, "<option $selected value=\"double\">{$lang["tourney"]["de"]}</option>");
		($_POST["tournament_mode"] == "liga") ? $selected = "selected" : $selected = "";
		array_push ($t_array, "<option $selected value=\"liga\">{$lang["tourney"]["league"]}</option>");
		($_POST["tournament_mode"] == "groups") ? $selected = "selected" : $selected = "";
		array_push ($t_array, "<option $selected value=\"groups\">{$lang["tourney"]["groups"]}</option>");
		($_POST["tournament_mode"] == "all") ? $selected = "selected" : $selected = "";
		array_push ($t_array, "<option $selected value=\"all\">{$lang["tourney"]["all"]}</option>");
		$dsp->AddDropDownFieldRow("tournament_mode", $lang["tourney"]["details_mode"], $t_array, $tournament_mode_error);

		$dsp->AddCheckBoxRow("tournament_blind_draw", $lang["tourney"]["add_blind_draw"], $lang["tourney"]["add_blind_draw2"], "", 1, $_POST["tournament_blind_draw"]);

		$dsp->AddSingleRow("<b>". $lang["tourney"]["details_reg_limits"] ."</b>");

		// Group_ID
		if($_POST["tournament_groupid"] == 0) $selected = "selected";		
		$t_array = array("<option $selected value=\"0\">{$lang["tourney"]["details_none"]}</option>");
		for($i = 1; $i <= 20; $i++) {
			($_POST["tournament_groupid"] == $i) ? $selected = "selected" : $selected = "";
			array_push ($t_array, "<option $selected value=\"$i\">$i</option>");
		}
		$dsp->AddDropDownFieldRow("tournament_groupid", $lang["tourney"]["details_group"], $t_array, "", 1);

		// Coins
		$t_array = array();
		for($i = 0; $i <= 10; $i++) {
			($_POST["tournament_coins"] == $i) ? $selected = "selected" : $selected = "";
			array_push ($t_array, "<option $selected value=\"$i\">{$lang["tourney"]["t_add_coin_cost"]} $i {$lang["tourney"]["t_add_coin_name"]}</option>");
		}
		$dsp->AddDropDownFieldRow("tournament_coins", $lang["tourney"]["details_coins"], $t_array, "", 1);

		// Age
		$dsp->AddCheckBoxRow("tournament_age", $lang["tourney"]["details_u18"], $lang["tourney"]["t_add_u18_detail"], "", 1, $_POST["tournament_age"]);

		$dsp->AddSingleRow("<b>". $lang["tourney"]["details_times"] ."</b>");

		// Turnierbegin
		$dsp->AddDateTimeRow("startzeit", $lang["tourney"]["details_startat"], "", $startzeit_error, $startzeit);

		// Game Duration
		$t_array = array();
		if($_POST["tournament_game_duration"] == "") { $_POST["tournament_game_duration"] = "30";}
		for($i = 10; $i <= 120; $i+=5) {
			($_POST["tournament_game_duration"] == $i) ? $selected = "selected" : $selected = "";
			array_push ($t_array, "<option $selected value=\"$i\">$i Min</option>");
		}
		$dsp->AddDropDownFieldRow("tournament_game_duration", $lang["tourney"]["details_game_duration"], $t_array, "");

		// Max Games
		if ($_POST["tournament_max_games"] == "") $_POST["tournament_max_games"] = 1;
		$max_game_array = array("1" => "1",
			"2" => "2",
			"3" => "3 (Best Of 3)",
			"4" => "4",
			"5" => "5 (Best Of 5)",
			);
		$t_array = array();
		reset ($max_game_array);
		while (list ($key, $val) = each ($max_game_array)) {
			($_POST["tournament_max_games"] == $key) ? $selected = "selected" : $selected = "";
			array_push ($t_array, "<option $selected value=\"$key\">$val</option>");
		}
		$dsp->AddDropDownFieldRow("tournament_max_games", $lang["tourney"]["details_max_games"], $t_array, "");

		// Break Duration
		$t_array = array();
		if($_POST["tournament_break_duration"] == "") { $_POST["tournament_break_duration"] = "15";}
		for($i = 0; $i <= 30; $i+=5) {
			($_POST["tournament_break_duration"] == $i) ? $selected = "selected" : $selected = "";
			array_push ($t_array, "<option $selected value=\"$i\">$i Min</option>");
		}
		$dsp->AddDropDownFieldRow("tournament_break_duration", $lang["tourney"]["details_break_duration"], $t_array, "");

		// Defwin_on_time_exceed
		$dsp->AddCheckBoxRow("tournament_defwin_on_time_exceed", $lang["tourney"]["t_add_defwin_on_time_exceed"], $lang["tourney"]["t_add_defwin_on_time_exceed_detail"], "", 1, $_POST["tournament_defwin_on_time_exceed"]);


		$dsp->AddSingleRow("<b>". $lang["tourney"]["t_add_league_rules"] ."</b>");

		// Icon
		$dsp->AddPictureDropDownRow("tournament_icon", $lang["tourney"]["t_add_icon"], "ext_inc/tournament_icons", "", "optional", $_POST["tournament_icon"]);

		// WWCL-Spiel Auswahl
		$xml_file = "";
		$file = "ext_inc/tournament_rules/gameini.xml";
		$handle = fopen ($file, "rb");
		$xml_file = fread ($handle, filesize ($file));
		fclose ($handle);

		$t_array = array();
		($_POST["tournament_wwcl_gameid"] == 0) ? $selected = "selected" : $selected = "";
		array_push ($t_array, "<option $selected value=\"0\">{$lang["tourney"]["t_add_no_wwcl"]}</option>");

		$game_ids = $xml->get_tag_content_array("id", $xml_file);
		$game_namen = $xml->get_tag_content_array("name", $xml_file);
		while ($akt_game_id = array_shift($game_ids)) {
			$akt_game_name = array_shift($game_namen);
			($_POST["tournament_wwcl_gameid"] == $akt_game_id) ? $selected = "selected" : $selected = "";
			array_push ($t_array, "<option $selected value=\"$akt_game_id\">$akt_game_name</option>");
		}
		$dsp->AddDropDownFieldRow("tournament_wwcl_gameid", $lang["tourney"]["t_add_wwcl_game"], $t_array, $tournament_wwcl_gameid_error, 1);

		// NGL-Spiel auswahl
		$xml_file = "";
		$file = "ext_inc/tournament_rules/games.xml";
		$handle = fopen ($file, "rb");
		$xml_file = fread ($handle, filesize ($file));
		fclose ($handle);

		$t_array = array();
		($_POST["tournament_ngl_gamename"] == 0) ? $selected = "selected" : $selected = "";
		array_push ($t_array, "<option $selected value=\"\">{$lang["tourney"]["t_add_no_ngl"]}</option>");

		if ($cfg["sys_country"] != "de" and $cfg["sys_country"] != "at" and $cfg["sys_country"] != "ch")
			$dsp->AddDoubleRow($lang["tourney"]["t_add_ngl_game"], $lang["tourney"]["ngl_in_de_at_ch_only"]);
		else {
			$country_xml = $xml->get_tag_content("country short=\"{$cfg["sys_country"]}\"", $xml_file);
			$liga_xml = $xml->get_tag_content_array("liga", $country_xml);
			while ($akt_liga = array_shift($liga_xml)) {
				$info_xml = $xml->get_tag_content("info", $akt_liga);
				$liga_name = $xml->get_tag_content("name", $info_xml);

				$game_xml = $xml->get_tag_content_array("game", $akt_liga);

				while ($game_xml_id = array_shift($game_xml)) {
					$akt_game_id = $xml->get_tag_content("short", $game_xml_id);
					$akt_game_name = $xml->get_tag_content("name", $game_xml_id);
		#			$akt_game_name = array_shift($game_namen);
					($_POST["tournament_ngl_gamename"] == $akt_game_id) ? $selected = "selected" : $selected = "";
					array_push ($t_array, "<option $selected value=\"$akt_game_id\">$liga_name - $akt_game_name</option>");
				}
			}
			$dsp->AddDropDownFieldRow("tournament_ngl_gamename", $lang["tourney"]["t_add_ngl_game"], $t_array, $tournament_ngl_gamename_error, 1);
		}

		// LGZ-Spiel auswahl
		$t_array = array();
		($_POST["tournament_lgz_gamename"] == 0) ? $selected = "selected" : $selected = "";
		array_push ($t_array, "<option $selected value=\"\">{$lang["tourney"]["t_add_no_lgz"]}</option>");

		$game_ids = array (
			"cs_5on5" => "Counter-Strike 5on5",
			"wc3tft_1on1" => "Warcraft TFT 1on1",
			"proevo4_1on1" => "Pro Evolution soccer 4",
			"bf2_6on6" => "Battlefield 6on6",
			"cs_2on2" => "Counter-Strike 2on2",
			"bv_1on1" => "Blobby Volley 1on1",
			"hdr_1on1" => "Herr der Ringe S.u.M 1on1",
			"css_5on5" => "Counter-Strike:Source 5on5",
			"nfsu2_1on1" => "Need For Speed Underground2 1on1"
			);

		reset($game_ids);
		while (list ($key, $val) = each($game_ids)) {
			($_POST["tournament_lgz_gamename"] == $key) ? $selected = "selected" : $selected = "";
			array_push($t_array, "<option $selected value=\"$key\">$val</option>");
		}
		$dsp->AddDropDownFieldRow("tournament_lgz_gamename", $lang["tourney"]["t_add_lgz_game"], $t_array, $tournament_lgz_gamename_error, 1);


		// Rules (Extern)
		if($_POST["tournament_rules_ext"] == "") { $selected = "selected";}
		$t_array = array("<option $selected value=\"\">{$lang["tourney"]["t_add_none"]}</option>\r\n");
		$verz = @opendir("ext_inc/tournament_rules/");
		while ($file_name = @readdir($verz)) if (($file_name != ".") && ($file_name != "..") && ($file_name != "CVS")) {
			($_POST["tournament_rules_ext"] == $file_name) ? $selected = "selected" : $selected = "";
			array_push ($t_array, "<option $selected value=\"$file_name\">$file_name</option>\r\n");
		}
		@closedir($verz);
		$dsp->AddDropDownFieldRow("tournament_rules_ext", $lang["tourney"]["t_add_ext_rules"], $t_array, "", 1);

		// Comments
		$dsp->AddTextAreaPlusRow("tournament_comment", $lang["tourney"]["t_add_comment"], $_POST["tournament_comment"], $tournament_comment_error, "", "", 1);

		// Mapcycle
		$dsp->AddTextAreaRow("tournament_mapcycle", $lang["tourney"]["t_add_mapcycle"], $_POST["tournament_mapcycle"], "", "", 4, 1);

		$dsp->AddFormSubmitRow("add");
		$dsp->AddBackButton("index.php?mod=tournament2&action=$action", "tournament2/tournaments"); 
		$dsp->AddContent();
	break;

	case 2:
		if($_SESSION["add_blocker_tournament"] == TRUE) { $func->error("NO_REFRESH", "index.php?mod=tournament2&action=$action"); }
		else {
			$_POST["tournament_name"] 	= $func->text2db($_POST["tournament_name"]);
			$_POST["tournament_game"] 	= $func->text2db($_POST["tournament_game"]);
			$_POST["tournament_version"] = $func->text2db($_POST["tournament_version"]);			
			$_POST["tournament_comment"]	= $func->text2db($_POST["tournament_comment"]);
			$_POST["tournament_mapcycle"]	= $func->text2db($_POST["tournament_mapcycle"]);

			if ($action == "change") {
				$sql_status = $db->query("UPDATE {$config["tables"]["tournament_tournaments"]} SET
									name = 		'{$_POST["tournament_name"]}',
									game = 		'{$_POST["tournament_game"]}',
									version = 	'{$_POST["tournament_version"]}',
									maxteams = 	'{$_POST["tournament_teams"]}',
									teamplayer = 	'{$_POST["tournament_teammembers"]}',
									starttime = 	'$starttime',
									rules_ext = 	'{$_POST["tournament_rules_ext"]}',
									icon = 	'{$_POST["tournament_icon"]}',
									comment = 	'{$_POST["tournament_comment"]}',
									mode = 		'{$_POST["tournament_mode"]}',
									wwcl_gameid =	'{$_POST["tournament_wwcl_gameid"]}',
									ngl_gamename = 	'{$_POST["tournament_ngl_gamename"]}',
									lgz_gamename = 	'{$_POST["tournament_lgz_gamename"]}',
									over18 =	'{$_POST["tournament_age"]}',
									groupid = 	'{$_POST["tournament_groupid"]}',
									coins =  	'{$_POST["tournament_coins"]}',
									game_duration =  	'{$_POST["tournament_game_duration"]}',
									max_games =  	'{$_POST["tournament_max_games"]}',
									break_duration =  	'{$_POST["tournament_break_duration"]}',
									defwin_on_time_exceed =  	'{$_POST["tournament_defwin_on_time_exceed"]}',
									blind_draw =  	'{$_POST["tournament_blind_draw"]}',
									mapcycle =  	'{$_POST["tournament_mapcycle"]}'
									WHERE tournamentid = $tournamentid");
				$func->log_event(str_replace("%T%", $_POST["tournament_name"], $lang["tourney"]["t_add_log_change"]), 1, $lang["tourney"]["log_t_manage"]);

			} else {
				$sql_status = $db->query("INSERT INTO {$config["tables"]["tournament_tournaments"]} SET
									name = 		'{$_POST["tournament_name"]}',
									party_id = 	'$party->party_id',
									game = 		'{$_POST["tournament_game"]}',
									version = 	'{$_POST["tournament_version"]}',
									maxteams = 	'{$_POST["tournament_teams"]}',
									teamplayer = 	'{$_POST["tournament_teammembers"]}',
									starttime = 	'$starttime',
									rules_ext = 	'{$_POST["tournament_rules_ext"]}',
									icon = 	'{$_POST["tournament_icon"]}',
									comment = 	'{$_POST["tournament_comment"]}',
									mode = 		'{$_POST["tournament_mode"]}',
									wwcl_gameid =	'{$_POST["tournament_wwcl_gameid"]}',
									ngl_gamename = 	'{$_POST["tournament_ngl_gamename"]}',
									lgz_gamename = 	'{$_POST["tournament_lgz_gamename"]}',
									over18 =	'{$_POST["tournament_age"]}',
									groupid = 	'{$_POST["tournament_groupid"]}',
									coins =  	'{$_POST["tournament_coins"]}',
									game_duration =  	'{$_POST["tournament_game_duration"]}',
									max_games =  	'{$_POST["tournament_max_games"]}',
									break_duration =  	'{$_POST["tournament_break_duration"]}',
									defwin_on_time_exceed =  	'{$_POST["tournament_defwin_on_time_exceed"]}',
									blind_draw =  	'{$_POST["tournament_blind_draw"]}',
									mapcycle =  	'{$_POST["tournament_mapcycle"]}'
									");
				$func->log_event(str_replace("%T%", $_POST["tournament_name"], $lang["tourney"]["t_add_log_add"]), 1, $lang["tourney"]["log_t_manage"]);
            }    	
			if($sql_status == 1) $func->confirmation($lang["tourney"]["t_add_success"], "index.php?mod=tournament2&action=$action");
			$_SESSION["add_blocker_tournament"] = TRUE;
		} // else blocker
	break;
}//switch action
}//action!=change
?>
