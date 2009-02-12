<?php
/*
 * Created on 06.02.2009
 *
 * @author Pieringer Johannes
 *
 * Integrates phpBB in lansuite by making the phpbb_user to a view of lansuite_user.
 */

include_once('modules/board2/integration/class_versionspecific_2_0_19.php');

class Versionspecific_2_0_22_plus_1_5_3 extends Versionspecific_2_0_19 {

	/**
	 * Adds the delta to all phpBB user id's to ensure that the lansuite and the phpBB user id's compete.
	 * @param $delta, the number about the phpBB users should be shifted.
	 * @return unknown_type, nothing
	 */
	public function shiftPhpBBUserID($oldPhpBBID, $newPhpBBID, $usertable = TRUE){
		global $config;
		$this->getPhpbbConstants();

		$table_prefix = $config['board2']['prefix'];
		$this->shiftPhpBBUserIDDetail(BANLIST_TABLE, 'ban_userid', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail($table_prefix . 'album' , 'pic_user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail($table_prefix . 'album_cat' , 'cat_user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail($table_prefix . 'album_comment' , 'comment_user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail($table_prefix . 'album_comment' , 'comment_edit_user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail($table_prefix . 'album_rate' , 'rate_user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail($table_prefix . 'attachments' , 'user_id_1', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail($table_prefix . 'attachments' , 'user_id_2', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail($table_prefix . 'attach_quota' , 'user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(BANNERS_TABLE, 'banner_owner', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(BANNER_STATS_TABLE, 'click_user', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(BOOKMARK_TABLE, 'user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(GROUPS_TABLE, 'group_moderator', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(JR_ADMIN_TABLE, 'user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail($table_prefix . 'kb_articles' , 'article_author_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail($table_prefix . 'kb_votes' , 'votes_userid', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(LINKS_TABLE , 'user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail($table_prefix . 'pa_comments' , 'poster_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail($table_prefix . 'pa_download_info' , 'user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail($table_prefix . 'pa_files' , 'user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail($table_prefix . 'pa_votes' , 'user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(POSTS_TABLE, 'poster_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(PRIVMSGS_TABLE, 'privmsgs_from_userid', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(PRIVMSGS_TABLE, 'privmsgs_to_userid', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(SESSIONS_TABLE, 'session_user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(SESSIONS_KEYS_TABLE, 'user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(SHOUTBOX_TABLE, 'shout_user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(TOPICS_TABLE, 'topic_poster', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(TOPIC_VIEW_TABLE, 'user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(TOPICS_WATCH_TABLE, 'user_id', $oldPhpBBID, $newPhpBBID);
		if ($usertable) $this->shiftPhpBBUserIDDetail(USERS_TABLE, 'user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(USER_GROUP_TABLE, 'user_id', $oldPhpBBID, $newPhpBBID);
		$this->shiftPhpBBUserIDDetail(VOTE_USERS_TABLE, 'vote_user_id', $oldPhpBBID, $newPhpBBID);
	}
	
/**
	 * Creates a new user in lansuite with the data of the phpBB user.
	 * @param $phpbbUserID, The id of the user in phpBB
	 * @return Returns the id of the new user in lansuite.
	 */
	public function transferPhpbbUserToLansuite($phpbbUserID){
		global $db, $config;
			
		$this->getPhpbbConstants();
		$query_id = $db->qry('SELECT `user_id`, `user_active`, `username`, `user_password`, `user_session_time`, `user_session_page`, `user_session_topic`, `user_lastvisit`, `user_regdate`, `user_level`, `user_posts`, `user_timezone`, `user_style`, `user_lang`, `user_dateformat`, `user_new_privmsg`, `user_unread_privmsg`, `user_last_privmsg`, `user_login_tries`, `user_last_login_try`, `user_emailtime`, `user_viewemail`, `user_attachsig`, `user_setbm`, `user_allowhtml`, `user_allowbbcode`, `user_allowsmile`, `user_allowavatar`, `user_allow_pm`, `user_allow_viewonline`, `user_notify`, `user_notify_pm`, `user_popup_pm`, `user_rank`, `user_avatar`, `user_avatar_type`, `user_email`, `user_icq`, `user_website`, `user_from`, `user_from_flag`, `user_sig`, `user_sig_bbcode_uid`, `user_aim`, `user_yim`, `user_msnm`, `user_occ`, `user_interests`, `user_actkey`, `user_newpasswd`, `ct_logintry`, `ct_unsucclogin`, `ct_pwreset`, `ct_mailcount`, `ct_postcount`, `ct_posttime`, `ct_searchcount`, `ct_searchtime`, `user_sub_forum`, `user_split_cat`, `user_last_topic_title`, `user_sub_level_links`, `user_display_viewonline`, `user_announcement_date_display`, `user_announcement_display`, `user_announcement_display_forum`, `user_announcement_split`, `user_announcement_forum`, `user_split_global_announce`, `user_split_announce`, `user_split_sticky`, `user_split_news`, `user_split_topic_split`, `user_birthday`, `user_next_birthday_greeting`, `user_gender`, `user_color_group`, `user_lastlogon`, `user_totaltime`, `user_totallogon`, `user_totalpages`, `user_calendar_display_open`, `user_calendar_header_cells`, `user_calendar_week_start`, `user_calendar_nb_row`, `user_calendar_birthday`, `user_calendar_forum`, `user_warnings`, `user_passwd_change`, `user_badlogin`, `user_blocktime`, `user_block_by`, `user_absence`, `user_absence_mode`, `user_absence_text`, `user_use_ajax_preview`, `user_use_ajax_edit` '.
			'FROM ' . USERS_TABLE . ' WHERE user_id = %int%;', $phpbbUserID);

		$data = $db->fetch_array($query_id);
		if ($data == null) return FALSE;

		include_once('./modules/board2/class_db_insertStatement.php');
		$insertStmt = new insertStatement($config['database']['prefix'] . 'user');
		$insertStmt->addParameter('int',	'userid', $data['user_id']);
		$insertStmt->addParameter('string',	'username', $data['username']);
		$insertStmt->addParameter('string',	'password', $data['user_password']);
		$insertStmt->addParameter('string',	'email', $data['user_email']);
		$insertStmt->addParameter('string',	'icq', $data['user_icq']);
		$insertStmt->addParameter('string',	'msn', $data['user_msnm']);
		$insertStmt->addParameter('int', 	'phpbb_user_active', $data['user_active']);
		$insertStmt->addParameter('int',	'phpbb_user_timezone', $data['user_timezone']);
		$insertStmt->addParameter('int',	'phpbb_ct_unsucclogin', $data['ct_unsucclogin']);
		$insertStmt->addParameter('int',	'phpbb_ct_mailcount', $data['ct_mailcount']);
		$insertStmt->addParameter('int',	'phpbb_ct_postcount', $data['ct_postcount']);
		$insertStmt->addParameter('int',	'phpbb_ct_posttime', $data['ct_posttime']);
		$insertStmt->addParameter('int',	'phpbb_ct_searchcount', $data['ct_searchcount']);
		$insertStmt->addParameter('int',	'phpbb_ct_searchtime', $data['ct_searchtime']);
		$insertStmt->addParameter('int',	'phpbb_user_session_time', $data['user_session_time']);
		$insertStmt->addParameter('int',	'phpbb_user_session_topic', $data['user_session_topic']);
		$insertStmt->addParameter('int',	'phpbb_user_lastvisit', $data['user_lastvisit']);
		$insertStmt->addParameter('int',	'phpbb_user_regdate', $data['user_regdate']);
		$insertStmt->addParameter('int',	'phpbb_user_last_privmsg', $data['user_last_privmsg']);
		$insertStmt->addParameter('int',	'phpbb_user_last_login_try', $data['user_last_login_try']);
		$insertStmt->addParameter('int',	'phpbb_user_emailtime', $data['user_emailtime']);
		$insertStmt->addParameter('int',	'phpbb_user_rank', $data['user_rank']);
		$insertStmt->addParameter('int',	'phpbb_user_birthday', $data['user_birthday']);
		$insertStmt->addParameter('int',	'phpbb_user_next_birthday_greeting', $data['user_next_birthday_greeting']);
		$insertStmt->addParameter('int',	'phpbb_user_lastlogon', $data['user_lastlogon']);
		$insertStmt->addParameter('int',	'phpbb_user_totaltime', $data['user_totaltime']);
		$insertStmt->addParameter('int',	'phpbb_user_totallogon', $data['user_totallogon']);
		$insertStmt->addParameter('int',	'phpbb_user_totalpages', $data['user_totalpages']);
		$insertStmt->addParameter('int',	'phpbb_user_passwd_change', $data['user_passwd_change']);
		$insertStmt->addParameter('int',	'phpbb_user_blocktime', $data['user_blocktime']);
		$insertStmt->addParameter('int',	'phpbb_ct_logintry', $data['ct_logintry']);
		$insertStmt->addParameter('int',	'phpbb_ct_pwreset', $data['ct_pwreset']);
		$insertStmt->addParameter('int',	'phpbb_user_absence_mode', $data['user_absence_mode']);
		$insertStmt->addParameter('int',	'phpbb_user_posts', $data['user_posts']);
		$insertStmt->addParameter('int',	'phpbb_user_color_group', $data['user_color_group']);
		$insertStmt->addParameter('int',	'phpbb_user_session_page', $data['user_session_page']);
		$insertStmt->addParameter('int',	'phpbb_user_warnings', $data['user_warnings']);
		$insertStmt->addParameter('int',	'phpbb_user_badlogin', $data['user_badlogin']);
		$insertStmt->addParameter('int',	'phpbb_user_new_privmsg', $data['user_new_privmsg']);
		$insertStmt->addParameter('int',	'phpbb_user_unread_privmsg', $data['user_unread_privmsg']);
		$insertStmt->addParameter('int',	'phpbb_user_login_tries', $data['user_login_tries']);
		$insertStmt->addParameter('string',	'phpbb_user_sig', $data['user_sig']);
		$insertStmt->addParameter('string',	'phpbb_user_absence_text', $data['user_absence_text']);
		$insertStmt->addParameter('int',	'phpbb_user_viewemail', $data['user_viewemail']);
		$insertStmt->addParameter('int',	'phpbb_user_attachsig', $data['user_attachsig']);
		$insertStmt->addParameter('int',	'phpbb_user_setbm', $data['user_setbm']);
		$insertStmt->addParameter('int',	'phpbb_user_allowhtml', $data['user_allowhtml']);
		$insertStmt->addParameter('int',	'phpbb_user_allowbbcode', $data['user_allowbbcode']);
		$insertStmt->addParameter('int',	'phpbb_user_allowsmile', $data['user_allowsmile']);
		$insertStmt->addParameter('int',	'phpbb_user_allowavatar', $data['user_allowavatar']);
		$insertStmt->addParameter('int',	'phpbb_user_allow_pm', $data['user_allow_pm']);
		$insertStmt->addParameter('int',	'phpbb_user_allow_viewonline', $data['user_allow_viewonline']);
		$insertStmt->addParameter('int',	'phpbb_user_notify', $data['user_notify']);
		$insertStmt->addParameter('int',	'phpbb_user_notify_pm', $data['user_notify_pm']);
		$insertStmt->addParameter('int',	'phpbb_user_popup_pm', $data['user_popup_pm']);
		$insertStmt->addParameter('int',	'phpbb_user_sub_forum', $data['user_sub_forum']);
		$insertStmt->addParameter('int',	'phpbb_user_split_cat', $data['user_split_cat']);
		$insertStmt->addParameter('int',	'phpbb_user_last_topic_title', $data['user_last_topic_title']);
		$insertStmt->addParameter('int',	'phpbb_user_sub_level_links', $data['user_sub_level_links']);
		$insertStmt->addParameter('int',	'phpbb_user_display_viewonline', $data['user_display_viewonline']);
		$insertStmt->addParameter('int',	'phpbb_user_announcement_date_display', $data['user_announcement_date_display']);
		$insertStmt->addParameter('int',	'phpbb_user_announcement_display', $data['user_announcement_display']);
		$insertStmt->addParameter('int',	'phpbb_user_announcement_display_forum', $data['user_announcement_display_forum']);
		$insertStmt->addParameter('int',	'phpbb_user_announcement_split', $data['user_announcement_split']);
		$insertStmt->addParameter('int',	'phpbb_user_announcement_forum', $data['user_announcement_forum']);
		$insertStmt->addParameter('int',	'phpbb_user_split_global_announce', $data['user_split_global_announce']);
		$insertStmt->addParameter('int',	'phpbb_user_split_announce', $data['user_split_announce']);
		$insertStmt->addParameter('int',	'phpbb_user_split_sticky', $data['user_split_sticky']);
		$insertStmt->addParameter('int',	'phpbb_user_split_news', $data['user_split_news']);
		$insertStmt->addParameter('int',	'phpbb_user_split_topic_split', $data['user_split_topic_split']);
		$insertStmt->addParameter('int',	'phpbb_user_calendar_display_open', $data['user_calendar_display_open']);
		$insertStmt->addParameter('int',	'phpbb_user_calendar_header_cells', $data['user_calendar_header_cells']);
		$insertStmt->addParameter('int',	'phpbb_user_calendar_week_start', $data['user_calendar_week_start']);
		$insertStmt->addParameter('int',	'phpbb_user_calendar_birthday', $data['user_calendar_birthday']);
		$insertStmt->addParameter('int',	'phpbb_user_calendar_forum', $data['user_calendar_forum']);
		$insertStmt->addParameter('int',	'phpbb_user_absence', $data['user_absence']);
		$insertStmt->addParameter('int',	'phpbb_user_use_ajax_preview', $data['user_use_ajax_preview']);
		$insertStmt->addParameter('int',	'phpbb_user_use_ajax_edit', $data['user_use_ajax_edit']);
		$insertStmt->addParameter('int',	'phpbb_user_calendar_nb_row', $data['user_calendar_nb_row']);
		$insertStmt->addParameter('int',	'phpbb_user_level', $data['user_level']);
		$insertStmt->addParameter('int',	'phpbb_user_style', $data['user_style']);
		$insertStmt->addParameter('int',	'phpbb_user_avatar_type', $data['user_avatar_type']);
		$insertStmt->addParameter('int',	'phpbb_user_gender', $data['user_gender']);
		$insertStmt->addParameter('string',	'phpbb_user_sig_bbcode_uid', $data['user_sig_bbcode_uid']);
		$insertStmt->addParameter('string',	'phpbb_user_avatar', $data['user_avatar']);
		$insertStmt->addParameter('string',	'phpbb_user_website', $data['user_website']);
		$insertStmt->addParameter('string',	'phpbb_user_from', $data['user_from']);
		$insertStmt->addParameter('string',	'phpbb_user_occ', $data['user_occ']);
		$insertStmt->addParameter('string',	'phpbb_user_dateformat', $data['user_dateformat']);
		$insertStmt->addParameter('string',	'phpbb_user_from_flag', $data['user_from_flag']);
		$insertStmt->addParameter('string',	'phpbb_user_lang', $data['user_lang']);
		$insertStmt->addParameter('string',	'phpbb_user_aim', $data['user_aim']);
		$insertStmt->addParameter('string',	'phpbb_user_yim', $data['user_yim']);
		$insertStmt->addParameter('string',	'phpbb_user_interests', $data['user_interests']);
		$insertStmt->addParameter('string',	'phpbb_user_actkey', $data['user_actkey']);
		$insertStmt->addParameter('string',	'phpbb_user_newpasswd', $data['user_newpasswd']);
		$insertStmt->addParameter('string',	'phpbb_user_block_by', $data['user_block_by']);
		
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
		$query_id = $db->qry('SELECT `user_id`, `user_active`, `username`, `user_password`, `user_session_time`, `user_session_page`, `user_session_topic`, `user_lastvisit`, `user_regdate`, `user_level`, `user_posts`, `user_timezone`, `user_style`, `user_lang`, `user_dateformat`, `user_new_privmsg`, `user_unread_privmsg`, `user_last_privmsg`, `user_login_tries`, `user_last_login_try`, `user_emailtime`, `user_viewemail`, `user_attachsig`, `user_setbm`, `user_allowhtml`, `user_allowbbcode`, `user_allowsmile`, `user_allowavatar`, `user_allow_pm`, `user_allow_viewonline`, `user_notify`, `user_notify_pm`, `user_popup_pm`, `user_rank`, `user_avatar`, `user_avatar_type`, `user_email`, `user_icq`, `user_website`, `user_from`, `user_from_flag`, `user_sig`, `user_sig_bbcode_uid`, `user_aim`, `user_yim`, `user_msnm`, `user_occ`, `user_interests`, `user_actkey`, `user_newpasswd`, `ct_logintry`, `ct_unsucclogin`, `ct_pwreset`, `ct_mailcount`, `ct_postcount`, `ct_posttime`, `ct_searchcount`, `ct_searchtime`, `user_sub_forum`, `user_split_cat`, `user_last_topic_title`, `user_sub_level_links`, `user_display_viewonline`, `user_announcement_date_display`, `user_announcement_display`, `user_announcement_display_forum`, `user_announcement_split`, `user_announcement_forum`, `user_split_global_announce`, `user_split_announce`, `user_split_sticky`, `user_split_news`, `user_split_topic_split`, `user_birthday`, `user_next_birthday_greeting`, `user_gender`, `user_color_group`, `user_lastlogon`, `user_totaltime`, `user_totallogon`, `user_totalpages`, `user_calendar_display_open`, `user_calendar_header_cells`, `user_calendar_week_start`, `user_calendar_nb_row`, `user_calendar_birthday`, `user_calendar_forum`, `user_warnings`, `user_passwd_change`, `user_badlogin`, `user_blocktime`, `user_block_by`, `user_absence`, `user_absence_mode`, `user_absence_text`, `user_use_ajax_preview`, `user_use_ajax_edit` '.
			'FROM ' . USERS_TABLE . ' WHERE user_id = %int%;', $phpbbUserID);

		$data = $db->fetch_array($query_id);
		if ($data == null) return FALSE;

		include_once('./modules/board2/class_db_updatestatement.php');
		$updateStmt = new updateStatement('%prefix%user');
		$updateStmt->addCondition('int', 	'userid', $lsUserID);
		$updateStmt->addParameter('int', 	'phpbb_user_active', $data['user_active']);
		$updateStmt->addParameter('int',	'phpbb_user_timezone', $data['user_timezone']);
		$updateStmt->addParameter('int',	'phpbb_ct_unsucclogin', $data['ct_unsucclogin']);
		$updateStmt->addParameter('int',	'phpbb_ct_mailcount', $data['ct_mailcount']);
		$updateStmt->addParameter('int',	'phpbb_ct_postcount', $data['ct_postcount']);
		$updateStmt->addParameter('int',	'phpbb_ct_posttime', $data['ct_posttime']);
		$updateStmt->addParameter('int',	'phpbb_ct_searchcount', $data['ct_searchcount']);
		$updateStmt->addParameter('int',	'phpbb_ct_searchtime', $data['ct_searchtime']);
		$updateStmt->addParameter('int',	'phpbb_user_session_time', $data['user_session_time']);
		$updateStmt->addParameter('int',	'phpbb_user_session_topic', $data['user_session_topic']);
		$updateStmt->addParameter('int',	'phpbb_user_lastvisit', $data['user_lastvisit']);
		$updateStmt->addParameter('int',	'phpbb_user_regdate', $data['user_regdate']);
		$updateStmt->addParameter('int',	'phpbb_user_last_privmsg', $data['user_last_privmsg']);
		$updateStmt->addParameter('int',	'phpbb_user_last_login_try', $data['user_last_login_try']);
		$updateStmt->addParameter('int',	'phpbb_user_emailtime', $data['user_emailtime']);
		$updateStmt->addParameter('int',	'phpbb_user_rank', $data['user_rank']);
		$updateStmt->addParameter('int',	'phpbb_user_birthday', $data['user_birthday']);
		$updateStmt->addParameter('int',	'phpbb_user_next_birthday_greeting', $data['user_next_birthday_greeting']);
		$updateStmt->addParameter('int',	'phpbb_user_lastlogon', $data['user_lastlogon']);
		$updateStmt->addParameter('int',	'phpbb_user_totaltime', $data['user_totaltime']);
		$updateStmt->addParameter('int',	'phpbb_user_totallogon', $data['user_totallogon']);
		$updateStmt->addParameter('int',	'phpbb_user_totalpages', $data['user_totalpages']);
		$updateStmt->addParameter('int',	'phpbb_user_passwd_change', $data['user_passwd_change']);
		$updateStmt->addParameter('int',	'phpbb_user_blocktime', $data['user_blocktime']);
		$updateStmt->addParameter('int',	'phpbb_ct_logintry', $data['ct_logintry']);
		$updateStmt->addParameter('int',	'phpbb_ct_pwreset', $data['ct_pwreset']);
		$updateStmt->addParameter('int',	'phpbb_user_absence_mode', $data['user_absence_mode']);
		$updateStmt->addParameter('int',	'phpbb_user_posts', $data['user_posts']);
		$updateStmt->addParameter('int',	'phpbb_user_color_group', $data['user_color_group']);
		$updateStmt->addParameter('int',	'phpbb_user_session_page', $data['user_session_page']);
		$updateStmt->addParameter('int',	'phpbb_user_warnings', $data['user_warnings']);
		$updateStmt->addParameter('int',	'phpbb_user_badlogin', $data['user_badlogin']);
		$updateStmt->addParameter('int',	'phpbb_user_new_privmsg', $data['user_new_privmsg']);
		$updateStmt->addParameter('int',	'phpbb_user_unread_privmsg', $data['user_unread_privmsg']);
		$updateStmt->addParameter('int',	'phpbb_user_login_tries', $data['user_login_tries']);
		$updateStmt->addParameter('string',	'phpbb_user_sig', $data['user_sig']);
		$updateStmt->addParameter('string',	'phpbb_user_absence_text', $data['user_absence_text']);
		$updateStmt->addParameter('int',	'phpbb_user_viewemail', $data['user_viewemail']);
		$updateStmt->addParameter('int',	'phpbb_user_attachsig', $data['user_attachsig']);
		$updateStmt->addParameter('int',	'phpbb_user_setbm', $data['user_setbm']);
		$updateStmt->addParameter('int',	'phpbb_user_allowhtml', $data['user_allowhtml']);
		$updateStmt->addParameter('int',	'phpbb_user_allowbbcode', $data['user_allowbbcode']);
		$updateStmt->addParameter('int',	'phpbb_user_allowsmile', $data['user_allowsmile']);
		$updateStmt->addParameter('int',	'phpbb_user_allowavatar', $data['user_allowavatar']);
		$updateStmt->addParameter('int',	'phpbb_user_allow_pm', $data['user_allow_pm']);
		$updateStmt->addParameter('int',	'phpbb_user_allow_viewonline', $data['user_allow_viewonline']);
		$updateStmt->addParameter('int',	'phpbb_user_notify', $data['user_notify']);
		$updateStmt->addParameter('int',	'phpbb_user_notify_pm', $data['user_notify_pm']);
		$updateStmt->addParameter('int',	'phpbb_user_popup_pm', $data['user_popup_pm']);
		$updateStmt->addParameter('int',	'phpbb_user_sub_forum', $data['user_sub_forum']);
		$updateStmt->addParameter('int',	'phpbb_user_split_cat', $data['user_split_cat']);
		$updateStmt->addParameter('int',	'phpbb_user_last_topic_title', $data['user_last_topic_title']);
		$updateStmt->addParameter('int',	'phpbb_user_sub_level_links', $data['user_sub_level_links']);
		$updateStmt->addParameter('int',	'phpbb_user_display_viewonline', $data['user_display_viewonline']);
		$updateStmt->addParameter('int',	'phpbb_user_announcement_date_display', $data['user_announcement_date_display']);
		$updateStmt->addParameter('int',	'phpbb_user_announcement_display', $data['user_announcement_display']);
		$updateStmt->addParameter('int',	'phpbb_user_announcement_display_forum', $data['user_announcement_display_forum']);
		$updateStmt->addParameter('int',	'phpbb_user_announcement_split', $data['user_announcement_split']);
		$updateStmt->addParameter('int',	'phpbb_user_announcement_forum', $data['user_announcement_forum']);
		$updateStmt->addParameter('int',	'phpbb_user_split_global_announce', $data['user_split_global_announce']);
		$updateStmt->addParameter('int',	'phpbb_user_split_announce', $data['user_split_announce']);
		$updateStmt->addParameter('int',	'phpbb_user_split_sticky', $data['user_split_sticky']);
		$updateStmt->addParameter('int',	'phpbb_user_split_news', $data['user_split_news']);
		$updateStmt->addParameter('int',	'phpbb_user_split_topic_split', $data['user_split_topic_split']);
		$updateStmt->addParameter('int',	'phpbb_user_calendar_display_open', $data['user_calendar_display_open']);
		$updateStmt->addParameter('int',	'phpbb_user_calendar_header_cells', $data['user_calendar_header_cells']);
		$updateStmt->addParameter('int',	'phpbb_user_calendar_week_start', $data['user_calendar_week_start']);
		$updateStmt->addParameter('int',	'phpbb_user_calendar_birthday', $data['user_calendar_birthday']);
		$updateStmt->addParameter('int',	'phpbb_user_calendar_forum', $data['user_calendar_forum']);
		$updateStmt->addParameter('int',	'phpbb_user_absence', $data['user_absence']);
		$updateStmt->addParameter('int',	'phpbb_user_use_ajax_preview', $data['user_use_ajax_preview']);
		$updateStmt->addParameter('int',	'phpbb_user_use_ajax_edit', $data['user_use_ajax_edit']);
		$updateStmt->addParameter('int',	'phpbb_user_calendar_nb_row', $data['user_calendar_nb_row']);
		$updateStmt->addParameter('int',	'phpbb_user_level', $data['user_level']);
		$updateStmt->addParameter('int',	'phpbb_user_style', $data['user_style']);
		$updateStmt->addParameter('int',	'phpbb_user_avatar_type', $data['user_avatar_type']);
		$updateStmt->addParameter('int',	'phpbb_user_gender', $data['user_gender']);
		$updateStmt->addParameter('string',	'phpbb_user_sig_bbcode_uid', $data['user_sig_bbcode_uid']);
		$updateStmt->addParameter('string',	'phpbb_user_avatar', $data['user_avatar']);
		$updateStmt->addParameter('string',	'phpbb_user_website', $data['user_website']);
		$updateStmt->addParameter('string',	'phpbb_user_from', $data['user_from']);
		$updateStmt->addParameter('string',	'phpbb_user_occ', $data['user_occ']);
		$updateStmt->addParameter('string',	'phpbb_user_dateformat', $data['user_dateformat']);
		$updateStmt->addParameter('string',	'phpbb_user_from_flag', $data['user_from_flag']);
		$updateStmt->addParameter('string',	'phpbb_user_lang', $data['user_lang']);
		$updateStmt->addParameter('string',	'phpbb_user_aim', $data['user_aim']);
		$updateStmt->addParameter('string',	'phpbb_user_yim', $data['user_yim']);
		$updateStmt->addParameter('string',	'phpbb_user_interests', $data['user_interests']);
		$updateStmt->addParameter('string',	'phpbb_user_actkey', $data['user_actkey']);
		$updateStmt->addParameter('string',	'phpbb_user_newpasswd', $data['user_newpasswd']);
		$updateStmt->addParameter('string',	'phpbb_user_block_by', $data['user_block_by']);

		$updateStmt->execute();
		$this->shiftPhpBBUserID($phpbbUserID, $lsUserID, FALSE);
		return TRUE;
	}


	private function transferAllLansuiteUserToPhpbb() {
		global $db;
		$this->getPhpbbConstants();

		$db->qry('INSERT INTO ' . USERS_TABLE . ' (`user_id`, `user_active`, `username`, `user_password`, `user_session_time`, `user_session_page`, 
			`user_session_topic`, `user_lastvisit`, `user_regdate`, `user_level`, `user_posts`, `user_timezone`, `user_style`, `user_lang`, 
			`user_dateformat`, `user_new_privmsg`, `user_unread_privmsg`, `user_last_privmsg`, `user_login_tries`, `user_last_login_try`, `user_emailtime`, 
			`user_viewemail`, `user_attachsig`, `user_setbm`, `user_allowhtml`, `user_allowbbcode`, `user_allowsmile`, `user_allowavatar`, `user_allow_pm`, 
			`user_allow_viewonline`, `user_notify`, `user_notify_pm`, `user_popup_pm`, `user_rank`, `user_avatar`, `user_avatar_type`, `user_email`, 
			`user_icq`, `user_website`, `user_from`, `user_from_flag`, `user_sig`, `user_sig_bbcode_uid`, `user_aim`, `user_yim`, `user_msnm`, `user_occ`, 
			`user_interests`, `user_actkey`, `user_newpasswd`, `ct_logintry`, `ct_unsucclogin`, `ct_pwreset`, `ct_mailcount`, `ct_postcount`, `ct_posttime`, 
			`ct_searchcount`, `ct_searchtime`, `user_sub_forum`, `user_split_cat`, `user_last_topic_title`, `user_sub_level_links`, 
			`user_display_viewonline`, `user_announcement_date_display`, `user_announcement_display`, `user_announcement_display_forum`, 
			`user_announcement_split`, `user_announcement_forum`, `user_split_global_announce`, `user_split_announce`, `user_split_sticky`, `user_split_news`, 
			`user_split_topic_split`, `user_birthday`, `user_next_birthday_greeting`, `user_gender`, `user_color_group`, `user_lastlogon`, `user_totaltime`, 
			`user_totallogon`, `user_totalpages`, `user_calendar_display_open`, `user_calendar_header_cells`, `user_calendar_week_start`, 
			`user_calendar_nb_row`, `user_calendar_birthday`, `user_calendar_forum`, `user_warnings`, `user_passwd_change`, `user_badlogin`, 
			`user_blocktime`, `user_block_by`, `user_absence`, `user_absence_mode`, `user_absence_text`, `user_use_ajax_preview`, `user_use_ajax_edit`)
			SELECT  `userid`, `phpbb_user_active`, `username`, `password`, `phpbb_user_session_time`, `phpbb_user_session_page`, `phpbb_user_session_topic`, 
			`phpbb_user_lastvisit`, `phpbb_user_regdate`, `phpbb_user_level`, `phpbb_user_posts`, `phpbb_user_timezone`, `phpbb_user_style`, `phpbb_user_lang`, 
			`phpbb_user_dateformat`, `phpbb_user_new_privmsg`, `phpbb_user_unread_privmsg`, `phpbb_user_last_privmsg`, `phpbb_user_login_tries`, 
			`phpbb_user_last_login_try`, `phpbb_user_emailtime`, `phpbb_user_viewemail`, `phpbb_user_attachsig`, `phpbb_user_setbm`, `phpbb_user_allowhtml`, 
			`phpbb_user_allowbbcode`, `phpbb_user_allowsmile`, `phpbb_user_allowavatar`, `phpbb_user_allow_pm`, `phpbb_user_allow_viewonline`, 
			`phpbb_user_notify`, `phpbb_user_notify_pm`, `phpbb_user_popup_pm`, `phpbb_user_rank`, `phpbb_user_avatar`, `phpbb_user_avatar_type`, `email`, 
			`icq`, `phpbb_user_website`, `phpbb_user_from`, `phpbb_user_from_flag`, `phpbb_user_sig`, `phpbb_user_sig_bbcode_uid`, `phpbb_user_aim`, 
			`phpbb_user_yim`, `msnm`, `phpbb_user_occ`, `phpbb_user_interests`, `phpbb_user_actkey`, `phpbb_user_newpasswd`, `phpbb_ct_logintry`, 
			`phpbb_ct_unsucclogin`, `phpbb_ct_pwreset`, `phpbb_ct_mailcount`, `phpbb_ct_postcount`, `phpbb_ct_posttime`, `phpbb_ct_searchcount`, 
			`phpbb_ct_searchtime`, `phpbb_user_sub_forum`, `phpbb_user_split_cat`, `phpbb_user_last_topic_title`, `phpbb_user_sub_level_links`, 
			`phpbb_user_display_viewonline`, `phpbb_user_announcement_date_display`, `phpbb_user_announcement_display`, 
			`phpbb_user_announcement_display_forum`, `phpbb_user_announcement_split`, `phpbb_user_announcement_forum`, `phpbb_user_split_global_announce`, 
			`phpbb_user_split_announce`, `phpbb_user_split_sticky`, `phpbb_user_split_news`, `phpbb_user_split_topic_split`, `phpbb_user_birthday`, 
			`phpbb_user_next_birthday_greeting`, `phpbb_user_gender`, `phpbb_user_color_group`, `phpbb_user_lastlogon`, `phpbb_user_totaltime`, 
			`phpbb_user_totallogon`, `phpbb_user_totalpages`, `phpbb_user_calendar_display_open`, `phpbb_user_calendar_header_cells`, 
			`phpbb_user_calendar_week_start`, `phpbb_user_calendar_nb_row`, `phpbb_user_calendar_birthday`, `phpbb_user_calendar_forum`, 
			`phpbb_user_warnings`, `phpbb_user_passwd_change`, `phpbb_user_badlogin`, `phpbb_user_blocktime`, `phpbb_user_block_by`, `phpbb_user_absence`, 
			`phpbb_user_absence_mode`, `phpbb_user_absence_text`, `phpbb_user_use_ajax_preview`, `phpbb_user_use_ajax_edit` FROM `%prefix%user`');
	}


	protected function createPhpbbUserTable() {
		global $db;
		$this->getPhpbbConstants();

		$db->qry('CREATE TABLE ' . USERS_TABLE . ' (
			  `user_id` mediumint(8) NOT NULL DEFAULT \'0\',
			  `user_active` tinyint(1) DEFAULT \'1\',
			  `username` varchar(25) COLLATE latin1_general_ci NOT NULL DEFAULT \'\',
			  `user_password` varchar(32) COLLATE latin1_general_ci NOT NULL DEFAULT \'\',
			  `user_session_time` int(11) NOT NULL DEFAULT \'0\',
			  `user_session_page` smallint(5) NOT NULL DEFAULT \'0\',
			  `user_session_topic` int(11) NOT NULL,
			  `user_lastvisit` int(11) NOT NULL DEFAULT \'0\',
			  `user_regdate` int(11) NOT NULL DEFAULT \'0\',
			  `user_level` tinyint(4) DEFAULT \'0\',
			  `user_posts` mediumint(8) unsigned NOT NULL DEFAULT \'0\',
			  `user_timezone` decimal(5,2) NOT NULL DEFAULT \'0.00\',
			  `user_style` tinyint(4) DEFAULT NULL,
			  `user_lang` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
			  `user_dateformat` varchar(14) COLLATE latin1_general_ci NOT NULL DEFAULT \'d M Y H:i\',
			  `user_new_privmsg` smallint(5) unsigned NOT NULL DEFAULT \'0\',
			  `user_unread_privmsg` smallint(5) unsigned NOT NULL DEFAULT \'0\',
			  `user_last_privmsg` int(11) NOT NULL DEFAULT \'0\',
			  `user_login_tries` smallint(5) unsigned NOT NULL DEFAULT \'0\',
			  `user_last_login_try` int(11) NOT NULL DEFAULT \'0\',
			  `user_emailtime` int(11) DEFAULT NULL,
			  `user_viewemail` tinyint(1) DEFAULT NULL,
			  `user_attachsig` tinyint(1) DEFAULT NULL,
			  `user_setbm` tinyint(1) NOT NULL DEFAULT \'0\',
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
			  `user_avatar` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
			  `user_avatar_type` tinyint(4) NOT NULL DEFAULT \'0\',
			  `user_email` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
			  `user_icq` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
			  `user_website` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
			  `user_from` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
			  `user_from_flag` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
			  `user_sig` text COLLATE latin1_general_ci,
			  `user_sig_bbcode_uid` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
			  `user_aim` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
			  `user_yim` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
			  `user_msnm` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
			  `user_occ` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
			  `user_interests` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
			  `user_actkey` varchar(32) COLLATE latin1_general_ci DEFAULT NULL,
			  `user_newpasswd` varchar(32) COLLATE latin1_general_ci DEFAULT NULL,
			  `ct_logintry` int(2) DEFAULT \'0\',
			  `ct_unsucclogin` int(10) DEFAULT NULL,
			  `ct_pwreset` int(2) NOT NULL,
			  `ct_mailcount` int(10) NOT NULL,
			  `ct_postcount` int(10) NOT NULL,
			  `ct_posttime` int(10) NOT NULL,
			  `ct_searchcount` int(10) NOT NULL,
			  `ct_searchtime` int(10) NOT NULL,
			  `user_sub_forum` tinyint(1) NOT NULL DEFAULT \'1\',
			  `user_split_cat` tinyint(1) NOT NULL DEFAULT \'1\',
			  `user_last_topic_title` tinyint(1) NOT NULL DEFAULT \'1\',
			  `user_sub_level_links` tinyint(1) NOT NULL DEFAULT \'2\',
			  `user_display_viewonline` tinyint(1) NOT NULL DEFAULT \'2\',
			  `user_announcement_date_display` tinyint(1) NOT NULL DEFAULT \'1\',
			  `user_announcement_display` tinyint(1) NOT NULL DEFAULT \'1\',
			  `user_announcement_display_forum` tinyint(1) NOT NULL DEFAULT \'1\',
			  `user_announcement_split` tinyint(1) NOT NULL DEFAULT \'1\',
			  `user_announcement_forum` tinyint(1) NOT NULL DEFAULT \'1\',
			  `user_split_global_announce` tinyint(1) NOT NULL DEFAULT \'1\',
			  `user_split_announce` tinyint(1) NOT NULL DEFAULT \'1\',
			  `user_split_sticky` tinyint(1) NOT NULL DEFAULT \'1\',
			  `user_split_news` tinyint(1) NOT NULL DEFAULT \'1\',
			  `user_split_topic_split` tinyint(1) NOT NULL DEFAULT \'0\',
			  `user_birthday` int(11) NOT NULL DEFAULT \'999999\',
			  `user_next_birthday_greeting` int(11) NOT NULL DEFAULT \'0\',
			  `user_gender` tinyint(4) NOT NULL DEFAULT \'0\',
			  `user_color_group` mediumint(8) unsigned NOT NULL,
			  `user_lastlogon` int(11) NOT NULL DEFAULT \'0\',
			  `user_totaltime` int(11) DEFAULT \'0\',
			  `user_totallogon` int(11) DEFAULT \'0\',
			  `user_totalpages` int(11) DEFAULT \'0\',
			  `user_calendar_display_open` tinyint(1) NOT NULL DEFAULT \'0\',
			  `user_calendar_header_cells` tinyint(1) NOT NULL DEFAULT \'7\',
			  `user_calendar_week_start` tinyint(1) NOT NULL DEFAULT \'1\',
			  `user_calendar_nb_row` tinyint(2) unsigned NOT NULL DEFAULT \'5\',
			  `user_calendar_birthday` tinyint(1) NOT NULL DEFAULT \'1\',
			  `user_calendar_forum` tinyint(1) NOT NULL DEFAULT \'1\',
			  `user_warnings` smallint(5) DEFAULT \'0\',
			  `user_passwd_change` int(11) NOT NULL,
			  `user_badlogin` smallint(5) NOT NULL,
			  `user_blocktime` int(11) NOT NULL,
			  `user_block_by` varchar(8) COLLATE latin1_general_ci DEFAULT NULL,
			  `user_absence` tinyint(1) NOT NULL DEFAULT \'0\',
			  `user_absence_mode` mediumint(8) NOT NULL DEFAULT \'0\',
			  `user_absence_text` text COLLATE latin1_general_ci NOT NULL,
			  `user_use_ajax_preview` tinyint(1) DEFAULT \'1\',
			  `user_use_ajax_edit` tinyint(1) DEFAULT \'1\',
			  PRIMARY KEY (`user_id`),
			  KEY `user_session_time` (`user_session_time`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
		');
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
				`phpbb_user_session_topic` AS `user_session_topic`,
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
				`phpbb_user_setbm` AS `user_setbm`,
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
				`phpbb_user_from_flag` AS `user_from_flag`,
				`phpbb_user_sig` AS `user_sig`,
				`phpbb_user_sig_bbcode_uid` AS `user_sig_bbcode_uid`,
				`phpbb_user_aim` AS `user_aim`,
				`phpbb_user_yim` AS `user_yim`,
				`msn` AS `user_msnm`,
				`phpbb_user_occ` AS `user_occ`,
				`phpbb_user_interests` AS `user_interests`,
				`phpbb_user_actkey` AS `user_actkey`,
				`phpbb_user_newpasswd` AS `user_newpasswd`,
				`phpbb_ct_logintry` AS `ct_logintry`,
				`phpbb_ct_unsucclogin` AS `ct_unsucclogin`,
				`phpbb_ct_pwreset` AS `ct_pwreset`,
				`phpbb_ct_mailcount` AS `ct_mailcount`,
				`phpbb_ct_postcount` AS `ct_postcount`,
				`phpbb_ct_posttime` AS `ct_posttime`,
				`phpbb_ct_searchcount` AS `ct_searchcount`,
				`phpbb_ct_searchtime` AS `ct_searchtime`,
				`phpbb_user_sub_forum` AS `user_sub_forum`,
				`phpbb_user_split_cat` AS `user_split_cat`,
				`phpbb_user_last_topic_title` AS `user_last_topic_title`,
				`phpbb_user_sub_level_links` AS `user_sub_level_links`,
				`phpbb_user_display_viewonline` AS `user_display_viewonline`,
				`phpbb_user_announcement_date_display` AS `user_announcement_date_display`,
				`phpbb_user_announcement_display` AS `user_announcement_display`,
				`phpbb_user_announcement_display_forum` AS `user_announcement_display_forum`,
				`phpbb_user_announcement_split` AS `user_announcement_split`,
				`phpbb_user_announcement_forum` AS `user_announcement_forum`,
				`phpbb_user_split_global_announce` AS `user_split_global_announce`,
				`phpbb_user_split_announce` AS `user_split_announce`,
				`phpbb_user_split_sticky` AS `user_split_sticky`,
				`phpbb_user_split_news` AS `user_split_news`,
				`phpbb_user_split_topic_split` AS `user_split_topic_split`,
				`phpbb_user_birthday` AS `user_birthday`,
				`phpbb_user_next_birthday_greeting` AS `user_next_birthday_greeting`,
				`phpbb_user_gender` AS `user_gender`,
				`phpbb_user_color_group` AS `user_color_group`,
				`phpbb_user_lastlogon` AS `user_lastlogon`,
				`phpbb_user_totaltime` AS `user_totaltime`,
				`phpbb_user_totallogon` AS `user_totallogon`,
				`phpbb_user_totalpages` AS `user_totalpages`,
				`phpbb_user_calendar_display_open` AS `user_calendar_display_open`,
				`phpbb_user_calendar_header_cells` AS `user_calendar_header_cells`,
				`phpbb_user_calendar_week_start` AS `user_calendar_week_start`,
				`phpbb_user_calendar_nb_row` AS `user_calendar_nb_row`,
				`phpbb_user_calendar_birthday` AS `user_calendar_birthday`,
				`phpbb_user_calendar_forum` AS `user_calendar_forum`,
				`phpbb_user_warnings` AS `user_warnings`,
				`phpbb_user_passwd_change` AS `user_passwd_change`,
				`phpbb_user_badlogin` AS `user_badlogin`,
				`phpbb_user_blocktime` AS `user_blocktime`,
				`phpbb_user_block_by` AS `user_block_by`,
				`phpbb_user_absence` AS `user_absence`,
				`phpbb_user_absence_mode` AS `user_absence_mode`,
				`phpbb_user_absence_text` AS `user_absence_text`,
				`phpbb_user_use_ajax_preview` AS `user_use_ajax_preview`,
				`phpbb_user_use_ajax_edit` AS `user_use_ajax_edit`
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
				ADD `phpbb_user_session_topic` int(11) NOT NULL,
				ADD `phpbb_user_lastvisit` int(11) NOT NULL DEFAULT \'0\',
				ADD `phpbb_user_regdate` int(11) NOT NULL DEFAULT \'0\',
				ADD `phpbb_user_level` tinyint(4) DEFAULT \'0\',
				ADD `phpbb_user_posts` mediumint(8) unsigned NOT NULL DEFAULT \'0\',
				ADD `phpbb_user_timezone` decimal(5,2) NOT NULL DEFAULT \'0.00\',
				ADD `phpbb_user_style` tinyint(4) DEFAULT NULL,
				ADD `phpbb_user_lang` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
				ADD `phpbb_user_dateformat` varchar(14) COLLATE latin1_general_ci NOT NULL DEFAULT \'d M Y H:i\',
				ADD `phpbb_user_new_privmsg` smallint(5) unsigned NOT NULL DEFAULT \'0\',
				ADD `phpbb_user_unread_privmsg` smallint(5) unsigned NOT NULL DEFAULT \'0\',
				ADD `phpbb_user_last_privmsg` int(11) NOT NULL DEFAULT \'0\',
				ADD `phpbb_user_login_tries` smallint(5) unsigned NOT NULL DEFAULT \'0\',
				ADD `phpbb_user_last_login_try` int(11) NOT NULL DEFAULT \'0\',
				ADD `phpbb_user_emailtime` int(11) DEFAULT NULL,
				ADD `phpbb_user_viewemail` tinyint(1) DEFAULT NULL,
				ADD `phpbb_user_attachsig` tinyint(1) DEFAULT NULL,
				ADD `phpbb_user_setbm` tinyint(1) NOT NULL DEFAULT \'0\',
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
				ADD `phpbb_user_avatar` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
				ADD `phpbb_user_avatar_type` tinyint(4) NOT NULL DEFAULT \'0\',
				ADD `phpbb_user_website` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
				ADD `phpbb_user_from` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
				ADD `phpbb_user_from_flag` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
				ADD `phpbb_user_sig` text COLLATE latin1_general_ci,
				ADD `phpbb_user_sig_bbcode_uid` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
				ADD `phpbb_user_aim` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
				ADD `phpbb_user_yim` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
				ADD `phpbb_user_occ` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
				ADD `phpbb_user_interests` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
				ADD `phpbb_user_actkey` varchar(32) COLLATE latin1_general_ci DEFAULT NULL,
				ADD `phpbb_user_newpasswd` varchar(32) COLLATE latin1_general_ci DEFAULT NULL,
				ADD `phpbb_ct_logintry` int(2) DEFAULT \'0\',
				ADD `phpbb_ct_unsucclogin` int(10) DEFAULT NULL,
				ADD `phpbb_ct_pwreset` int(2) NOT NULL,
				ADD `phpbb_ct_mailcount` int(10) NOT NULL,
				ADD `phpbb_ct_postcount` int(10) NOT NULL,
				ADD `phpbb_ct_posttime` int(10) NOT NULL,
				ADD `phpbb_ct_searchcount` int(10) NOT NULL,
				ADD `phpbb_ct_searchtime` int(10) NOT NULL,
				ADD `phpbb_user_sub_forum` tinyint(1) NOT NULL DEFAULT \'1\',
				ADD `phpbb_user_split_cat` tinyint(1) NOT NULL DEFAULT \'1\',
				ADD `phpbb_user_last_topic_title` tinyint(1) NOT NULL DEFAULT \'1\',
				ADD `phpbb_user_sub_level_links` tinyint(1) NOT NULL DEFAULT \'2\',
				ADD `phpbb_user_display_viewonline` tinyint(1) NOT NULL DEFAULT \'2\',
				ADD `phpbb_user_announcement_date_display` tinyint(1) NOT NULL DEFAULT \'1\',
				ADD `phpbb_user_announcement_display` tinyint(1) NOT NULL DEFAULT \'1\',
				ADD `phpbb_user_announcement_display_forum` tinyint(1) NOT NULL DEFAULT \'1\',
				ADD `phpbb_user_announcement_split` tinyint(1) NOT NULL DEFAULT \'1\',
				ADD `phpbb_user_announcement_forum` tinyint(1) NOT NULL DEFAULT \'1\',
				ADD `phpbb_user_split_global_announce` tinyint(1) NOT NULL DEFAULT \'1\',
				ADD `phpbb_user_split_announce` tinyint(1) NOT NULL DEFAULT \'1\',
				ADD `phpbb_user_split_sticky` tinyint(1) NOT NULL DEFAULT \'1\',
				ADD `phpbb_user_split_news` tinyint(1) NOT NULL DEFAULT \'1\',
				ADD `phpbb_user_split_topic_split` tinyint(1) NOT NULL DEFAULT \'0\',
				ADD `phpbb_user_birthday` int(11) NOT NULL DEFAULT \'999999\',
				ADD `phpbb_user_next_birthday_greeting` int(11) NOT NULL DEFAULT \'0\',
				ADD `phpbb_user_gender` tinyint(4) NOT NULL DEFAULT \'0\',
				ADD `phpbb_user_color_group` mediumint(8) unsigned NOT NULL,
				ADD `phpbb_user_lastlogon` int(11) NOT NULL DEFAULT \'0\',
				ADD `phpbb_user_totaltime` int(11) DEFAULT \'0\',
				ADD `phpbb_user_totallogon` int(11) DEFAULT \'0\',
				ADD `phpbb_user_totalpages` int(11) DEFAULT \'0\',
				ADD `phpbb_user_calendar_display_open` tinyint(1) NOT NULL DEFAULT \'0\',
				ADD `phpbb_user_calendar_header_cells` tinyint(1) NOT NULL DEFAULT \'7\',
				ADD `phpbb_user_calendar_week_start` tinyint(1) NOT NULL DEFAULT \'1\',
				ADD `phpbb_user_calendar_nb_row` tinyint(2) unsigned NOT NULL DEFAULT \'5\',
				ADD `phpbb_user_calendar_birthday` tinyint(1) NOT NULL DEFAULT \'1\',
				ADD `phpbb_user_calendar_forum` tinyint(1) NOT NULL DEFAULT \'1\',
				ADD `phpbb_user_warnings` smallint(5) DEFAULT \'0\',
				ADD `phpbb_user_passwd_change` int(11) NOT NULL,
				ADD `phpbb_user_badlogin` smallint(5) NOT NULL,
				ADD `phpbb_user_blocktime` int(11) NOT NULL,
				ADD `phpbb_user_block_by` varchar(8) COLLATE latin1_general_ci DEFAULT NULL,
				ADD `phpbb_user_absence` tinyint(1) NOT NULL DEFAULT \'0\',
				ADD `phpbb_user_absence_mode` mediumint(8) NOT NULL DEFAULT \'0\',
				ADD `phpbb_user_absence_text` text COLLATE latin1_general_ci NOT NULL,
				ADD `phpbb_user_use_ajax_preview` tinyint(1) DEFAULT \'1\',
				ADD `phpbb_user_use_ajax_edit` tinyint(1) DEFAULT \'1\',
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
				DROP `phpbb_user_session_topic`,
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
				DROP `phpbb_user_setbm`,
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
				DROP `phpbb_user_from_flag`,
				DROP `phpbb_user_sig`,
				DROP `phpbb_user_sig_bbcode_uid`,
				DROP `phpbb_user_aim`,
				DROP `phpbb_user_yim`,
				DROP `phpbb_user_occ`,
				DROP `phpbb_user_interests`,
				DROP `phpbb_user_actkey`,
				DROP `phpbb_user_newpasswd`,
				DROP `phpbb_ct_logintry`,
				DROP `phpbb_ct_unsucclogin`,
				DROP `phpbb_ct_pwreset`,
				DROP `phpbb_ct_mailcount`,
				DROP `phpbb_ct_postcount`,
				DROP `phpbb_ct_posttime`,
				DROP `phpbb_ct_searchcount`,
				DROP `phpbb_ct_searchtime`,
				DROP `phpbb_user_sub_forum`,
				DROP `phpbb_user_split_cat`,
				DROP `phpbb_user_last_topic_title`,
				DROP `phpbb_user_sub_level_links`,
				DROP `phpbb_user_display_viewonline`,
				DROP `phpbb_user_announcement_date_display`,
				DROP `phpbb_user_announcement_display`,
				DROP `phpbb_user_announcement_display_forum`,
				DROP `phpbb_user_announcement_split`,
				DROP `phpbb_user_announcement_forum`,
				DROP `phpbb_user_split_global_announce`,
				DROP `phpbb_user_split_announce`,
				DROP `phpbb_user_split_sticky`,
				DROP `phpbb_user_split_news`,
				DROP `phpbb_user_split_topic_split`,
				DROP `phpbb_user_birthday`,
				DROP `phpbb_user_next_birthday_greeting`,
				DROP `phpbb_user_gender`,
				DROP `phpbb_user_color_group`,
				DROP `phpbb_user_lastlogon`,
				DROP `phpbb_user_totaltime`,
				DROP `phpbb_user_totallogon`,
				DROP `phpbb_user_totalpages`,
				DROP `phpbb_user_calendar_display_open`,
				DROP `phpbb_user_calendar_header_cells`,
				DROP `phpbb_user_calendar_week_start`,
				DROP `phpbb_user_calendar_nb_row`,
				DROP `phpbb_user_calendar_birthday`,
				DROP `phpbb_user_calendar_forum`,
				DROP `phpbb_user_warnings`,
				DROP `phpbb_user_passwd_change`,
				DROP `phpbb_user_badlogin`,
				DROP `phpbb_user_blocktime`,
				DROP `phpbb_user_block_by`,
				DROP `phpbb_user_absence`,
				DROP `phpbb_user_absence_mode`,
				DROP `phpbb_user_absence_text`,
				DROP `phpbb_user_use_ajax_preview`,
				DROP `phpbb_user_use_ajax_edit`,
				DROP KEY `username_unique`'
				);
	}
}

//
// Please make a history at the end of file of your changes !!
//

/* HISTORY
 * 06. 2. 2009 : Mostly rewriten for phpbb 2 Plus (interface)
 */
?>