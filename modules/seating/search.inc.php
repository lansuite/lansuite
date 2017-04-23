<?php
/*
$current_url = 'index.php?mod=seating';
include_once('modules/seating/search_basic_blockselect.inc.php');
*/

$row = $db->qry_first('SELECT blockid FROM %prefix%seat_block
  WHERE party_id = %int%', $party->party_id);

$_GET['step'] = 2;
$_GET['blockid'] = $row['blockid'];

include('modules/seating/show.php');
