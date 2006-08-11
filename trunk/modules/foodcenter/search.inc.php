<?php
include_once('modules/usrmgr/search_main.inc.php');


function SeatNameLink($userid){
  global $seat2;

  return $seat2->SeatNameLink($userid);
}

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


$ms2->AddTextSearchDropDown($lang['usrmgr']['add_type'], 'u.type', array('' => $lang['usrmgr']['all'], '1' => $lang['usrmgr']['details_guest'], '!1' => 'Nicht Gast', '<0' => $lang['usrmgr']['search_deactivated'], '2' => $lang['usrmgr']['add_type_admin'], '3' => $lang['usrmgr']['add_type_operator'], '2,3' => $lang['usrmgr']['search_orga']));
	
$party_list = array('' => 'Alle');
$row = $db->query("SELECT party_id, name FROM {$config['tables']['partys']}");
while($res = $db->fetch_array($row)) $party_list[$res['party_id']] = $res['name'];
$db->free_result($row);
$ms2->AddTextSearchDropDown('Party', 'p.party_id', $party_list, $party->party_id);

$ms2->AddTextSearchDropDown($lang['usrmgr']['checkin'], 'p.checkin', array('' => $lang['usrmgr']['all'], '0' => $lang['usrmgr']['checkin_no'], '>1' => $lang['usrmgr']['checkin']));
$ms2->AddTextSearchDropDown($lang['usrmgr']['checkout'], 'p.checkout', array('' => $lang['usrmgr']['all'], '0' => $lang['usrmgr']['checkout_no'], '>1' => $lang['usrmgr']['checkout']));

$ms2->AddSelect('u.type');
// If Party selected
if ($_POST["search_dd_input"][1] != '' or $_GET["search_dd_input"][1] != '') {
  $ms2->AddResultField($lang['usrmgr']['paid'], 'p.paid', 'PaidIconLink');
  $ms2->AddResultField('In', 'p.checkin', 'MS2GetDate');
  $ms2->AddResultField('Out', 'p.checkout', 'MS2GetDate');
}

$ms2->AddResultField('Sitz', 'u.userid', 'SeatNameLink');
$ms2->AddIconField('assign', $target_url, $lang['ms2']['assign']);
$ms2->PrintSearch($current_url, 'u.userid');
?>