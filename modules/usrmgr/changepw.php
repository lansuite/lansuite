<?php

require_once("inc/classes/class_pwhash.php");

function CheckOldPW($old_password)
{
    global $db, $auth, $lang;

    $get_dbpwd = $db->qry_first("SELECT password FROM %prefix%user WHERE userid = %int%", $auth["userid"]);
    if (!PasswordHash::verify($old_password, $get_dbpwd["password"])) {
        return t('Passwort inkorrekt');
    }

    return false;
}

$_GET['userid'] = $auth['userid'];
include_once('inc/classes/class_masterform.php');
$mf = new masterform();

$mf->AddField(t('Derzeitiges Passwort'), 'old_password', IS_PASSWORD, '', FIELD_OPTIONAL, 'CheckOldPW');
$mf->AddField(t('Neues Passwort'), 'password', IS_NEW_PASSWORD);

if ($mf->SendForm('index.php?mod=usrmgr&action=changepw', 'user', 'userid', $_GET['userid'])) {
    $authentication->set_cookie_pw($auth["userid"]);
}
