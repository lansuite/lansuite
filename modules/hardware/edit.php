<?php

// Edit Hardwareinfos for User
if ($auth['type'] >= 2 or ($_GET['userid'] == $auth['userid'] and $cfg['user_self_details_change'])) {
    $mf = new \LanSuite\MasterForm();
    
    $dsp->NewContent(t("Hardware &auml;ndern"), t("Hier kannst du die Hardware eingeben"));
    $mf->AddField('CPU', 'cpu', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField('Ram (in MB)', 'ram', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField('Grafikkarte', 'graka', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField('Festplatte 1', 'hdd1', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField('Festplatte 2', 'hdd2', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField('Optisches Laufwerk 1', 'cd1', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField('Optisches Laufwerk 2', 'cd2', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField('Maus', 'maus', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField('Tastatur', 'tasta', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField('Monitor', 'monitor', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField('Betriebssystem', 'os', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField('Computername', 'name', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField('Sonstiges', 'sonstiges', text, '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddFix('userid', $_GET['userid']);
    $mf->SendForm('index.php?mod=hardware&action=edit&userid='.$_GET['userid'], 'hardware', 'hardwareid', $_GET['hardwareid']);
} else {
    $func->error(t('Du hast keine Berechtigung diese Daten zu &auml;ndern'));
}
