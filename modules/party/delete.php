<?php

$md = new \LanSuite\MasterDelete();
$md->Delete('partys', 'party_id', $_GET['party_id']);
