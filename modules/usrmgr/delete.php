<?php

$md = new masterdelete();

/* Checks which MD does not perform */
function CheckDeleteUser($userid)
{
    global $db, $auth, $lang, $func;
    
    $get_data = $db->qry_first("SELECT username, type FROM %prefix%user WHERE userid = %int%", $userid);
    $get_party_data = $db->qry_first('SELECT 1 AS found FROM %prefix%party_user AS pu
        LEFT JOIN %prefix%partys AS p ON p.party_id = pu.party_id
        WHERE pu.user_id = %int% AND UNIX_TIMESTAMP(p.enddate) > UNIX_TIMESTAMP(NOW())', $userid);
    
    if ($auth["type"] == 2 and $get_data["type"] >= 2) {
        $func->error(t('Du hast nicht die erforderlichen Rechte, um einen Admin zu löschen'), "index.php?mod=usrmgr");
    } elseif ($get_party_data["found"]) {
        $func->error(t('Dieser Benutzer ist noch zu einer Party angemeldet. Melden sie ihn zuerst ab'), "index.php?mod=usrmgr");
    } elseif ($get_data["type"] < 0) {
        $func->error(t('Dieser Benutzer wurde bereits gelöscht'), "index.php?mod=usrmgr");
    } elseif ($auth["userid"] == $userid) {
        $func->error(t('Du kannst dich nicht selbst löschen'), "index.php?mod=usrmgr");
    } else {
        $db->qry("UPDATE %prefix%seat_seats SET status = '1', userid='0' WHERE userid = %int%", $userid);
        return true;
    }
    return false;
}

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
/*
$userid 	= $_GET["userid"];

switch ($_GET["step"]) {
    default:
    include_once('modules/usrmgr/search.inc.php');
    break;

    case 2:
        $get_data = $db->qry_first("SELECT username FROM %prefix%user WHERE userid = %int%", $userid);
        $username = $get_data["username"];

        if ($username != "") {
            $get_seat = $db->qry_first("SELECT count(*) as n FROM %prefix%seat_seats WHERE userid = %int%", $userid);
            if ($get_seat["n"] > 0) $seattext = t('und den vorhandenen Sitzplatz freigeben');

            $func->question(t('Bist du sicher, dass du den Benutzer %1 wirklich löschen %2 willst?', "<b>$username</b>", $seattext), "index.php?mod=usrmgr&action=delete&step=3&userid=$userid", "index.php?mod=usrmgr&action=details&userid=".$userid);
        } else $func->error(t('Dieser Benutzer existiert nicht'),"index.php?mod=usrmgr&action=delete");
    break;

    case 3:
    if (DeleteUser($userid)) $func->confirmation(t('Der Benutzer wurde erfolgreich gelöscht'), "index.php?mod=usrmgr&action=details&userid=".$userid);
    break;

    // Multi-Delete
    case 10:
      $success = true;
    foreach ($_POST['action'] as $key => $val) {
      if (!DeleteUser($key)) $success = false;
    }
        if ($success) $func->confirmation(t('Der Benutzer wurde erfolgreich gelöscht'), 'index.php?mod=usrmgr');
    break;
}*/
