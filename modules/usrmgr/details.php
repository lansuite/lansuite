<?php

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
$user_data = $db->query_first("SELECT u.*, c.avatar_path, c.signature, s.seatid, s.blockid, s.col, s.row, s.ip, clan.name AS clan, clan.url AS clanurl
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

	$menunames[] = $lang['usrmgr']['details_state'];
	$menunames[] = $lang['usrmgr']['details_playerinfos'];
	$menunames[] = $lang['usrmgr']['details_private'];
	$menunames[] = $lang['usrmgr']['details_misc'];
	$menunames[] = $lang['usrmgr']['details_tournament'];
	if(!$vars['headermenuitem']) { $vars['headermenuitem'] = 1; }

	# Beginn der Ausgabe
	$dsp->NewContent(str_replace("%USER%", $user_data['username'], $lang['usrmgr']['details_caption']), $lang['usrmgr']['details_subcaption']);
	$dsp->AddHeaderMenu($menunames,"index.php?mod=usrmgr&action=details&userid=".$_GET['userid'],$vars['headermenuitem']);

	// User Name
	$dsp->AddDoubleRow($lang['usrmgr']['details_username'], $user_data['username']." &nbsp; (".$_GET['userid'].")");

	// < menu details (step/headermenuitem)
	switch($vars['headermenuitem']){
		default:
		case 1: // Main
			// User-Type
			$dsp->AddDoubleRow($lang['usrmgr']['details_type'], GetTypeDescription($user_data['type']));

			// Signon
			$dsp->AddDoubleRow($lang['usrmgr']['details_signon'], ($user_party['user_id']) ? $lang['sys']['yes'] : $lang['sys']['no']);

			// Paid , Seatcontrol (platzpfand)
			if ($user_party['user_id'] AND ($user_data['type'] == 1 OR $cfg['orga_showpaid'] == 1)) {
				if ($user_party['paid'] == 1) $paidtxt = $lang['usrmgr']['details_paid_vvk'];
				elseif ($user_party['paid'] == 2) $paidtxt = $lang['usrmgr']['details_paid_ak']; 
				else $paidtxt = $lang['usrmgr']['details_not_paid'];

				if($party_seatcontrol['depot_price'] > 0){
					$paidtxt .= ",&nbsp;" . $party_seatcontrol['depot_desc']; 
					$paidtxt .= ($user_party['seatcontrol']) ? $lang['usrmgr']['details_seat_paid'] : $lang['usrmgr']['details_seat_not_paid'];
				}
				$dsp->AddDoubleRow($lang['usrmgr']['details_paid'], $paidtxt);
				
			}
			
			// Check IN/OUT
			if (!$cfg['sys_internet'] AND ($user_data['type'] OR $cfg['orga_showcheck'])) {
				if (!$user_party['checkin'] AND !$user_party['checkout']) $checktxt = $lang['usrmgr']['details_not_checked_in'];
				elseif ($user_party['checkin'] AND !$user_party['checkout']) $checktxt =  $lang['usrmgr']['details_checked_in'] . $func->unixstamp2date($user_party['checkin'], "datetime");
				elseif($user_party['checkin'] AND $user_party['checkout']) $checktxt =  $lang['usrmgr']['details_checked_out'] . $func->unixstamp2date($user_party['checkout'], "datetime") ." ({$lang['usrmgr']['details_in']}: ". $func->unixstamp2date($user_party['checkin'], "datetime") .")";

				$dsp->AddDoubleRow($lang['usrmgr']['details_check_in_out'], $checktxt);
			}

			// Online/Offline
			$dsp->AddDoubleRow($lang['usrmgr']['details_online'], ($user_auth['count'] >= "1") ? $lang['sys']['yes'] : $lang['sys']['no']);

			// Newsletter
			$dsp->AddDoubleRow($lang['usrmgr']['details_newsletter'], ($user_data['newsletter']) ? $lang['sys']['yes'] : $lang['sys']['no']);

			// Seating
			if ($user_data['blockid'] == "") $dsp->AddDoubleRow($lang['usrmgr']['details_seat'], $lang['usrmgr']['details_no_seat']);
			else $dsp->AddDoubleRow($lang['usrmgr']['details_seat'], $seat2->SeatOfUser($_GET['userid'],0,2));

			// IPAdress
			if($cfg['sys_internet'] == 0) {
				$dsp->AddDoubleRow($lang['usrmgr']['details_ip'], ($user_data['ip'] == "") ? $lang['usrmgr']['details_no_ip'] : $user_data['ip']);
			}

			// Comment
			$dsp->AddDoubleRow($lang['usrmgr']['details_comment'], ($user_data['comment'] == "") ? "" : $func->text2html($user_data['comment']));
		break;

		case 2:
			// clan
			$dsp->AddDoubleRow($lang['usrmgr']['details_clan'], $user_data['clan']);

			// clanurl
			$dsp->AddDoubleRow($lang['usrmgr']['details_clan_url'], "<a href=\"http://{$user_data['clanurl']}\" target=\"_blank\">{$user_data['clanurl']}</a>");

			// wwclid
			$dsp->AddDoubleRow($lang['usrmgr']['details_wwcl_id'], ($user_data['wwclid'] == 0) ? "" : $user_data['wwclid']);

			// nglid
			$dsp->AddDoubleRow($lang['usrmgr']['details_ngl_id'], ($user_data['wwclid'] == 0) ? "" : $user_data['nglid']);
		break;

		case 3:
			// first+name
			if (!$cfg['sys_internet'] OR $auth['type'] > 1 OR $auth['userid'] == $_GET['userid']) $dsp->AddDoubleRow("Vor und Nachname", $user_data['firstname'] ." ". $user_data['name']);
			else $dsp->AddDoubleRow($lang['usrmgr']['details_first_lastname'], $lang['usrmgr']['details_only_orga_self']);

			// orga and self only
			if ($auth['type'] >= 2 OR ($auth['userid'] == $_GET['userid'] AND $cfg['user_showownstreet'] == '1')) {
				$dsp->AddDoubleRow($lang['usrmgr']['details_street_nr'], $user_data['street']." ".$user_data['hnr']);
				$dsp->AddDoubleRow($lang['usrmgr']['details_passport_misc'], $user_data['perso']);
				$dsp->AddSingleRow($lang['usrmgr']['details_hint']);
			}

			// City/Plz
			if ($cfg['user_showcity4all'] == '1' OR $auth['type'] >= 2 OR $auth['userid'] == $_GET['userid']) {
				$dsp->AddDoubleRow($lang['usrmgr']['details_postal_city'], $user_data['plz']." ".$user_data['city']);
			}

			// Email
			if ((!$cfg['sys_internet'] AND $cfg['user_showmail4all']) OR $auth['type'] >= 2 OR $auth['userid'] == $_GET['userid']) $dsp->AddDoubleRow("eMail", $user_data['email']);

			// Birthday
			if ($cfg['sys_internet'] == 0 OR $auth['type'] >= 2 OR $auth['userid'] == $_GET['userid']) $dsp->AddDoubleRow("Geburtstag", ((int) $user_data['birthday'])? $func->unixstamp2date($user_data['birthday'], "date") : $lang['usrmgr']['details_not_entered']);

			// Gender
			$geschlecht[0] = $lang['usrmgr']['details_not_entered'];
			$geschlecht[1] = $lang['usrmgr']['details_male'];
			$geschlecht[2] = $lang['usrmgr']['details_female'];
			$dsp->AddDoubleRow($lang['usrmgr']['details_sex'], $geschlecht[$user_data['sex']]);

			if ($user_data['picture'] != '') $dsp->AddDoubleRow($lang['usrmgr']['picture'], '<img src="'. $user_data['picture'] .'">');
		break;

		case 4:
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
		
    // Tournament list
		case 5:
      include_once("modules/tournament2/class_tournament.php");
      $tfunc = new tfunc;
		
      $dsp->AddSingleRow('<b>'. $lang['usrmgr']['details_leader_teams'] .'</b>');
			$leader_teams = $db->query("SELECT t.name, t.tournamentid AS tid, team.name AS teamname, team.teamid FROM {$config['tables']['t2_teams']} AS team
		    LEFT JOIN {$config['tables']['tournament_tournaments']} AS t ON t.tournamentid = team.tournamentid
        WHERE team.leaderid = '{$_GET['userid']}'");
      if ($db->num_rows($leader_teams) == 0) $dsp->AddSingleRow('<i>-'. $lang["sys"]["none"] .'-</i>');
      else while ($leader_team = $db->fetch_array($leader_teams)) {
	      $dsp->AddDoubleRow('<a href="index.php?mod=tournament2&action=details&tournamentid='. $leader_team['tid']. '">'. $leader_team['name'] .'</a>', $leader_team['teamname'] .' '. $tfunc->button_team_details($leader_team['teamid'], $leader_team['tid']));
      }
      		  
      $dsp->AddSingleRow('<b>'. $lang['usrmgr']['details_member_teams'] .'</b>');
			$member_teams = $db->query("SELECT t.name, t.tournamentid AS tid, team.name AS teamname, team.teamid FROM {$config['tables']['t2_teams']} AS team
		    LEFT JOIN {$config['tables']['tournament_tournaments']} AS t ON t.tournamentid = team.tournamentid
		    LEFT JOIN {$config['tables']['t2_teammembers']} AS m ON team.teamid = m.teamid
        WHERE m.userid = '{$_GET['userid']}'");
      if ($db->num_rows($member_teams) == 0) $dsp->AddSingleRow('<i>-'. $lang["sys"]["none"] .'-</i>');
      else while ($member_team = $db->fetch_array($member_teams)) {
	      $dsp->AddDoubleRow('<a href="index.php?mod=tournament2&action=details&tournamentid='. $member_team['tid']. '">'. $member_team['name'] .'</a>', $member_team['teamname'] .' '. $tfunc->button_team_details($member_team['teamid'], $member_team['tid']));
      }
		break;
	} // end switch

	//
	// BUTTONS
	//

	$userdetails_back .= $dsp->FetchButton("index.php?mod=usrmgr&action=search", "back", $lang['usrmgr']['details_back_help'])." ";

	// Nur Buttons die einen LogIn erfordern
	if ($auth['login']) {
		// Marco Müller
		// Nachsehen ob Mail-Modul aktiv ist.
		$module = $db->query_first("SELECT * FROM {$config['tables']['modules']} WHERE name = 'mail'");
		if ($module['active']){
			$userdetails_buttons .= $dsp->FetchButton("index.php?mod=mail&action=newmail&step=2&userID=". $_GET['userid'], "sendmail", $lang['usrmgr']['details_mail_help'])." ";
		}
		
		// Marco Müller
		// Nachsehen ob Mail-Modul aktiv ist.
		if ($_GET['userid'] <> $auth['userid']) {
			$module = $db->query_first("SELECT * FROM {$config['tables']['modules']} WHERE name = 'msgsys'");
			if ($module['active']){
				$userdetails_buttons .= $dsp->FetchButton("index.php?mod=msgsys&action=addbuddy&step=2&checkbox[]=". $_GET['userid'], "add_to_buddylist", $lang['usrmgr']['details_buddy_help'])." ";
			}
		}
		
		if (($auth['type'] >= 2) && ($auth['type'] >= $user_data['type'])) {
			$userdetails_adminbuttons_user = $dsp->FetchButton("index.php?mod=usrmgr&action=newpwd&step=2&userid=".$_GET['userid'], "newpassword")." ";
#			if ($user_data['party_id'] > 0){
				if ($user_party['checkin'] == 0)
					$userdetails_adminbuttons_user .= $dsp->FetchButton("index.php?mod=usrmgr&action=checkin&step=2&userid=". $_GET['userid'], "checkin")." ";
				if ($user_party['checkin'] > 0 AND $user_party['checkout'] == 0)
					$userdetails_adminbuttons_user .= $dsp->FetchButton("index.php?mod=usrmgr&action=checkout&step=2&userid=". $_GET['userid'], "checkout")." ";
				if ($user_party['checkin'] > 0 AND $user_party['checkout'] > 0)
					$userdetails_adminbuttons_user .= $dsp->FetchButton("index.php?mod=usrmgr&action=checkout&step=10&userid=". $_GET['userid'], "checkin_reset")." ";

				$userdetails_adminbuttons_user .= $dsp->FetchButton("index.php?mod=usrmgr&action=changepaid&step=2&userid=". $_GET['userid'], "paidchange", $lang['usrmgr']['details_chpaid_help'])." ";
				$userdetails_adminbuttons_seat .= $dsp->FetchButton("index.php?mod=seating&action=seatadmin&step=2&userid=". $_GET['userid'], "edit")." ";
				if ($user_data['blockid'] != '' and $user_data['row'] != '' and $user_data['col'] != '') $userdetails_adminbuttons_seat .=
					$dsp->FetchButton("index.php?mod=seating&action=free_seat&step=3&blockid={$user_data['blockid']}&row={$user_data['row']}&col={$user_data['col']}", "delete")." ";
#			}
			$userdetails_adminbuttons_user .= $dsp->FetchButton("index.php?mod=usrmgr&action=switch_user&step=10&userid=". $_GET['userid'], "switch_user")." ";
		}

		if ((($auth['type'] >= 2) && ($auth['type'] >= $user_data['type'])) || (($_GET['userid'] == $auth['userid']) && $cfg['user_self_details_change']))
			$userdetails_adminbuttons_user .= $dsp->FetchButton("index.php?mod=usrmgr&action=change&step=1&userid=". $_GET['userid'], "edit")." ";

		if ($auth['type'] >= 3)
			$userdetails_adminbuttons_user .= $dsp->FetchButton("index.php?mod=usrmgr&action=delete&step=2&userid=". $_GET['userid'], "delete")." ";

		if($user_data['clan'] != "" &&($user_data['clan'] == $auth['clan'] || $auth['type'] > 1)){
			$userdetails_adminbuttons_user .= $dsp->FetchButton("index.php?mod=usrmgr&action=changeclanpw&clan=". urlencode($user_data['clan']),"changeclanpw");
		}
		$dsp->AddHRuleRow();
		$dsp->AddDoubleRow($userdetails_back, $userdetails_buttons);
	} // end if login = true

	if ($userdetails_adminbuttons_user) $dsp->AddDoubleRow($lang['usrmgr']['details_user_options'], $userdetails_adminbuttons_user);
	if ($userdetails_adminbuttons_seat) $dsp->AddDoubleRow($lang['usrmgr']['details_seat_options'], $userdetails_adminbuttons_seat);

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
