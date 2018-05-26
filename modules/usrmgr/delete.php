<?php

$md = new \LanSuite\MasterDelete();

switch ($_GET['step']) {
    default:
        include_once('modules/usrmgr/search.inc.php');
        break;
    
    case 2:
        // Do some checks, before calling MD
        if (CheckDeleteUser($_GET['userid'])) {
            $md->Delete('user', 'userid', $_GET['userid']);
        }
        break;
    
    case 10:
        $success = 1;
        // Do some checks, before calling MD
        foreach ($_POST['action'] as $key => $val) {
            if (!CheckDeleteUser($key)) {
                $success = 0;
            }
        }
        if ($success) {
            $md->MultiDelete('user', 'userid');
        }
        break;
}
