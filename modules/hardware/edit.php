<?php
// Edit Hardwareinfos for User
if ($auth['type'] >= 2 or ($_GET['userid'] == $auth['userid'] and $cfg['user_self_details_change'])) {
    $mf = new masterform();
    
    $dsp->NewContent(t("Hardware &auml;ndern"), t("Hier kannst du die Hardware eingeben"));
    $mf->AddField('CPU', 'cpu', '', '', masterform::FIELD_OPTIONAL);
    $mf->AddField('Ram (in MB)', 'ram', '', '', masterform::FIELD_OPTIONAL);
    $mf->AddField('Grafikkarte', 'graka', '', '', masterform::FIELD_OPTIONAL);
    $mf->AddField('Festplatte 1', 'hdd1', '', '', masterform::FIELD_OPTIONAL);
    $mf->AddField('Festplatte 2', 'hdd2', '', '', masterform::FIELD_OPTIONAL);
    $mf->AddField('Optisches Laufwerk 1', 'cd1', '', '', masterform::FIELD_OPTIONAL);
    $mf->AddField('Optisches Laufwerk 2', 'cd2', '', '', masterform::FIELD_OPTIONAL);
    $mf->AddField('Maus', 'maus', '', '', masterform::FIELD_OPTIONAL);
    $mf->AddField('Tastatur', 'tasta', '', '', masterform::FIELD_OPTIONAL);
    $mf->AddField('Monitor', 'monitor', '', '', masterform::FIELD_OPTIONAL);
    $mf->AddField('Betriebssystem', 'os', '', '', masterform::FIELD_OPTIONAL);
    $mf->AddField('Computername', 'name', '', '', masterform::FIELD_OPTIONAL);
    $mf->AddField('Sonstiges', 'sonstiges', text, '', masterform::FIELD_OPTIONAL);
    $mf->AddFix('userid', $_GET['userid']);
    $mf->SendForm('index.php?mod=hardware&action=edit&userid='.$_GET['userid'], 'hardware', 'hardwareid', $_GET['hardwareid']);
} else {
    $func->error(t('Du hast keine Berechtigung diese Daten zu &auml;ndern'));
}
