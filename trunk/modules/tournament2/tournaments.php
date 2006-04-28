<?php

function CheckModeForLeague($league) {
  if ($league and $_POST['mode'] != 'single' and $_POST['mode'] != 'double') return 'Only in SE and DE';
  else return false;
}

function CheckDateInFuture($date) {
  global $lang;

  if (!$_GET['mf_id'] and $date < time()) return $lang['tourney']['t_add_err_date_past'];
  else return false;
}

function CheckModeChangeAllowed($mode) {
global $mf, $lang;

  if ($_GET['mf_id'] and $mf->CurrentDBFields['status'] != 'open' and $mf->CurrentDBFields['mode'] != $mode) {
    if ($mf->CurrentDBFields['mode'] == 'single' or $mf->CurrentDBFields['mode'] == 'double') {
      if ($mode != 'single' and $mode != 'double') return $lang['tourney']['t_add_err_chgsedeonly'];
    } else {
      return $lang['tourney']['t_add_err_chgsedeonly2'];
    }
  }
  return false;
}


include_once('inc/classes/class_masterform.php');
$mf = new masterform();

// Name
$mf->AddField($lang['tourney']['details_name'], 'name');
$mf->AddField($lang['tourney']['details_game'], 'game');
$mf->AddField($lang['tourney']['details_version'], 'version', 1);
$mf->AddGroup('Alg.');


// Mode
$selecttions = array();
for($i = 1; $i <= 20; $i++) $selecttions[$i] = $i;
$mf->AddField($lang['tourney']['t_add_playerperteam'], 'teamplayer', '', '', $selecttions);

$selecttions = array();
if($_POST['maxteams'] == '') $_POST['maxteams'] = 1024;
for($i = 8; $i <= 1024; $i*=2) $selecttions[$i] = $i;
$mf->AddField($lang['tourney']['t_add_maxteamanz'], 'maxteams', '', '', $selecttions);

$selecttions = array();
if ($_POST['mode'] == '') $_POST['mode'] = 'double';
$selecttions['single'] = $lang['tourney']['se'];
$selecttions['double'] = $lang['tourney']['de'];
$selecttions['liga'] = $lang['tourney']['league'];
$selecttions['groups'] = $lang['tourney']['groups'];
$selecttions['all'] = $lang['tourney']['all'];
$mf->AddField($lang['tourney']['details_mode'], 'mode', '', 'CheckModeChangeAllowed', $selecttions);

$mf->AddField($lang['tourney']['add_blind_draw'].'|'.$lang['tourney']['add_blind_draw2'], 'blind_draw', 1);
$mf->AddGroup($lang['tourney']['t_add_mode']);


// Limits
$selecttions = array();
$selecttions[0] = $lang["tourney"]["details_none"];
for($i = 1; $i <= 20; $i++) $selecttions[$i] = $i;
$mf->AddField($lang['tourney']['details_group'], 'groupid', 1, '', $selecttions);

$selecttions = array();
for($i = 0; $i <= 10; $i++) $selecttions[$i] = $lang['tourney']['t_add_coin_cost'] .' '. $i .' '. $lang['tourney']['t_add_coin_name'];
$mf->AddField($lang['tourney']['details_coins'], 'coins', 1, '', $selecttions);

$mf->AddField($lang['tourney']['details_u18'].'|'.$lang['tourney']['t_add_u18_detail'], 'over18', 1);
$mf->AddGroup($lang['tourney']['details_reg_limits']);


// Times
$mf->AddField($lang["tourney"]["details_startat"], 'starttime', '', CheckDateInFuture);

$selecttions = array();
if($_POST['game_duration'] == '') { $_POST['game_duration'] = '30';}
for($i = 10; $i <= 120; $i+=5) $selecttions[$i] = $i .' Min';
$mf->AddField($lang['tourney']['details_game_duration'], 'game_duration', '', '', $selecttions);

$selecttions = array();
if ($_POST['mode'] == '') $_POST['mode'] = 'double';
$selecttions['1'] = '1';
$selecttions['2'] = '2';
$selecttions['3'] = '3 (Best Of 3)';
$selecttions['4'] = '4';
$selecttions['5'] = '5 (Best Of 5)';
$mf->AddField($lang['tourney']['details_max_games'], 'max_games', '', '', $selecttions);

$selecttions = array();
if($_POST['break_duration'] == '') { $_POST['break_duration'] = '30';}
for($i = 0; $i <= 30; $i+=5) $selecttions[$i] = $i .' Min';
$mf->AddField($lang['tourney']['details_break_duration'], 'break_duration', '', '', $selecttions);

$mf->AddField($lang['tourney']['t_add_defwin_on_time_exceed'].'|'.$lang['tourney']['t_add_defwin_on_time_exceed_detail'], 'defwin_on_time_exceed', 1);
$mf->AddGroup($lang['tourney']['details_times']);


// League + Misc
$mf->AddField($lang['tourney']['t_add_icon'], 'icon', 1, '', 'ext_inc/tournament_icons');

// WWCL-Spiel Auswahl
$xml_file = "";
$file = "ext_inc/tournament_rules/gameini.xml";
$handle = fopen ($file, "rb");
$xml_file = fread ($handle, filesize ($file));
fclose ($handle);

$selecttions = array();
($_POST["tournament_wwcl_gameid"] == 0) ? $selected = "selected" : $selected = "";
$selecttions['0'] = $lang["tourney"]["t_add_no_wwcl"];

$game_ids = $xml->get_tag_content_array("id", $xml_file);
$game_namen = $xml->get_tag_content_array("name", $xml_file);
while ($akt_game_id = array_shift($game_ids)) {
	$akt_game_name = array_shift($game_namen);
	($_POST["tournament_wwcl_gameid"] == $akt_game_id) ? $selected = "selected" : $selected = "";
	$selecttions[$akt_game_id] = $akt_game_name;
}
$mf->AddField($lang['tourney']['t_add_wwcl_game'], 'wwcl_gameid', 1, 'CheckModeForLeague', $selecttions);

// NGL-Spiel auswahl
$xml_file = "";
$file = "ext_inc/tournament_rules/games.xml";
$handle = fopen ($file, "rb");
$xml_file = fread ($handle, filesize ($file));
fclose ($handle);

$selecttions = array();
($_POST["tournament_ngl_gamename"] == 0) ? $selected = "selected" : $selected = "";
$selecttions[''] = $lang["tourney"]["t_add_no_ngl"];

#if ($cfg["sys_country"] != "de" and $cfg["sys_country"] != "at" and $cfg["sys_country"] != "ch")
#	$dsp->AddDoubleRow($lang["tourney"]["t_add_ngl_game"], $lang["tourney"]["ngl_in_de_at_ch_only"]);
#else {
#	$country_xml = $xml->get_tag_content("country short=\"{$cfg["sys_country"]}\"", $xml_file);
	$liga_xml = $xml->get_tag_content_array("league", $xml_file);
	while ($akt_liga = array_shift($liga_xml)) {
		$info_xml = $xml->get_tag_content_array("info", $akt_liga);
		while ($akt_info = array_shift($info_xml)) $info_title = $xml->get_tag_content("title", $akt_info);

		$game_xml = $xml->get_tag_content_array("game", $akt_liga);

		while ($game_xml_id = array_shift($game_xml)) {
			$akt_game_id = $xml->get_tag_content("short", $game_xml_id);
			$akt_game_name = $xml->get_tag_content("title", $game_xml_id);
			($_POST["tournament_ngl_gamename"] == $akt_game_id) ? $selected = "selected" : $selected = "";
			$selecttions[$akt_game_id] = $info_title .' - '. $akt_game_name;
		}
	}
	$mf->AddField($lang['tourney']['t_add_ngl_game'], 'ngl_gamename', 1, 'CheckModeForLeague', $selecttions);
#}

// LGZ-Spiel auswahl
$selecttions = array();
if ($_POST['mode'] == '') $_POST['mode'] = 'double';
$selecttions[''] = $lang["tourney"]["t_add_no_lgz"];
$selecttions['cs_5on5'] = 'Counter-Strike 5on5';
$selecttions['wc3tft_1on1'] = 'Warcraft TFT 1on1';
$selecttions['proevo4_1on1'] = 'Pro Evolution soccer 4';
$selecttions['bf2_6on6'] = 'Battlefield 6on6';
$selecttions['cs_2on2'] = 'Counter-Strike 2on2';
$selecttions['bv_1on1'] = 'Blobby Volley 1on1';
$selecttions['hdr_1on1'] = 'Herr der Ringe S.u.M 1on1';
$selecttions['css_5on5'] = 'Counter-Strike:Source 5on5';
$selecttions['nfsu2_1on1'] = 'Need For Speed Underground2 1on1';
$mf->AddField($lang['tourney']['t_add_lgz_game'], 'lgz_gamename', 1, 'CheckModeForLeague', $selecttions);

// Rules (Extern)
$selecttions = array();
$selecttions[] = $lang['tourney']['t_add_none'];
$verz = @opendir('ext_inc/tournament_rules/');
while ($file_name = @readdir($verz)) if (!is_dir($file_name)) $selecttions[$file_name] = $file_name;
@closedir($verz);
$mf->AddField($lang['tourney']['t_add_ext_rules'], 'rules_ext', 1, '', $selecttions);

$mf->AddField($lang['tourney']['t_add_comment'], 'comment', 1);
$mf->AddField($lang['tourney']['t_add_mapcycle'], 'mapcycle', 1);
$mf->AddGroup($lang['tourney']['t_add_league_rules']);

if ($mf->SendForm('index.php?mod=tournament2&action='. $_GET['action'], 'tournament_tournaments', 'tournamentid', $_GET['tournamentid'])) {
  $func->log_event(str_replace("%T%", $_POST["tournament_name"], $lang["tourney"]["t_add_log_add"]), 1, $lang["tourney"]["log_t_manage"]);
}
?>