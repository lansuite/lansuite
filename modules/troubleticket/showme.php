<?php

include_once('modules/troubleticket/search_main.inc.php');
$ms2->query['where'] .=  " AND t.target_userid = '{$auth['userid']}'";
$ms2->PrintSearch('index.php?mod=troubleticket', 't.ttid');
