<?php

include_once('modules/troubleticket/search_main.inc.php');

if ($auth['type'] < 2) {
    $ms2->query['where'] .=  "AND orgaonly = '0'";
}

$ms2->PrintSearch('index.php?mod=troubleticket', 't.ttid');
