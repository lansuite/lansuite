<?php

function CheckOldPW($old_password) {
  global $db, $config, $auth, $lang;

	$get_dbpwd = $db->query_first("SELECT password FROM {$config["tables"]["user"]} WHERE userid = '{$auth["userid"]}'");
	if ($get_dbpwd["password"] != md5($old_password)) return $lang["usrmgr"]["chpwd_err_wrongoldpass"];

  return false;
}

$_GET['userid'] = $auth['userid'];
include_once('inc/classes/class_masterform.php');
$mf = new masterform();

$mf->AddField($lang['usrmgr']['chpwd_oldpassword'], 'old_password', IS_PASSWORD, '', '', 'CheckOldPW');
$mf->AddField($lang['usrmgr']['chpwd_password'], 'password', IS_NEW_PASSWORD);

$mf->SendForm('index.php?mod=usrmgr&action=changepw', 'user', 'userid', $_GET['userid']);

?>
