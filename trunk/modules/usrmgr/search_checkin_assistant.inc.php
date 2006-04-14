<?
include_once('modules/usrmgr/search_main.inc.php');

function SeatNameLink($userid){
  global $seat2;

  return $seat2->SeatNameLink($userid);
}

function PaidIcon($paid){
  global $dsp, $templ, $line, $party;
  
  if ($paid) {
    $templ['ms2']['icon_name'] = 'paid';
    $templ['ms2']['icon_title'] = 'Paid';
  } else {
    $templ['ms2']['icon_name'] = 'not_paid';
    $templ['ms2']['icon_title'] = 'Not Paid';
  }
  return $dsp->FetchModTpl('mastersearch2', 'result_icon');
}

function ClanURLLink($clan_name) {
  global $line;

  if ($clan_name != '' and $line['clanurl'] != '' and $line['clanurl'] != 'http://') {
    if (substr($line['clanurl'], 0, 7) != 'http://') $line['clanurl'] = 'http://'. $line['clanurl'];
    return '<a href="'. $line['clanurl'] .'" target="_blank">'. $clan_name .'</a>';
  } else return $clan_name;
}

if ($_GET['signon']) $ms2->query['where'] = "(p.checkin = '0' OR p.checkout != '0') AND u.type > 0 AND p.party_id = {$party->party_id}";
else $ms2->query['where'] = 'u.type > 0';

$ms2->AddTextSearchField('NGL/WWCL/LGZ-ID', array('u.nglid' => 'exact', 'u.nglclanid' => 'exact', 'u.wwclid' => 'exact', 'u.wwclclanid' => 'exact', 'u.lgzid' => 'exact', 'u.lgzclanid' => 'exact',));

$ms2->AddTextSearchDropDown($lang['usrmgr']['add_type'], 'u.type', array('' => $lang['usrmgr']['all'], '1' => $lang['usrmgr']['details_guest'], '!1' => 'Nicht Gast', '<0' => $lang['usrmgr']['search_deactivated'], '2' => $lang['usrmgr']['add_type_admin'], '3' => $lang['usrmgr']['add_type_operator'], '2,3' => $lang['usrmgr']['search_orga']));
	
$ms2->AddTextSearchDropDown($lang['usrmgr']['add_paid'], 'p.paid', array('' => $lang['usrmgr']['all'], '0' => $lang['usrmgr']['add_paid_no'], '>1' => $lang['usrmgr']['details_paid'], '1' => 'Bezahlt per Vorverkauf', '2' => $lang['usrmgr']['search_paid_ak']));
$ms2->AddTextSearchDropDown($lang['usrmgr']['add_gender'], 'u.sex', array('' => $lang['usrmgr']['all'], '0' => $lang['usrmgr']['search_unknown_sex'], '1' => $lang['usrmgr']['search_male'], '2' => $lang['usrmgr']['search_female']));

$ms2->AddSelect('c.url AS clanurl');
$ms2->AddResultField($lang['usrmgr']['details_clan'], 'c.name AS clan', 'ClanURLLink');
$ms2->AddResultField('Bez.', 'p.paid', 'PaidIcon');

$ms2->AddResultField('Sitz', 'u.userid', 'SeatNameLink');

$ms2->AddIconField('assign', 'index.php?mod=usrmgr&action=entrance&step=3&umode=change&userid=', $lang['ms2']['assign']);

$ms2->PrintSearch('index.php?mod=usrmgr&action=entrance&step=2&umode=change&signon='. $_GET['signon'], 'u.userid');
?>