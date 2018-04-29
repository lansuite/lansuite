<?php

$mf = new \LanSuite\MasterForm();

if ($_POST['poster'] == '') {
    $_POST['poster'] = $auth['username'];
}

$mf->AddField(t('Autor'), 'poster', '', '');
if (!$auth['login']) {
    $mf->AddField('', 'captcha', \LanSuite\MasterForm::IS_CAPTCHA);
}
$mf->AddField(t('Eintrag'), 'text', '', \LanSuite\MasterForm::LSCODE_ALLOWED);
$mf->AddGroup(t('Eintrag'));

$mf->AddFix('date', time());
if (!$_GET['guestbookid']) {
    $mf->AddFix('userid', $auth['userid']);
}

$mf->SendForm('index.php?mod=guestbook&action=add', 'guestbook', 'guestbookid', $_GET['guestbookid']);
