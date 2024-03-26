<?php

$row = $database->queryWithOnlyFirstRow('
  SELECT
    blockid
  FROM %prefix%seat_block
  WHERE
    party_id = ?', [$party->party_id]);

$_GET['step'] = 2;
$_GET['blockid'] = $row['blockid'];

include('modules/seating/show.php');
