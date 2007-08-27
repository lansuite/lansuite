<?php

include_once('inc/classes/class_masterform.php');
$mf = new masterform();

$dsp->NewContent(t('Betrag Buchen'), t('Fixbetrag (z.B Miete oder Sponsoring) oder Geldschiebungen'));

$mf->AddField('Betreff', 'comment');
$mf->AddField('Betrag (bei Negativen, minus davor)', 'movement');

$party_list = array();
	$row = $db->query("SELECT party_id, name FROM {$config['tables']['partys']}");
	while($res = $db->fetch_array($row)) $party_list[$res['party_id']] = $res['name'];
	
$group_list = array();
	$row = $db->query("SELECT id, caption FROM {$config['tables']['cashmgr_group']}");
	while($res = $db->fetch_array($row)) $group_list[$res['id']] = $res['caption'];

	
$mf->AddField('Party', 'partyid', IS_SELECTION, $party_list);
$mf->AddField('Gruppe', 'groupid', IS_SELECTION, $group_list);
$mf->AddFix('fix', '1');
$mf->AddFix('editorid', $auth['userid']);
$mf->AddFix('modul', 'cashmgr');

$mf->SendForm('index.php?mod=cashmgr&action=fixbooking', 'cashmgr_accounting', 'ID', $_GET['cashid'])

?>