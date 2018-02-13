<?php

include_once("modules/usrmgr/class_usrmgr.php");
$usrmgr = new UsrMgr;

function IsAuthorizedAdmin()
{
    global $auth, $user_data, $link;
  
    $link = '';
    if ($auth['type'] >= 2 and $auth['type'] >= $user_data['type']) {
        return true;
    } else {
        return false;
    }
}

function getCheckin($checkin)
{
    global $dsp;
  
    if ($checkin) {
        return "<img src='design/images/icon_yes.png' border='0' alt='Ja' />";
    } else {
        return "<img src='design/images/icon_no.png' border='0' alt='Ja' />";
    }
}

function GetTypeDescription($type)
{
    global $lang;

    switch ($type) {
        case -2:
            return t('Organisator (gesperrt)');
        break;
        case -1:
            return t('Gast (gesperrt)');
        break;
        default:
            return t('Gast (deaktiviert)');
        break;
        case 1:
            return t('Gast');
        break;
        case 2:
            return t('Organisator');
        break;
        case 3:
            return t('Superadmin');
        break;
    }
}

//Get Barcode if exists and translate to userid
if ($_POST['barcodefield']) {
    $row = $db->qry_first('SELECT userid FROM %prefix%user WHERE barcode = %string%', $_POST["barcodefield"]);
    $_GET['userid']=$row['userid'];
}

// Select from table_user
// username,type,name,firstname,clan,email,paid,seatcontrol,checkin,checkout,portnumber,posts,wwclid,wwclclanid,comment
$user_data = $db->qry_first(
    "SELECT u.*, g.*, UNIX_TIMESTAMP(u.birthday) AS birthday, DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(u.birthday)), '%Y') + 0 AS age, u.avatar_path, u.signature, clan.name AS clan, clan.url AS clanurl, UNIX_TIMESTAMP(lastlogin) AS lastlogin
    FROM %prefix%user AS u
    LEFT JOIN %prefix%party_usergroups AS g ON u.group_id = g.group_id
    LEFT JOIN %prefix%clan AS clan ON u.clanid = clan.clanid
    WHERE u.userid = %int%",
    $_GET['userid']
);

// If exists
if (!$user_data['userid']) {
    $func->error(t('Dieser Benutzer existiert nicht'));
} else {
    $framework->AddToPageTitle($user_data['username']);

    $user_party = $db->qry_first("SELECT u.*, p.*, UNIX_TIMESTAMP(u.checkin) AS checkin, UNIX_TIMESTAMP(u.checkout) AS checkout FROM %prefix%party_user AS u
    LEFT JOIN %prefix%party_prices AS p ON u.price_id = p.price_id
    WHERE user_id = %int% AND u.party_id = %int%
    GROUP BY u.user_id
    ", $_GET['userid'], $party->party_id);
    $count_rows = $db->qry_first('SELECT COUNT(*) AS count FROM %prefix%board_posts WHERE userid = %int%', $_GET['userid']);
    $party_seatcontrol = $db->qry_first('SELECT * FROM %prefix%party_prices WHERE price_id = %int%', $user_party['price_id']);

    $user_fields = $db->qry("SELECT name, caption, optional FROM %prefix%user_fields");
    ($db->num_rows($user_fields) > 0)? $hasUserFields = 1 : $hasUserFields = 0;

    $dsp->NewContent(t('Benutzerdetails von %1', $user_data['username']), t('Hier findest du alle Details zu dem Benutzer %1', $user_data['username']));
    $dsp->StartTabs();

    $dsp->StartTab(t('Spieler'), 'assign');

  // First name, last name, username, user ID
    $name = '<table width="100%" cellspacing="0" cellpadding="0"><tr><td>';
    if (!$cfg['sys_internet'] or $auth['type'] > 1 or $auth['userid'] == $_GET['userid']) {
        if ($user_data['firstname']) {
            $name .= $user_data['firstname'] .' ';
        }
        if ($user_data['name']) {
            $name .= $user_data['name'] .' ';
        }
    }
    if ($user_data['username']) {
        $name .= '('. $user_data['username'] .') ';
    }
    $name .= '['. $user_data['userid'] .']</td><td align="right">&nbsp;';
    if (IsAuthorizedAdmin()) {
        ($user_data['locked'])? $name .= ' '. $dsp->AddIcon('locked', 'index.php?mod=usrmgr&step=11&userid='. $_GET['userid'], t('Account freigeben'))
        : $name .= ' '. $dsp->AddIcon('unlocked', 'index.php?mod=usrmgr&step=10&userid='. $_GET['userid'], t('Account sperren'));
    }
    if (IsAuthorizedAdmin()) {
        $name .= ' '. $dsp->AddIcon('assign', 'index.php?mod=auth&action=switch_to&userid='. $_GET['userid'], t('Benutzer wechseln'));
    }
    if ($_GET['userid'] == $auth['userid']) {
        $name .= ' '. $dsp->AddIcon('change_pw', 'index.php?mod=usrmgr&action=changepw', t('Passwort ändern'));
    } elseif (IsAuthorizedAdmin()) {
        $name .= ' '. $dsp->AddIcon('change_pw', 'index.php?mod=usrmgr&action=newpwd&step=2&userid='. $_GET['userid'], t('Passwort ändern'));
    }
    if (IsAuthorizedAdmin() or ($_GET['userid'] == $auth['userid'])) { # and $cfg['user_self_details_change']
        $name .= ' '. $dsp->AddIcon('edit', 'index.php?mod=usrmgr&action=change&step=1&userid='. $_GET['userid'], t('Editieren'));
    }
    if ($auth['type'] >= 3) {
        $name .= ' '. $dsp->AddIcon('delete', 'index.php?mod=usrmgr&action=delete&step=2&userid='. $_GET['userid'], t('Löschen'));
    }
    $name .= '</td></tr></table>';
    $dsp->AddDoubleRow(t('Benutzername'), $name);

  // User group
    if (!$user_data['group_name']) {
        $dsp->AddDoubleRow(t('Benutzergruppe'), t('Keiner Gruppe zugeordnet'));
    } else {
        $dsp->AddDoubleRow(t('Benutzergruppe'), $user_data['group_name'] .' ['. $user_data['group_id'] .']');
    }

  // Clan
    if ($cfg['signon_show_clan']) {
        $clan = '<table width="100%" cellspacing="0" cellpadding="0"><tr><td>';
        $clan .= '<a href="index.php?mod=clanmgr&step=2&clanid='.$user_data["clanid"].'">'.$user_data["clan"].'</a>';
        if (substr($user_data['clanurl'], 0, 7) != 'http://') {
            $user_data['clanurl'] = 'http://'. $user_data['clanurl'];
        }
        if ($user_data['clanurl']) {
            $clan .= " [<a href=\"{$user_data['clanurl']}\" target=\"_blank\">{$user_data['clanurl']}</a>]";
        }
        $clan .= '</td><td align="right">&nbsp;';
        if ($user_data['clan'] != '' and (IsAuthorizedAdmin() or ($user_data['clanid'] == $auth['clanid'] and $user_data['clanadmin'] == 1))) {
            $clan .= $dsp->AddIcon('change_pw', 'index.php?mod=clanmgr&action=clanmgr&step=10&clanid='. $user_data['clanid'], t('Passwort ändern')) .
            $dsp->AddIcon('edit', 'index.php?mod=clanmgr&action=clanmgr&step=30&clanid='. $user_data['clanid'], t('Editieren'));
        }
        $clan .= '</td></tr></table>';
        $dsp->AddDoubleRow(t('Clan'), $clan);
    }

  // Party Checkin, paid, ...
    if ($party->count > 0) {
        $clan = '<table width="100%"><tr><td>';
        $party_row = '';
        $link = '';
        ($user_party['user_id'])? $party_row .= t('Angemeldet') :  $party_row .= t('Nicht Angemeldet');
        if (IsAuthorizedAdmin()) {
            ($user_party['paid'])? $link = 'index.php?mod=guestlist&step=11&userid='. $_GET['userid']
            : $link = 'index.php?mod=guestlist&step=10&userid='. $_GET['userid'];
        }
        // Paid
        ($user_party['paid'])? $party_row .= ', '. $dsp->AddIcon('paid', $link, t('Bezahlt')) : $party_row .= ', '. $dsp->AddIcon('not_paid', $link, t('Nicht bezahlt'));
        if ($user_party['price_text']) {
            $party_row .= ' ['. $user_party['price_text'] .']';
        }
        // Platzpfand
        if ($party_seatcontrol['depot_price'] > 0) {
            $party_row .= ', '. $party_seatcontrol['depot_desc'];
            $party_row .= ($user_party['seatcontrol']) ? t(' gezahlt') : t(' NICHT gezahlt');
        }
        // CheckIn CheckOut
        $link = '';
        if (IsAuthorizedAdmin() and !$user_party['checkin']) {
            $link = 'index.php?mod=guestlist&step=20&userid='. $_GET['userid'];
        }
        if ($user_party['checkin']) {
            $party_row .= ' '. $dsp->AddIcon('in', $link, t('Eingecheckt')) .'['. $func->unixstamp2date($user_party['checkin'], 'datetime') .']';
        } else {
            $party_row .= ' '.$dsp->AddIcon('not_in', $link, t('Nicht eingecheckt'));
        }

          $link = '';
        if (IsAuthorizedAdmin() and !$user_party['checkout'] and $user_party['checkin']) {
            $link = 'index.php?mod=guestlist&step=21&userid='. $_GET['userid'];
        }
        if ($user_party['checkout']) {
            $party_row .= ' '. $dsp->AddIcon('out', $link, t('Ausgecheckt')) .'['. $func->unixstamp2date($user_party['checkout'], 'datetime') .']';
        } else {
            $party_row .= ' '.$dsp->AddIcon('not_out', $link, t('Nicht ausgecheckt'));
        }

        if (IsAuthorizedAdmin() and $user_party['checkin'] > 0 and $user_party['checkout'] > 0) {
            $party_row .= $dsp->AddIcon('delete', 'index.php?mod=guestlist&step=22&userid='. $_GET['userid'], 'Reset Checkin');
        }

          $dsp->AddDoubleRow("Party '<i>". $_SESSION['party_info']['name'] ."</i>'", $party_row);
    }

  // Seating
    if ($func->isModActive('seating')) {
        include_once("modules/seating/class_seat.php");
        $seat2 = new seat2();

        $user_data_seating = $seat2->SeatOfUserArray($_GET['userid']);
        if ($user_data_seating['block'] == '') {
            $seat = t('Kein Sitzplatz ausgewählt / zugeteilt.');
        } else {
            $seat = $seat2->SeatOfUser($_GET['userid'], 0, 2);
            if (IsAuthorizedAdmin()) {
                $seat .= ' '. $dsp->AddIcon('delete', "index.php?mod=seating&action=seatadmin&step=20&blockid={$user_data_seating['block']}&row={$user_data_seating['row']}&col={$user_data_seating['col']}&userid={$user_data['userid']}", t('Löschen'));
            }
        }
        if (IsAuthorizedAdmin()) {
            $seat .= ' '. $dsp->AddIcon('edit', 'index.php?mod=seating&action=seatadmin&step=2&userid='. $_GET['userid'], t('Editieren'));
        }
        if ($cfg['sys_internet'] == 0 and $user_data_seating['ip']) {
            $seat .= ' IP:'. $user_data_seating['ip'];
        }
          $dsp->AddDoubleRow(t('Sitzplatz'), $seat);
    }

  //kontostand
    if ($func->isModActive('foodcenter')) {
        $result = $db->qry_first("SELECT SUM(movement) AS total FROM %prefix%food_accounting WHERE userid = %int%", $_GET['userid']);
        if ($result['total'] == "") {
            $amount = 0;
        } else {
            $amount = $result['total'];
        }

          $kontostand = round($amount, 2) . " " . $cfg['sys_currency'];
          $kontostand .= ', '. t('Zahlung vornehmen') .': '.$dsp->AddIcon('paid', 'index.php?mod=foodcenter&action=account&act=payment&step=2&userid='.$_GET['userid']);
          $dsp->AddDoubleRow(t('Aktueller Kontobetrag:'), $kontostand);
    }

    $dsp->AddFieldsetStart(t('Kontakt'));
  // Address
    $address = '';
    if (($user_data['street'] != '' or $user_data['hnr']) and ($auth['type'] >= 2 or ($auth['userid'] == $_GET['userid'] and $cfg['user_showownstreet'] == '1'))) {
        $address .= $user_data['street'] .' '. $user_data['hnr'] .', ';
    }
    if (($user_data['plz'] != '' or $user_data['city']) and ($cfg['user_showcity4all'] == '1' or $auth['type'] >= 2 or $auth['userid'] == $_GET['userid'])) {
        $address .= $user_data['plz'] .' '. $user_data['city'];
    }
    if ($address) {
        $dsp->AddDoubleRow(t('Adresse'), $address);
    }

  // Phone
    $phone = '';
    if ($user_data['telefon'] and (IsAuthorizedAdmin() or $auth['userid'] == $_GET['userid'])) {
        $phone .= $dsp->AddIcon('phone', '', 'Phone'). ' '. $user_data['telefon'] . ' ';
    }
    if ($user_data['handy'] and (IsAuthorizedAdmin() or $auth['userid'] == $_GET['userid'])) {
        $phone .= $dsp->AddIcon('cellphone', '', 'Handy'). ' '. $user_data['handy'] . ' ';
    }
    $dsp->AddDoubleRow(t('Telefon'), $phone);

  // Mail
    $mail = '<table width="100%" cellspacing="0" cellpadding="0"><tr><td>';
    if ((!$cfg['sys_internet'] and $cfg['user_showmail4all']) or $auth['type'] >= 2 or $auth['userid'] == $_GET['userid']) {
        $mail .= '<a href="mailto:'. $user_data['email'] .'">'. $user_data['email'] .'</a> ';
    }
    $mail .= '[Newsletter-Abo:';
    ($user_data['newsletter']) ? $mail .= $dsp->AddIcon('yes') : $mail .= $dsp->AddIcon('no');
    $mail .= ']';
    $mail .= '</td><td align="right">&nbsp;';
    if ($auth['login'] and $func->isModActive('mail')) {
        $mail .= $dsp->AddIcon('send_mail', 'index.php?mod=mail&action=newmail&step=2&userID='. $_GET['userid'], t('LANSuite-Mail an den User senden')) .' ';
    }
    $mail .= '</td></tr></table>';
    $dsp->AddDoubleRow(t('Email'), $mail);
    

  // Messenger
    $messenger = '<table width="100%" cellspacing="0" cellpadding="0"><tr><td>';
    if ($user_data['icq']) {
        if ($cfg['sys_internet']) {
            $messenger .= ' <a href="http://wwp.icq.com/scripts/search.dll?to='. $user_data['icq'] .'" target="_blank"><img src="ext_inc/footer_buttons/icq.gif" alt="ICQ" title="ICQ#'. $user_data['icq'] .'" border="0" /></a> ';
        } else {
            $messenger .= ' <img src="ext_inc/footer_buttons/icq.gif" alt="ICQ" title="ICQ: #'. $user_data['icq'] .'" border="0" /> ';
        }
    }
    if ($user_data['msn']) {
        $messenger .= ' <img src="ext_inc/footer_buttons/msn.gif" alt="MSN" title="MSN: '. $user_data['msn'] .'" border="0" /> ';
    }
    if ($user_data['xmpp']) {
        $messenger .= ' <img src="ext_inc/footer_buttons/msn.gif" alt="XMPP" title="XMPP: '. $user_data['xmpp'] .'" border="0" /> ';
    }
    if ($user_data['skype']) {
        if ($cfg['sys_internet']) {
            $messenger .= '<a href="skype:'. $user_data['skype'] .'?call"><img src="ext_inc/footer_buttons/skype.gif" alt="Skype" title="Skype: '. $user_data['skype'] .'" border="0" /></a>';
        } else {
            $messenger .= ' <img src="ext_inc/footer_buttons/skype.gif" alt="Skype" title="Skype: '. $user_data['skype'] .'" border="0" />';
        }
    }
    $messenger .= '</td><td align="right">&nbsp;';
    (in_array($_GET['userid'], $authentication->online_users))? $messenger .= $dsp->AddIcon('yes', '', t('Benutzer ist Online')) : $messenger .= $dsp->AddIcon('no', '', t('Benutzer ist Offline'));
    if ($auth['login'] and $func->isModActive('msgsys')) {
        $messenger .= $dsp->AddIcon('add_user', 'index.php?mod=msgsys&action=addbuddy&step=2&userid='. $_GET['userid'], t('Den User zu deiner Buddyliste hinzufügen')) .' ';
    }
    $messenger .= '</td></tr></table>';
    $dsp->AddDoubleRow('Messenger', $messenger);
    $dsp->AddFieldsetEnd();

    $dsp->AddFieldsetStart(t('Verschiedenes'));

  // User-Type
    $dsp->AddDoubleRow(t('Benutzertyp'), GetTypeDescription($user_data['type']));

  // Perso
    if ($user_data['perso'] and ($auth['type'] >= 2 or ($auth['userid'] == $_GET['userid'] and $cfg['user_showownstreet'] == '1'))) {
        $dsp->AddDoubleRow(t('Passnummer / Sonstiges'), $user_data['perso'] .'<br>'. t('Hinweis: Die Angaben zu Straße und Passnummer sind nur für dich und die Organisatoren sichtbar.'));
    }

  // Birthday
    if ($cfg['sys_internet'] == 0 or $auth['type'] >= 2 or $auth['userid'] == $_GET['userid']) {
        $dsp->AddDoubleRow("Geburtstag", ((int) $user_data['birthday'])? $func->unixstamp2date($user_data['birthday'], 'date') .' ('. $user_data['age']  .')' : t('Nicht angegeben'));
    }

  // Gender
    $geschlecht[0] = t('Nicht angegeben');
    $geschlecht[1] = t('Männlich');
    $geschlecht[2] = t('Weiblich');
    $dsp->AddDoubleRow(t('Geschlecht'), $geschlecht[$user_data['sex']]);

  // Picture
    if ($func->chk_img_path($user_data['picture'])) {
        $dsp->AddDoubleRow(t('Benutzerbild'), '<img src="'. $user_data['picture'] .'">');
    }

  // Comment
    $dsp->AddDoubleRow(t('Kommentar'), ($user_data['comment'] == "") ? "" : $func->text2html($user_data['comment']));
    $dsp->AddFieldsetEnd();
  
    $plugin = new plugin('usrmgr_details_main');
    while (list($caption, $inc) = $plugin->fetch()) {
        $dsp->AddFieldsetStart($caption);
        include_once($inc);
        $dsp->AddFieldsetEnd();
    }
    $dsp->EndTab();

    $dsp->StartTab(t('Lesezeichen'), 'details');
    $dsp->AddFieldsetStart(t('In Kommentaren'));
    switch ($_GET['step']) {
        case 10:
            $md = new masterdelete();
            $md->MultiDelete('comments_bookmark', 'bid');
            break;
    }

    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2();

    $ms2->query['from'] = "%prefix%comments_bookmark AS b";
    $ms2->query['where'] = 'b.userid = '. (int)$_GET['userid'];
    $ms2->query['default_order_by'] = 'b.relatedto_item';
    $ms2->config['EntriesPerPage'] = 20;

    $ms2->AddResultField(t('Modul'), 'b.relatedto_item');
    $ms2->AddResultField(t('Beitrags ID'), 'b.relatedto_id');
    $ms2->AddResultField(t('Internet-Mail'), 'b.email');
    $ms2->AddResultField(t('System-Mail'), 'b.sysemail');

    if ($auth['type'] >= 3) {
        $ms2->AddMultiSelectAction(t('Löschen'), 'index.php?mod=usrmgr&action=details&userid='. $_GET['userid'] .'&step=10&tab=1', 1);
    }

    $ms2->PrintSearch('index.php?mod=usrmgr&action=details&userid='. $_GET['userid'] .'&tab=1', 'b.bid');
    $dsp->AddFieldsetEnd();
    $dsp->EndTab();
  
    $dsp->StartTab(t('Sonstiges'));
  // logins, last login
    if ($auth['type'] >= 2) {
        $lastLoginTS = $db->qry_first("SELECT max(logintime) FROM %prefix%stats_auth WHERE userid = %int% AND login = '1'", $_GET['userid']);
        $dsp->AddDoubleRow(t('Logins'), $user_data['logins']);
        if ($user_data['lastlogin']) {
            $loginTime = t('am/um: ') . $func->unixstamp2date($user_data['lastlogin'], 'daydatetime');
        } else {
            $loginTime = t('noch nicht eingeloggt');
        }
          $dsp->AddDoubleRow(t('Letzter Login'), $loginTime);
    }

  // signature
    $dsp->AddDoubleRow(t('Signatur'), $func->text2html($user_data['signature']));

  // avatar
    ($user_data['avatar_path'] != "" and $user_data['avatar_path'] != "0") ?
      $avatar = "<img border=\"0\" src=\"". $user_data['avatar_path'] . "\">"
      : $avatar = t('Dieser Benutzer hat keinen Avatar ausgewählt.');
    $dsp->AddDoubleRow(t('Avatar'), $avatar);

  // Including comment-engine
    if ($auth["login"] == 1) {
        new Mastercomment('User', $_GET['userid']);
    }

    $dsp->EndTab();
  
    if ($hasUserFields) {
        $dsp->StartTab(t('Eigene Felder'), 'database');
        while ($user_field = $db->fetch_array($user_fields)) {
            $dsp->AddDoubleRow($user_field['caption'], $user_data[$user_field['name']]);
        }
        $dsp->EndTab();
    }

    if ($auth['type'] >= 3) {
        $dsp->StartTab(t('Sessions'), 'generate');

        include_once('modules/mastersearch2/class_mastersearch2.php');
        $ms2 = new mastersearch2('usrmgr');

        $ms2->query['from'] = "%prefix%stats_auth a";
        $ms2->query['where'] = "a.userid = ". (int)$_GET['userid'];

        $ms2->config['EntriesPerPage'] = 50;

        $ms2->AddResultField(t('Session-ID'), 'a.sessid');
        $ms2->AddResultField(t('IP'), 'a.ip');
    #$ms2->AddResultField(t('Login?'), 'a.login');
        $ms2->AddResultField(t('Hits'), 'a.hits');
        $ms2->AddResultField(t('Visits'), 'a.visits');
    #$ms2->AddResultField(t('Letzter Aufruf'), 'a.logtime', 'MS2GetDate');
        $ms2->AddResultField(t('Eingeloggt'), 'a.logintime', 'MS2GetDate');
        $ms2->AddResultField(t('Letzter Aufruf'), 'a.lasthit', 'MS2GetDate');

        $ms2->PrintSearch('index.php?mod=usrmgr&action=details&userid='. $_GET['userid'] .'&headermenuitem=5', 'a.sessid');

        $dsp->EndTab();
    }

    $plugin = new plugin('usrmgr_details_tab');
    while (list($caption, $inc, $icon) = $plugin->fetch()) {
        $dsp->StartTab($caption, $icon);
        include_once($inc);
        $dsp->EndTab();
    }

    $dsp->EndTabs();

    $db->free_result($user_fields);

    if ($auth['type'] >= 2) {
        $buttons = $dsp->FetchSpanButton(t('Benutzerübersicht'), 'index.php?mod='. $_GET['mod'] .'&action=search').' ';
    } else {
        $buttons = $dsp->FetchSpanButton(t('Benutzerübersicht'), 'index.php?mod=guestlist&action=guestlist').' ';
    }
    $row = $db->qry_first('SELECT userid FROM %prefix%user WHERE type > 0 AND userid < %int% order by userid desc', $_GET['userid']);
    if ($row['userid']) {
        $buttons .= $dsp->FetchSpanButton(t('Vorheriger Benutzer'), 'index.php?mod=usrmgr&action=details&userid='. $row['userid']).' ';
    }
    $row = $db->qry_first('SELECT userid FROM %prefix%user WHERE type > 0 AND userid > %int%', $_GET['userid']);
    if ($row['userid']) {
        $buttons .= $dsp->FetchSpanButton(t('Nächster Benutzer'), 'index.php?mod=usrmgr&action=details&userid='. $row['userid']);
    }

    $dsp->AddDoubleRow('', $buttons);
    $dsp->AddContent();
}
