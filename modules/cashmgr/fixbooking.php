<?php

$mf = new \LanSuite\MasterForm();

$dsp->NewContent(t('Betrag Buchen'), t('Fixbetrag (z.B Miete oder Sponsoring) oder Geldschiebungen'));

$mf->AddField('Betreff', 'comment');
$mf->AddField('Betrag (bei Negativen, minus davor)', 'movement');

$party_list = [];
$row = $db->qry("SELECT party_id, name FROM %prefix%partys");
while ($res = $db->fetch_array($row)) {
    $party_list[$res['party_id']] = $res['name'];
}
    
$group_list = [];
$row = $db->qry("SELECT id, caption FROM %prefix%cashmgr_group");
while ($res = $db->fetch_array($row)) {
    $group_list[$res['id']] = $res['caption'];
}

$mf->AddDropDownFromTable(t('Party'), 'partyid', 'party_id', 'name', 'partys');
$mf->AddDropDownFromTable(t('Gruppe'), 'groupid', 'id', 'caption', 'cashmgr_group');
$mf->AddFix('fix', '1');
$mf->AddFix('editorid', $auth['userid']);
$mf->AddFix('modul', 'cashmgr');

$cashIdParameter = $_GET['cashid'] ?? 0;
$mf->SendForm('index.php?mod=cashmgr&action=fixbooking', 'cashmgr_accounting', 'ID', $cashIdParameter);
