<?php

$md = new \LanSuite\MasterDelete();
$md->Delete('partylist', 'partyid', $_GET['partyid']);
