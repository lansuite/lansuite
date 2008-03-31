<?php

include_once("modules/cashmgr/class_accounting.php");

$dsp->NewContent(t('Kalkulation'), t('Zur aktuellen Lanparty zum derzeitigen Stand'));

$account = new accounting($party->party_id);
$account->showCalculation();

//$dsp->AddDoubleRow("Barausgaben insgesamt", $account->getCashTotalBudget());
//$dsp->AddDoubleRow("Guthaben insgesamt", $account->getOnlineTotalBudget());


?>