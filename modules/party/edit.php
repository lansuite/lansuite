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

$partyID = $_GET['party_id'] ?? 0;
$mf->SendForm('index.php?mod=party&action=edit', 'partys', 'party_id', $partyID);

// Write ext_inc/party_infos/infos.xml on Change
$masterFormStepParam = $_GET['mf_step'] ?? 0;
if ($masterFormStepParam == '2') {
    $partyObj = new \LanSuite\Module\Party\Party($partyID);
    $partyObj->WriteStatFiles();
}