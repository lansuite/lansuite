<?php

function ShowField($key)
{
    global $cfg;

    if ($cfg["signon_show_".$key] > 0) {
        return 1;
    } else {
        return 0;
    }
}

$mf = new masterform();

$dsp->NewContent(t('Betrag Buchen'), t('Fixbetrag (z.B Miete oder Sponsoring) oder Geldschiebungen'));

$mf->AddField('Betreff', 'comment');
$mf->AddField('Betrag (bei Negativen, minus davor)', 'movement');

$user_list = array('' => '(keine Auswahl)');
    $row = $db->qry("SELECT userid, username FROM %prefix%user");
while ($res = $db->fetch_array($row)) {
    $user_list[$res['userid']] = $res['username'];
}

$mf->AddDropDownFromTable(t('Party'), 'partyid', 'party_id', 'name', 'partys');
$mf->AddDropDownFromTable(t('Betrifft Benutzer'), 'userid', 'userid', 'username', 'user', t('keine Auswahl'));
$mf->AddField('Fix Betrag', 'fix', 'tinyint(1)', FIELD_OPTIONAL);
$mf->AddFix('editorid', $auth['userid']);
$mf->AddFix('modul', 'cashmgr');

if (ShowField('fix')) {
    $dsp->AddSingleRow("Der zu buchende Betrag ist kein Fix-Betrag");
}

if ($mf->SendForm('index.php?mod=cashmgr&action=booking', 'cashmgr_accounting', 'ID', $_GET['cashid'])) {
}
