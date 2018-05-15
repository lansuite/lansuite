<?php

$mf = new \LanSuite\MasterForm();

function CheckEndDate($enddate)
{
    global $func;

    if ($func->str2time($enddate) < $func->str2time($_POST['startdate'])) {
        return t('Der Endzeitpunkt muss nach dem Startzeitpunkt liegen');
    } else {
        return false;
    }
}

function CheckSignonStartDate($sstartdate)
{
    global $func;
  
    if ($func->str2time($sstartdate) > $func->str2time($_POST['startdate'])) {
        return t('Der Anmeldestart muss vor dem Partystart liegen');
    } else {
        return false;
    }
}

function CheckSignonEndDate($senddate)
{
    global $func;
    if ($func->str2time($senddate) < $func->str2time($_POST['sstartdate'])) {
        return t('Der Anmeldeschluss muss nach dem Anmeldestart liegen');
    }
    if ($func->str2time($senddate) > $func->str2time($_POST['startdate'])) {
        return t('Der Anmeldeschluss muss vor dem Partystart liegen');
    } else {
        return false;
    }
}


function UpdatePartyID($id)
{
    global $db, $func, $cfg;
  
    if (!$cfg['signon_partyid']) {
        $db->qry("UPDATE %prefix%config SET cfg_value = %int% WHERE cfg_key = 'signon_partyid'", $id);
    }
    $_SESSION['party_id'] = $id;
    $func->confirmation(t('Die Daten wurden erfolgreich geändert.'), 'index.php?mod=party');
}

$mf->AddField(t('Partyname'), 'name');
$mf->AddField(t('Anzahl Plätze'), 'max_guest');
$mf->AddField(t('PLZ'), 'plz');
$mf->AddField(t('Ort'), 'ort');
$mf->AddField(t('Mindestalter (0 = keine Altersbeschr&auml;nkung)'), 'minage');

$mf->AddField(t('Party startet am'), 'startdate');
$mf->AddField(t('Party endet am'), 'enddate', '', '', '', 'CheckEndDate');
$mf->AddField(t('Anmeldung startet am'), 'sstartdate', '', '', '', 'CheckSignonStartDate');
$mf->AddField(t('Anmeldung endet am'), 'senddate', '', '', '', 'CheckSignonEndDate');

$mf->AdditionalDBUpdateFunction = 'UpdatePartyID';
$mf->SendForm('index.php?mod=party&action=edit', 'partys', 'party_id', $_GET['party_id']);

// Write ext_inc/party_infos/infos.xml on Change
if ($_GET['mf_step'] == '2') {
    include_once("modules/usrmgr/class_usrmgr.php");
    $usrmgr->WriteXMLStatFile();
}
