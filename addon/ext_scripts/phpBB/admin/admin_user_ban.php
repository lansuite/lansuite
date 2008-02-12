<?php
/***************************************************************************
 *                            admin_user_ban.php
 *                            -------------------
 *   begin                : Tuesday, Jul 31, 2001
 *   copyright            : (C) 2001 The phpBB Group
 *   email                : support@phpbb.com
 *
 *   $Id: admin_user_ban.php,v 1.21.2.6 2005/10/30 15:17:13 acydburn Exp $
 *
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

define('IN_PHPBB', 1);

if ( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['Users']['Ban_Management'] = $filename;

	return;
}

//
// Load default header
//
$phpbb_root_path = './../';
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);

//
// Start program
//
if ( isset($HTTP_POST_VARS['submit']) )
{
	$user_bansql = '';
	$email_bansql = '';
	$ip_bansql = '';

	$user_list = array();
	if ( !empty($HTTP_POST_VARS['username']) )
	{
		$this_userdata = get_userdata($HTTP_POST_VARS['username'], true);
		if( !$this_userdata )
		{
			message_die(GENERAL_MESSAGE, $lang['No_user_id_specified'] );
		}

		$user_list[] = $this_userdata['user_id'];
	}

	$ip_list = array();
	if ( isset($HTTP_POST_VARS['ban_ip']) )
	{
		$ip_list_temp = explode(',', $HTTP_POST_VARS['ban_ip']);

		for($i = 0; $i < count($ip_list_temp); $i++)
		{
			if ( preg_match('/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})[ ]*\-[ ]*([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$/', trim($ip_list_temp[$i]), $ip_range_explode) )
			{
				//
				// Don't ask about all this, just don't ask ... !
				//
				$ip_1_counter = $ip_range_explode[1];
				$ip_1_end = $ip_range_explode[5];

				while ( $ip_1_counter <= $ip_1_end )
				{
					$ip_2_counter = ( $ip_1_counter == $ip_range_explode[1] ) ? $ip_range_explode[2] : 0;
					$ip_2_end = ( $ip_1_counter < $ip_1_end ) ? 254 : $ip_range_explode[6];

					if ( $ip_2_counter == 0 && $ip_2_end == 254 )
					{
						$ip_2_counter = 255;
						$ip_2_fragment = 255;

						$ip_list[] = encode_ip("$ip_1_counter.255.255.255");
					}

					while ( $ip_2_counter <= $ip_2_end )
					{
						$ip_3_counter = ( $ip_2_counter == $ip_range_explode[2] && $ip_1_counter == $ip_range_explode[1] ) ? $ip_range_explode[3] : 0;
						$ip_3_end = ( $ip_2_counter < $ip_2_end || $ip_1_counter < $ip_1_end ) ? 254 : $ip_range_explode[7];

						if ( $ip_3_counter == 0 && $ip_3_end == 254 )
						{
							$ip_3_counter = 255;
							$ip_3_fragment = 255;

							$ip_list[] = encode_ip("$ip_1_counter.$ip_2_counter.255.255");
						}

						while ( $ip_3_counter <= $ip_3_end )
						{
							$ip_4_counter = ( $ip_3_counter == $ip_range_explode[3] && $ip_2_counter == $ip_range_explode[2] && $ip_1_counter == $ip_range_explode[1] ) ? $ip_range_explode[4] : 0;
							$ip_4_end = ( $ip_3_counter < $ip_3_end || $ip_2_counter < $ip_2_end ) ? 254 : $ip_range_explode[8];

							if ( $ip_4_counter == 0 && $ip_4_end == 254 )
							{
								$ip_4_counter = 255;
								$ip_4_fragment = 255;

								$ip_list[] = encode_ip("$ip_1_counter.$ip_2_counter.$ip_3_counter.255");
							}

							while ( $ip_4_counter <= $ip_4_end )
							{
								$ip_list[] = encode_ip("$ip_1_counter.$ip_2_counter.$ip_3_counter.$ip_4_counter");
								$ip_4_counter++;
							}
							$ip_3_counter++;
						}
						$ip_2_counter++;
					}
					$ip_1_counter++;
				}
			}
			else if ( preg_match('/^([\w\-_]\.?){2,}$/is', trim($ip_list_temp[$i])) )
			{
				$ip = gethostbynamel(trim($ip_list_temp[$i]));

				for($j = 0; $j < count($ip); $j++)
				{
					if ( !empty($ip[$j]) )
					{
						$ip_list[] = encode_ip($ip[$j]);
					}
				}
			}
			else if ( preg_match('/^([0-9]{1,3})\.([0-9\*]{1,3})\.([0-9\*]{1,3})\.([0-9\*]{1,3})$/', trim($ip_list_temp[$i])) )
			{
				$ip_list[] = encode_ip(str_replace('*', '255', trim($ip_list_temp[$i])));
			}
		}
	}

	$email_list = array();
	if ( isset($HTTP_POST_VARS['ban_email']) )
	{
		$email_list_temp = explode(',', $HTTP_POST_VARS['ban_email']);

		for($i = 0; $i < count($email_list_temp); $i++)
		{
			//
			// This ereg match is based on one by php@unreelpro.com
			// contained in the annotated php manual at php.com (ereg
			// section)
			//
			if (preg_match('/^(([a-z0-9&\'\.\-_\+])|(\*))+@(([a-z0-9\-])|(\*))+\.([a-z0-9\-]+\.)*?[a-z]+$/is', trim($email_list_temp[$i])))
			{
				$email_list[] = trim($email_list_temp[$i]);
			}
		}
	}

	$sql = "SELECT *
		FROM " . BANLIST_TABLE;
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, "Couldn't obtain banlist information", "", __LINE__, __FILE__, $sql);
	}

	$current_banlist = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	$kill_session_sql = '';
	for($i = 0; $i < count($user_list); $i++)
	{
		$in_banlist = false;
		for($j = 0; $j < count($current_banlist); $j++)
		{
			if ( $user_list[$i] == $current_banlist[$j]['ban_userid'] )
			{
				$in_banlist = true;
			}
		}

		if ( !$in_banlist )
		{
			$kill_session_sql .= ( ( $kill_session_sql != '' ) ? ' OR ' : '' ) . "session_user_id = " . $user_list[$i];

			$sql = "INSERT INTO " . BANLIST_TABLE . " (ban_userid)
				VALUES (" . $user_list[$i] . ")";
			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, "Couldn't insert ban_userid info into database", "", __LINE__, __FILE__, $sql);
			}
		}
	}

	for($i = 0; $i < count($ip_list); $i++)
	{
		$in_banlist = false;
		for($j = 0; $j < count($current_banlist); $j++)
		{
			if ( $ip_list[$i] == $current_banlist[$j]['ban_ip'] )
			{
				$in_banlist = true;
			}
		}

		if ( !$in_banlist )
		{
			if ( preg_match('/(ff\.)|(\.ff)/is', chunk_split($ip_list[$i], 2, '.')) )
			{
				$kill_ip_sql = "session_ip LIKE '" . str_replace('.', '', preg_replace('/(ff\.)|(\.ff)/is', '%', chunk_split($ip_list[$i], 2, "."))) . "'";
			}
			else
			{
				$kill_ip_sql = "session_ip = '" . $ip_list[$i] . "'";
			}

			$kill_session_sql .= ( ( $kill_session_sql != '' ) ? ' OR ' : '' ) . $kill_ip_sql;

			$sql = "INSERT INTO " . BANLIST_TABLE . " (ban_ip)
				VALUES ('" . $ip_list[$i] . "')";
			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, "Couldn't insert ban_ip info into database", "", __LINE__, __FILE__, $sql);
			}
		}
	}

	//
	// Now we'll delete all entries from the session table with any of the banned
	// user or IP info just entered into the ban table ... this will force a session
	// initialisation resulting in an instant ban
	//
	if ( $kill_session_sql != '' )
	{
		$sql = "DELETE FROM " . SESSIONS_TABLE . "
			WHERE $kill_session_sql";
		if ( !$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, "Couldn't delete banned sessions from database", "", __LINE__, __FILE__, $sql);
		}
	}

	for($i = 0; $i < count($email_list); $i++)
	{
		$in_banlist = false;
		for($j = 0; $j < count($current_banlist); $j++)
		{
			if ( $email_list[$i] == $current_banlist[$j]['ban_email'] )
			{
				$in_banlist = true;
			}
		}

		if ( !$in_banlist )
		{
			$sql = "INSERT INTO " . BANLIST_TABLE . " (ban_email)
				VALUES ('" . str_replace("\'", "''", $email_list[$i]) . "')";
			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, "Couldn't insert ban_email info into database", "", __LINE__, __FILE__, $sql);
			}
		}
	}

	$where_sql = '';

	if ( isset($HTTP_POST_VARS['unban_user']) )
	{
		$user_list = $HTTP_POST_VARS['unban_user'];

		for($i = 0; $i < count($user_list); $i++)
		{
			if ( $user_list[$i] != -1 )
			{
				$where_sql .= ( ( $where_sql != '' ) ? ', ' : '' ) . intval($user_list[$i]);
			}
		}
	}

	if ( isset($HTTP_POST_VARS['unban_ip']) )
	{
		$ip_list = $HTTP_POST_VARS['unban_ip'];

		for($i = 0; $i < count($ip_list); $i++)
		{
			if ( $ip_list[$i] != -1 )
			{
				$where_sql .= ( ( $where_sql != '' ) ? ', ' : '' ) . str_replace("\'", "''", $ip_list[$i]);
			}
		}
	}

	if ( isset($HTTP_POST_VARS['unban_email']) )
	{
		$email_list = $HTTP_POST_VARS['unban_email'];

		for($i = 0; $i < count($email_list); $i++)
		{
			if ( $email_list[$i] != -1 )
			{
				$where_sql .= ( ( $where_sql != '' ) ? ', ' : '' ) . str_replace("\'", "''", $email_list[$i]);
			}
		}
	}

	if ( $where_sql != '' )
	{
		$sql = "DELETE FROM " . BANLIST_TABLE . "
			WHERE ban_id IN ($where_sql)";
		if ( !$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, "Couldn't delete ban info from database", "", __LINE__, __FILE__, $sql);
		}
	}

	$message = $lang['Ban_update_sucessful'] . '<br /><br />' . sprintf($lang['Click_return_banadmin'], '<a href="' . append_sid("admin_user_ban.$phpEx") . '">', '</a>') . '<br /><br />' . sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid("index.$phpEx?pane=right") . '">', '</a>');

	message_die(GENERAL_MESSAGE, $message);

}
else
{
	$template->set_filenames(array(
		'body' => 'admin/user_ban_body.tpl')
	);

	$template->assign_vars(array(
		'L_BAN_TITLE' => $lang['Ban_control'],
		'L_BAN_EXPLAIN' => $lang['Ban_explain'],
		'L_BAN_EXPLAIN_WARN' => $lang['Ban_explain_warn'],
		'L_IP_OR_HOSTNAME' => $lang['IP_hostname'],
		'L_EMAIL_ADDRESS' => $lang['Email_address'],
		'L_SUBMIT' => $lang['Submit'],
		'L_RESET' => $lang['Reset'],

		'S_BANLIST_ACTION' => append_sid("admin_user_ban.$phpEx"))
	);

	$template->assign_vars(array(
		'L_BAN_USER' => $lang['Ban_username'],
		'L_BAN_USER_EXPLAIN' => $lang['Ban_username_explain'],
		'L_BAN_IP' => $lang['Ban_IP'],
		'L_BAN_IP_EXPLAIN' => $lang['Ban_IP_explain'],
		'L_BAN_EMAIL' => $lang['Ban_email'],
		'L_BAN_EMAIL_EXPLAIN' => $lang['Ban_email_explain'])
	);

	$userban_count = 0;
	$ipban_count = 0;
	$emailban_count = 0;

	$sql = "SELECT b.ban_id, u.user_id, u.username
		FROM " . BANLIST_TABLE . " b, " . USERS_TABLE . " u
		WHERE u.user_id = b.ban_userid
			AND b.ban_userid <> 0
			AND u.user_id <> " . ANONYMOUS . "
		ORDER BY u.user_id ASC";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not select current user_id ban list', '', __LINE__, __FILE__, $sql);
	}

	$user_list = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	$select_userlist = '';
	for($i = 0; $i < count($user_list); $i++)
	{
		$select_userlist .= '<option value="' . $user_list[$i]['ban_id'] . '">' . $user_list[$i]['username'] . '</option>';
		$userban_count++;
	}

	if( $select_userlist == '' )
	{
		$select_userlist = '<option value="-1">' . $lang['No_banned_users'] . '</option>';
	}

	$select_userlist = '<select name="unban_user[]" multiple="multiple" size="5">' . $select_userlist . '</select>';

	$sql = "SELECT ban_id, ban_ip, ban_email
		FROM " . BANLIST_TABLE;
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not select current ip ban list', '', __LINE__, __FILE__, $sql);
	}

	$banlist = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	$select_iplist = '';
	$select_emaillist = '';

	for($i = 0; $i < count($banlist); $i++)
	{
		$ban_id = $banlist[$i]['ban_id'];

		if ( !empty($banlist[$i]['ban_ip']) )
		{
			$ban_ip = str_replace('255', '*', decode_ip($banlist[$i]['ban_ip']));
			$select_iplist .= '<option value="' . $ban_id . '">' . $ban_ip . '</option>';
			$ipban_count++;
		}
		else if ( !empty($banlist[$i]['ban_email']) )
		{
			$ban_email = $banlist[$i]['ban_email'];
			$select_emaillist .= '<option value="' . $ban_id . '">' . $ban_email . '</option>';
			$emailban_count++;
		}
	}

	if ( $select_iplist == '' )
	{
		$select_iplist = '<option value="-1">' . $lang['No_banned_ip'] . '</option>';
	}

	if ( $select_emaillist == '' )  
	{
		$select_emaillist = '<option value="-1">' . $lang['No_banned_email'] . '</option>';
	}

	$select_iplist = '<select name="unban_ip[]" multiple="multiple" size="5">' . $select_iplist . '</select>';
	$select_emaillist = '<select name="unban_email[]" multiple="multiple" size="5">' . $select_emaillist . '</select>';

	$template->assign_vars(array(
		'L_UNBAN_USER' => $lang['Unban_username'],
		'L_UNBAN_USER_EXPLAIN' => $lang['Unban_username_explain'],
		'L_UNBAN_IP' => $lang['Unban_IP'],
		'L_UNBAN_IP_EXPLAIN' => $lang['Unban_IP_explain'],
		'L_UNBAN_EMAIL' => $lang['Unban_email'],
		'L_UNBAN_EMAIL_EXPLAIN' => $lang['Unban_email_explain'], 
		'L_USERNAME' => $lang['Username'], 
		'L_LOOK_UP' => $lang['Look_up_User'],
		'L_FIND_USERNAME' => $lang['Find_username'],

		'U_SEARCH_USER' => append_sid("./../search.$phpEx?mode=searchuser"), 
		'S_UNBAN_USERLIST_SELECT' => $select_userlist,
		'S_UNBAN_IPLIST_SELECT' => $select_iplist,
		'S_UNBAN_EMAILLIST_SELECT' => $select_emaillist,
		'S_BAN_ACTION' => append_sid("admin_user_ban.$phpEx"))
	);
}

$template->pparse('body');

include('./page_footer_admin.'.$phpEx);

?>