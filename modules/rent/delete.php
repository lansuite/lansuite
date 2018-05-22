<?php

$md = new \LanSuite\MasterDelete();
$md->References['rentuser'] = '';
$md->Delete('rentstuff', 'stuffid', $_GET['stuffid']);
