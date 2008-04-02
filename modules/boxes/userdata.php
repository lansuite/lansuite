<?php
$templ['box']['rows'] = "";

// If an admin is logged in as an user
// show admin name and switch back link

if ($olduserid > 0) {
    $old_user = $db->query_first("SELECT username FROM {$config['tables']['user']} WHERE userid='{$olduserid}'");

	if (strlen($old_user['username']) > 14) $old_user['username'] = substr($old_user['username'], 0, 11) . "...";

	$box->DotRow(t('Admin').':', "", "", "admin", 0);
    $box->EngangedRow("<b>{$old_user["username"]}</b>". $dsp->FetchUserIcon($olduserid), "", "", "admin", 0);
    $box->EngangedRow(t('Zurück wechseln'), "index.php?mod=auth&amp;action=switch_back", "", "admin", 0);
	$box->EmptyRow();
}

// Show username and ID
if (strlen($auth['username']) > 14) $username = substr($auth['username'], 0, 11) . "...";
else $username = $auth['username'];
$userid_formated = sprintf( "%0".$config['size']['userid_digits']."d", $auth['userid']);

$box->DotRow(t('Benutzer').": [<i>#$userid_formated</i>]". ' <a href="index.php?mod=auth&action=logout" onmouseover="return overlib(\''. t('Logout') .'\');" onmouseout="return nd();"><img src="design/'. $auth['design'] .'/images/arrows_delete.gif" width="12" height="13" border="0" /></a>');
$box->EngangedRow("<b>$username</b> ". $dsp->FetchUserIcon($auth["userid"]));
#$box->EngangedRow("");

#$icons .= $dsp->FetchIcon('index.php?mod=usrmgr&amp;action=details&amp;userid='. $auth["userid"], 'details', t('Pers. Details')) .' ';
#$icons .= $dsp->FetchIcon('index.php?mod=usrmgr&amp;action=settings', 'generate', t('Pers. Einstellungen')) .' ';
#$icons .= $dsp->FetchIcon('index.php?mod=auth&action=logout', 'no', t('Logout')) .' ';
#$box->EngangedRow($icons);

// Show last log in and login count
$user_lg = $db->query_first("SELECT user.logins, max(auth.logintime) AS logintime
	FROM {$config['tables']['user']} AS user
	LEFT JOIN {$config['tables']['stats_auth']} AS auth ON auth.userid = user.userid
	WHERE user.userid=\"".$auth["userid"]."\"
	GROUP BY auth.userid");

if (isset($_POST['login']) and isset($_POST['password'])) {
  $box->DotRow(t('Logins'). ": <b>". $user_lg["logins"] .'</b>');
  $box->DotRow(t('Zuletzt eingeloggt'));
  $box->EngangedRow("<b>". date('d.m H:i', $user_lg["logintime"]) ."</b>");
}


// Show other links
$box->DotRow(t('Meine Einstellungen'), "index.php?mod=usrmgr&amp;action=settings", '', "menu");

// New-Mail Notice
if (in_array('mail', $ActiveModules)) {
	$mails_new = $db->query("SELECT mailID
		FROM {$config["tables"]["mail_messages"]}
		WHERE ToUserID = '{$auth['userid']}' AND mail_status = 'active' AND rx_date IS NULL
		");

	if ($db->num_rows($mails_new) > 0) {
    $found_not_popped_up_mail = false;
    while ($mail_new = $db->fetch_array($mails_new)) {
      if (!isset($_SESSION['mail_popup'][$mail_new['mailID']])) {
        $_SESSION['mail_popup'][$mail_new['mailID']] = 1;
        $found_not_popped_up_mail = true;
      }
    }
    if ($cfg['mail_popup_on_new_mails'] and $found_not_popped_up_mail) {
      $box->EngangedRow($dsp->FetchIcon('index.php?mod=mail', 'receive_mail') .' <font color="red">'. t('Sie haben Post!') .'</font>');
#      $templ['box']['rows'] .= '<script language="JavaScript">
#      OpenWindow("index.php?mod=mail&amp;action=mail_popup&amp;design=popup", "new_mail");
#      </script>';
    }

  }
  $db->free_result($mails_new);
  
  $box->DotRow(t('Mein Postfach') , 'index.php?mod=mail', '', 'menu');
}

// PDF-Ticket
if ($cfg["user_show_ticket"]) $box->DotRow(t('Meine Eintrittskarte'), "index.php?mod=usrmgr&amp;action=myticket", "", "menu");

//Zeige Anmeldestatus
if($party->count != 0 & $_SESSION['party_info']['partyend'] > time())
{
$query_signstat = $db->query_first("SELECT * FROM {$config["tables"]["party_user"]} AS pu
				WHERE pu.user_id = '{$auth["userid"]}' AND pu.party_id = '{$_SESSION["party_id"]}'");
				
				if($query_signstat == null) 
				{
					$signstat = '<font color="red">'. t('Nein') .'!</font>';
					$signstat_info = '<a href="index.php?mod=signon"><i> '. t('Hier anmelden') .'</i></a>';
					$paidstat = '<font color="red">'. t('Nein') .'!</font>';
				}
				else
				{
					$signstat = '<font color="green">'. t('Ja') .'!</font>';
					
					if(($query_signstat["paid"] == 1)||($query_signstat["paid"] == 2))
						$paidstat = '<font color="green">'. t('Ja') .'!</font>';
					else
						$paidstat = '<font color="red">'. t('Nein') .'!</font>';
					}

$query_partys = $db->query_first("SELECT * FROM {$config["tables"]["partys"]} AS p
				WHERE p.party_id = '{$_SESSION["party_id"]}'");	
					
$box->DotRow("<b>".$query_partys["name"]."</b> ". t('Status') .':');
$box->EngangedRow(t('Angemeldet') .': <b>'. $signstat .'</b><br> '. $signstat_info);
$box->EngangedRow(t('Bezahlt') .': <b>'. $paidstat .'</b>');
}

?>
