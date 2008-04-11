<?php

function ShowField($key){
	global $cfg;

	if ($cfg["signon_show_".$key] > 0) return 1;
	else return 0;
}

include_once('inc/classes/class_masterform.php');
$mf = new masterform();

$dsp->NewContent(t('Betrag Buchen'), t('Fixbetrag (z.B Miete oder Sponsoring) oder Geldschiebungen'));

$mf->AddField('Betreff', 'comment');
$mf->AddField('Betrag (bei Negativen, minus davor)', 'movement');

$party_list = array();
	$row = $db->query("SELECT party_id, name FROM {$config['tables']['partys']}");
	while($res = $db->fetch_array($row)) $party_list[$res['party_id']] = $res['name'];
	
$user_list = array('' => '(keine Auswahl)');
	$row = $db->query("SELECT userid, username FROM {$config['tables']['user']}");
	while($res = $db->fetch_array($row)) $user_list[$res['userid']] = $res['username'];

$mf->AddField('Party', 'partyid', IS_SELECTION, $party_list);
$mf->AddField('Betrifft Benutzer', 'userid', IS_SELECTION, $user_list, FIELD_OPTIONAL);
$mf->AddField('Fix Betrag', 'fix', 'tinyint(1)', FIELD_OPTIONAL);
$mf->AddFix('editorid', $auth['userid']);
$mf->AddFix('modul', 'cashmgr');

if(ShowField('fix'))
	$dsp->AddSingleRow("Der zu buchende Betrag ist kein Fix-Betrag");

if($mf->SendForm('index.php?mod=cashmgr&action=booking', 'cashmgr_accounting', 'ID', $_GET['cashid']))
{
	}

?>