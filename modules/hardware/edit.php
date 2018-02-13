<?php
// Edit Hardwareinfos for User
if ($auth['type'] >= 2 or ($_GET['userid'] == $auth['userid'] and $cfg['user_self_details_change'])) {
    $mf = new masterform();
    
    $dsp->NewContent(t("Hardware &auml;ndern"), t("Hier kannst du die Hardware eingeben"));
    $mf->AddField('CPU', 'cpu', '', '', FIELD_OPTIONAL);
    $mf->AddField('Ram (in MB)', 'ram', '', '', FIELD_OPTIONAL);
    $mf->AddField('Grafikkarte', 'graka', '', '', FIELD_OPTIONAL);
    $mf->AddField('Festplatte 1', 'hdd1', '', '', FIELD_OPTIONAL);
    $mf->AddField('Festplatte 2', 'hdd2', '', '', FIELD_OPTIONAL);
    $mf->AddField('Optisches Laufwerk 1', 'cd1', '', '', FIELD_OPTIONAL);
    $mf->AddField('Optisches Laufwerk 2', 'cd2', '', '', FIELD_OPTIONAL);
    $mf->AddField('Maus', 'maus', '', '', FIELD_OPTIONAL);
    $mf->AddField('Tastatur', 'tasta', '', '', FIELD_OPTIONAL);
    $mf->AddField('Monitor', 'monitor', '', '', FIELD_OPTIONAL);
    $mf->AddField('Betriebssystem', 'os', '', '', FIELD_OPTIONAL);
    $mf->AddField('Computername', 'name', '', '', FIELD_OPTIONAL);
    $mf->AddField('Sonstiges', 'sonstiges', text, '', FIELD_OPTIONAL);
    $mf->AddFix('userid', $_GET['userid']);
    $mf->SendForm('index.php?mod=hardware&action=edit&userid='.$_GET['userid'], 'hardware', 'hardwareid', $_GET['hardwareid']);
    $dsp->AddContent();
} else {
    $func->error(t('Du hast keine Berechtigung diese Daten zu &auml;ndern'));
}
