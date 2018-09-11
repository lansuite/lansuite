<?php

include_once('modules/usrmgr/search_main.inc.php');

$seat2 = new \LanSuite\Module\Seating\Seat2();

$ms2->AddTextSearchDropDown(t('Benutzertyp'), 'u.type', array('' => t('Alle'), '1' => t('Gast'), '!1' => 'Nicht Gast', '<0' => t('Gelöschte User'), '2' => t('Administrator'), '3' => t('Superadmin'), '2,3' => t('Orgas')));

$party_list = array('' => 'Alle');
$row = $db->qry("SELECT party_id, name FROM %prefix%partys");
while ($res = $db->fetch_array($row)) {
    $party_list[$res['party_id']] = $res['name'];
}
$db->free_result($row);
$ms2->AddTextSearchDropDown('Party', 'p.party_id', $party_list, $party->party_id);

$ms2->AddTextSearchDropDown(t('Eingecheckt'), 'p.checkin', array('' => t('Alle'), '0' => t('Nicht eingecheckt'), '>1' => t('Eingecheckt')));
$ms2->AddTextSearchDropDown(t('Ausgecheckt'), 'p.checkout', array('' => t('Alle'), '0' => t('Nicht ausgecheckt'), '>1' => t('Ausgecheckt')));

$ms2->AddSelect('u.type');

// If party is selected
if ($_POST["search_dd_input"][1] != '' || $_GET["search_dd_input"][1] != '') {
    $ms2->AddResultField(t('Bez.'), 'p.paid', 'PaidIconLinkFoodcenter');
    $ms2->AddResultField('In', 'p.checkin', 'MS2GetDate');
    $ms2->AddResultField('Out', 'p.checkout', 'MS2GetDate');
}

$ms2->AddResultField('Sitz', 'u.userid', 'SeatNameLinkFoodcenter');
$ms2->AddIconField('assign', $target_url, t('Zuweisen'));
$ms2->PrintSearch($current_url, 'u.userid');
