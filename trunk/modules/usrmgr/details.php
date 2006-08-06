<?php

include_once("modules/usrmgr/class_usrmgr.php");
$usrmgr = new UsrMgr;

function IsAuthorizedAdmin() {
  global $auth, $user_data, $link;
  
  $link = '';
  if ($auth['type'] >= 2 and $auth['type'] >= $user_data['type']) return true;
  else return false;
}


function GetTypeDescription($type) {
	global $lang;

	switch ($type) {
		case -2:	return $lang['usrmgr']['details_orga_disabled'];		break;
		case -1:	return $lang['usrmgr']['details_guest_disabled'];		break;
		default:	return $lang['usrmgr']['details_guest_deactivated'];	break;
		case 1:		return $lang['usrmgr']['details_guest'];				break;
		case 2:		return $lang['usrmgr']['details_orga'];					break;
		case 3:		return $lang['usrmgr']['details_operator'];				break;
	}
}


// Select from table_user
// username,type,name,firstname,clan,email,paid,seatcontrol,checkin,checkout,portnumber,posts,wwclid,wwclclanid,comment
$user_data = $db->query_first("SELECT u.*, u.clanid, c.avatar_path, c.signature, s.seatid, s.blockid, s.col, s.row, s.ip, clan.name AS clan, clan.url AS clanurl
	FROM {$config['tables']['user']} AS u
	LEFT JOIN {$config['tables']['usersettings']} AS c ON u.userid = c.userid
	LEFT JOIN {$config['tables']['clan']} AS clan ON u.clanid = clan.clanid
	LEFT JOIN {$config['tables']['seat_seats']} AS s ON u.userid = s.userid
	WHERE u.userid='{$_GET['userid']}'
	");

// If exists
if (!$user_data['userid']) $func->error($lang['usrmgr']['checkin_nouser'], '');
else {
	// Select party-related user details
	$user_party = $db->query_first("SELECT * FROM {$config['tables']['party_user']} WHERE user_id = {$_GET['userid']} AND party_id = {$party->party_id}");

	// Select from table_stats_auth
	$user_auth = $db->query_first("SELECT count(*) as count FROM {$config['tables']['stats_auth']} WHERE userid = '{$_GET['userid']}' AND login = '1' AND lasthit > " . (time() - $config['lansuite']['user_timeout']) );

	// Select from table_board_posts/threads
	$get_board_threads = $db->query("SELECT b.tid, b.date, t.caption FROM {$config[tables][board_posts]} AS b
			LEFT JOIN {$config[tables][board_threads]} AS t ON b.tid = t.tid
			WHERE b.userid = '{$_GET['userid']}'
			GROUP BY b.tid
			LIMIT 10
			");
	
	// Select from table_seat_seats
	$party_seatcontrol = $db->query_first("SELECT * FROM {$config[tables][party_prices]}
			WHERE price_id = '{$user_party['price_id']}'
			");

	$menunames[] = $lang['usrmgr']['details_playerinfos'];
	$menunames[] = $lang['usrmgr']['details_tournament'];
	$menunames[] = $lang['usrmgr']['details_misc'];
	if(!$vars['headermenuitem']) { $vars['headermenuitem'] = 1; }



	$dsp->NewContent(str_replace("%USER%", $user_data['username'], $lang['usrmgr']['details_caption']), $lang['usrmgr']['details_subcaption']);
	$dsp->AddHeaderMenu($menunames,"index.php?mod=usrmgr&action=details&userid=".$_GET['userid'],$vars['headermenuitem']);

	// < menu details (step/headermenuitem)
	switch($vars['headermenuitem']){
		default:
		case 1: // Main
    	// First name, last name, username, user ID
    	$name = '';
    	if (!$cfg['sys_internet'] or $auth['type'] > 1 or $auth['userid'] == $_GET['userid']) {
      	if ($user_data['firstname']) $name .= $user_data['firstname'] .' ';
      	if ($user_data['name']) $name .= $user_data['name'] .' ';
      }
    	if ($user_data['username']) $name .= '('. $user_data['username'] .') ';
    	$name .= '['. $user_data['userid'] .']';
    	if (IsAuthorizedAdmin()) {
        $name .= ' '. $dsp->AddIcon('assign', 'index.php?mod=usrmgr&action=switch_user&step=10&userid='. $_GET['userid'], $lang['button']['switch_user']);
        $name .= ' '. $dsp->AddIcon('change_pw', 'index.php?mod=usrmgr&action=newpwd&step=2&userid='. $_GET['userid'], $lang['ms2']['change_pw']);
      } elseif ($_GET['userid'] == $auth['userid']) $name .= ' '. $dsp->AddIcon('change_pw', 'index.php?mod=usrmgr&action=changepw', $lang['ms2']['change_pw']);
      if (IsAuthorizedAdmin() or ($_GET['userid'] == $auth['userid'] and $cfg['user_self_details_change']))
        $name .= ' '. $dsp->AddIcon('edit', 'index.php?mod=usrmgr&action=change&step=1&userid='. $_GET['userid'], $lang['button']['edit']);
      if ($auth['type'] >= 3)
        $name .= ' '. $dsp->AddIcon('delete', 'index.php?mod=usrmgr&action=delete&step=2&userid='. $_GET['userid'], $lang['button']['delete']);
    	$dsp->AddDoubleRow('Benutzername', $name);


      // Party Checkin, paid, ...
      $party_info = '';
      $link = '';
      ($user_party['user_id'])? $party_info .= $lang['usrmgr']['details_signon'] :  $party_info .= $lang['usrmgr']['details_not_signon'];
      if (IsAuthorizedAdmin()) $link = 'index.php?mod=usrmgr&action=changepaid&step=2&userid='. $_GET['userid'];
      // Paid
      ($user_party['paid'])? $party_info .= ', '. $dsp->AddIcon('paid', $link) : $party_info .= ', '. $dsp->AddIcon('not_paid', $link);
      if ($user_party['paid'] == 1) $party_info .= ' ['. $lang['usrmgr']['details_paid_vvk'] .']';
    	elseif ($user_party['paid'] == 2) $party_info .= ' ['. $lang['usrmgr']['details_paid_ak'] .']';
      // Platzpfand
      if ($party_seatcontrol['depot_price'] > 0){
      	$party_info .= ', '. $party_seatcontrol['depot_desc'];
      	$party_info .= ($user_party['seatcontrol']) ? $lang['usrmgr']['details_seat_paid'] : $lang['usrmgr']['details_seat_not_paid'];
      }
      // CheckIn CheckOut
      $link = '';
      if (IsAuthorizedAdmin() and !$user_party['checkin']) $link = 'index.php?mod=usrmgr&action=checkin&step=2&userid='. $_GET['userid'];
      if ($user_party['checkin']) $party_info .= ' '. $dsp->AddIcon('in', $link, $lang['usrmgr']['checkin']) .'['. $func->unixstamp2date($user_party['checkin'], 'datetime') .']';
      else $party_info .= $dsp->AddIcon('not_in', $link, $lang['usrmgr']['checkin_no']);

      $link = '';
      if (IsAuthorizedAdmin() and !$user_party['checkout'] and $user_party['checkin']) $link = 'index.php?mod=usrmgr&action=checkout&step=2&userid='. $_GET['userid'];
      if ($user_party['checkout']) $party_info .= ' '. $dsp->AddIcon('out', $link, $lang['usrmgr']['checkout']) .'['. $func->unixstamp2date($user_party['checkout'], 'datetime') .']';
      else $party_info .= $dsp->AddIcon('not_out', $link, $lang['usrmgr']['checkout_no']);

      if (IsAuthorizedAdmin() and $user_party['checkin'] > 0 and $user_party['checkout'] > 0) $party_info .= $dsp->AddIcon('delete', 'index.php?mod=usrmgr&action=checkout&step=10&userid='. $_GET['userid'], 'Reset Checkin');

      $dsp->AddDoubleRow('Party '. $_SESSION['party_info']['name'], $party_info);


			// Clan
			$clan = $user_data['clan'];
			if ($user_data['clanurl']) $clan .= " [<a href=\"http://{$user_data['clanurl']}\" target=\"_blank\">{$user_data['clanurl']}</a>]";
			if ($user_data['clan'] != '' and (IsAuthorizedAdmin() or $user_data['clanid'] == $auth['clanid']))
        $clan .= $dsp->AddIcon('change_pw', 'index.php?mod=usrmgr&action=changeclanpw&clanid='. $user_data['clanid'], $lang['ms2']['change_pw']) .
          $dsp->AddIcon('edit', 'index.php?mod=usrmgr&action=clanmgr&step=30&clanid='. $user_data['clanid'], $lang['ms2']['edit']);
			$dsp->AddDoubleRow($lang['usrmgr']['details_clan'], $clan);


			// Seating
			if ($user_data['blockid'] == '') $seat = $lang['usrmgr']['details_no_seat'];
			else {
			  $seat = $seat2->SeatOfUser($_GET['userid'], 0, 2);
			  if (IsAuthorizedAdmin()) {
  			  $seat .= ' '. $dsp->AddIcon('edit', 'index.php?mod=seating&action=seatadmin&step=2&userid='. $_GET['userid'], $lang['button']['edit']);
  			  $seat .= ' '. $dsp->AddIcon('delete', "index.php?mod=seating&action=free_seat&step=3&blockid={$user_data['blockid']}&row={$user_data['row']}&col={$user_data['col']}", $lang['button']['delete']);
        }
			}
			if ($cfg['sys_internet'] == 0 and $user_data['ip']) $seat .= ' IP:'. $user_data['ip'];
      $dsp->AddDoubleRow($lang['usrmgr']['details_seat'], $seat);


			// Phone
			$phone = '';
      if ($user_data['telefon'] and (IsAuthorizedAdmin() or $auth['userid'] == $_GET['userid'])) $phone .= $dsp->AddIcon('phone', '', 'Phone'). ' '. $user_data['telefon'] . ' ';
      if ($user_data['handy'] and (IsAuthorizedAdmin() or $auth['userid'] == $_GET['userid'])) $phone .= $dsp->AddIcon('cellphone', '', 'Handy'). ' '. $user_data['handy'] . ' ';
      if ($user_data['skype']) {
        if ($cfg['sys_internet']) $phone .= '<a href="skype:'. $user_data['skype'] .'?call"><img src="http://download.skype.com/share/skypebuttons/buttons/call_blue_transparent_34x34.png" style="border: none;" width="20" height="20" alt="Skype" title="Skype:'. $user_data['skype'] .'" /></a>';
        else $phone .= '[Skype:'. $user_data['skype'] .']';
      }
      $dsp->AddDoubleRow($lang['usrmgr']['telefon'], $phone);


			// Messenger
			$messenger = '';
      if ($user_data['icq']) {
        if ($cfg['sys_internet']) $messenger .= '<a href="http://wwp.icq.com/scripts/search.dll?to='. $user_data['icq'] .'" target="_blank"><img src="http://status.icq.com/online.gif?icq='. $user_data['icq'] .'&img=26" alt="ICQ" title="ICQ#'. $user_data['icq'] .'" border="0" /></a> ';
        else $messenger .= '[ICQ#'. $user_data['icq'] .'] ';
      }
      if ($user_data['msn']) $messenger .= '[MSN:'. $user_data['msn'] .'] ';
      $messenger .= 'Online:';
      ($user_auth['count'] >= '1') ? $messenger .= $dsp->AddIcon('yes') : $messenger .= $dsp->AddIcon('no');
		  if ($auth['login'] and in_array('msgsys', $ActiveModules)) $messenger .= $dsp->AddIcon('add_user', 'index.php?mod=msgsys&action=addbuddy&step=2&userid='. $_GET['userid'], $lang['usrmgr']['details_buddy_help']) .' ';
      $dsp->AddDoubleRow('Messenger', $messenger);


			// Mail
			$mail = '';
			if ((!$cfg['sys_internet'] and $cfg['user_showmail4all']) or $auth['type'] >= 2 or $auth['userid'] == $_GET['userid']) {
        $mail .= '<a href="mailto:'. $user_data['email'] .'">'. $user_data['email'] .'</a> ';
      }
		  if ($auth['login'] and in_array('mail', $ActiveModules)) $mail .= $dsp->AddIcon('send_mail', 'index.php?mod=mail&action=newmail&step=2&userID='. $_GET['userid']. $_GET['userid'], $lang['usrmgr']['details_mail_help']) .' ';
      $mail .= '[Newsletter-Abo:';
      ($user_data['newsletter']) ? $mail .= $dsp->AddIcon('yes') : $mail .= $dsp->AddIcon('no');
      $mail .= ']';
      $dsp->AddDoubleRow($lang['usrmgr']['add_email'], $mail);


			// Address
			$address = '';
			if (($user_data['street'] != '' or $user_data['hnr']) and ($auth['type'] >= 2 or ($auth['userid'] == $_GET['userid'] and $cfg['user_showownstreet'] == '1')))
				$address .= $user_data['street'] .' '. $user_data['hnr'] .', ';
			if (($user_data['plz'] != '' or $user_data['city']) and ($cfg['user_showcity4all'] == '1' or $auth['type'] >= 2 or $auth['userid'] == $_GET['userid']))
				$address .= $user_data['plz'] .' '. $user_data['city'];
			if ($address) $dsp->AddDoubleRow($lang['usrmgr']['address'], $address);


			// User-Type
			$dsp->AddDoubleRow($lang['usrmgr']['details_type'], GetTypeDescription($user_data['type']));


      // Perso
			if ($user_data['perso'] and ($auth['type'] >= 2 or ($auth['userid'] == $_GET['userid'] and $cfg['user_showownstreet'] == '1')))
				$dsp->AddDoubleRow($lang['usrmgr']['details_passport_misc'], $user_data['perso'] .'<br>'. $lang['usrmgr']['details_hint']);


			// Birthday
			if ($cfg['sys_internet'] == 0 OR $auth['type'] >= 2 OR $auth['userid'] == $_GET['userid'])
        $dsp->AddDoubleRow("Geburtstag", ((int) $user_data['birthday'])? $func->unixstamp2date($user_data['birthday'], "date") : $lang['usrmgr']['details_not_entered']);


			// Gender
			$geschlecht[0] = $lang['usrmgr']['details_not_entered'];
			$geschlecht[1] = $lang['usrmgr']['details_male'];
			$geschlecht[2] = $lang['usrmgr']['details_female'];
			$dsp->AddDoubleRow($lang['usrmgr']['details_sex'], $geschlecht[$user_data['sex']]);


      // Picture
			if ($user_data['picture'] != '') $dsp->AddDoubleRow($lang['usrmgr']['picture'], '<img src="'. $user_data['picture'] .'">');


			// Comment
			$dsp->AddDoubleRow($lang['usrmgr']['details_comment'], ($user_data['comment'] == "") ? "" : $func->text2html($user_data['comment']));
		break;


    // Tournament list
		case 2:
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
		break;


		case 3:
			// forumposts
			$dsp->AddDoubleRow($lang['usrmgr']['details_posts'], $user_data['posts']);

			// Threads
			while($row_threads = $db->fetch_array($get_board_threads)) {
				$threads .= $func->unixstamp2date($row_threads['date'], "datetime")." - <a href=\"index.php?mod=board&action=thread&tid={$row_threads['tid']}\">{$row_threads['caption']}</a>". HTML_NEWLINE;
			}
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
				$avatar	= "<img border=\"0\" src=\"ext_inc/avatare/" . $user_data['avatar_path'] . "\">"
				: $avatar = $lang['usrmgr']['details_no_avatar'];
			$dsp->AddDoubleRow($lang['usrmgr']['details_avatar'], $avatar);
		break;
	} // end switch

	$dsp->AddBackButton('index.php?mod=usrmgr&action=search');
	$dsp->AddContent();
	
	// Including comment-engine     
	if($auth["login"] == 1) {
		include_once("modules/mastercomment/class_mastercomment.php");
		$comment = new Mastercomment($vars, "index.php?mod=usrmgr&action=details&userid=". $_GET["userid"], "User", $_GET["userid"], $user_data['username']);
		$comment->action();
	}
	//End comment-engine	
	
} // else end if exist userid
?>
