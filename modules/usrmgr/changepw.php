<?php

$_GET['userid'] = $auth['userid'];
$mf = new \LanSuite\MasterForm();

$mf->AddField(t('Derzeitiges Passwort'), 'old_password', \LanSuite\MasterForm::IS_PASSWORD, '', \LanSuite\MasterForm::FIELD_OPTIONAL, 'CheckOldPW');
$mf->AddField(t('Neues Passwort'), 'password', \LanSuite\MasterForm::IS_NEW_PASSWORD);

if ($mf->SendForm('index.php?mod=usrmgr&action=changepw', 'user', 'userid', $_GET['userid'])) {
    $authentication->set_cookie_pw($auth["userid"]);
}
