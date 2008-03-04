<?php

include_once("modules/usrmgr/class_usrmgr.php");
$usrmgr = new UsrMgr;

function IsAuthorizedAdmin() {
  global $auth, $user_data, $link;
  
  $link = '';
  if ($auth['type'] >= 2 and $auth['type'] >= $user_data['type']) return true;
  else return false;
}

function getCheckin($checkin) {
  global $dsp;
  
  if($checkin)
    return "<img src='design/images/icon_yes.png' border='0' alt='Ja' />";
  else
    return "<img src='design/images/icon_no.png' border='0' alt='Ja' />";

}



function GetTypeDescription($type) {
    global $lang;

    switch ($type) {
        case -2:    return $lang['usrmgr']['details_orga_disabled'];        break;
        case -1:    return $lang['usrmgr']['details_guest_disabled'];       break;
        default:    return $lang['usrmgr']['details_guest_deactivated'];    break;
        case 1:     return $lang['usrmgr']['details_guest'];                break;
        case 2:     return $lang['usrmgr']['details_orga'];                 break;
        case 3:     return $lang['usrmgr']['details_superadmin'];               break;
    }
}

#$db->qry('SELECT * FROM %prefix%user WHERE name = %string% and id = %int%', 'jo\'ch\en', 1);

// Select from table_user
// username,type,name,firstname,clan,email,paid,seatcontrol,checkin,checkout,portnumber,posts,wwclid,wwclclanid,comment
$user_data = $db->qry_first("SELECT u.*, g.*, u.birthday AS birthday, DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(u.birthday)), '%Y') + 0 AS age, c.avatar_path, c.signature, s.seatid, s.blockid, s.col, s.row, s.ip, clan.name AS clan, clan.url AS clanurl
    FROM %prefix%user AS u
    LEFT JOIN %prefix%usersettings AS c ON u.userid = c.userid
    LEFT JOIN %prefix%party_usergroups AS g ON u.group_id = g.group_id
    LEFT JOIN %prefix%clan AS clan ON u.clanid = clan.clanid
    LEFT JOIN %prefix%seat_seats AS s ON u.userid = s.userid
    WHERE u.userid = %int%",
  $_GET['userid']);

// If exists
if (!$user_data['userid']) $func->error($lang['usrmgr']['checkin_nouser'], '');
else {
    $user_party = $db->qry_first('SELECT *, UNIX_TIMESTAMP(checkin) AS checkin, UNIX_TIMESTAMP(checkout) AS checkout FROM %prefix%party_user WHERE user_id = %int% AND party_id = %int%', $_GET['userid'], $party->party_id);
    $user_online = $db->qry_first('SELECT 1 AS found FROM %prefix%stats_auth WHERE userid = %int% AND login = \'1\' AND lasthit > %int%', $_GET['userid'], time() - 60*10);
    $count_rows = $db->qry_first('SELECT COUNT(*) AS count FROM %prefix%board_posts WHERE userid = %int%', $_GET['userid']);
    $party_seatcontrol = $db->qry_first('SELECT * FROM %prefix%party_prices WHERE price_id = %int%', $user_party['price_id']);

    $menunames[1] = $lang['usrmgr']['details_playerinfos'];
    if (in_array('tournament2', $ActiveModules)) $menunames[2] = $lang['usrmgr']['details_tournament'];
    $menunames[3] = $lang['usrmgr']['details_misc'];
  $user_fields = $db->query("SELECT name, caption, optional FROM {$config['tables']['user_fields']}");
  if ($db->num_rows($user_fields) > 0) $menunames[4] = $lang['usrmgr']['details_own_fields'];
    if(!$vars['headermenuitem']) { $vars['headermenuitem'] = 1; }
    if ($auth['type'] >= 3) $menunames[5] = t('Sessions');

// BEGIN Pluginsystem for Usermanager
    include_once("inc/classes/class_plugin.php");
    $plug_headerm = new plugin($ActiveModules,'usrmgr_headermenue');
    // make headermenuekeys
    $plug_hm_count = 6;  // Start headermenuecount at 6 because last switch = 5
    foreach ($plug_headerm->get_list() as $plug_headerm_set) {
        $menunames[$plug_hm_count] = $plug_headerm_set['caption'];
        // find correct Caption for Headermenueitem
        $arr = array_keys($menunames,$plug_headerm_set['caption']);
        $plug_headermenue_modul[$arr[0]] = $plug_headerm_set['modul'];
        $plug_hm_count++;
    }
// END Pluginsystem for Usermanager


    $dsp->NewContent(str_replace("%USER%", $user_data['username'], $lang['usrmgr']['details_caption']), $lang['usrmgr']['details_subcaption']);
    $dsp->AddHeaderMenu($menunames,"index.php?mod=usrmgr&action=details&userid=".$_GET['userid'],$vars['headermenuitem']);

    // < menu details (step/headermenuitem)
    switch($vars['headermenuitem']){

        case 1: // Main
        // First name, last name, username, user ID
        $name = '<table width="100%" cellspacing="0" cellpadding="0"><tr><td>';
        if (!$cfg['sys_internet'] or $auth['type'] > 1 or $auth['userid'] == $_GET['userid']) {
            if ($user_data['firstname']) $name .= $user_data['firstname'] .' ';
            if ($user_data['name']) $name .= $user_data['name'] .' ';
        }
        if ($user_data['username']) $name .= '('. $user_data['username'] .') ';
        $name .= '['. $user_data['userid'] .']</td><td align="right">&nbsp;';
        if (IsAuthorizedAdmin()) {
        ($user_data['locked'])? $name .= ' '. $dsp->AddIcon('locked', 'index.php?mod=usrmgr&step=11&userid='. $_GET['userid'], t('Account freigeben'))
        : $name .= ' '. $dsp->AddIcon('unlocked', 'index.php?mod=usrmgr&step=10&userid='. $_GET['userid'], t('Account sperren'));
      }
        if (IsAuthorizedAdmin())
        $name .= ' '. $dsp->AddIcon('assign', 'index.php?mod=usrmgr&action=switch_user&step=10&userid='. $_GET['userid'], $lang['button']['switch_user']);
      if ($_GET['userid'] == $auth['userid'])
        $name .= ' '. $dsp->AddIcon('change_pw', 'index.php?mod=usrmgr&action=changepw', $lang['ms2']['change_pw']);
      elseif (IsAuthorizedAdmin())
        $name .= ' '. $dsp->AddIcon('change_pw', 'index.php?mod=usrmgr&action=newpwd&step=2&userid='. $_GET['userid'], $lang['ms2']['change_pw']);
      if (IsAuthorizedAdmin() or ($_GET['userid'] == $auth['userid'] and $cfg['user_self_details_change']))
        $name .= ' '. $dsp->AddIcon('edit', 'index.php?mod=usrmgr&action=change&step=1&userid='. $_GET['userid'], $lang['button']['edit']);
      if ($auth['type'] >= 3)
        $name .= ' '. $dsp->AddIcon('delete', 'index.php?mod=usrmgr&action=delete&step=2&userid='. $_GET['userid'], $lang['button']['delete']);
      $name .= '</td></tr></table>'; 
        $dsp->AddDoubleRow('Benutzername', $name);


      // User group
      if (!$user_data['group_name']) $dsp->AddDoubleRow(t('Benutzergruppe'), t('Keiner Gruppe zugeordnet'));
      else $dsp->AddDoubleRow(t('Benutzergruppe'), $user_data['group_name'] .' ['. $user_data['group_id'] .']');
      

            // Clan
      if ($cfg['signon_show_clan']) {
        $clan = '<table width="100%" cellspacing="0" cellpadding="0"><tr><td>';
            $clan .= $user_data['clan'];
            if (substr($user_data['clanurl'], 0, 7) != 'http://') $user_data['clanurl'] = 'http://'. $user_data['clanurl'];
            if ($user_data['clanurl']) $clan .= " [<a href=\"{$user_data['clanurl']}\" target=\"_blank\">{$user_data['clanurl']}</a>]";
        $clan .= '</td><td align="right">&nbsp;';
            if ($user_data['clan'] != '' and (IsAuthorizedAdmin() or $user_data['clanid'] == $auth['clanid']))
          $clan .= $dsp->AddIcon('change_pw', 'index.php?mod=clanmgr&action=clanmgr&step=10&clanid='. $user_data['clanid'], $lang['ms2']['change_pw']) .
            $dsp->AddIcon('edit', 'index.php?mod=clanmgr&action=clanmgr&step=30&clanid='. $user_data['clanid'], $lang['ms2']['edit']);
        $clan .= '</td></tr></table>';
            $dsp->AddDoubleRow($lang['usrmgr']['details_clan'], $clan);
      }

      // Party Checkin, paid, ...
      if ($party->count > 0) {
        $clan = '<table width="100%"><tr><td>';
        $party_row = '';
        $link = '';
        ($user_party['user_id'])? $party_row .= $lang['usrmgr']['details_signon'] :  $party_row .= $lang['usrmgr']['details_not_signon'];
        if (IsAuthorizedAdmin()) ($user_party['paid'])? $link = 'index.php?mod=guestlist&step=11&userid='. $_GET['userid']
          : $link = 'index.php?mod=guestlist&step=10&userid='. $_GET['userid'];
        // Paid
        ($user_party['paid'])? $party_row .= ', '. $dsp->AddIcon('paid', $link, $lang['usrmgr']['paid_yes']) : $party_row .= ', '. $dsp->AddIcon('not_paid', $link, $lang['usrmgr']['paid_no']);
        if ($user_party['paid'] == 1) $party_row .= ' ['. $lang['usrmgr']['details_paid_vvk'] .']';
        elseif ($user_party['paid'] == 2) $party_row .= ' ['. $lang['usrmgr']['details_paid_ak'] .']';
        // Platzpfand
        if ($party_seatcontrol['depot_price'] > 0){
            $party_row .= ', '. $party_seatcontrol['depot_desc'];
            $party_row .= ($user_party['seatcontrol']) ? $lang['usrmgr']['details_seat_paid'] : $lang['usrmgr']['details_seat_not_paid'];
        }
        // CheckIn CheckOut
        $link = '';
        if (IsAuthorizedAdmin() and !$user_party['checkin']) $link = 'index.php?mod=guestlist&step=20&userid='. $_GET['userid'];
        if ($user_party['checkin']) $party_row .= ' '. $dsp->AddIcon('in', $link, $lang['usrmgr']['checkin']) .'['. $func->unixstamp2date($user_party['checkin'], 'datetime') .']';
        else $party_row .= ' '.$dsp->AddIcon('not_in', $link, $lang['usrmgr']['checkin_no']);
  
        $link = '';
        if (IsAuthorizedAdmin() and !$user_party['checkout'] and $user_party['checkin']) $link = 'index.php?mod=guestlist&step=21&userid='. $_GET['userid'];
        if ($user_party['checkout']) $party_row .= ' '. $dsp->AddIcon('out', $link, $lang['usrmgr']['checkout']) .'['. $func->unixstamp2date($user_party['checkout'], 'datetime') .']';
        else $party_row .= ' '.$dsp->AddIcon('not_out', $link, $lang['usrmgr']['checkout_no']);
  
        if (IsAuthorizedAdmin() and $user_party['checkin'] > 0 and $user_party['checkout'] > 0) $party_row .= $dsp->AddIcon('delete', 'index.php?mod=guestlist&step=22&userid='. $_GET['userid'], 'Reset Checkin');
  
        $dsp->AddDoubleRow("Party '<i>". $_SESSION['party_info']['name'] ."</i>'", $party_row);
      }

            // Seating
            if (in_array('seating', $ActiveModules)) { 
            if ($user_data['blockid'] == '') $seat = $lang['usrmgr']['details_no_seat'];
            else {
              $seat = $seat2->SeatOfUser($_GET['userid'], 0, 2);
              if (IsAuthorizedAdmin()) {
            $seat .= ' '. $dsp->AddIcon('delete', "index.php?mod=seating&action=free_seat&step=3&blockid={$user_data['blockid']}&row={$user_data['row']}&col={$user_data['col']}", $lang['button']['delete']);
          }
            }
              if (IsAuthorizedAdmin()) $seat .= ' '. $dsp->AddIcon('edit', 'index.php?mod=seating&action=seatadmin&step=2&userid='. $_GET['userid'], $lang['button']['edit']);
            if ($cfg['sys_internet'] == 0 and $user_data['ip']) $seat .= ' IP:'. $user_data['ip'];
        $dsp->AddDoubleRow($lang['usrmgr']['details_seat'], $seat);
      }
      

      $dsp->AddFieldsetStart($lang['usrmgr']['contact']);
            // Address
            $address = '';
            if (($user_data['street'] != '' or $user_data['hnr']) and ($auth['type'] >= 2 or ($auth['userid'] == $_GET['userid'] and $cfg['user_showownstreet'] == '1')))
                $address .= $user_data['street'] .' '. $user_data['hnr'] .', ';
            if (($user_data['plz'] != '' or $user_data['city']) and ($cfg['user_showcity4all'] == '1' or $auth['type'] >= 2 or $auth['userid'] == $_GET['userid']))
                $address .= $user_data['plz'] .' '. $user_data['city'];
            if ($address) $dsp->AddDoubleRow($lang['usrmgr']['address'], $address);


            // Phone
            $phone = '';
      if ($user_data['telefon'] and (IsAuthorizedAdmin() or $auth['userid'] == $_GET['userid'])) $phone .= $dsp->AddIcon('phone', '', 'Phone'). ' '. $user_data['telefon'] . ' ';
      if ($user_data['handy'] and (IsAuthorizedAdmin() or $auth['userid'] == $_GET['userid'])) $phone .= $dsp->AddIcon('cellphone', '', 'Handy'). ' '. $user_data['handy'] . ' ';
      $dsp->AddDoubleRow($lang['usrmgr']['telefon'], $phone);


            // Mail
        $mail = '<table width="100%" cellspacing="0" cellpadding="0"><tr><td>';
            if ((!$cfg['sys_internet'] and $cfg['user_showmail4all']) or $auth['type'] >= 2 or $auth['userid'] == $_GET['userid']) {
        $mail .= '<a href="mailto:'. $user_data['email'] .'">'. $user_data['email'] .'</a> ';
      }
      $mail .= '[Newsletter-Abo:';
      ($user_data['newsletter']) ? $mail .= $dsp->AddIcon('yes') : $mail .= $dsp->AddIcon('no');
      $mail .= ']';
      $mail .= '</td><td align="right">&nbsp;';
          if ($auth['login'] and in_array('mail', $ActiveModules)) $mail .= $dsp->AddIcon('send_mail', 'index.php?mod=mail&action=newmail&step=2&userID='. $_GET['userid'], $lang['usrmgr']['details_mail_help']) .' ';
      $mail .= '</td></tr></table>';
      $dsp->AddDoubleRow($lang['usrmgr']['add_email'], $mail);
      

            // Messenger
        $messenger = '<table width="100%" cellspacing="0" cellpadding="0"><tr><td>';
      if ($user_data['icq']) {
        if ($cfg['sys_internet']) $messenger .= ' <a href="http://wwp.icq.com/scripts/search.dll?to='. $user_data['icq'] .'" target="_blank"><img src="ext_inc/footer_buttons/icq.gif" alt="ICQ" title="ICQ#'. $user_data['icq'] .'" border="0" /></a> ';
        else $messenger .= ' <img src="ext_inc/footer_buttons/icq.gif" alt="ICQ" title="ICQ: #'. $user_data['icq'] .'" border="0" /> ';
      }
      if ($user_data['msn']) $messenger .= ' <img src="ext_inc/footer_buttons/msn.gif" alt="MSN" title="MSN: '. $user_data['msn'] .'" border="0" /> ';
      if ($user_data['skype']) {
        if ($cfg['sys_internet']) $messenger .= '<a href="skype:'. $user_data['skype'] .'?call"><img src="ext_inc/footer_buttons/skype.gif" alt="Skype" title="Skype: '. $user_data['skype'] .'" border="0" /></a>';
        else $messenger .= ' <img src="ext_inc/footer_buttons/skype.gif" alt="Skype" title="Skype: '. $user_data['skype'] .'" border="0" />';
      }
      $messenger .= '</td><td align="right">&nbsp;';
      ($user_online['found']) ? $messenger .= $dsp->AddIcon('yes', '', t('Benutzer ist Online')) : $messenger .= $dsp->AddIcon('no', '', t('Benutzer ist Offline'));
          if ($auth['login'] and in_array('msgsys', $ActiveModules)) $messenger .= $dsp->AddIcon('add_user', 'index.php?mod=msgsys&action=addbuddy&step=2&userid='. $_GET['userid'], $lang['usrmgr']['details_buddy_help']) .' ';
      $messenger .= '</td></tr></table>';
      $dsp->AddDoubleRow('Messenger', $messenger);
      $dsp->AddFieldsetEnd();
      

      $dsp->AddFieldsetStart($lang['usrmgr']['misc']);
            // User-Type
            $dsp->AddDoubleRow($lang['usrmgr']['details_type'], GetTypeDescription($user_data['type']));


      // Perso
            if ($user_data['perso'] and ($auth['type'] >= 2 or ($auth['userid'] == $_GET['userid'] and $cfg['user_showownstreet'] == '1')))
                $dsp->AddDoubleRow($lang['usrmgr']['details_passport_misc'], $user_data['perso'] .'<br>'. $lang['usrmgr']['details_hint']);

            // Birthday
            if ($cfg['sys_internet'] == 0 OR $auth['type'] >= 2 OR $auth['userid'] == $_GET['userid'])
            list($tyear,$tmonth,$tday) = explode("-", $user_data['birthday']);
            $dsp->AddDoubleRow("Geburtstag", ((int) $user_data['birthday'])? "$tday.$tmonth.$tyear" .' ('. $user_data['age']  .')' : $lang['usrmgr']['details_not_entered']);

            // Gender
            $geschlecht[0] = $lang['usrmgr']['details_not_entered'];
            $geschlecht[1] = $lang['usrmgr']['details_male'];
            $geschlecht[2] = $lang['usrmgr']['details_female'];
            $dsp->AddDoubleRow($lang['usrmgr']['details_sex'], $geschlecht[$user_data['sex']]);


      // Picture
            if ($user_data['picture'] != '' AND $user_data['picture'] != '0') $dsp->AddDoubleRow($lang['usrmgr']['picture'], '<img src="'. $user_data['picture'] .'">');


            // Comment
            $dsp->AddDoubleRow($lang['usrmgr']['details_comment'], ($user_data['comment'] == "") ? "" : $func->text2html($user_data['comment']));
      $dsp->AddFieldsetEnd();

// BEGIN Pluginsystem for Usermanager
    include_once("inc/classes/class_plugin.php");
    $plug_main = new plugin($ActiveModules,'usrmgr_main');
    foreach ($plug_main->get_list() as $plug_main_set) {
        if (file_exists($plug_main_set['file'])) {
            $dsp->AddFieldsetStart($plug_main_set['caption']);
            include($plug_main_set['file']);
            $dsp->AddFieldsetEnd();
        }
    }
    // END Pluginsystem for Usermanager

break;


    // Tournament list
        case 2:
          if (in_array('tournament2', $ActiveModules)) { 
            // League IDs
        $dsp->AddFieldsetStart($lang['usrmgr']['leagues']);
            $wwcl = '';
            if ($user_data['wwclid']) $wwcl .= $user_data['wwclid'] .' ';
            if ($user_data['wwclclanid']) $wwcl .= '('. $user_data['wwclclanid'] .')';
            $dsp->AddDoubleRow($lang['usrmgr']['details_wwcl_id']. ' (Clan-ID)', $wwcl);
            $ngl = '';
            if ($user_data['nglid']) $ngl .= $user_data['nglid'] .' ';
            if ($user_data['nglclanid']) $ngl .= '('. $user_data['nglclanid'] .')';
            $dsp->AddDoubleRow($lang['usrmgr']['details_ngl_id']. ' (Clan-ID)', $ngl);
            $lgz = '';
            if ($user_data['lgzid']) $lgz .= $user_data['lgzid'] .' ';
            if ($user_data['lgzclanid']) $lgz .= '('. $user_data['lgzclanid'] .')';
            $dsp->AddDoubleRow('LGZ-ID (Clan-ID)', $lgz);
        $dsp->AddFieldsetEnd();
  
  
        include_once("modules/tournament2/class_tournament.php");
        $tfunc = new tfunc;
  
        $dsp->AddFieldsetStart($lang['usrmgr']['details_leader_teams']);
            $leader_teams = $db->query("SELECT t.name, t.tournamentid AS tid, team.name AS teamname, team.teamid FROM {$config['tables']['t2_teams']} AS team
            LEFT JOIN {$config['tables']['tournament_tournaments']} AS t ON t.tournamentid = team.tournamentid
          WHERE team.leaderid = '{$_GET['userid']}'");
        if ($db->num_rows($leader_teams) == 0) $dsp->AddSingleRow('<i>-'. $lang["sys"]["none"] .'-</i>');
        else while ($leader_team = $db->fetch_array($leader_teams)) {
          $dsp->AddDoubleRow('<a href="index.php?mod=tournament2&action=details&tournamentid='. $leader_team['tid']. '">'. $leader_team['name'] .'</a>', $leader_team['teamname'] .' '. $tfunc->button_team_details($leader_team['teamid'], $leader_team['tid']));
        }
        $dsp->AddFieldsetEnd();
  
        $dsp->AddFieldsetStart($lang['usrmgr']['details_member_teams']);
            $member_teams = $db->query("SELECT t.name, t.tournamentid AS tid, team.name AS teamname, team.teamid FROM {$config['tables']['t2_teams']} AS team
            LEFT JOIN {$config['tables']['tournament_tournaments']} AS t ON t.tournamentid = team.tournamentid
            LEFT JOIN {$config['tables']['t2_teammembers']} AS m ON team.teamid = m.teamid
          WHERE m.userid = '{$_GET['userid']}'");
        if ($db->num_rows($member_teams) == 0) $dsp->AddSingleRow('<i>-'. $lang["sys"]["none"] .'-</i>');
        else while ($member_team = $db->fetch_array($member_teams)) {
          $dsp->AddDoubleRow('<a href="index.php?mod=tournament2&action=details&tournamentid='. $member_team['tid']. '">'. $member_team['name'] .'</a>', $member_team['teamname'] .' '. $tfunc->button_team_details($member_team['teamid'], $member_team['tid']));
        }
        $dsp->AddFieldsetEnd();
      }
        break;


        case 3:
            // forumposts
            $dsp->AddDoubleRow($lang['usrmgr']['details_posts'], $user_data['posts'].$count_rows['count']);

            // Threads
        $get_board_threads = $db->query("SELECT b.tid, b.date, t.caption FROM {$config['tables']['board_posts']} AS b
                LEFT JOIN {$config['tables']['board_threads']} AS t ON b.tid = t.tid
                WHERE b.userid = '{$_GET['userid']}'
                GROUP BY b.tid
                ORDER BY b.date DESC
                LIMIT 10
                ");
            while($row_threads = $db->fetch_array($get_board_threads)) {
                $threads .= $func->unixstamp2date($row_threads['date'], "datetime")." - <a href=\"index.php?mod=board&action=thread&tid={$row_threads['tid']}\">{$row_threads['caption']}</a>". HTML_NEWLINE;
            }
            $db->free_result($get_board_threads);
            $dsp->AddDoubleRow($lang['usrmgr']['details_top10_threads'], $threads);

            // logins, last login
            if ($auth['type'] >= 2) {
                $lastLoginTS = $db->query_first("SELECT max(logintime) FROM {$config['tables']['stats_auth']} WHERE userid = '{$_GET['userid']}' AND login = '1'");
                $dsp->AddDoubleRow($lang['usrmgr']['details_logins'], $user_data['logins']);
                if ($lastLoginTS['max(logintime)']) $loginTime = $lang['usrmgr']['details_at'] . $func->unixstamp2date($lastLoginTS['max(logintime)'], "datetime");
                else $loginTime = $lang['usrmgr']['details_not_logged_in'];
                $dsp->AddDoubleRow($lang['usrmgr']['details_last_login'], $loginTime);
            }

            // signature
            $dsp->AddDoubleRow($lang['usrmgr']['details_signature'], $func->db2text2html($user_data['signature']));

            // avatar
            ($user_data['avatar_path'] != "" AND $user_data['avatar_path'] != "0") ?
                $avatar = "<img border=\"0\" src=\"". $user_data['avatar_path'] . "\">"
                : $avatar = $lang['usrmgr']['details_no_avatar'];
            $dsp->AddDoubleRow($lang['usrmgr']['details_avatar'], $avatar);

        // Including comment-engine
        if($auth["login"] == 1) {
        include('inc/classes/class_mastercomment.php');
        new Mastercomment('User', $_GET['userid']);
        }
        break;


        case 4:
          if ($db->num_rows($user_fields) > 0) { 
        // Add extra admin-defined fields    
        while ($user_field = $db->fetch_array($user_fields)) {
                $dsp->AddDoubleRow($user_field['caption'], $user_data[$user_field['name']]);
        }
      }
        break;

        case 5:
            if ($auth['type'] >= 3) {
                include_once('modules/mastersearch2/class_mastersearch2.php');
                $ms2 = new mastersearch2('usrmgr');

                $ms2->query['from'] = "{$config["tables"]["stats_auth"]} a";
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
            }
        break;

        default:
            // BEGIN Pluginsystem for Usermanager
            // Load Plugins, if found
            $plug_file = "modules/".$plug_headermenue_modul[$_GET['headermenuitem']]."/plugins/inc_usrmgr_headermenue.php";
            if (file_exists($plug_file)) {
                include($plug_file);
            }
            // END Pluginsystem for Usermanager
        break;
    } // end switch

  $db->free_result($user_fields);

  if ($auth['type'] >= 2) $buttons = $dsp->FetchSpanButton(t('Benutzerübersicht'), 'index.php?mod='. $_GET['mod'] .'&action=search').' ';
  else $buttons = $dsp->FetchSpanButton(t('Benutzerübersicht'), 'index.php?mod=guestlist&action=guestlist').' ';
  $row = $db->qry_first('SELECT userid FROM %prefix%user WHERE type > 0 AND userid < %int% order by userid desc', $_GET['userid']);
  if ($row['userid']) $buttons .= $dsp->FetchSpanButton(t('Vorheriger Benutzer'), 'index.php?mod=usrmgr&action=details&userid='. $row['userid']).' ';
  $row = $db->qry_first('SELECT userid FROM %prefix%user WHERE type > 0 AND userid > %int%', $_GET['userid']);
  if ($row['userid']) $buttons .= $dsp->FetchSpanButton(t('Nächster Benutzer'), 'index.php?mod=usrmgr&action=details&userid='. $row['userid']);

  $dsp->AddDoubleRow('', $buttons);
    $dsp->AddContent();
}
?>