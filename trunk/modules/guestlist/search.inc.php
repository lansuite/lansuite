<?php
include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2();

function SeatNameLink($userid){
  global $seat2;

  return $seat2->SeatNameLink($userid);
}

function PaidIconLink($paid){
  global $dsp, $templ, $line, $auth;
  
  if ($paid) {
    $templ['ms2']['icon_name'] = 'paid';
    $templ['ms2']['icon_title'] = 'Paid';
    $templ['ms2']['link'] = 'index.php?mod=guestlist&step=11&userid='. $line['userid'];
  } else {
    $templ['ms2']['icon_name'] = 'not_paid';
    $templ['ms2']['icon_title'] = 'Not Paid';
    $templ['ms2']['link'] = 'index.php?mod=guestlist&step=10&userid='. $line['userid'];
  }
  $templ['ms2']['link_item'] = $dsp->FetchModTpl('mastersearch2', 'result_icon');
  if ($auth['type'] > 1) $templ['ms2']['link_item'] = $dsp->FetchModTpl('mastersearch2', 'result_link');
  
  return $templ['ms2']['link_item'];
}

function ClanURLLink($clan_name) {
  global $line;
  
  if ($clan_name != '' and $line['clanurl'] != '' and $line['clanurl'] != 'http://') {
    if (substr($line['clanurl'], 0, 7) != 'http://') $line['clanurl'] = 'http://'. $line['clanurl'];
    return '<a href="'. $line['clanurl'] .'" target="_blank">'. $clan_name .'</a>';
  } else return $clan_name;
}


$ms2->query['from'] = "{$config['tables']['user']} AS u
    LEFT JOIN {$config['tables']['clan']} AS c ON u.clanid = c.clanid
    LEFT JOIN {$config['tables']['party_user']} AS p ON u.userid = p.user_id";

if ($party->party_id) $ms2->query['where'] = 'p.party_id = '. (int)$party->party_id;
else $ms2->query['where'] = '1 = 1';
($cfg['guestlist_showorga'])? $ms2->query['where'] .= ' AND u.type >= 1' : $ms2->query['where'] .= ' AND u.type = 1';

$ms2->config['EntriesPerPage'] = 20;

$ms2->AddTextSearchField('Benutzername', array('u.username' => '1337'));
$ms2->AddTextSearchField('Userid', array('u.userid' => 'exact'));
$ms2->AddTextSearchField('Name', array('u.name' => 'like', 'u.firstname' => 'like'));

if ($party->party_id) {
  $ms2->AddTextSearchDropDown('Bezahlt', 'p.paid', array('' => 'Alle', '0' => 'Nicht bezahlt', '>1' => 'Bezahlt'));
  if (!$cfg['sys_internet']) {
    $ms2->AddTextSearchDropDown('Eingecheckt', 'p.checkin', array('' => 'Alle', '0' => 'Nicht Eingecheckt', '>1' => 'Eingecheckt'));
    $ms2->AddTextSearchDropDown('Ausgecheckt', 'p.checkout', array('' => 'Alle', '0' => 'Nicht Ausgecheckt', '>1' => 'Ausgecheckt'));
  }
}

$ms2->AddResultField('Benutzername', 'u.username');
if ($auth['type'] >= 2 or !$cfg['sys_internet'] or $cfg['guestlist_shownames']) {
  $ms2->AddResultField('Vorname', 'u.firstname');
  $ms2->AddResultField('Nachname', 'u.name');
}
$ms2->AddSelect('c.url AS clanurl');
$ms2->AddResultField('Clan', 'c.name AS clan', 'ClanURLLink');

if ($party->party_id) {
  $ms2->AddResultField('Bez.', 'p.paid', 'PaidIconLink');
  $ms2->AddResultField('Sitz', 'u.userid', 'SeatNameLink');

  if (!$cfg['sys_internet']) {
    $ms2->AddResultField('In', 'p.checkin', 'MS2GetDate');
    $ms2->AddResultField('Out', 'p.checkout', 'MS2GetDate');
  }
}
$ms2->AddIconField('details', 'index.php?mod=guestlist&action=details&userid=', $lang['ms2']['details']);
$ms2->AddIconField('send_mail', 'index.php?mod=mail&action=newmail&step=2&userID=', $lang['ms2']['send_mail']);

if ($auth['type'] >= 2) {
  $ms2->AddMultiSelectAction(t('Auf "Bezahlt" setzen'), "index.php?mod=guestlist&step=10", 1, 'paid');
  $ms2->AddMultiSelectAction(t('Auf "Nicht Bezahlt" setzen'), "index.php?mod=guestlist&step=11", 1, 'not_paid');
  $ms2->AddMultiSelectAction(t('Einchecken'), "index.php?mod=guestlist&step=20", 1, 'in');
  $ms2->AddMultiSelectAction(t('Auschecken'), "index.php?mod=guestlist&step=21", 1, 'out');
  $ms2->AddMultiSelectAction(t('Ein- und Auschecken rückgängig'), "index.php?mod=guestlist&step=22", 1, 'not_out');
}
$ms2->PrintSearch('index.php?mod=guestlist', 'u.userid');
?>
