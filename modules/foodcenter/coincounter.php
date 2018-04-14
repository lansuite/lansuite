<?php

$dsp->NewContent(t('Münzzähler'), t('Hier kannst du die gezählten M&uuml;nzen eintragen um den Total zu erfahren.'));
    
$smarty->assign('cents', t('Cent') . " " .t('M&uuml;nzen'));
$smarty->assign('coins', $cfg['sys_currency'] . " " . t('M&uuml;nzen'));
$smarty->assign('note', $cfg['sys_currency'] . " " . t('Noten'));
$smarty->assign('hardmoney', t('Hartgeld total:'));
$smarty->assign('softmoney', t('Weichgeld total:'));
$smarty->assign('money', t('Total'));
$smarty->assign('currency', $cfg['sys_currency']);

$dsp->AddSmartyTpl('coincounter', 'foodcenter');
