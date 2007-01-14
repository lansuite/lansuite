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
$mf->AddField($lang['tourney']['details_version'], 'version', '', '', FIELD_OPTIONAL);
$mf->AddGroup('Alg.');


// Mode
$selections = array();
for($i = 1; $i <= 20; $i++) $selections[$i] = $i;
$mf->AddField($lang['tourney']['t_add_playerperteam'], 'teamplayer', IS_SELECTION, $selections);

$selections = array();
if($_POST['maxteams'] == '') $_POST['maxteams'] = 1024;
for($i = 8; $i <= 1024; $i*=2) $selections[$i] = $i;
$mf->AddField($lang['tourney']['t_add_maxteamanz'], 'maxteams', IS_SELECTION, $selections);

$selections = array();
if ($_POST['mode'] == '') $_POST['mode'] = 'double';
$selections['single'] = $lang['tourney']['se'];
$selections['double'] = $lang['tourney']['de'];
$selections['liga'] = $lang['tourney']['league'];
$selections['groups'] = $lang['tourney']['groups'];
$selections['all'] = $lang['tourney']['all'];
$mf->AddField($lang['tourney']['details_mode'], 'mode', IS_SELECTION, $selections, '', 'CheckModeChangeAllowed');

$mf->AddField($lang['tourney']['add_blind_draw'].'|'.$lang['tourney']['add_blind_draw2'], 'blind_draw', '', '', FIELD_OPTIONAL);
$mf->AddGroup($lang['tourney']['t_add_mode']);


// Limits
$selections = array();
$selections[0] = $lang["tourney"]["details_none"];
for($i = 1; $i <= 20; $i++) $selections[$i] = $i;
$mf->AddField($lang['tourney']['details_group'], 'groupid', IS_SELECTION, $selections, FIELD_OPTIONAL);

$selections = array();
for($i = 0; $i <= 10; $i++) $selections[$i] = $lang['tourney']['t_add_coin_cost'] .' '. $i .' '. $lang['tourney']['t_add_coin_name'];
$mf->AddField($lang['tourney']['details_coins'], 'coins', IS_SELECTION, $selections, FIELD_OPTIONAL);

$mf->AddField($lang['tourney']['details_u18'].'|'.$lang['tourney']['t_add_u18_detail'], 'over18', '', '', FIELD_OPTIONAL);
$mf->AddGroup($lang['tourney']['details_reg_limits']);


// Times
$mf->AddField($lang["tourney"]["details_startat"], 'starttime', '', '', '', CheckDateInFuture);

$selections = array();
if($_POST['game_duration'] == '') { $_POST['game_duration'] = '30';}
for($i = 10; $i <= 120; $i+=5) $selections[$i] = $i .' Min';
$mf->AddField($lang['tourney']['details_game_duration'], 'game_duration', IS_SELECTION, $selections, '', '');

$selections = array();
if ($_POST['mode'] == '') $_POST['mode'] = 'double';
$selections['1'] = '1';
$selections['2'] = '2';
$selections['3'] = '3 (Best Of 3)';
$selections['4'] = '4';
$selections['5'] = '5 (Best Of 5)';
$mf->AddField($lang['tourney']['details_max_games'], 'max_games', IS_SELECTION, $selections);

$selections = array();
if($_POST['break_duration'] == '') { $_POST['break_duration'] = '30';}
for($i = 0; $i <= 30; $i+=5) $selections[$i] = $i .' Min';
$mf->AddField($lang['tourney']['details_break_duration'], 'break_duration', IS_SELECTION, $selections);

$mf->AddField($lang['tourney']['t_add_defwin_on_time_exceed'].'|'.$lang['tourney']['t_add_defwin_on_time_exceed_detail'], 'defwin_on_time_exceed', '', 1, FIELD_OPTIONAL);
$mf->AddGroup($lang['tourney']['details_times']);


// League + Misc
$mf->AddField($lang['tourney']['t_add_icon'], 'icon', IS_PICTURE_SELECT, 'ext_inc/tournament_icons', FIELD_OPTIONAL);

// WWCL-Spiel Auswahl
$xml_file = "";
$file = "ext_inc/tournament_rules/gameini.xml";
$handle = fopen ($file, "rb");
$xml_file = fread ($handle, filesize ($file));
fclose ($handle);

$selections = array();
($_POST["wwcl_gameid"] == 0) ? $selected = "selected" : $selected = "";
$selections['0'] = $lang["tourney"]["t_add_no_wwcl"];

$game_ids = $xml->get_tag_content_array("id", $xml_file);
$game_namen = $xml->get_tag_content_array("name", $xml_file);
while ($akt_game_id = array_shift($game_ids)) {
	$akt_game_name = array_shift($game_namen);
	($_POST["wwcl_gameid"] == $akt_game_id) ? $selected = "selected" : $selected = "";
	$selections[$akt_game_id] = $akt_game_name;
}
$mf->AddField($lang['tourney']['t_add_wwcl_game'], 'wwcl_gameid', IS_SELECTION, $selections, FIELD_OPTIONAL, 'CheckModeForLeague');

// NGL-Spiel auswahl
$xml_file = "";
$file = "ext_inc/tournament_rules/games.xml";
$handle = fopen ($file, "rb");
$xml_file = fread ($handle, filesize ($file));
fclose ($handle);

$selections = array();
($_POST["ngl_gamename"] == 0) ? $selected = "selected" : $selected = "";
$selections[''] = $lang["tourney"]["t_add_no_ngl"];

# and $cfg["sys_country"] != "at" and $cfg["sys_country"] != "ch"
if ($cfg["sys_country"] != "de") $mf->AddField(t('NGL-Support ist nur für Partys in Deutschland möglich. Das Land deiner Party kannst du auf der Adminseite einstellen'), 'ngl_gamename', IS_TEXT_MESSAGE, $lang['tourney']['ngl_in_de_at_ch_only']);
else {
	$country_xml = $xml->get_tag_content("country short=\"{$cfg["sys_country"]}\"", $xml_file);
	$liga_xml = $xml->get_tag_content_array("league", $xml_file);
	while ($akt_liga = array_shift($liga_xml)) {
		$info_xml = $xml->get_tag_content_array("info", $akt_liga);
		while ($akt_info = array_shift($info_xml)) $info_title = $xml->get_tag_content("title", $akt_info);

		$game_xml = $xml->get_tag_content_array("game", $akt_liga);

		if(is_array($game_xml)){
			while ($game_xml_id = array_shift($game_xml)) {
				$akt_game_id = $xml->get_tag_content("short", $game_xml_id);
				$akt_game_name = $xml->get_tag_content("title", $game_xml_id);
				($_POST["ngl_gamename"] == $akt_game_id) ? $selected = "selected" : $selected = "";
				$selections[$akt_game_id] = $info_title .' - '. $akt_game_name;
			}
		}
	}
	$mf->AddField($lang['tourney']['t_add_ngl_game'], 'ngl_gamename', IS_SELECTION, $selections, FIELD_OPTIONAL, 'CheckModeForLeague');
}

// LGZ-Spiel auswahl
$xml_file = "";
$file = "ext_inc/tournament_rules/xml_games.xml";
$handle = fopen ($file, "rb");
$xml_file = fread ($handle, filesize ($file));
fclose ($handle);

$selections = array();
($_POST["lgz_gamename"] == 0) ? $selected = "selected" : $selected = "";
$selections[''] = $lang["tourney"]["t_add_no_lgz"];

$games = $xml->get_tag_content_array("game", $xml_file);
foreach ($games as $game){
  $akt_game_name = $xml->get_tag_content("contest", $game) .' - '. $xml->get_tag_content("name", $game);
  $syscode = $xml->get_tag_content("syscode", $game);
	($_POST["lgz_gamename"] == $syscode) ? $selected = "selected" : $selected = "";
	$selections[$syscode] = $akt_game_name;
}
$mf->AddField($lang['tourney']['t_add_lgz_game'], 'lgz_gamename', IS_SELECTION, $selections, FIELD_OPTIONAL, 'CheckModeForLeague');

// Rules (Extern)
$selections = array();
$selections[] = $lang['tourney']['t_add_none'];
$verz = opendir('ext_inc/tournament_rules/');
while ($file_name = readdir($verz)) if (!is_dir('ext_inc/tournament_rules/'.$file_name) and $file_name != 'gameini.xml'
  and $file_name != 'games.xml' and $file_name != 'info.txt' and $file_name != 'xml_games.xml')
  $selections[$file_name] = $file_name;
closedir($verz);
$mf->AddField($lang['tourney']['t_add_ext_rules'], 'rules_ext', IS_SELECTION, $selections, FIELD_OPTIONAL);

$mf->AddField($lang['tourney']['t_add_comment'], 'comment', '', HTML_ALLOWED, FIELD_OPTIONAL);
$mf->AddField($lang['tourney']['t_add_mapcycle'], 'mapcycle', '', '', FIELD_OPTIONAL);
$mf->AddGroup($lang['tourney']['t_add_league_rules']);

if (!$_GET['tournamentid']) {
  $mf->AddFix('party_id', $party->party_id);
}

if ($mf->SendForm('index.php?mod=tournament2&action='. $_GET['action'], 'tournament_tournaments', 'tournamentid', $_GET['tournamentid'])) {
  $func->log_event(str_replace("%T%", $_POST["name"], $lang["tourney"]["t_add_log_add"]), 1, $lang["tourney"]["log_t_manage"]);
}
?>
