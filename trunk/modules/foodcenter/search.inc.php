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
    $templ['ms2']['icon_title'] = t('Bezahlt');
  } else {
    $templ['ms2']['icon_name'] = 'not_paid';
    $templ['ms2']['icon_title'] = t('Nicht bezahlt');
  }
  $templ['ms2']['link_item'] = $dsp->FetchModTpl('mastersearch2', 'result_icon');
  if ($templ['ms2']['link']) $templ['ms2']['link_item'] = $dsp->FetchModTpl('mastersearch2', 'result_link');
  return $templ['ms2']['link_item'];
}


$ms2->AddTextSearchDropDown(t('Benutzertyp'), 'u.type', array('' => t('Alle'), '1' => t('Gast'), '!1' => 'Nicht Gast', '<0' => t('Gelöschte User'), '2' => t('Administrator'), '3' => t('Superadmin'), '2,3' => t('Orgas')));
	
$party_list = array('' => 'Alle');
$row = $db->qry("SELECT party_id, name FROM %prefix%partys");
while($res = $db->fetch_array($row)) $party_list[$res['party_id']] = $res['name'];
$db->free_result($row);
$ms2->AddTextSearchDropDown('Party', 'p.party_id', $party_list, $party->party_id);

$ms2->AddTextSearchDropDown(t('Eingecheckt'), 'p.checkin', array('' => t('Alle'), '0' => t('Nicht eingecheckt'), '>1' => t('Eingecheckt')));
$ms2->AddTextSearchDropDown(t('Ausgecheckt'), 'p.checkout', array('' => t('Alle'), '0' => t('Nicht ausgecheckt'), '>1' => t('Ausgecheckt')));

$ms2->AddSelect('u.type');
// If Party selected
if ($_POST["search_dd_input"][1] != '' or $_GET["search_dd_input"][1] != '') {
  $ms2->AddResultField(t('Bez.'), 'p.paid', 'PaidIconLink');
  $ms2->AddResultField('In', 'p.checkin', 'MS2GetDate');
  $ms2->AddResultField('Out', 'p.checkout', 'MS2GetDate');
}

$ms2->AddResultField('Sitz', 'u.userid', 'SeatNameLink');
$ms2->AddIconField('assign', $target_url, t('Zuweisen'));
$ms2->PrintSearch($current_url, 'u.userid');
?>