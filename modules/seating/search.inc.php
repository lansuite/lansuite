<?php
include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2('seating');

// Get number of seats in block
function SeatsAvailable($blockid) {
	global $db, $config;

	$row = $db->query_first("SELECT COUNT(*) AS SeatsAvailable FROM {$config['tables']['seat_seats']} WHERE blockid='$blockid' AND status > 0 AND status < 7");
	return $row['SeatsAvailable'];
}

// Get number of seats in block
function SeatsOccupied($blockid) {
	global $db, $config;

	$row = $db->query_first("SELECT COUNT(*) AS SeatsOccupied FROM {$config['tables']['seat_seats']} WHERE blockid='$blockid' AND status = 2");
	return $row['SeatsOccupied'];
}

// Get number of seats in block
function SeatLoad($blockid) {
	global $dsp, $templ;
	
	$seats = SeatsAvailable($blockid);
	if ($seats != 0) {
		$SeatLoad = SeatsOccupied($blockid) / $seats * 100;
	} else {
		$SeatLoad = 0;
	}
	$templ['bar']['width'] = round($SeatLoad, 0) * 2;
	$templ['bar']['text'] = round($SeatLoad, 1) .'%';
	return $dsp->FetchModTpl('seating', 'bar');
}


$ms2->query['from'] = $config['tables']['seat_block'] .' AS b';
$ms2->query['where'] = 'b.party_id = '. $party->party_id;


$ms2->config['EntriesPerPage'] = 30;

$ms2->AddResultField('Blockname', 'b.name');
$ms2->AddResultField('Plätze', 'b.blockid', 'SeatsAvailable');
$ms2->AddResultField('Belegt', 'b.blockid', 'SeatsOccupied');
$ms2->AddResultField('Auslastung', 'b.blockid', 'SeatLoad');

$ms2->AddIconField('details', 'index.php?mod=seating&action=show&step=2&blockid=', 'Details');
$ms2->AddIconField('edit', 'index.php?mod=seating&action=edit&step=2&blockid=', 'Edit');
$ms2->AddIconField('delete', 'index.php?mod=seating&action=delete&step=2&blockid=', 'Delete');

$ms2->PrintSearch('index.php?mod=seating', 'b.blockid');
?>