<?php

$mf = new \LanSuite\MasterForm();
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
    $mail = new \LanSuite\Module\Mail\Mail();
    $usrmgr = new \LanSuite\Module\UsrMgr\UserManager($mail);
    $usrmgr->WriteXMLStatFile();
}
