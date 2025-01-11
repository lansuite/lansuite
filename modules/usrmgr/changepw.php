<?php

$_GET['userid'] = $auth['userid'];
$mf = new \LanSuite\MasterForm();

$mf->AddField(t('Derzeitiges Passwort'), 'old_password', \LanSuite\MasterForm::IS_PASSWORD, '', \LanSuite\MasterForm::FIELD_OPTIONAL, 'CheckOldPW');
$mf->AddField(t('Neues Passwort'), 'password', \LanSuite\MasterForm::IS_NEW_PASSWORD);
