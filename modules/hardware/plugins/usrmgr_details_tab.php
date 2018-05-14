<?php

/**
 * This file is part of the LS-Pluginsystem. It will be included in
 * modules/usrmgr/details.php to generate a module specific header menue
 * for user details
 */

$hardware = $db->qry_first("SELECT * FROM %prefix%hardware WHERE userid=%int%", $_GET['userid']);
$dsp->AddDoubleRow(t('CPU'), $dsp->FetchIcon('cpu').' '.$hardware['cpu']);
$dsp->AddDoubleRow(t('Ram'), $dsp->FetchIcon('ram').' '.$hardware['ram']);
$dsp->AddDoubleRow(t('Grafikkarte'), $dsp->FetchIcon('graka').' '.$hardware['graka']);
$dsp->AddDoubleRow(t('Festplatte 1'), $dsp->FetchIcon('hdd').' '.$hardware['hdd1']);
$dsp->AddDoubleRow(t('Festplatte 2'), $dsp->FetchIcon('hdd').' '.$hardware['hdd2']);
$dsp->AddDoubleRow(t('Optisches Laufwerk 1'), $dsp->FetchIcon('cd').' '.$hardware['cd1']);
$dsp->AddDoubleRow(t('Optisches Laufwerk 2'), $dsp->FetchIcon('cd').' '.$hardware['cd2']);
$dsp->AddDoubleRow(t('Maus'), $dsp->FetchIcon('maus').' '.$hardware['maus']);
$dsp->AddDoubleRow(t('Tastatur'), $dsp->FetchIcon('tasta').' '.$hardware['tasta']);
$dsp->AddDoubleRow(t('Monitor'), $dsp->FetchIcon('screen').' '.$hardware['monitor']);
$dsp->AddDoubleRow(t('Betriebssystem'), $dsp->FetchIcon('os').' '.$hardware['os']);
$dsp->AddDoubleRow(t('Computername'), $dsp->FetchIcon('pc').' '.$hardware['name']);
$dsp->AddDoubleRow(t('Sonstiges'), $hardware['sonstiges']);

/**
 * Allow edits of profile if user is admin or if it is the logged in user
 * and change of details is allowed via $cfg['user_self_details_change']
 */

if ($auth['type'] >= 2 || ($_GET['userid'] == $auth['userid'] && $cfg['user_self_details_change'])) {
    if ($hardware['hardwareid']) {
        $plug_bttn_hw = $dsp->FetchSpanButton(t('Editieren'), 'index.php?mod=hardware&action=edit&userid='. $_GET['userid'].'&hardwareid='.$hardware['hardwareid']);
    } else {
        $plug_bttn_hw .= $dsp->FetchSpanButton(t('Hinzufügen'), 'index.php?mod=hardware&action=edit&userid='. $_GET['userid'].'&hardwareid='.$hardware['hardwareid']);
    }
    $dsp->AddDoubleRow('', $plug_bttn_hw);
}
