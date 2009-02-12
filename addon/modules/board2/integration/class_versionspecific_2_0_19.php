<?php
/*
 * Created on 17.02.2006
 *
 * @author Pieringer Johannes
 *
 * Integrates phpBB in lansuite by making the phpbb_user to a view of lansuite_user.
 */

include_once('modules/board2/class_versionspecific.php');
define('IN_PHPBB',true);

class Versionspecific_2_0_19 extends Versionspecific {

	/**
	 * Checks if ther are lansuite Accouts that have reserved phpBB usernames.
	 * @return, array with usernames.
	 */
	public function checkForSpecialUser() {
		global $db;

		$data = $db->qry_first('SELECT username FROM %prefix%user WHERE username LIKE \'ANONYMOUS\'');
		if ($data['username'] != '') return array('Anonymous');
		return array();
	}

	/**
	 * Gets an array with the phpBB translation of its installation.
	 * @return unknown_type, nothing
	 */
	public function getPhpbbTranslations() {
		global $config, $language;

		$tempConfig = $config;
		$phpbblang = '';
		switch ($language) {
			case 'de':
				$phpbblang='german';
				break;
			default:
				$phpbblang='english';
				break;
		}

		//The default language of phpBB is english.
		//Use the translation from phpBB to explain the user how to fill in the installationform.
		if (file_exists($config[board2][path]."language/lang_" . $phpbblang . "/lang_admin.php")) {
			include_once($config[board2][path]."language/lang_" . $phpbblang . "/lang_admin.php");
		} else {
			include_once($config[board2][path]."language/lang_english/lang_admin.php");
		}
		// $lang is not from lansuite, but set from phpbb
		$trans_array = array();
		$trans_array['Default_lang'] = htmlentities($lang['Default_lang']);
		$trans_array['dbms'] = htmlentities($lang['dbms']);
		$trans_array['DB_Host'] = htmlentities($lang['DB_Host']);
		$trans_array['DB_Name'] = htmlentities($lang['DB_Name']);
		$trans_array['DB_Username'] = htmlentities($lang['DB_Username']);
		$trans_array['DB_Password'] = htmlentities($lang['DB_Password']);
		$trans_array['Table_Prefix'] = htmlentities($lang['Table_Prefix']);
		$trans_array['Admin_email'] = htmlentities($lang['Admin_email']);
		$trans_array['Admin_Username'] = htmlentities($lang['Admin_Username']);
		$trans_array['Admin_Password'] = htmlentities($lang['Admin_Password']);
		$trans_array['Admin_Password_confirm'] = htmlentities($lang['Admin_Password_confirm']);
		$config = $tempConfig;
		return $trans_array;
	}

	/**
	 * Gets the installation url of phpbb
	 * @return, returns the installation url of phpbb
	 */
	public function getPhpbbInstallationUrl(){
		global $config;
		return $config[board2][path].'install/install.php';
	}

	/**
	 * Prepares lansuite for the integration and alters the user table e.g. to add the necessary columns.
	 * @return nothing
	 */
	public function preparePhpbbIntegration(){
		$this->alterLansuiteUserTableAdd();
	}

	/**
	 * Removes the phpBB user table and creates a view for phpBB that points to the lansuite user table.
	 * @return unknown_type, nothing
	 */
	public function integratePhpbb(){
		global $db;
		$this->getPhpbbConstants();

		//Adds the Anonymous Account from phpBB to lansuite
		include_once('./modules/board2/class_board2.php');
			
		// Delete the Table lansuite_phpbb_users
		$db->qry('DROP TABLE ' . USERS_TABLE . ';');

		// Create a View witch has the same colums as the lansuite_phpbb_users from phpbb and call it lansuite_phpbb_users.
		// After this query phpbb 'thinks' that it is writing on the table lansuite_phpbb_users but in real it writes changes to lansuite_user.
		$this->createPhpbbUserView();
	}
	
	/**
	 * Creates a new user in lansuite with the data of the phpBB user.
	 * @param $phpbbUserID, The id of the user in phpBB
	 * @return Returns the id of the new user in lansuite.
	 */
	public function transferPhpbbUserToLansuite($phpbbUserID){
		global $db, $config;
			
		$this->getPhpbbConstants();
		$query_id = $db->qry('SELECT `user_id`, `user_active`, `username`, `user_password`, `user_session_time`, `user_session_page`, `user_lastvisit`, `user_regdate`, `user_level`, `user_posts`, `user_timezone`, `user_style`, `user_lang`, `user_dateformat`, `user_new_privmsg`, `user_unread_privmsg`, `user_last_privmsg`, `user_login_tries`, `user_last_login_try`, `user_emailtime`, `user_viewemail`, `user_attachsig`, `user_allowhtml`, `user_allowbbcode`, `user_allowsmile`, `user_allowavatar`, `user_allow_pm`, `user_allow_viewonline`, `user_notify`, `user_notify_pm`, `user_popup_pm`, `user_rank`, `user_avatar`, `user_avatar_type`, `user_email`, `user_icq`, `user_website`, `user_from`, `user_sig`, `user_sig_bbcode_uid`, `user_aim`, `user_yim`, `user_msnm`, `user_occ`, `user_interests`, `user_actkey`, `user_newpasswd` '.
			'FROM ' . USERS_TABLE . ' WHERE user_id = %int%;', $phpbbUserID);

		$data = $db->fetch_array($query_id);
		if ($data == null) return FALSE;

		include_once('./modules/board2/class_db_insertStatement.php');
		$insertStmt = new insertStatement($config['database']['prefix'] . 'user');

		$insertStmt->addParameter('int',	'userid', $data['user_id']);
		$insertStmt->addParameter('int', 	'phpbb_user_active', $data['user_active']);
		$insertStmt->addParameter('string',	'username', $data['username']);
		$insertStmt->addParameter('string',	'password', $data['user_password']);
		$insertStmt->addParameter('int',	'phpbb_user_session_time', $data['user_session_time']);
		$insertStmt->addParameter('int',	'phpbb_user_session_page', $data['user_session_page']);
		$insertStmt->addParameter('int',	'phpbb_user_lastvisit', $data['user_lastvisit']);
		$insertStmt->addParameter('int', 	'phpbb_user_regdate', $data['user_regdate']);
		$insertStmt->addParameter('int', 	'phpbb_user_level', $data['user_level']);
		$insertStmt->addParameter('int',	'phpbb_user_posts', $data['user_posts']);
		$insertStmt->addParameter('int',	'phpbb_user_timezone', $data['user_timezone']);
		$insertStmt->addParameter('int',	'phpbb_user_style', $data['user_style']);
		$insertStmt->addParameter('string',	'phpbb_user_lang', $data['user_lang']);
		$insertStmt->addParameter('string',	'phpbb_user_dateformat', $data['user_dateformat']);
		$insertStmt->addParameter('int',	'phpbb_user_new_privmsg', $data['user_new_privmsg']);
		$insertStmt->addParameter('int',	'phpbb_user_unread_privmsg', $data['user_unread_privmsg']);
		$insertStmt->addParameter('int',	'phpbb_user_last_privmsg', $data['user_last_privmsg']);
		$insertStmt->addParameter('int',	'phpbb_user_login_tries', $data['user_login_tries']);
		$insertStmt->addParameter('int',	'phpbb_user_last_login_try', $data['user_last_login_try']);
		$insertStmt->addParameter('int',	'phpbb_user_emailtime', $data['user_emailtime']);
		$insertStmt->addParameter('int',	'phpbb_user_viewemail', $data['user_viewemail']);
		$insertStmt->addParameter('int',	'phpbb_user_allowhtml', $data['user_allowhtml']);
		$insertStmt->addParameter('int',	'phpbb_user_allowbbcode', $data['user_allowbbcode']);
		$insertStmt->addParameter('int',	'phpbb_user_allowsmile', $data['user_allowsmile']);
		$insertStmt->addParameter('int',	'phpbb_user_allowavatar', $data['user_allowavatar']);
		$insertStmt->addParameter('int',	'phpbb_user_allow_pm', $data['user_allow_pm']);
		$insertStmt->addParameter('int',	'phpbb_user_allow_viewonline', $data['user_allow_viewonline']);
		$insertStmt->addParameter('int',	'phpbb_user_notify', $data['user_notify']);
		$insertStmt->addParameter('int',	'phpbb_user_notify_pm', $data['user_notify_pm']);
		$insertStmt->addParameter('int',	'phpbb_user_popup_pm', $data['user_popup_pm']);
		$insertStmt->addParameter('int',	'phpbb_user_rank', $data['user_rank']);
		$insertStmt->addParameter('string',	'phpbb_user_avatar', $data['user_avatar']);
		$insertStmt->addParameter('int',	'phpbb_user_avatar_type', $data['user_avatar_type']);
		$insertStmt->addParameter('string',	'email', $data['user_email']);
		$insertStmt->addParameter('string',	'icq', $data['user_icq']);
		$insertStmt->addParameter('string',	'phpbb_user_website', $data['user_website']);
		$insertStmt->addParameter('string',	'phpbb_user_from', $data['user_from']);
		$insertStmt->addParameter('string',	'phpbb_user_sig', $data['user_sig']);
		$insertStmt->addParameter('string',	'phpbb_user_sig_bbcode_uid', $data['user_sig_bbcode_uid']);
		$insertStmt->addParameter('string',	'phpbb_user_aim', $data['user_aim']);
		$insertStmt->addParameter('string',	'phpbb_user_yim', $data['user_yim']);
		$insertStmt->addParameter('string',	'msn', $data['user_msnm']);
		$insertStmt->addParameter('string',	'phpbb_user_occ', $data['user_occ']);
		$insertStmt->addParameter('string',	'phpbb_user_interests', $data['user_interests']);
		$insertStmt->addParameter('string',	'phpbb_user_actkey', $data['user_actkey']);
		$insertStmt->addParameter('string',	'phpbb_user_newpasswd', $data['user_newpasswd']);

		$insertStmt->execute();
		return TRUE;
	}

	/**
	 * Copies the phpBB specific user info to the lansuite user table.
	 * @param $phpbbUserID, the id of the user in phpBB
	 * @param $lsUserID, the id of the user in lansuite
	 * @return unknown_type, nothing
	 */
	public function transferPhpBBUserInfoToLansuite($phpbbUserID, $lsUserID) {
		global $db, $config;
		$this->getPhpbbConstants();
		$query_id = $db->qry('SELECT `user_id`, `user_active`, `username`, `user_password`, `user_session_time`, `user_session_page`, `user_lastvisit`, `user_regdate`, `user_level`, `user_posts`, `user_timezone`, `user_style`, `user_lang`, `user_dateformat`, `user_new_privmsg`, `user_unread_privmsg`, `user_last_privmsg`, `user_login_tries`, `user_last_login_try`, `user_emailtime`, `user_viewemail`, `user_attachsig`, `user_allowhtml`, `user_allowbbcode`, `user_allowsmile`, `user_allowavatar`, `user_allow_pm`, `user_allow_viewonline`, `user_notify`, `user_notify_pm`, `user_popup_pm`, `user_rank`, `user_avatar`, `user_avatar_type`, `user_email`, `user_icq`, `user_website`, `user_from`, `user_sig`, `user_sig_bbcode_uid`, `user_aim`, `user_yim`, `user_msnm`, `user_occ`, `user_interests`, `user_actkey`, `user_newpasswd` '.
			'FROM ' . USERS_TABLE . ' WHERE user_id = %int%;', $phpbbUserID);

		$data = $db->fetch_array($query_id);
		if ($data == null) return FALSE;

		include_once('./modules/board2/class_db_updatestatement.php');
		$updateStmt = new updateStatement('%prefix%user');
		$updateStmt->addCondition('int', 'userid', $lsUserID);
		$updateStmt->addParameter('int', 	'phpbb_user_active', $data['user_active']);
		$updateStmt->addParameter('int',	'phpbb_user_session_time', $data['user_session_time']);
		$updateStmt->addParameter('int',	'phpbb_user_session_page', $data['user_session_page']);
		$updateStmt->addParameter('int',	'phpbb_user_lastvisit', $data['user_lastvisit']);
		$updateStmt->addParameter('int', 	'phpbb_user_regdate', $data['user_regdate']);
		$updateStmt->addParameter('int', 	'phpbb_user_level', $data['user_level']);
		$updateStmt->addParameter('int',	'phpbb_user_posts', $data['user_posts']);
		$updateStmt->addParameter('int',	'phpbb_user_timezone', $data['user_timezone']);
		$updateStmt->addParameter('int',	'phpbb_user_style', $data['user_style']);
		$updateStmt->addParameter('string',	'phpbb_user_lang', $data['user_lang']);
		$updateStmt->addParameter('string',	'phpbb_user_dateformat', $data['user_dateformat']);
		$updateStmt->addParameter('int',	'phpbb_user_new_privmsg', $data['user_new_privmsg']);
		$updateStmt->addParameter('int',	'phpbb_user_unread_privmsg', $data['user_unread_privmsg']);
		$updateStmt->addParameter('int',	'phpbb_user_last_privmsg', $data['user_last_privmsg']);
		$updateStmt->addParameter('int',	'phpbb_user_login_tries', $data['user_login_tries']);
		$updateStmt->addParameter('int',	'phpbb_user_last_login_try', $data['user_last_login_try']);
		$updateStmt->addParameter('int',	'phpbb_user_emailtime', $data['user_emailtime']);
		$updateStmt->addParameter('int',	'phpbb_user_viewemail', $data['user_viewemail']);
		$updateStmt->addParameter('int',	'phpbb_user_allowhtml', $data['user_allowhtml']);
		$updateStmt->addParameter('int',	'phpbb_user_allowbbcode', $data['user_allowbbcode']);
		$updateStmt->addParameter('int',	'phpbb_user_allowsmile', $data['user_allowsmile']);
		$updateStmt->addParameter('int',	'phpbb_user_allowavatar', $data['user_allowavatar']);
		$updateStmt->addParameter('int',	'phpbb_user_allow_pm', $data['user_allow_pm']);
		$updateStmt->addParameter('int',	'phpbb_user_allow_viewonline', $data['user_allow_viewonline']);
		$updateStmt->addParameter('int',	'phpbb_user_notify', $data['user_notify']);
		$updateStmt->addParameter('int',	'phpbb_user_notify_pm', $data['user_notify_pm']);
		$updateStmt->addParameter('int',	'phpbb_user_popup_pm', $data['user_popup_pm']);
		$updateStmt->addParameter('int',	'phpbb_user_rank', $data['user_rank']);
		$updateStmt->addParameter('string',	'phpbb_user_avatar', $data['user_avatar']);
		$updateStmt->addParameter('int',	'phpbb_user_avatar_type', $data['user_avatar_type']);
		$updateStmt->addParameter('string',	'icq', $data['user_icq']);
		$updateStmt->addParameter('string',	'phpbb_user_website', $data['user_website']);
		$updateStmt->addParameter('string',	'phpbb_user_from', $data['user_from']);
		$updateStmt->addParameter('string',	'phpbb_user_sig', $data['user_sig']);
		$updateStmt->addParameter('string',	'phpbb_user_sig_bbcode_uid', $data['user_sig_bbcode_uid']);
		$updateStmt->addParameter('string',	'phpbb_user_aim', $data['user_aim']);
		$updateStmt->addParameter('string',	'phpbb_user_yim', $data['user_yim']);
		$updateStmt->addParameter('string',	'msn', $data['user_msnm']);
		$updateStmt->addParameter('string',	'phpbb_user_occ', $data['user_occ']);
		$updateStmt->addParameter('string',	'phpbb_user_interests', $data['user_interests']);
		$updateStmt->addParameter('string',	'phpbb_user_actkey', $data['user_actkey']);
		$updateStmt->addParameter('string',	'phpbb_user_newpasswd', $data['user_newpasswd']);

		$updateStmt->execute();
		$this->shiftPhpBBUserID($phpbbUserID, $lsUserID, FALSE);
		return TRUE;
	}


	private function transferAllLansuiteUserToPhpbb() {
		global $db;
		$this->getPhpbbConstants();

		$db->qry('INSERT INTO ' . USERS_TABLE . ' (`user_id`, `user_email`, `username`, `user_password`, `user_icq`, `user_msnm`, `user_active`, `user_session_time`,
			`user_session_page`, `user_lastvisit`, `user_regdate`, `user_level`, `user_posts`, `user_timezone`, `user_style`, `user_lang`, `user_dateformat`, 
			`user_new_privmsg`, `user_unread_privmsg`, `user_last_privmsg`, `user_login_tries`, `user_last_login_try`, `user_emailtime`, `user_viewemail`, 
			`user_attachsig`, `user_allowhtml`, `user_allowbbcode`, `user_allowsmile`, `user_allowavatar`, `user_allow_pm`, `user_allow_viewonline`, 
			`user_notify`, `user_notify_pm`, `user_popup_pm`, `user_rank`, `user_avatar`, `user_avatar_type`, `user_website`, `user_from`, `user_sig`, 
			`user_sig_bbcode_uid`, `user_aim`, `user_yim`, `user_occ`, `user_interests`, `user_actkey`, `user_newpasswd`)
			SELECT `userid`, `email`, `username`, `password`, `icq`, `msn`, `phpbb_user_active`, `phpbb_user_session_time`, `phpbb_user_session_page`, 
			`phpbb_user_lastvisit`, `phpbb_user_regdate`, `phpbb_user_level`, `phpbb_user_posts`, `phpbb_user_timezone`, `phpbb_user_style`, 
			`phpbb_user_lang`, `phpbb_user_dateformat`, `phpbb_user_new_privmsg`, `phpbb_user_unread_privmsg`, `phpbb_user_last_privmsg`, 
			`phpbb_user_login_tries`, `phpbb_user_last_login_try`, `phpbb_user_emailtime`, `phpbb_user_viewemail`, `phpbb_user_attachsig`, 
			`phpbb_user_allowhtml`, `phpbb_user_allowbbcode`, `phpbb_user_allowsmile`, `phpbb_user_allowavatar`, `phpbb_user_allow_pm`, 
			`phpbb_user_allow_viewonline`, `phpbb_user_notify`, `phpbb_user_notify_pm`, `phpbb_user_popup_pm`, `phpbb_user_rank`, `phpbb_user_avatar`, 
			`phpbb_user_avatar_type`, `phpbb_user_website`, `phpbb_user_from`, `phpbb_user_sig`, `phpbb_user_sig_bbcode_uid`, `phpbb_user_aim`, 
			`phpbb_user_yim`, `phpbb_user_occ`, `phpbb_user_interests`, `phpbb_user_actkey`, `phpbb_user_newpasswd` FROM `%prefix%user`');
	}
	
	/**
	 * Adds the delta to all phpBB user id's to ensure that the lansuite and the phpBB user id's compete.
	 * @param $delta, the number about the phpBB users should be shifted.
	 * @return unknown_type, nothing
	 */
	public function shiftAllPhpBBUser($delta) {
		global $db;
		
		$query_id = $db->qry('SELECT user_id FROM ' . USERS_TABLE);
		while($rowdata = $db->fetch_array($query_id)){
			$this->shiftPhpBBUserID($rowdata[0], $rowdata[0] + $delta);
		}
	}

	/**
	 * Changes the phpbb user_id of the specified phpBB user.
	 * @return unknown_type, nothing
	 */
	public function shiftPhpBBUserID($oldPhpBBID, $newPhpBBID, $usertable = TRUE){
		$this->getPhpbbConstants();

		$this->shiftPhpBBUserIDDetail(BANLIST_TABLE, 'ban_userid', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(GROUPS_TABLE, 'group_moderator', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(POSTS_TABLE, 'poster_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(TOPICS_TABLE, 'topic_poster', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(PRIVMSGS_TABLE, 'privmsgs_from_userid', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(PRIVMSGS_TABLE, 'privmsgs_to_userid', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(SESSIONS_TABLE, 'session_user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(SESSIONS_KEYS_TABLE, 'user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(TOPICS_WATCH_TABLE, 'user_id', $oldPhpBBID, $newPhpBBID);
		if ($usertable) $this->shiftPhpBBUserIDDetail(USERS_TABLE, 'user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(USER_GROUP_TABLE, 'user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(VOTE_USERS_TABLE, 'vote_user_id', $oldPhpBBID, $newPhpBBID);
	}

	protected function shiftPhpBBUserIDDetail($table, $column, $oldPhpBBID, $newPhpBBID) {
		global $db;
		$db->qry('UPDATE '.$table.' SET ' . $column . ' = ' . $newPhpBBID . ' WHERE ' . $column . ' = ' . $oldPhpBBID);
	}

	/**
	 * Checks if the integration is possible and throws an exception if it isn't
	 * @return, nothing
	 */
	public function isIntegrationPossible(){
		global $db, $config;
		$this->getPhpbbConstants();

		if (!(file_exists($config[board2][path] . 'config.php'))) throw new Exception(t('PhpBB kann nicht integriert werden, da phpBB im Dateisystem nicht gefunden wurde. Bitte &uuml;berpr&uuml;fen Sie den Pfad.'));

		$data = $db->qry_first('SHOW FULL TABLES LIKE \'' . USERS_TABLE . '\';');
		if ($data['Table_type'] == 'VIEW') throw new Exception(t('PhpBB kann nicht integriert werden, da die phpBB User-Tabelle bereits eine View ist. Wenn ein vorheriger Integrationsversuch fehlgeschlagen ist, spielen Sie bitte das Bakcup ein!'));
		if ($data['Table_type'] != 'BASE TABLE') throw new Exception(t('PhpBB kann nicht integriert werden, da die phpBB User-Tabelle nicht gefunden wurde.'));

		return TRUE;
	}

	/**
	 * Returns all phpbb user that share a username but do not share an email with an lansuite user
	 * @return mastersearch
	 */
	public function getPhpBBUserWithEqualLsAccount_Username_MasterSearch() {
		global $config, $auth;
		$this->getPhpbbConstants();

		include_once('modules/mastersearch2/class_mastersearch2.php');
		$ms2 = new MasterSearch2('board2');
		 
		$ms2->query['from'] = USERS_TABLE . " p INNER JOIN {$config["tables"]["user"]} AS l";
		$ms2->query['where'] = "p.user_email != l.email AND p.username = l.username";
		$ms2->query['default_order_by'] = 'l.username DESC';
	  
		$ms2->AddResultField('Username', 'l.username');
		$ms2->AddResultField('Email', 'l.email');
		$ms2->AddResultField('Nachname', 'l.name');
		$ms2->AddResultField('Vorname', 'l.firstname');
		$ms2->AddResultField('PhpBB Email', 'p.user_email');

		$ms2->AddIconField('details', 'index.php?mod=usrmgr&action=details&userid=', t('Details'));
		if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=usrmgr&action=change&step=1&userid=', t('Editieren'));
		if ($auth['type'] >= 2) $ms2->AddIconField('delete', 'index.php?mod=usrmgr&action=delete&step=2&userid=', t('L&ouml;schen'));

		return $ms2;
	}

	/**
	 * Returns all phpbb user that share a username but do not share an email with an lansuite user
	 * @return mastersearch
	 */
	public function getPhpBBUserWithEqualLsAccount_Username() {
		global $db, $config;
		$this->getPhpbbConstants();
		include_once('modules/board2/class_userintegrationitem.php');

		$query_id = $db->qry('SELECT p.user_id, p.username, p.user_email, l.userid, l.username, l.email
								FROM ' . USERS_TABLE . ' p
								INNER JOIN %prefix%user l
								WHERE p.user_email != l.email
								AND p.username = l.username');

		$users_array = array();
		while($rowdata = $db->fetch_array($query_id)){
			$item = new UserIntegrationItem($rowdata[0], $rowdata[1], $rowdata[2], $rowdata[3], $rowdata[4], $rowdata[5]);
			$users_array[$rowdata[0]] = $item;
		}

		return $users_array;
	}

	/**
	 * Returns all phpbb user that share a email adress.
	 * @return mastersearch
	 */
	public function getPhpBBUserWithEqualLsAccount_Email_MasterSearch() {
		global $config, $auth;
		$this->getPhpbbConstants();

		include_once('modules/mastersearch2/class_mastersearch2.php');
		$ms2 = new MasterSearch2('board2');
		 
		$ms2->query['from'] = USERS_TABLE . " p INNER JOIN {$config["tables"]["user"]} AS l";
		$ms2->query['where'] = "p.user_email = l.email";
		$ms2->query['default_order_by'] = 'l.username DESC';
	  
		$ms2->AddResultField('Username', 'l.username');
		$ms2->AddResultField('Email', 'l.email');
		$ms2->AddResultField('Nachname', 'l.name');
		$ms2->AddResultField('Vorname', 'l.firstname');
		$ms2->AddResultField('PhpBB Username', 'p.username');

		$ms2->AddIconField('details', 'index.php?mod=usrmgr&action=details&userid=', t('Details'));
		if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=usrmgr&action=change&step=1&userid=', t('Editieren'));
		if ($auth['type'] >= 2) $ms2->AddIconField('delete', 'index.php?mod=usrmgr&action=delete&step=2&userid=', t('Löschen'));

		return $ms2;
	}

	/**
	 * Returns all phpbb user that share a email adress.
	 * @return mastersearch
	 */
	public function getPhpBBUserWithEqualLsAccount_Email() {
		global $db, $config;
		$this->getPhpbbConstants();
		include_once('modules/board2/class_userintegrationitem.php');

		$query_id = $db->qry('SELECT p.user_id, p.username, p.user_email, l.userid, l.username, l.email
								FROM ' . USERS_TABLE . ' p
								INNER JOIN %prefix%user l
								WHERE p.user_email = l.email');

		$users_array = array();
		while($rowdata = $db->fetch_array($query_id)){
			$item = new UserIntegrationItem($rowdata[0], $rowdata[1], $rowdata[2], $rowdata[3], $rowdata[4], $rowdata[5]);
			$users_array[$rowdata[0]] = $item;
		}

		return $users_array;
	}

	/**
	 * Returns all phpbb user that share neither a username nor an email adress.
	 * @return mastersearch
	 */
	public function getPhpBBUserWithoutEqualLsAccount() {
		global $db, $config;
		$this->getPhpbbConstants();

		$query_id = $db->qry('SELECT user_id, username, user_email
								FROM ' . USERS_TABLE . '
								WHERE (user_id NOT IN (SELECT p.user_id
								FROM ' . USERS_TABLE . ' p
								INNER JOIN %prefix%user l
								WHERE p.user_email = l.email
								OR p.username = l.username))');

		$users_array = array();
			
		while($rowdata = $db->fetch_array($query_id)){
			$item = new UserIntegrationItem($rowdata[0], $rowdata[1], $rowdata[2]);
			$users_array[$rowdata[0]] = $item;
		}

		return $users_array;
	}

	/**
	 * Sets the Option 'Enable account activation' to 'Admin' to ensure that all new user register per lansuite.
	 * @return unknown_type, nothing
	 */
	public function setAccountAdminActivation(){
		global $db;
		$this->getPhpbbConstants();
		$db->qry('UPDATE ' . CONFIG_TABLE . ' SET `config_value` = 2 WHERE `config_name` = \'require_activation\'');
	}
	/**
	 * Sets the user to a phpBB Administrator.
	 * @param $lsUserID, the id of the user.
	 * @return unknown_type, nothing
	 */
	public function setPhpbbAdmin($lsUserID){
		global $db;
		$db->qry('UPDATE %prefix%user SET phpbb_user_level = 1 WHERE userid = %int%', $lsUserID);
	}

	/**
	 * Sets the user active in phpBB
	 * @param $lsUserID, the id of the user.
	 * @return unknown_type, nothing
	 */
	public function setActive($lsUserID){
		global $db;
		$db->qry('UPDATE %prefix%user SET phpbb_user_active = 1 WHERE userid = %int%', $lsUserID);
	}

	/**
	 * Deintegrates the phpBB forum from lansuite.
	 * Removes the phpBB_users view, creates the pbpBB_users table and fills the table with ls user.
	 * @return unknown_type, nothing
	 */
	public function deIntegrate(){
		global $db;

		$this->getPhpbbConstants();
		$db->qry('DROP VIEW ' . USERS_TABLE . ';');

		$this->createPhpbbUserTable();
		$this->transferAllLansuiteUserToPhpbb();
		$this->alterLansuiteUserTableDrop();
	}

	/**
	 * Logs the user with the phpbb user id on the phpBB board.
	 *
	 * This function is under the LGPL and orginaly written by Duncan Gough
	 *
	 * Distributed under the LGPL license:
	 * http://www.gnu.org/licenses/lgpl.html
	 *
	 * Duncan Gough
	 * 3rdSense.com
	 *
	 * Home  http://www.suttree.com
	 * Work  http://www.3rdsense.com
	 * Play! http://www.playaholics.com
	 * @param $phpbbUserID
	 * @return unknown_type
	 */
	public function loginPhpbb($phpbbUserID){
		global $db, $board_config, $config, $phpbb_root_path, $phpEx;
		global $HTTP_COOKIE_VARS, $HTTP_GET_VARS, $SID;

		$temp_db = $db;
		$tempboard_config = $board_config;
		$tempConfig = $config;

		// Setup the phpbb environment and then
		// run through the phpbb login process
		$phpbb_root_path = $config[board2][path];			// The $phpbb_root_path is a phpBB variable
		include( $config[board2][path] . 'config.php' );
		include( $config[board2][path] . 'extension.inc' );
		include( $config[board2][path] . 'common.php' );

		session_begin( $phpbbUserID, $user_ip, PAGE_INDEX, FALSE, TRUE );
		$db = $temp_db;
		$board_config = $tempboard_config;
		$config = $tempConfig;
	}

	/**
	 * Logoff the user with the phpbb_user_id of the phpBB board.
	 *
	 * This function is under the LGPL and orginaly written by Duncan Gough
	 *
	 * Distributed under the LGPL license:
	 * http://www.gnu.org/licenses/lgpl.html
	 *
	 * Duncan Gough
	 * 3rdSense.com
	 *
	 * Home  http://www.suttree.com
	 * Work  http://www.3rdsense.com
	 * Play! http://www.playaholics.com
	 * @param $phpbbUserID
	 * @return unknown_type
	 */
	public function logoutPhpbb($phpbbUserID){
		global $db, $board_config, $config, $phpbb_root_path, $phpEx;
		global $HTTP_COOKIE_VARS, $HTTP_GET_VARS, $SID;

		$temp_db = $db;
		$tempboard_config = $board_config;
		$tempConfig = $config;

		// Setup the phpbb environment and then
		// run through the phpbb login process
		$phpbb_root_path = $config[board2][path];			// The $phpbb_root_path is a phpBB variable
		include( $config[board2][path] . 'config.php' );
		include( $config[board2][path] . 'extension.inc' );
		include( $config[board2][path] . 'common.php' );

		session_end( session_id(), $phpbbUserID );
		 
		// session_end doesn't seem to get rid of these cookies,
		// so we'll do it here just in to make certain.
		setcookie( $board_config[ 'cookie_name' ] . '_sid', '', time() - 3600, ' ' );
		setcookie( $board_config[ 'cookie_name' ] . '_mysql', '', time() - 3600, ' ' );

		$db = $temp_db;
		$board_config = $tempboard_config;
		$config = $tempConfig;
	}

	/**
	 * Gets the id of the anonymous user.
	 * @return unknown_type, returns the id of the user.
	 */
	public function getAnonymousUserID(){
		$this->getPhpbbConstants();
		return ANONYMOUS;
	}

	/**
	 * Sets the anonymous user id of phpbb
	 * @return, returns TRUE if everything went fine, otherwise false.
	 */
	public function setAnonymousUserID(){
		global $db, $lang, $board_config, $config;
		$this->getPhpbbConstants();

		$data = $db->qry_first('SELECT userid FROM %prefix%user WHERE username = \'Anonymous\'');
		$id = $data['userid'];

		$tempConfig = $config;
		$lines = file ($config[board2][path] . 'includes/constants.php');
		if ($lines == FALSE) return FALSE;
			
		$result_array = array();

		// Durchgehen des Arrays und Anzeigen des HTML Source inkl. Zeilennummern
		foreach ($lines as $line_num => $line) {
			if (strstr($line, '\'ANONYMOUS\'')) {
				$statements_array = split(';', $line);

				foreach ($statements_array as $statement)
				if (!strstr($statement, '\'ANONYMOUS\'') && strlen($statement)>5) array_push($result_array, $statement . ';');
				$line = 'define(\'ANONYMOUS\', ' . $id . ');' . PHP_EOL;
			}
			array_push($result_array, $line);
		}

		$config = $tempConfig;
		$Handle = fopen($config[board2][path] . 'includes/constants.php', 'w');
		if ($Handle == FALSE) return FALSE;
		foreach ($result_array as $line) if (fwrite($Handle, $line) == FALSE) return FALSE;
		fclose($Handle);

		return TRUE;
	}

	/**
	 * Gets an int array with all bot user_id's
	 * @return, an int array containing the user_id's of the bot user
	 */
	public function getAllPhpbbBotUser() {
		return array();
	}

	/**
	 * Loads the phpbb constants.
	 * @return unknown_type
	 */
	protected function getPhpbbConstants() {
		global $db, $lang, $board_config, $config, $gd;

		//Saves all Variables which are used by phpBB and lansuite
		$temp_db = $db;
		$temp_lang = $lang;
		$tempboard_config = $board_config;
		$temp_gd = $gd;
		$tempConfig = $config;

		$phpbb_root_path = $config[board2][path];	// The $phpbb_root_path is a phpBB variable
		include_once($config[board2][path] . 'config.php');
		include_once($config[board2][path] . 'extension.inc');
		include_once($config[board2][path] . 'includes/constants.php' );

		//Returns all Variables which are used by phpBB and lansuite
		$db = $temp_db;
		$lang = $temp_lang;
		$board_config = $tempboard_config;
		$gd = $temp_gd;
		$config = $tempConfig;
	}

	protected function createPhpbbUserTable() {
		global $db;
		$this->getPhpbbConstants();

		$db->qry('CREATE TABLE ' . USERS_TABLE . ' (
				  `user_id` mediumint(8) NOT NULL,
				  `user_active` tinyint(1) DEFAULT \'1\',
				  `username` varchar(25) NOT NULL,
				  `user_password` varchar(32) NOT NULL,
				  `user_session_time` int(11) NOT NULL DEFAULT \'0\',
				  `user_session_page` smallint(5) NOT NULL DEFAULT \'0\',
				  `user_lastvisit` int(11) NOT NULL DEFAULT \'0\',
				  `user_regdate` int(11) NOT NULL DEFAULT \'0\',
				  `user_level` tinyint(4) DEFAULT \'0\',
				  `user_posts` mediumint(8) unsigned NOT NULL DEFAULT \'0\',
				  `user_timezone` decimal(5,2) NOT NULL DEFAULT \'0.00\',
				  `user_style` tinyint(4) DEFAULT NULL,
				  `user_lang` varchar(255) DEFAULT NULL,
				  `user_dateformat` varchar(14) NOT NULL DEFAULT \'d M Y H:i\',
				  `user_new_privmsg` smallint(5) unsigned NOT NULL DEFAULT \'0\',
				  `user_unread_privmsg` smallint(5) unsigned NOT NULL DEFAULT \'0\',
				  `user_last_privmsg` int(11) NOT NULL DEFAULT \'0\',
				  `user_login_tries` smallint(5) unsigned NOT NULL DEFAULT \'0\',
				  `user_last_login_try` int(11) NOT NULL DEFAULT \'0\',
				  `user_emailtime` int(11) DEFAULT NULL,
				  `user_viewemail` tinyint(1) DEFAULT NULL,
				  `user_attachsig` tinyint(1) DEFAULT NULL,
				  `user_allowhtml` tinyint(1) DEFAULT \'1\',
				  `user_allowbbcode` tinyint(1) DEFAULT \'1\',
				  `user_allowsmile` tinyint(1) DEFAULT \'1\',
				  `user_allowavatar` tinyint(1) NOT NULL DEFAULT \'1\',
				  `user_allow_pm` tinyint(1) NOT NULL DEFAULT \'1\',
				  `user_allow_viewonline` tinyint(1) NOT NULL DEFAULT \'1\',
				  `user_notify` tinyint(1) NOT NULL DEFAULT \'1\',
				  `user_notify_pm` tinyint(1) NOT NULL DEFAULT \'0\',
				  `user_popup_pm` tinyint(1) NOT NULL DEFAULT \'0\',
				  `user_rank` int(11) DEFAULT \'0\',
				  `user_avatar` varchar(100) DEFAULT NULL,
				  `user_avatar_type` tinyint(4) NOT NULL DEFAULT \'0\',
				  `user_email` varchar(255) DEFAULT NULL,
				  `user_icq` varchar(15) DEFAULT NULL,
				  `user_website` varchar(100) DEFAULT NULL,
				  `user_from` varchar(100) DEFAULT NULL,
				  `user_sig` text,
				  `user_sig_bbcode_uid` char(10) DEFAULT NULL,
				  `user_aim` varchar(255) DEFAULT NULL,
				  `user_yim` varchar(255) DEFAULT NULL,
				  `user_msnm` varchar(255) DEFAULT NULL,
				  `user_occ` varchar(100) DEFAULT NULL,
				  `user_interests` varchar(255) DEFAULT NULL,
				  `user_actkey` varchar(32) DEFAULT NULL,
				  `user_newpasswd` varchar(32) DEFAULT NULL,
				  PRIMARY KEY (`user_id`),
				  KEY `user_session_time` (`user_session_time`)
				);');
	}

	/**
	 * Creates the phpBB user view that points to the lansuite user table.
	 * @return unknown_type
	 */
	protected function createPhpbbUserView()	{
		global $db;
		$this->getPhpbbConstants();

		$db->qry('CREATE VIEW ' . USERS_TABLE . ' AS SELECT
			`userid` AS `user_id`,
			`phpbb_user_active` AS `user_active`,
			`username`,
			`password` AS `user_password`,
			`phpbb_user_session_time` AS `user_session_time`,
			`phpbb_user_session_page` AS `user_session_page`,
			`phpbb_user_lastvisit` AS `user_lastvisit`,
			`phpbb_user_regdate` AS `user_regdate`,
			`phpbb_user_level` AS `user_level`,
			`phpbb_user_posts` AS `user_posts`,
			`phpbb_user_timezone` AS `user_timezone`,
			`phpbb_user_style` AS `user_style`,
			`phpbb_user_lang` AS `user_lang`,
			`phpbb_user_dateformat` AS `user_dateformat`,
			`phpbb_user_new_privmsg` AS `user_new_privmsg`,
			`phpbb_user_unread_privmsg` AS `user_unread_privmsg`,
			`phpbb_user_last_privmsg` AS `user_last_privmsg`,
			`phpbb_user_login_tries` AS `user_login_tries`,
			`phpbb_user_last_login_try` AS `user_last_login_try`,
			`phpbb_user_emailtime` AS `user_emailtime`,
			`phpbb_user_viewemail` AS `user_viewemail`,
			`phpbb_user_attachsig` AS `user_attachsig`,
			`phpbb_user_allowhtml` AS `user_allowhtml`,
			`phpbb_user_allowbbcode` AS `user_allowbbcode`,
			`phpbb_user_allowsmile` AS `user_allowsmile`,
			`phpbb_user_allowavatar` AS `user_allowavatar`,
			`phpbb_user_allow_pm` AS `user_allow_pm`,
			`phpbb_user_allow_viewonline` AS `user_allow_viewonline`,
			`phpbb_user_notify` AS `user_notify`,
			`phpbb_user_notify_pm` AS `user_notify_pm`,
			`phpbb_user_popup_pm` AS `user_popup_pm`,
			`phpbb_user_rank` AS `user_rank`,
			`phpbb_user_avatar` AS `user_avatar`,
			`phpbb_user_avatar_type` AS `user_avatar_type`,
			`email` AS `user_email`,
			`icq` AS `user_icq`,
			`phpbb_user_website` AS `user_website`,
			`phpbb_user_from` AS `user_from`,
			`phpbb_user_sig` AS `user_sig`,
			`phpbb_user_sig_bbcode_uid` AS `user_sig_bbcode_uid`,
			`phpbb_user_aim` AS `user_aim`,
			`phpbb_user_yim` AS `user_yim`,
			`msn` AS `user_msnm`,
			`phpbb_user_occ` AS `user_occ`,
			`phpbb_user_interests` AS `user_interests`,
			`phpbb_user_actkey` AS `user_actkey`,
			`phpbb_user_newpasswd` AS `user_newpasswd`
			FROM %prefix%user;
		');
	}

	/**
	 * Alters the lansuite user table and adds all necessary columns for phpbb
	 * @return unknown_type
	 */
	protected function alterLansuiteUserTableAdd() {
		global $db;

		$db->qry('ALTER TABLE `%prefix%user`
			ADD `phpbb_user_active` tinyint(1) DEFAULT \'1\',
			ADD `phpbb_user_session_time` int(11) NOT NULL DEFAULT \'0\',
			ADD `phpbb_user_session_page` smallint(5) NOT NULL DEFAULT \'0\',
			ADD `phpbb_user_lastvisit` int(11) NOT NULL DEFAULT \'0\',
			ADD `phpbb_user_regdate` int(11) NOT NULL DEFAULT \'0\',
			ADD `phpbb_user_level` tinyint(4) DEFAULT \'0\',
			ADD `phpbb_user_posts` mediumint(8) unsigned NOT NULL DEFAULT \'0\',
			ADD `phpbb_user_timezone` decimal(5,2) NOT NULL DEFAULT \'0.00\',
			ADD `phpbb_user_style` tinyint(4) DEFAULT NULL,
			ADD `phpbb_user_lang` varchar(255) DEFAULT NULL,
			ADD `phpbb_user_dateformat` varchar(14) NOT NULL DEFAULT \'d M Y H:i\',
			ADD `phpbb_user_new_privmsg` smallint(5) unsigned NOT NULL DEFAULT \'0\',
			ADD `phpbb_user_unread_privmsg` smallint(5) unsigned NOT NULL DEFAULT \'0\',
			ADD `phpbb_user_last_privmsg` int(11) NOT NULL DEFAULT \'0\',
			ADD `phpbb_user_login_tries` smallint(5) unsigned NOT NULL DEFAULT \'0\',
			ADD `phpbb_user_last_login_try` int(11) NOT NULL DEFAULT \'0\',
			ADD `phpbb_user_emailtime` int(11) DEFAULT NULL,
			ADD `phpbb_user_viewemail` tinyint(1) DEFAULT NULL,
			ADD `phpbb_user_attachsig` tinyint(1) DEFAULT NULL,
			ADD `phpbb_user_allowhtml` tinyint(1) DEFAULT \'1\',
			ADD `phpbb_user_allowbbcode` tinyint(1) DEFAULT \'1\',
			ADD `phpbb_user_allowsmile` tinyint(1) DEFAULT \'1\',
			ADD `phpbb_user_allowavatar` tinyint(1) NOT NULL DEFAULT \'1\',
			ADD `phpbb_user_allow_pm` tinyint(1) NOT NULL DEFAULT \'1\',
			ADD `phpbb_user_allow_viewonline` tinyint(1) NOT NULL DEFAULT \'1\',
			ADD `phpbb_user_notify` tinyint(1) NOT NULL DEFAULT \'1\',
			ADD `phpbb_user_notify_pm` tinyint(1) NOT NULL DEFAULT \'0\',
			ADD `phpbb_user_popup_pm` tinyint(1) NOT NULL DEFAULT \'0\',
			ADD `phpbb_user_rank` int(11) DEFAULT \'0\',
			ADD `phpbb_user_avatar` varchar(100) DEFAULT NULL,
			ADD `phpbb_user_avatar_type` tinyint(4) NOT NULL DEFAULT \'0\',
			ADD `phpbb_user_website` varchar(100) DEFAULT NULL,
			ADD `phpbb_user_from` varchar(100) DEFAULT NULL,
			ADD `phpbb_user_sig` text,
			ADD `phpbb_user_sig_bbcode_uid` char(10) DEFAULT NULL,
			ADD `phpbb_user_aim` varchar(255) DEFAULT NULL,
			ADD `phpbb_user_yim` varchar(255) DEFAULT NULL,
			ADD `phpbb_user_occ` varchar(100) DEFAULT NULL,
			ADD `phpbb_user_interests` varchar(255) DEFAULT NULL,
			ADD `phpbb_user_actkey` varchar(32) DEFAULT NULL,
			ADD `phpbb_user_newpasswd` varchar(32) DEFAULT NULL,
			ADD KEY `phpbb_user_session_time` (`phpbb_user_session_time`),
			ADD UNIQUE KEY `username_unique` (username)'
			);
	}

	protected function alterLansuiteUserTableDrop() {
		global $db;

		$db->qry('ALTER TABLE `%prefix%user`
				DROP `phpbb_user_active`,
				DROP `phpbb_user_session_time`,
				DROP `phpbb_user_session_page`,
				DROP `phpbb_user_lastvisit`,
				DROP `phpbb_user_regdate`,
				DROP `phpbb_user_level`,
				DROP `phpbb_user_posts`,
				DROP `phpbb_user_timezone`,
				DROP `phpbb_user_style`,
				DROP `phpbb_user_lang`,
				DROP `phpbb_user_dateformat`,
				DROP `phpbb_user_new_privmsg`,
				DROP `phpbb_user_unread_privmsg`,
				DROP `phpbb_user_last_privmsg`,
				DROP `phpbb_user_login_tries`,
				DROP `phpbb_user_last_login_try`,
				DROP `phpbb_user_emailtime`,
				DROP `phpbb_user_viewemail`,
				DROP `phpbb_user_attachsig`,
				DROP `phpbb_user_allowhtml`,
				DROP `phpbb_user_allowbbcode`,
				DROP `phpbb_user_allowsmile`,
				DROP `phpbb_user_allowavatar`,
				DROP `phpbb_user_allow_pm`,
				DROP `phpbb_user_allow_viewonline`,
				DROP `phpbb_user_notify`,
				DROP `phpbb_user_notify_pm`,
				DROP `phpbb_user_popup_pm`,
				DROP `phpbb_user_rank`,
				DROP `phpbb_user_avatar`,
				DROP `phpbb_user_avatar_type`,
				DROP `phpbb_user_website`,
				DROP `phpbb_user_from`,
				DROP `phpbb_user_sig`,
				DROP `phpbb_user_sig_bbcode_uid`,
				DROP `phpbb_user_aim`,
				DROP `phpbb_user_yim`,
				DROP `phpbb_user_occ`,
				DROP `phpbb_user_interests`,
				DROP `phpbb_user_actkey`,
				DROP `phpbb_user_newpasswd`,
				DROP KEY `username_unique`'
				);
	}
	//
	// Please make a history at the end of file of your changes !!
	//

	/* HISTORY
	 * 06. 2. 2009 : Major changes, lansuite 3.9 compatibility, phpbb 3 interface
	 */
}
