<?php
include_once('inc/classes/class_masterdelete.php');
$md = new masterdelete();
$md->Delete('partylist', 'partyid', $_GET['partyid']);
