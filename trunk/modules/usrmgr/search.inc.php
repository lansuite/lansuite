<?php
include_once('modules/usrmgr/search_main.inc.php');

function PaidIconLink($paid){
  global $dsp, $templ, $line, $party, $lang;
  
  // Only link, if selected party = current party
  if ($_POST["search_dd_input"][1] == $party->party_id) $templ['ms2']['link'] = 'index.php?mod=usrmgr&action=changepaid&step=2&userid='. $line['userid'];
  else $templ['ms2']['link'] = '';
  
  if ($paid) {
    $templ['ms2']['icon_name'] = 'paid';
    $templ['ms2']['icon_title'] = $lang['usrmgr']['paid_yes'];
  } else {
    $templ['ms2']['icon_name'] = 'not_paid';
    $templ['ms2']['icon_title'] = $lang['usrmgr']['paid_no'];
  }
  $templ['ms2']['link_item'] = $dsp->FetchModTpl('mastersearch2', 'result_icon');
  if ($templ['ms2']['link']) $templ['ms2']['link_item'] = $dsp->FetchModTpl('mastersearch2', 'result_link');
  return $templ['ms2']['link_item'];
}

function ClanURLLink($clan_name) {
  global $line;

  if ($clan_name != '' and $line['clanurl'] != '' and $line['clanurl'] != 'http://') {
    if (substr($line['clanurl'], 0, 7) != 'http://') $line['clanurl'] = 'http://'. $line['clanurl'];
    return '<a href="'. $line['clanurl'] .'" target="_blank">'. $clan_name .'</a>';
  } else return $clan_name;
}

function IfLowerUserlevel($userid) {
  global $line, $auth;
  
  if ($line['type'] < $auth['type']) return true;
  else return false;
}

function IfLowerOrEqualUserlevel($userid) {
  global $line, $auth;
  
  if ($line['type'] <= $auth['type']) return true;
  else return false;
}


$ms2->AddTextSearchField('NGL/WWCL/LGZ-ID', array('u.nglid' => 'exact', 'u.nglclanid' => 'exact', 'u.wwclid' => 'exact', 'u.wwclclanid' => 'exact', 'u.lgzid' => 'exact', 'u.lgzclanid' => 'exact',));

$ms2->AddTextSearchDropDown($lang['usrmgr']['add_type'], 'u.type', array('' => $lang['usrmgr']['all'], '1' => $lang['usrmgr']['details_guest'], '!1' => 'Nicht Gast', '<0' => $lang['usrmgr']['search_deactivated'], '2' => $lang['usrmgr']['add_type_admin'], '3' => $lang['usrmgr']['add_type_operator'], '2,3' => $lang['usrmgr']['search_orga']));
	
$party_list = array('' => 'Alle');
$row = $db->query("SELECT party_id, name FROM {$config['tables']['partys']}");
while($res = $db->fetch_array($row)) $party_list[$res['party_id']] = $res['name'];
$db->free_result($row);
$ms2->AddTextSearchDropDown('Party', 'p.party_id', $party_list, $party->party_id);

$ms2->AddTextSearchDropDown($lang['usrmgr']['add_paid'], 'p.paid', array('' => $lang['usrmgr']['all'], '0' => $lang['usrmgr']['add_paid_no'], '>1' => $lang['usrmgr']['details_paid'], '1' => 'Bezahlt per Vorverkauf', '2' => $lang['usrmgr']['search_paid_ak']));
$ms2->AddTextSearchDropDown($lang['usrmgr']['checkin'], 'p.checkin', array('' => $lang['usrmgr']['all'], '0' => $lang['usrmgr']['checkin_no'], '>1' => $lang['usrmgr']['checkin']));
$ms2->AddTextSearchDropDown($lang['usrmgr']['checkout'], 'p.checkout', array('' => $lang['usrmgr']['all'], '0' => $lang['usrmgr']['checkout_no'], '>1' => $lang['usrmgr']['checkout']));
$ms2->AddTextSearchDropDown($lang['usrmgr']['add_gender'], 'u.sex', array('' => $lang['usrmgr']['all'], '0' => $lang['usrmgr']['search_unknown_sex'], '1' => $lang['usrmgr']['search_male'], '2' => $lang['usrmgr']['search_female']));
$ms2->AddTextSearchDropDown($lang['usrmgr']['accounts'], 'u.locked', array('' => $lang['usrmgr']['all'], '0' => $lang['usrmgr']['unlocked'], '1' => $lang['usrmgr']['locked']));

$ms2->AddSelect('c.url AS clanurl');
$ms2->AddSelect('u.type');
$ms2->AddResultField($lang['usrmgr']['details_clan'], 'c.name AS clan', 'ClanURLLink');
// If Party selected
if ($_POST["search_dd_input"][1] != '' or $_GET["search_dd_input"][1] != '') {
  $ms2->AddResultField($lang['usrmgr']['paid'], 'p.paid', 'PaidIconLink');
  $ms2->AddResultField('In', 'p.checkin', 'MS2GetDate');
  $ms2->AddResultField('Out', 'p.checkout', 'MS2GetDate');
}

$ms2->AddIconField('details', 'index.php?mod=usrmgr&action=details&userid=', $lang['ms2']['details']);
if ($party->count > 0) $ms2->AddIconField('signon', 'index.php?mod=usrmgr&action=party&user_id=', $lang['ms2']['signon']);
$ms2->AddIconField('send_mail', 'index.php?mod=mail&action=newmail&step=2&userID=', $lang['ms2']['send_mail']);
$ms2->AddIconField('change_pw', 'index.php?mod=usrmgr&action=newpwd&step=2&userid=', $lang['ms2']['change_pw'], 'IfLowerOrEqualUserlevel');
if ($auth['type'] >= 2) $ms2->AddIconField('assign', 'index.php?mod=usrmgr&action=switch_user&step=10&userid=', $lang['ms2']['switch_user'], 'IfLowerUserlevel');
if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=usrmgr&action=change&step=1&userid=', $lang['ms2']['edit']);
if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=usrmgr&action=delete&step=2&userid=', $lang['ms2']['delete']);


if ($auth['type'] >= 2) {
  $res = $db->query("SELECT * FROM {$config['tables']['party_usergroups']}");
  $ms2->AddMultiSelectAction("Gruppenzuordung aufheben", "index.php?mod=usrmgr&action=group&step=30&group_id=0", 0);
  while ($row = $db->fetch_array($res)) {
    $ms2->AddMultiSelectAction("Der Gruppe '{$row['group_name']}' zuordnen", "index.php?mod=usrmgr&action=group&step=30&group_id={$row['group_id']}", 0);
  }
  $db->free_result($res);
}
if ($auth['type'] >= 3) $ms2->AddMultiSelectAction($lang['usrmgr']['unlock'], "index.php?mod=usrmgr&action=account_lock&step=11", 1);
if ($auth['type'] >= 3) $ms2->AddMultiSelectAction($lang['usrmgr']['lock'], "index.php?mod=usrmgr&action=account_lock&step=10", 1);
if ($auth['type'] >= 3) $ms2->AddMultiSelectAction($lang['ms2']['delete'], "index.php?mod=usrmgr&action=delete&step=10", 1);

$ms2->PrintSearch('index.php?mod=usrmgr&action=search', 'u.userid');
?>
