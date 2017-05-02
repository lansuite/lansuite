<?php

include_once("modules/cashmgr/class_accounting.php");


$account = new accounting($auth['userid']);
$account->getAccounting();
