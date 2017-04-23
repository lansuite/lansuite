<?php
// This File is a Part of the LS-Pluginsystem. It will be included in
// modules/usrmgr/details.php to generate Modulspezific Headermenue
// for Userdetails

// ADD HERE MODULSPECIFIC INCLUDES

// ADD HERE MODULPUGINCODE
//hardwareliste
$hardware = $db->qry_first("SELECT * FROM %prefix%hardware WHERE userid=%int%", $_GET['userid']);
$dsp->AddDoubleRow(t('CPU'), $dsp->AddIcon('cpu', '', '').' '.$hardware['cpu']);
$dsp->AddDoubleRow(t('Ram'), $dsp->AddIcon('ram', '', '').' '.$hardware['ram']);
$dsp->AddDoubleRow(t('Grafikkarte'), $dsp->AddIcon('graka', '', '').' '.$hardware['graka']);
$dsp->AddDoubleRow(t('Festplatte 1'), $dsp->AddIcon('hdd', '', '').' '.$hardware['hdd1']);
$dsp->AddDoubleRow(t('Festplatte 2'), $dsp->AddIcon('hdd', '', '').' '.$hardware['hdd2']);
$dsp->AddDoubleRow(t('Optisches Laufwerk 1'), $dsp->AddIcon('cd', '', '').' '.$hardware['cd1']);
$dsp->AddDoubleRow(t('Optisches Laufwerk 2'), $dsp->AddIcon('cd', '', '').' '.$hardware['cd2']);
$dsp->AddDoubleRow(t('Maus'), $dsp->AddIcon('maus', '', '').' '.$hardware['maus']);
$dsp->AddDoubleRow(t('Tastatur'), $dsp->AddIcon('tasta', '', '').' '.$hardware['tasta']);
$dsp->AddDoubleRow(t('Monitor'), $dsp->AddIcon('screen', '', '').' '.$hardware['monitor']);
$dsp->AddDoubleRow(t('Betriebssystem'), $dsp->AddIcon('os', '', '').' '.$hardware['os']);
$dsp->AddDoubleRow(t('Computername'), $dsp->AddIcon('pc', '', '').' '.$hardware['name']);
$dsp->AddDoubleRow(t('Sonstiges'), $hardware['sonstiges']);

if ($auth['type'] >= 2 or ($_GET['userid'] == $auth['userid'] and $cfg['user_self_details_change'])) {
    if ($hardware['hardwareid']) {
        $plug_bttn_hw = $dsp->FetchSpanButton(t('Editieren'), 'index.php?mod=hardware&action=edit&userid='. $_GET['userid'].'&hardwareid='.$hardware['hardwareid']);
    } else {
        $plug_bttn_hw .= $dsp->FetchSpanButton(t('Hinzufügen'), 'index.php?mod=hardware&action=edit&userid='. $_GET['userid'].'&hardwareid='.$hardware['hardwareid']);
    }
    $dsp->AddDoubleRow('', $plug_bttn_hw);
}
