<?php

/**
 * @param string $old_password
 * @return bool|string
 */
function CheckOldPW($old_password)
{
    global $db, $auth;

    $get_dbpwd = $db->qry_first("SELECT password FROM %prefix%user WHERE userid = %int%", $auth["userid"]);
    if ($get_dbpwd["password"] != md5($old_password)) {
        return t('Passwort inkorrekt');
    }

    return false;
}

$_GET['userid'] = $auth['userid'];
$mf = new masterform();

$mf->AddField(t('Derzeitiges Passwort'), 'old_password', masterform::IS_PASSWORD, '', masterform::FIELD_OPTIONAL, 'CheckOldPW');
$mf->AddField(t('Neues Passwort'), 'password', masterform::IS_NEW_PASSWORD);

if ($mf->SendForm('index.php?mod=usrmgr&action=changepw', 'user', 'userid', $_GET['userid'])) {
    $authentication->set_cookie_pw($auth["userid"]);
}
