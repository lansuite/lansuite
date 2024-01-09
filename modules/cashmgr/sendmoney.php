<?php

$mf = new \LanSuite\MasterForm();
$dsp->NewContent(t('Geld überweisen'), t('Hier kannst du anderen Benutzern Geld überweisen'));

$AdminFound = 0;
$UserFound = 0;
$res = $db->qry("SELECT type, userid, username, firstname, name FROM %prefix%user WHERE type >= %string% ORDER BY type DESC, username", LS_AUTH_TYPE_USER);

if (!array_key_exists('toUserID', $_POST)) {
    $selections[-1] = "- Bitte wählen -";
}

while ($row = $db->fetch_array($res)) {
    if (!$AdminFound && $row['type'] > 1) {
        $selections['-OptGroup-1'] = t('Admins');
        $AdminFound = 1;
    }

    if (!$UserFound && $row['type'] <= 1) {
        $selections['-OptGroup-2'] = t('Benutzer');
        $UserFound = 1;
    }

    if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN || !$cfg['sys_internet'] || $cfg['guestlist_shownames']) {
        $selections[$row['userid']] = $row['username'] .' ('. $row['firstname'] .' '. $row['name'] .')';
    } else {
        $selections[$row['userid']] = $row['username'];
    }
}
$db->free_result($res);

$mf->AddField(t('Empfänger'), 'toUserid', \LanSuite\MasterForm::IS_SELECTION, $selections, \LanSuite\MasterForm::FIELD_OPTIONAL);
$mf->AddField('Betreff', 'comment');
$mf->AddField('Betrag', 'movement');
$mf->AddFix('fromUserid', $auth['userid']);
$mf->AddFix('modul', 'cashmgr');

$mf->CheckBeforeInserFunction = 'Check';

$cashIdParameter = $_GET['cashid'] ?? 0;
$mf->SendForm('index.php?mod=cashmgr&action=sendmoney', 'cashmgr_accounting', 'ID', $cashIdParameter);
