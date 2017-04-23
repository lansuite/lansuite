<?php

include_once("modules/cashmgr/class_accounting.php");


if (!$_GET['step']) {
    switch ($auth['type']) {
        default:
            $func->information("ACCESS_DENIED");
            break;
    
        case 1:
        case 2:
            $_GET['action'] = "myaccounting";
    
            break;
    
        case 3:
            $dia_quest[] .= t('Party Kalkulation')    ;
            $dia_quest[] .= t('Fremder Kontoauszug');
            $dia_quest[] .= t('Eigener Kontoauszug');
            $dia_link[]     .= "index.php?mod=cashmgr&action=show&step=1";
            $dia_link[]     .= "index.php?mod=cashmgr&action=myaccounting&act=him";
            $dia_link[]     .= "index.php?mod=cashmgr&action=myaccounting";
            $func->multiquestion($dia_quest, $dia_link, "");
            break;
    }
}

switch ($_GET['step']) {
    case 1:
        if ($auth['type'] < 3) {
            $func->information("ACCESS_DENIED");
        } else {
            $dsp->NewContent(t('Kalkulation'), t('Zur aktuellen Lanparty zum derzeitigen Stand'));
    
            $account = new accounting($party->party_id);
            $account->showCalculation();
        }
        break;
}
//$dsp->AddDoubleRow("Barausgaben insgesamt", $account->getCashTotalBudget());
//$dsp->AddDoubleRow("Guthaben insgesamt", $account->getOnlineTotalBudget());
