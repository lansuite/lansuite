<?php

include_once('inc/classes/class_masterform.php');
$mf = new masterform();

function CheckEndDate($enddate) {
  if ($enddate < $_POST['startdate']) return t('Der Endzeitpunkt muss nach dem Startzeitpunkt liegen');
  else return false;
}

function CheckSignonStartDate($sstartdate) {
  if ($sstartdate > $_POST['startdate']) return t('Der Anmeldestart muss vor dem Partystart liegen');
  else return false;
}

function CheckSignonEndDate($senddate) {
  if ($senddate < $_POST['sstartdate']) return t('Der Anmeldeschluss muss nach dem Anmeldestart liegen');
  if ($senddate > $_POST['startdate']) return t('Der Anmeldeschluss muss vor dem Partystart liegen');
  else return false;
}


function UpdatePartyID($id) {
  global $db, $config, $lang, $func;
  
  $db->query("UPDATE {$config['tables']['config']} SET cfg_value = '$id' WHERE cfg_key = 'signon_partyid'");
  $func->confirmation($lang['mf']['change_success']);  
}

$mf->AddField($lang['signon']['partyname'], 'name');
$mf->AddField($lang['signon']['max_guest'], 'max_guest');
$mf->AddField($lang['signon']['plz'], 'plz');
$mf->AddField($lang['signon']['ort'], 'ort');

$mf->AddField($lang['signon']['stime'], 'startdate');
$mf->AddField($lang['signon']['etime'], 'enddate', '', '', '', 'CheckEndDate');
$mf->AddField($lang['signon']['sstime'], 'sstartdate', '', '', '', 'CheckSignonStartDate');
$mf->AddField($lang['signon']['setime'], 'senddate', '', '', '', 'CheckSignonEndDate');

/*
		// erster Preis einfügen
		if($_GET['var'] == "new"){
			$dsp->AddTextFieldRow("price_text",$lang['signon']['price_text'],$_POST['price_text'],$signon_error['price_text']);
			$dsp->AddTextFieldRow("price",$lang['signon']['price'],$_POST['price'],$signon_error['price']);
		}
*/

$mf->AdditionalDBUpdateFunction = 'UpdatePartyID';
$mf->SendForm('index.php?mod=party&action=edit', 'partys', 'party_id', $_GET['party_id']);

?>