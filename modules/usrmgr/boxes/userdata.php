<?php
/**
 * Generate Box for Userdata
 *
 * @package lansuite_core
 * @author knox
 * @version $Id: userdata.php 1993 2009-11-08 09:14:02Z jochen.jung $
 */
 
// If an admin is logged in as an user
// show admin name and switch back link

if ($olduserid > 0) {
    $old_user = $db->qry_first('SELECT username FROM %prefix%user WHERE userid=%int%', $olduserid);

    if (strlen($old_user['username']) > 14) {
        $old_user['username'] = substr($old_user['username'], 0, 11) . "...";
    }

    $box->DotRow(t('Admin').':', "", "", "admin", 0);
    $box->EngangedRow($dsp->FetchUserIcon($olduserid, $old_user["username"]), "", "", "admin", 0);
    $box->EngangedRow(t('Zurück wechseln'), "index.php?mod=auth&amp;action=switch_back", "", "admin", 0);
    $box->EmptyRow();
}

// Show username and ID
if (strlen($auth['username']) > 14) {
    $username = substr($auth['username'], 0, 11) . "...";
} else {
    $username = $auth['username'];
}

$userid_formated = sprintf("%04d", $auth['userid']);

$box->DotRow(t('Benutzer').": [<i>#$userid_formated</i>]". ' <a href="index.php?mod=auth&action=logout" class="icon_delete" title="'. t('Ausloggen') .'"></a>');
$box->EngangedRow($dsp->FetchUserIcon($auth['userid'], $username));
#$box->EngangedRow("");

#$icons .= $dsp->FetchIcon('index.php?mod=usrmgr&amp;action=details&amp;userid='. $auth["userid"], 'details', t('Pers. Details')) .' ';
#$icons .= $dsp->FetchIcon('index.php?mod=usrmgr&amp;action=settings', 'generate', t('Pers. Einstellungen')) .' ';
#$icons .= $dsp->FetchIcon('index.php?mod=auth&action=logout', 'no', t('Logout')) .' ';
#$box->EngangedRow($icons);

// Show last log in and login count
$user_lg = $db->qry_first("SELECT user.logins, max(auth.logintime) AS logintime
	FROM %prefix%user AS user
	LEFT JOIN %prefix%stats_auth AS auth ON auth.userid = user.userid
	WHERE user.userid = %int%
	GROUP BY auth.userid", $auth["userid"]);

if (isset($_POST['login']) and isset($_POST['password'])) {
    $box->DotRow(t('Logins'). ": <b>". $user_lg["logins"] .'</b>');
    $box->DotRow(t('Zuletzt eingeloggt'));
    date_default_timezone_set($cfg['sys_timezone']);
    $box->EngangedRow("<b>". date('d.m H:i', $user_lg["logintime"]) ."</b>");
}


// Show other links
#$box->DotRow(t('Meine Einstellungen'), "index.php?mod=usrmgr&amp;action=settings", '', "menu");
// Show Clan
if (($auth['clanid'] != null and $auth['clanid'] > 0) and $func->isModActive('clanmgr')) {
    $box->DotRow(t('Mein Clan'), "index.php?mod=clanmgr&amp;step=2&clanid=".$auth['clanid'], '', "menu");
}

// New-Mail Notice
if ($func->isModActive('mail')) {
    $mails_new = $db->qry("SELECT mailID
		FROM %prefix%mail_messages
		WHERE ToUserID = %int% AND mail_status = 'active' AND rx_date IS NULL
		", $auth['userid']);

    if ($cfg['mail_popup_on_new_mails'] and $db->num_rows($mails_new) > 0) {
        $found_not_popped_up_mail = false;
        while ($mail_new = $db->fetch_array($mails_new)) {
            if (!isset($_SESSION['mail_popup'][$mail_new['mailID']])) {
                $_SESSION['mail_popup'][$mail_new['mailID']] = 1;
                $found_not_popped_up_mail = true;
            }
        }
        $box->DotRow(t('Mein Postfach'), 'index.php?mod=mail', '', 'menu', 1);
    } else {
        $box->DotRow(t('Mein Postfach'), 'index.php?mod=mail', '', 'menu');
    }
    $db->free_result($mails_new);
}

// PDF-Ticket
if ($cfg["user_show_ticket"]) {
    $box->DotRow(t('Meine Eintrittskarte'), "index.php?mod=usrmgr&amp;action=myticket", "", "menu");
}

// Zeige Anmeldestatus
if ($party->count > 0 and $_SESSION['party_info']['partyend'] > time()) {
    $query_signstat = $db->qry_first("SELECT * FROM %prefix%party_user AS pu
		WHERE pu.user_id = %int% AND pu.party_id = %int%", $auth["userid"], $party->party_id);
    if ($query_signstat == null) {
        $signstat = '<font color="red">'. t('Nein') .'!</font>';
        $signstat_info = '<a href="index.php?mod=signon"><i> '. t('Hier anmelden') .'</i></a>';
        $paidstat = '<font color="red">'. t('Nein') .'!</font>';
    } else {
        $signstat = '<font color="green">'. t('Ja') .'!</font>';
        if (($query_signstat["paid"] == 1)||($query_signstat["paid"] == 2)) {
            $paidstat = '<font color="green">'. t('Ja') .'!</font>';
        } else {
            $paidstat = '<font color="red">'. t('Nein') .'!</font>';
            if ($cfg['signon_paylink']) {
                $paidstat_info = '<a href="'.$cfg['signon_paylink'].'"><i> '. t('Bezahlinfos') .'</i></a>';
            }
        }
    }
  
    $query_partys = $db->qry_first("SELECT * FROM %prefix%partys AS p WHERE p.party_id = %int%", $_SESSION["party_id"]);
                    
    $box->DotRow("<b>".$query_partys["name"]."</b> ". t('Status') .':');
    $box->EngangedRow(t('Angemeldet') .': <b>'. $signstat .'</b><br> '. $signstat_info);
    $box->EngangedRow(t('Bezahlt') .': <b>'. $paidstat .'</b><br> '. $paidstat_info);
}
