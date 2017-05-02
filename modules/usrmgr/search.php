<?php

include_once('modules/usrmgr/class_usrmgr.php');
$UsrMgr = new UsrMgr;

switch ($_GET['step']) {
    case 10:
        if (!$_POST['action'] and $_GET['userid']) {
            $_POST['action'][$_GET['userid']] = 1;
        }

        $err = '';
        foreach ($_POST['action'] as $key => $val) {
            if ($key == $auth['userid']) {
                $err = t('Du kannst nicht deinen eigenen Account sperren');
                break;
            }
        }

        if ($err) {
            $func->information($err);
        } else {
            foreach ($_POST['action'] as $key => $val) {
                $UsrMgr->LockAccount($key);
            }
            $func->confirmation(t('Accounts wurden gesperrt'));
        }
        break;

    case 11:
        if (!$_POST['action'] and $_GET['userid']) {
            $_POST['action'][$_GET['userid']] = 1;
        }

        $err = '';
        foreach ($_POST['action'] as $key => $val) {
            if ($key == $auth['userid']) {
                $err = t('Du kannst nicht deinen eigenen Account freigeben');
                break;
            }
        }

        if ($err) {
            $func->information($err);
        } else {
            foreach ($_POST['action'] as $key => $val) {
                $UsrMgr->UnlockAccount($key);
            }
            $func->confirmation(t('Accounts wurden freigegeben'));
        }
        break;
}

if ($cfg['sys_barcode_on']) {
    $dsp->AddBarcodeForm("<strong>" . t('Strichcode') . "</strong>", "", "index.php?mod=usrmgr&action=details&userid=");
}
include_once('modules/usrmgr/search.inc.php');
