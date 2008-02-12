<?php
/*
 * Created on 17.02.2006
 *
 * @author Pieringer Johannes
 * 
 * Integrates phpBB in lansuite by making the phpbb_user to a view of lansuite_user.
 */
 
 	class FileIntegrationPhpBB
 	{
 		var $version = '2.0.19 - 2.0.21';
 		
 		/*
 		 * Creates the DB structure for the integration by removing the
 		 * phpBB_users and replacing it with an view of lansuite_user
 		 */
 		function integrateNewPhpBB()
 		{
			global $db, $config;
			
			//$data = $db->query_first('SELECT user_id FROM ' . $config["board2"]["prefix"] . 'users WHERE username = \'Anonymous\'');
		  	
		  	//$this->createsLansuiteUserFromPhpBBUser($data[0]);
		  	$query_id = $db->query('SELECT userid FROM ' . $config["database"]["prefix"] . 'user WHERE `type` = 3');
		  	
		  	while ($rowdata = $db->fetch_array($query_id))
		  	{
		  		$this->setphpBBAdmin($rowdata[0]);
		  	}
		  	$query_id = $db->query('UPDATE ' . $config["database"]["prefix"] . 'user SET user_active = \'1\' WHERE `type` = 3');

		  	$query_id = $db->query('SELECT userid FROM ' . $config["database"]["prefix"] . 'user_permissions WHERE module = \'board2\'');
		  	
		  	while ($rowdata = $db->fetch_array($query_id))
		  	{
		  		$this->setphpBBAdmin($rowdata[0]);
		  		$query_id = $db->query('UPDATE ' . $config["database"]["prefix"] . 'user SET user_active = \'1\' WHERE userid = \'' . $rowdata[0] . '\'');
		  	}
		  	$this->integratePhpBB();
 		}
		
		/*
		 * Deletes the phpBB users table and creats a phpBB users view of lansuite user
		 */
		function integratePhpBB()
		{
			global $db, $config;
			
			//Adds the Anonymous Account from phpBB to lansuite
			require_once('./modules/board2/class_board2.php');
			$board2 = new Board2();
			$this->createsLansuiteUserFromPhpBBUser($board2->getAnonymousUserID());
			
			// Delete the Table lansuite_phpbb_users
			$db->query('DROP TABLE ' . $config["board2"]["prefix"] . 'users;');
			 	
			// Create a View witch has the same colums as the lansuite_phpbb_users from phpbb and call it lansuite_phpbb_users.
			// After this query phpbb 'thinks' that it is writing on the table lansuite_phpbb_users but in real it writes changes to lansuite_user.
			$this->createphpBBUserView($config["board2"]["prefix"] . 'users');
		}
		
		function createphpBBUserView($name)
		{
			global $db, $config;
			$db->query('CREATE VIEW ' . $name . ' AS SELECT
			    userid AS user_id,
			    user_active,
			    username,
			    password AS user_password,
			    user_session_time,
			    user_session_page,
			    user_lastvisit,
			    user_regdate,
			    user_level,
			    user_posts,
			    user_timezone,
			    user_style,
			    user_lang,
			    user_dateformat,
			    user_new_privmsg,
			    user_unread_privmsg,
			    user_last_privmsg,
			    user_login_tries,
			    user_last_login_try,
			    user_emailtime,
			    user_viewemail,
			    user_attachsig,
			    user_allowhtml,
			    user_allowbbcode,
			    user_allowsmile,
			    user_allowavatar,
			    user_allow_pm,
			    user_allow_viewonline,
			    user_notify,
			    user_notify_pm,
			    user_popup_pm,
			    user_rank,
			    user_avatar,
			    user_avatar_type,
			    email AS user_email,
			    user_icq,
			    user_website,
			    user_from,
			    user_sig,
			    user_sig_bbcode_uid,
			    user_aim,
			    user_yim,
			    user_msnm,
			    user_occ,
			    user_interests,
			    user_actkey,
			    user_newpasswd
			 FROM ' . $config['database']['prefix'] . 'user;
			 ');
		}		
		/*
		 * Changes the UserID from the given phpBB User to the new ID
		 */
		function changePhpBBUserID($old_id, $new_id)
		{
			global $db, $config;
			
			$query_id = $db->query("UPDATE " . $config["board2"]["prefix"] . "groups SET `group_moderator` = " . $new_id . " WHERE `group_moderator` = " . $old_id . ";");
			$db->free_result($query_id);
			
			$query_id = $db->query("UPDATE " . $config["board2"]["prefix"] . "posts SET `poster_id` = " . $new_id . " WHERE `poster_id` = " . $old_id . ";");
			$db->free_result($query_id);
			
			$query_id = $db->query("UPDATE " . $config["board2"]["prefix"] . "topics SET `topic_poster` = " . $new_id . " WHERE `topic_poster` = " . $old_id . ";");
			$db->free_result($query_id);
			
			$query_id = $db->query("UPDATE " . $config["board2"]["prefix"] . "user_group SET `user_id` = " . $new_id . " WHERE `user_id` = " . $old_id . ";");
			$db->free_result($query_id);
			
			$query_id = $db->query("UPDATE " . $config["board2"]["prefix"] . "vote_voters SET `vote_user_id` = " . $new_id . " WHERE `vote_user_id` = " . $old_id . ";");
			$db->free_result($query_id);
			
			$query_id = $db->query("UPDATE " . $config["board2"]["prefix"] . "privmsgs SET `privmsgs_from_userid` = " . $new_id . " WHERE `privmsgs_from_userid` = " . $old_id . ";");
			$db->free_result($query_id);
			
			$query_id = $db->query("UPDATE " . $config["board2"]["prefix"] . "privmsgs SET `privmsgs_to_userid` = " . $new_id . " WHERE `privmsgs_to_userid` = " . $old_id . ";");
			$db->free_result($query_id);
			
			$query_id = $db->query("UPDATE " . $config["board2"]["prefix"] . "banlist SET `ban_userid` = " . $new_id . " WHERE `ban_userid` = " . $old_id . ";");
			$db->free_result($query_id);
			
			$query_id = $db->query("UPDATE " . $config["board2"]["prefix"] . "sessions SET `session_user_id` = " . $new_id . " WHERE `session_user_id` = " . $old_id . ";");
			$db->free_result($query_id);
			
			$query_id = $db->query("UPDATE " . $config["board2"]["prefix"] . "sessions_keys SET `user_id` = " . $new_id . " WHERE `user_id` = " . $old_id . ";");
			$db->free_result($query_id);
				
			$query_id = $db->query("UPDATE " . $config["board2"]["prefix"] . "topics_watch SET `user_id` = " . $new_id . " WHERE `user_id` = " . $old_id . ";");
			$db->free_result($query_id);
		}
		
		/*
		 * Creates a Lansuite User with the Data of the given phpBB Board User
		 */
		function createsLansuiteUserFromPhpBBUser($phpBBID)
		{
			global $db, $config, $func, $dsp;
			$data = $db->query_first('SELECT `user_id` , `user_active` , `username` , `user_password` , `user_session_time` , `user_session_page` , ' .
					'`user_lastvisit` , `user_regdate` , `user_level` , `user_posts` , `user_timezone` , `user_style` , `user_lang` , ' .
					'`user_dateformat` , `user_new_privmsg` , `user_unread_privmsg` , `user_last_privmsg` , `user_login_tries` , `user_last_login_try` ,' .
					' `user_emailtime` , `user_viewemail` , `user_attachsig` , `user_allowhtml` , `user_allowbbcode` , `user_allowsmile` , ' .
					'`user_allowavatar` , `user_allow_pm` , `user_allow_viewonline` , `user_notify` , `user_notify_pm` , `user_popup_pm` , `user_rank` , ' .
					'`user_avatar` , `user_avatar_type` , `user_email` , `user_icq` , `user_website` , `user_from` , `user_sig` ,' .
					' `user_sig_bbcode_uid` , `user_aim` , `user_yim` , `user_msnm` , `user_occ` , `user_interests` , `user_actkey`, `user_newpasswd` ' .
					'FROM '. $config["board2"]["prefix"] . 'users WHERE user_id = ' . $phpBBID . ';');
			
		
			require_once('./modules/board2/class_db_insertStatement.php');
			$insertStmt = new InsertStatement($config['database']['prefix'] . 'user');
			
			$insertStmt->addParameter('i', 'userid', $data['user_id']);			  	
		  	$insertStmt->addparameter('s', 'email', $data['user_email']);
			$insertStmt->addparameter('s', 'username', $data['username']);
			$insertStmt->addparameter('s', 'password', $data['user_password']);
			$insertStmt->addparameter('i', 'type', 0);
			$insertStmt->addparameter('s', 'city', $data['user_from']);
			$insertStmt->addparameter('i', 'user_active',  $data['user_active']);
			$insertStmt->addparameter('i', 'user_session_time', $data['user_session_time']);
			$insertStmt->addparameter('i', 'user_session_page', $data['user_session_page']);
			$insertStmt->addparameter('i', 'user_lastvisit', $data['user_lastvisit']);
			$insertStmt->addparameter('i', 'user_regdate', $data['user_regdate']);
			$insertStmt->addparameter('i', 'user_level', $data['user_level']);
			$insertStmt->addparameter('i', 'user_posts', $data['ser_posts']);
			$insertStmt->addparameter('i', 'user_timezone', $data['user_timezone']);
			$insertStmt->addparameter('i', 'user_style', $data['user_style']);
			$insertStmt->addparameter('s', 'user_lang', $data['user_lang']);
			$insertStmt->addparameter('s', 'user_dateformat', $data['user_dateformat']);
			$insertStmt->addparameter('i', 'user_new_privmsg', $data['user_new_privmsg']);
			$insertStmt->addparameter('i', 'user_unread_privmsg', $data['user_unread_privmsg']);
			$insertStmt->addparameter('i', 'user_last_privmsg', $data['user_last_privmsg']);
			$insertStmt->addparameter('i', 'user_login_tries', $data['user_login_tries']);
			$insertStmt->addparameter('i', 'user_last_login_try', $data['user_last_login_try']);
			$insertStmt->addparameter('i', 'user_emailtime', $data['user_emailtime']);
			$insertStmt->addparameter('i', 'user_viewemail', $data['user_viewemail']);
			$insertStmt->addparameter('i', 'user_attachsig', $data['user_attachsig']);
			$insertStmt->addparameter('i', 'user_allowhtml', $data['user_allowhtml']);
			$insertStmt->addparameter('i', 'user_allowbbcode', $data['user_allowbbcode']);
			$insertStmt->addparameter('i', 'user_allowsmile', $data['user_allowsmile']);
			$insertStmt->addparameter('i', 'user_allowavatar', $data['user_allowavatar']);
			$insertStmt->addparameter('i', 'user_allow_pm', $data['user_allow_pm']);
			$insertStmt->addparameter('i', 'user_allow_viewonline', $data['user_allow_viewonline']);
			$insertStmt->addparameter('i', 'user_notify', $data['user_notify']);
			$insertStmt->addparameter('i', 'user_notify_pm', $data['user_notify_pm']);
			$insertStmt->addparameter('i', 'user_popup_pm', $data['user_popup_pm']);
			$insertStmt->addparameter('i', 'user_rank', $data['user_rank']);
			$insertStmt->addparameter('s', 'user_avatar', $data['user_avatar']);
			$insertStmt->addparameter('i', 'user_avatar_type', $data['user_avatar_type']);
			$insertStmt->addparameter('s', 'user_icq', $data['user_icq']);
			$insertStmt->addparameter('s', 'user_website', $data['user_website']);
			$insertStmt->addparameter('s', 'user_from', $data['user_from']);
			$insertStmt->addparameter('s', 'user_sig', $data['user_sig']);
			$insertStmt->addparameter('s', 'user_sig_bbcode_uid', $data['user_sig_bbcode_uid']);
			$insertStmt->addparameter('s', 'user_aim', $data['user_aim']);
			$insertStmt->addparameter('s', 'user_yim', $data['user_yim']);
			$insertStmt->addparameter('s', 'user_msnm', $data['user_msnm']);
			$insertStmt->addparameter('s', 'user_occ', $data['user_occ']);
			$insertStmt->addparameter('s', 'user_interests', $data['user_interests']);
			$insertStmt->addparameter('s', 'user_actkey', $data['user_actkey']);
			$insertStmt->addparameter('s', 'user_newpasswd', $data['user_newpasswd']);
				
			$insertStmt->execute();
		}
		
		/*
		 * Transfers all userinformations from an phpBB User to an lansuite user
		 */
		function transferPhpBBUserInfoToLSUserInfo($phpbb_userid, $ls_userid)
		{
			global $db, $config, $func, $dsp;
			$data = $db->query_first('SELECT `user_id` , `user_active` , `username` , `user_password` , `user_session_time` , `user_session_page` , ' .
					'`user_lastvisit` , `user_regdate` , `user_level` , `user_posts` , `user_timezone` , `user_style` , `user_lang` , ' .
					'`user_dateformat` , `user_new_privmsg` , `user_unread_privmsg` , `user_last_privmsg` , `user_login_tries` , `user_last_login_try` ,' .
					' `user_emailtime` , `user_viewemail` , `user_attachsig` , `user_allowhtml` , `user_allowbbcode` , `user_allowsmile` , ' .
					'`user_allowavatar` , `user_allow_pm` , `user_allow_viewonline` , `user_notify` , `user_notify_pm` , `user_popup_pm` , `user_rank` , ' .
					'`user_avatar` , `user_avatar_type` , `user_email` , `user_icq` , `user_website` , `user_from` , `user_sig` ,' .
					' `user_sig_bbcode_uid` , `user_aim` , `user_yim` , `user_msnm` , `user_occ` , `user_interests` , `user_actkey`, `user_newpasswd` ' .
					'FROM '. $config["board2"]["prefix"] . 'users WHERE user_id = ' . $phpbb_userid . ';');
			
			
			require_once('./modules/board2/class_db_updateStatement.php');
			$updateStmt = new UpdateStatement($config['database']['prefix'] . 'user');
			
			$updateStmt->addparameter('i', 'user_active',  $data['user_active']);
			$updateStmt->addparameter('i', 'user_session_time', $data['user_session_time']);
			$updateStmt->addparameter('i', 'user_session_page', $data['user_session_page']);
			$updateStmt->addparameter('i', 'user_lastvisit', $data['user_lastvisit']);
			$updateStmt->addparameter('i', 'user_regdate', $data['user_regdate']);
			$updateStmt->addparameter('i', 'user_level', $data['user_level']);
			$updateStmt->addparameter('i', 'user_posts', $data['ser_posts']);
			$updateStmt->addparameter('i', 'user_timezone', $data['user_timezone']);
			$updateStmt->addparameter('i', 'user_style', $data['user_style']);
			$updateStmt->addparameter('s', 'user_lang', $data['user_lang']);
			$updateStmt->addparameter('s', 'user_dateformat', $data['user_dateformat']);
			$updateStmt->addparameter('i', 'user_new_privmsg', $data['user_new_privmsg']);
			$updateStmt->addparameter('i', 'user_unread_privmsg', $data['user_unread_privmsg']);
			$updateStmt->addparameter('i', 'user_last_privmsg', $data['user_last_privmsg']);
			$updateStmt->addparameter('i', 'user_login_tries', $data['user_login_tries']);
			$updateStmt->addparameter('i', 'user_last_login_try', $data['user_last_login_try']);
			$updateStmt->addparameter('i', 'user_emailtime', $data['user_emailtime']);
			$updateStmt->addparameter('i', 'user_viewemail', $data['user_viewemail']);
			$updateStmt->addparameter('i', 'user_attachsig', $data['user_attachsig']);
			$updateStmt->addparameter('i', 'user_allowhtml', $data['user_allowhtml']);
			$updateStmt->addparameter('i', 'user_allowbbcode', $data['user_allowbbcode']);
			$updateStmt->addparameter('i', 'user_allowsmile', $data['user_allowsmile']);
			$updateStmt->addparameter('i', 'user_allowavatar', $data['user_allowavatar']);
			$updateStmt->addparameter('i', 'user_allow_pm', $data['user_allow_pm']);
			$updateStmt->addparameter('i', 'user_allow_viewonline', $data['user_allow_viewonline']);
			$updateStmt->addparameter('i', 'user_notify', $data['user_notify']);
			$updateStmt->addparameter('i', 'user_notify_pm', $data['user_notify_pm']);
			$updateStmt->addparameter('i', 'user_popup_pm', $data['user_popup_pm']);
			$updateStmt->addparameter('i', 'user_rank', $data['user_rank']);
			$updateStmt->addparameter('s', 'user_avatar', $data['user_avatar']);
			$updateStmt->addparameter('i', 'user_avatar_type', $data['user_avatar_type']);
			$updateStmt->addparameter('s', 'user_icq', $data['user_icq']);
			$updateStmt->addparameter('s', 'user_website', $data['user_website']);
			$updateStmt->addparameter('s', 'user_from', $data['user_from']);
			$updateStmt->addparameter('s', 'user_sig', $data['user_sig']);
			$updateStmt->addparameter('s', 'user_sig_bbcode_uid', $data['user_sig_bbcode_uid']);
			$updateStmt->addparameter('s', 'user_aim', $data['user_aim']);
			$updateStmt->addparameter('s', 'user_yim', $data['user_yim']);
			$updateStmt->addparameter('s', 'user_msnm', $data['user_msnm']);
			$updateStmt->addparameter('s', 'user_occ', $data['user_occ']);
			$updateStmt->addparameter('s', 'user_interests', $data['user_interests']);
			$updateStmt->addparameter('s', 'user_actkey', $data['user_actkey']);
			$updateStmt->addparameter('s', 'user_newpasswd', $data['user_newpasswd']);
				
			$updateStmt->execute('(userid = ' . $ls_userid . ')');
		}
		
		/*
		 * Adds to all PhpBB Board userids the value $delta -> Shifts all phpBB UserIds about delta. 
		 */
		function addToAllPhpBBUserIDs($delta)
		{
			global $config, $db;
			
			$query_id = $db->query("select user_id from " . $config["board2"]["prefix"] . 'users');
			
			while($rowdata = $db->fetch_array($query_id)){
				$this->changePhpBBUserID($rowdata['user_id'], $delta + $rowdata[0]);
			}
			
			$db->free_result($query_id);
		}
		
		/*
		 * Returns the highest UserID of the phpBB
		 */
		function getHighestphpBBUserID()
		{
			global $config, $db;
			$data = ($db->query_first('SELECT max(user_id) FROM ' . $config["board2"]["prefix"] . 'users'));
			return $data[0];
		}
		
		/*
		 * Returns all PhpBB Users in this Style for the DropDownField:
		 * $phpbbUser[{userid}] = {username} - {email}
		 */
		function getPhpBBUser()
		{
			global $db, $config;
			
			require_once('./modules/board2/class_userlistitem.php');
			require_once('./modules/board2/class_board2.php');

			$board2 = new Board2();
			
			$query_id = $db->query("select user_id, username, user_email from " . $config["board2"]["prefix"] . 'users where user_id != '. $board2->getAnonymousUserID());
						
			$phpbbUser = array();
						
			while($rowdata = $db->fetch_array($query_id)){
				$item = new UserListItem();
				$item->setUsername($rowdata[1] . ' - ' . $rowdata[2]);
				$phpbbUser[$rowdata[0]] = $item;
			}
			
			return $phpbbUser;
		}
		
		/*
		 * Returns the language file from phpBB 
		 */
		function getPhpBBLang()
		{
		 	global $config, $language, $lang;
		  
		 	$temp = $lang;
		  
			//The default language of phpBB is english.
	    	//Use the translation from phpBB to explain the user how to fill in the installationform.
	    	if ($language == "de" and file_exists($config[board2][path]."language/lang_german/lang_admin.php"))
	        	include_once($config[board2][path]."language/lang_german/lang_admin.php");
	    	else
	         	include_once($config[board2][path]."language/lang_english/lang_admin.php");
			
		  	$langPhpBB = $lang;	
		  	$lang = $temp;
		  
		  	return $langPhpBB;
		}
		
		/*
		 * Sets the Option 'Enable account activation' to 'Admin'
		 */
		function setAccountAdminActivation()
		{
			global $db, $config;
			
			$db->query('UPDATE ' . $config["board2"]["prefix"] . 'config SET `config_value` = 2 WHERE `config_name` = \'require_activation\'');
		}
		
		/*
		 * Sets a lansuite user to phpbb Admin
		 */
		function setphpBBAdmin($ls_userid)
		{
			global $db, $config;
			
			$db->query('UPDATE ' . $config["database"]["prefix"] . 'user SET user_level = 1');
		}
		
		function getDoublePhpBBUser()
		{
			global $db, $config;
			
			$query_id = $db->query('SELECT l.username FROM '. $config["board2"]["prefix"] . 'users l WHERE 1 < (SELECT count(*) FROM '. $config["board2"]["prefix"] . 'users WHERE l.username = username)');
			
			$phpbbUser = array();
			
			while($rowdata = $db->fetch_array($query_id)){
				$phpbbUser[].= $rowdata[0];
			}
		}
		
		function deIntegrate()
		{
			global $db, $config;
			$query_id = $db->query('DROP VIEW '. $config["board2"]["prefix"] . 'users ');
			
			// Create a View witch has the same colums as the lansuite_phpbb_users from phpbb and call it lansuite_phpbb_users.
			// After this query phpbb 'thinks' that it is writing on the table lansuite_phpbb_users but in real it writes changes to lansuite_user.
			$this->createphpBBUserView('temp');
			
			$db->query('CREATE TABLE ' . $config["board2"]["prefix"] . 'users (
				  `user_id` mediumint(8) NOT NULL,
				  `user_active` tinyint(1) default \'1\',
				  `username` varchar(25) collate latin1_general_ci NOT NULL,
				  `user_password` varchar(32) collate latin1_general_ci NOT NULL,
				  `user_session_time` int(11) NOT NULL default \'0\',
				  `user_session_page` smallint(5) NOT NULL default \'0\',
				  `user_lastvisit` int(11) NOT NULL default \'0\',
				  `user_regdate` int(11) NOT NULL default \'0\',
				  `user_level` tinyint(4) default \'0\',
				  `user_posts` mediumint(8) unsigned NOT NULL default \'0\',
				  `user_timezone` decimal(5,2) NOT NULL default \'0.00\',
				  `user_style` tinyint(4) default NULL,
				  `user_lang` varchar(255) collate latin1_general_ci default NULL,
				  `user_dateformat` varchar(14) collate latin1_general_ci NOT NULL default \'d M Y H:i\',
				  `user_new_privmsg` smallint(5) unsigned NOT NULL default \'0\',
				  `user_unread_privmsg` smallint(5) unsigned NOT NULL default \'0\',
				  `user_last_privmsg` int(11) NOT NULL default \'0\',
				  `user_login_tries` smallint(5) unsigned NOT NULL default \'0\',
				  `user_last_login_try` int(11) NOT NULL default \'0\',
				  `user_emailtime` int(11) default NULL,
				  `user_viewemail` tinyint(1) default NULL,
				  `user_attachsig` tinyint(1) default NULL,
				  `user_allowhtml` tinyint(1) default \'1\',
				  `user_allowbbcode` tinyint(1) default \'1\',
				  `user_allowsmile` tinyint(1) default \'1\',
				  `user_allowavatar` tinyint(1) NOT NULL default \'1\',
				  `user_allow_pm` tinyint(1) NOT NULL default \'1\',
				  `user_allow_viewonline` tinyint(1) NOT NULL default \'1\',
				  `user_notify` tinyint(1) NOT NULL default \'1\',
				  `user_notify_pm` tinyint(1) NOT NULL default \'0\',
				  `user_popup_pm` tinyint(1) NOT NULL default \'0\',
				  `user_rank` int(11) default \'0\',
				  `user_avatar` varchar(100) collate latin1_general_ci default NULL,
				  `user_avatar_type` tinyint(4) NOT NULL default \'0\',
				  `user_email` varchar(255) collate latin1_general_ci default NULL,
				  `user_icq` varchar(15) collate latin1_general_ci default NULL,
				  `user_website` varchar(100) collate latin1_general_ci default NULL,
				  `user_from` varchar(100) collate latin1_general_ci default NULL,
				  `user_sig` text collate latin1_general_ci,
				  `user_sig_bbcode_uid` char(10) collate latin1_general_ci default NULL,
				  `user_aim` varchar(255) collate latin1_general_ci default NULL,
				  `user_yim` varchar(255) collate latin1_general_ci default NULL,
				  `user_msnm` varchar(255) collate latin1_general_ci default NULL,
				  `user_occ` varchar(100) collate latin1_general_ci default NULL,
				  `user_interests` varchar(255) collate latin1_general_ci default NULL,
				  `user_actkey` varchar(32) collate latin1_general_ci default NULL,
				  `user_newpasswd` varchar(32) collate latin1_general_ci default NULL,
				  PRIMARY KEY  (`user_id`),
				  KEY `user_session_time` (`user_session_time`))');
				  
			$db->query('INSERT INTO ' . $config["board2"]["prefix"] . 'users SELECT * FROM temp');
			
			$db->query('DROP VIEW temp');
			
			require_once('./modules/board2/class_board2.php');
			$board2 = new Board2();
			
			$db->query('DELETE FROM lansuite_user WHERE userid = ' . $board2->getAnonymousUserID());
		}
 	}
 	
	
 //
 // Please make a history at the end of file of your changes !!
 //

 /* HISTORY
 * 17. 2. 2006 : First adaption of the file from the sample module.
 * 19. 2. 2006 : Functionality added.
 * 14. 4. 2006 : Reimplemented and added Functionallity
 */
?>
