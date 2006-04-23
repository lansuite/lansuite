<?php

include_once('modules/usrmgr/class_usrmgr.php');
$UsrMgr = new UsrMgr;

switch ($_GET['step']) {
	case 10:
    foreach ($_POST['action'] as $key => $val) $UsrMgr->LockAccount($key);
    $func->confirmation($lang['usrmgr']['accounts_locked']);
	break;

	case 11:
    foreach ($_POST['action'] as $key => $val) $UsrMgr->UnlockAccount($key);
    $func->confirmation($lang['usrmgr']['accounts_unlocked']);
	break;
}

?>