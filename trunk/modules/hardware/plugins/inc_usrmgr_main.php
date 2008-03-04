<?php
// This File is a Part of the LS-Pluginsystem. It will be included in
// modules/usrmgr/details.php to generate Modulspezific Mainpage-entrys 
// (fielsets) for Userdetails

// ADD HERE MODULSPECIFIC INCLUDES

// ADD HERE MODULPUGINCODE

//hardwareliste
$hardware = $db->qry_first("SELECT * FROM %prefix%hardware WHERE userid=%int%", $_GET['userid']);
if ($hardware['cpu']) $dsp->AddDoubleRow(t('CPU'), $dsp->AddIcon('cpu','','').' '.$hardware['cpu']);
if ($hardware['ram']) $dsp->AddDoubleRow(t('Ram'),$dsp->AddIcon('ram','','').' '.$hardware['ram']);
if ($hardware['graka']) $dsp->AddDoubleRow(t('Grafikkarte'),$dsp->AddIcon('graka','','').' '.$hardware['graka']);
if ($hardware['hdd1']) $dsp->AddDoubleRow(t('Festplatte 1'),$dsp->AddIcon('hdd','','').' '.$hardware['hdd1']);
if ($hardware['hdd2']) $dsp->AddDoubleRow(t('Festplatte 2'),$dsp->AddIcon('hdd','','').' '.$hardware['hdd2']);
if ($hardware['cd1']) $dsp->AddDoubleRow(t('Optisches Laufwerk 1'),$dsp->AddIcon('cd','','').' '.$hardware['cd1']);
if ($hardware['cd2']) $dsp->AddDoubleRow(t('Optisches Laufwerk 2'),$dsp->AddIcon('cd','','').' '.$hardware['cd2']);
if ($hardware['maus']) $dsp->AddDoubleRow(t('Maus'),$dsp->AddIcon('maus','','').' '.$hardware['maus']);
if ($hardware['tasta']) $dsp->AddDoubleRow(t('Tastatur'),$dsp->AddIcon('tasta','','').' '.$hardware['tasta']);
if ($hardware['monitor']) $dsp->AddDoubleRow(t('Monitor'),$dsp->AddIcon('screen','','').' '.$hardware['monitor']);
if ($hardware['os']) $dsp->AddDoubleRow(t('Betriebssystem'),$dsp->AddIcon('xp','','').' '.$hardware['os']);
if ($hardware['name']) $dsp->AddDoubleRow(t('Computername'),$dsp->AddIcon('pc','','').' '.$hardware['name']);

if ($auth['type'] >= 2 or ($_GET['userid'] == $auth['userid'] and $cfg['user_self_details_change'])){
    if ($hardware['hardwareid']){
        $plug_bttn_hw = $dsp->FetchButton('index.php?mod=hardware&action=edit&userid='. $_GET['userid'].'&hardwareid='.$hardware['hardwareid'], 'edit');
    } else {
        $plug_bttn_hw .= $dsp->FetchButton('index.php?mod=hardware&action=edit&userid='. $_GET['userid'].'&hardwareid='.$hardware['hardwareid'],'add');
    }
    $dsp->AddDoubleRow('',$plug_bttn_hw);
}

?>