<?php
include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2();

$ms2->query['from'] = "{$config['tables']['user']} AS u
    LEFT JOIN {$config['tables']['party_user']} AS p ON u.userid = p.user_id
    LEFT JOIN {$config['tables']['seat_seats']} AS s ON u.userid = s.userid
    LEFT JOIN {$config['tables']['seat_block']} AS b ON s.blockid = b.blockid AND p.party_id = b.party_id";
$ms2->query['where'] = 'p.party_id = '. $party->party_id .' AND (s.status = 2 OR s.userid IS NULL)';

$ms2->config['EntriesPerPage'] = 20;

$ms2->AddTextSearchField('Userid', array('u.userid' => 'exact'));
$ms2->AddTextSearchField('Benutzername', array('u.username' => '1337'));
$ms2->AddTextSearchField('Name', array('u.name' => 'like', 'u.firstname' => 'like'));

$ms2->AddTextSearchDropDown('Bezahlt', 'p.paid', array('' => 'Alle', '0' => 'Nicht bezahlt', '>1' => 'Bezahlt'));
$ms2->AddTextSearchDropDown('Eingecheckt', 'p.checkin', array('' => 'Alle', '0' => 'Nicht Eingecheckt', '>1' => 'Eingecheckt'));
$ms2->AddTextSearchDropDown('Ausgecheckt', 'p.checkout', array('' => 'Alle', '0' => 'Nicht Ausgecheckt', '>1' => 'Ausgecheckt'));

$block_list = array('' => 'Alle');
$row = $db->query("SELECT blockid, name FROM {$config['tables']['seat_block']} WHERE party_id = {$party->party_id}");
while($res = $db->fetch_array($row)) $block_list[$res['blockid']] = $res['name'];
$db->free_result($row);
$ms2->AddTextSearchDropDown('Sitzblock', 'b.blockid', $block_list);

$ms2->AddResultField('Benutzername', 'u.username');
if ($auth['type'] >= 2) {
  $ms2->AddResultField('Vorname', 'u.firstname');
  $ms2->AddResultField('Nachname', 'u.name');
}
$ms2->AddResultField('Clan', 'u.clan', 'http://', 'u.clanurl');
$ms2->AddResultField('Bez.', 'p.paid', '', '', 'PaidIconLink');

$ms2->AddSelect('b.orientation');
$ms2->AddSelect('b.name AS blockname');
$ms2->AddSelect('s.blockid');
$ms2->AddSelect('s.col');
$ms2->AddSelect('s.row');
$ms2->AddResultField('Sitz', 's.status', '', '', 'SeatNameLink');

if (!$cfg['sys_internet']) {
  $ms2->AddResultField('In', 'p.checkin', '', '', 'GetDate');
  $ms2->AddResultField('Out', 'p.checkout', '', '', 'GetDate');
}
$ms2->AddIconField('details', 'index.php?mod=usrmgr&action=details&userid=', 'Details');

$ms2->PrintSearch('index.php?mod=guestlist', 'u.userid');
?>