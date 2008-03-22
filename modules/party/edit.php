<?php

include_once('inc/classes/class_masterform.php');
$mf = new masterform();

function CheckEndDate($enddate) {
  global $func;

  if ($func->str2time($enddate) < $func->str2time($_POST['startdate'])) return t('Der Endzeitpunkt muss nach dem Startzeitpunkt liegen');
  else return false;
}

function CheckSignonStartDate($sstartdate) {
  global $func;
  
  if ($func->str2time($sstartdate) > $func->str2time($_POST['startdate'])) return t('Der Anmeldestart muss vor dem Partystart liegen');
  else return false;
}

function CheckSignonEndDate($senddate) {
  global $func;
  if ($func->str2time($senddate) < $func->str2time($_POST['sstartdate'])) return t('Der Anmeldeschluss muss nach dem Anmeldestart liegen');
  if ($func->str2time($senddate) > $func->str2time($_POST['startdate'])) return t('Der Anmeldeschluss muss vor dem Partystart liegen');
  else return false;
}


function UpdatePartyID($id) {
  global $db, $config, $lang, $func;
  
  $db->query("UPDATE {$config['tables']['config']} SET cfg_value = '$id' WHERE cfg_key = 'signon_partyid'");
  $func->confirmation(t('Die Daten wurden erfolgreich geändert.')/* TRANS */);  
}

$mf->AddField(t('Partyname')/* TRANS */, 'name');
$mf->AddField(t('Anzahl Plätze')/* TRANS */, 'max_guest');
$mf->AddField(t('PLZ')/* TRANS */, 'plz');
$mf->AddField(t('Ort')/* TRANS */, 'ort');

$mf->AddField(t('Party startet am')/* TRANS */, 'startdate');
$mf->AddField(t('Party endet am')/* TRANS */, 'enddate', '', '', '', 'CheckEndDate');
$mf->AddField(t('Anmeldung startet am')/* TRANS */, 'sstartdate', '', '', '', 'CheckSignonStartDate');
$mf->AddField(t('Anmeldung endet am')/* TRANS */, 'senddate', '', '', '', 'CheckSignonEndDate');

/*
		// erster Preis einfügen
		if($_GET['var'] == "new"){
			$dsp->AddTextFieldRow("price_text",t('Text für Eintrittspreis'),$_POST['price_text'],$signon_error['price_text']);
			$dsp->AddTextFieldRow("price",t('Preis'),$_POST['price'],$signon_error['price']);
		}
*/

$mf->AdditionalDBUpdateFunction = 'UpdatePartyID';
$mf->SendForm('index.php?mod=party&action=edit', 'partys', 'party_id', $_GET['party_id']);

// Write ext_inc/party_infos/infos.xml on Change
if ($_GET['mf_step'] == '2') {
	include_once("modules/usrmgr/class_usrmgr.php");
	$usrmgr->WriteXMLStatFile();	
}

?>
