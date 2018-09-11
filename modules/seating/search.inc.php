<?php

$row = $db->qry_first('
  SELECT
    blockid
  FROM %prefix%seat_block
  WHERE
    party_id = %int%', $party->party_id);

$_GET['step'] = 2;
$_GET['blockid'] = $row['blockid'];

include('modules/seating/show.php');
