<?php

include_once('class_board2.php');

class Board2install
{
	/**
	 * Checks if ther are lansuite Accouts that have reserved phpBB usernames.
	 * @return unknown_type
	 */
	public static function checkForSpecialUser() {
		$versionspecific = Board2::getVersionspecificObject();
		return $versionspecific->checkForSpecialUser();
	}
	/**
	 * Integrates phpBB in lansuite by calling the phpbb version specific classes.
	 * @return, returns true, if the new id of the anonymous user was set correct
	 */
	public static function integrateFreshPhpBB() {
		global $db, $auth;
			
		$specificversion = Board2::getVersionspecificObject();
		if ($specificversion == null) throw new Exception(tr('Die ausgew&auml;hlte phpBB Version wird nicht unterst&uuml;t.'));
		
		$specificversion->isIntegrationPossible();
		
		// Adds the necessary columns to the lansuite user table.
		$specificversion->preparePhpbbIntegration();
		
		// Makes all admins of lansuite admin of phpBB
		$query_id = $db->qry('SELECT userid FROM %prefix%user WHERE `type` >= 2');
		while ($rowdata = $db->fetch_array($query_id)) {
			$specificversion->setPhpbbAdmin($rowdata[0]);
			$specificversion->setActive($rowdata[0]);
		}
		
		// Moves the phpbb userid range above the lansuite id range to rule out competing lansuite and phpbb id's
		$delta = Board2install::getMaxUserID()+10;

		$specificversion->shiftAllPhpBBUser($delta);
		$specificversion->transferPhpBBUserInfoToLansuite($delta + 2, $auth['userid']);	// The default allocated user of phpbb
		 	
		// Sets the new Anonymous userid in phpbb
		$anonymousID = $specificversion->getAnonymousUserID() + $delta;
		// Transfer the Anonymous and the Bot users to lansuite.
		$specificversion->transferPhpbbUserToLansuite($anonymousID);
		$anonymusSetCorrectly = $specificversion->setAnonymousUserID();
		
		$bots_array = $specificversion->getAllPhpbbBotUser();
		foreach ($bots_array as $phpbbId) $specificversion->transferPhpbbUserToLansuite($phpbbId);
		
		$specificversion->integratePhpBB();
		
		//Write the new db.xml of the usrmgr with the changes on the user table. 
		Board2install::rewriteUserDbXML();
			
		return $anonymusSetCorrectly;
	}

	/*
	 * Integrates phpBB in lansuite without installing phpBB
	 */
	public static function integrateExistingPhpBB($phpintegration) {
		global $db;

		$db->query("START TRANSACTION");
			
		try {
			$specificversion = Board2::getVersionspecificObject();
			$specificversion->isIntegrationPossible();
			$specificversion->preparePhpbbIntegration(); // Adds the necessary columns to the lansuite user table.

			// Makes all admins of lansuite admin of phpBB
			$query_id = $db->qry('SELECT userid FROM %prefix%user WHERE `type` >= 2');
			while ($rowdata = $db->fetch_array($query_id)) {
				$specificversion->setPhpbbAdmin($rowdata[0]);
				$specificversion->setActive($rowdata[0]);
			}

			// Save max id's and shift the phpbb id's above the lansuite id's
			$delta = Board2install::getMaxUserID() +10;
			$specificversion->shiftAllPhpBbUser($delta);

			// Start user transfer
			$processedLsUserIDs[] = array();

			$userWithCommonEmail = $specificversion->getPhpBBUserWithEqualLsAccount_Email();
			foreach ($userWithCommonEmail as $user) {
				$specificversion->transferPhpBBUserInfoToLansuite($user->getPhpbbUserID(), $user->getLansuiteUserID());
				$processedLsUserIDs[] .= $user->getPhpbbUserID();
			}

			$userWithCommonUsername = $specificversion->getPhpBBUserWithEqualLsAccount_Username();
			foreach ($userWithCommonUsername as $user) {
				$specificversion->transferPhpBBUserInfoToLansuite($user->getPhpbbUserID(), $user->getLansuiteUserID());
				$processedLsUserIDs[] .= $user->getPhpbbUserID();
			}

			foreach ($phpintegration as $phpBBID => $lsUserID) {
				//Tests if 1 lansuiteUser has more then one Relation with an phpBB User
				foreach ($processedLsUserIDs as $id => $userID)
				{
					if ($userID == $lsUserID && $userID != "new")
					{
						$db->qry("ROLLBACK");
						return 0;
					}
				}
				$processedLsUserIDs[] .= $lsUserID;

				if ($lsUserID == "new") {	//A new record with the phpBB User has to be created.
					$specificversion->transferPhpbbUserToLansuite($phpBBID + $delta);
				} else {					//The userid from the lansuite user and from the phpBB user have to be set the same.
					$specificversion->transferPhpBBUserInfoToLansuite($phpBBID + $delta, $lsUserID);
				}
			}

			// Sets the new Anonymous userid in phpbb
			$anonymusSetCorrectly = $specificversion->setAnonymousUserID();

			//Drops the Table and creates the view.
			$specificversion->integratePhpbb();
			
			//Write the new db.xml of the usrmgr with the changes on the user table. 
			Board2install::rewriteUserDbXML();
			
			//Saves all changes on the Databse if everything no errors were detected.
			$db->qry("COMMIT");
			return 1;
		}
		catch (Exception $exc)
		{
			$db->qry("ROLLBACK");
			throw $exc;
		}
	}

	/*
	 * Returns the highest UserID of the lansuiteDB
	 */
	public static function getHighestLansuiteUserID() {
		global $db;
			
		$data = ($db->qry_first('SELECT max(userid) FROM %prefix%user'));
		return $data[0];
	}
		
	/*
	 * Returns the highest UserID of the lansuite DB and the phpBB DB
	 */
	public static function getHighestLSphpBBUserID() {
		global $db;
		$fileintegrat = new FileIntegrationPhpBB();

		$phpBBMaxID =  $fileintegrat->getHighestphpBBUserID();
		$lsMaxID = $this->getHighestLansuiteUserID();

		if ($lsMaxID < $phpBBMaxID)	$curMaxID = $phpBBMaxID;	//biggest userid of both, lansuite and phpBB
		else $curMaxID = $lsMaxID;

		return $curMaxID;
	}

	/*
	 * Returns all Lansuite Users in this Style for the DropDownField:
	 * @return UserIntegrationItem array.
	 */
	public static function getLansuiteUser() {
		global $db;

		$query_id = $db->qry('select userid, username, email from %prefix%user ORDER BY username');
		include_once('modules\board2\class_userintegrationitem.php');
		$user_array = array();
		while($rowdata = $db->fetch_array($query_id)) {
			array_push($user_array, new UserIntegrationItem('', '', '', $rowdata[0], $rowdata[1], $rowdata[2]));
		}
		$db->free_result($query_id);
		$GLOBALS['lansuiteUser'] = $user_array;
		return $user_array;
	}

	/**
	 * Stores settings in the config file.
	 * @param $variable, the name of the variable
	 * @param $value, the value of the variable
	 * @return unknown_type, nothing
	 */
	public static function setConfigVariable($variable, $value) {
		global $config;
			
		include_once('modules/install/class_install.php');
		$install = new Install();
		$config['board2'][$variable] = $value;
		$install->WriteConfig();
	}

	/*
	 * Returns all PhpBB Users in this Style for the DropDownField:
	 * array of class_userlistitem.php
	 * $phpbbUser[{userid}] = userlistitem
	 * userlistitem->username = {username} - {email}
	 */
	public static function getPhpBBUser($error = null) {
		if ($error == null)
		{
			$versionspecific = Board2::getVersionspecificObject();
			$user = $versionspecific->getAllPhpBBUser();
			$GLOBALS['PhpBBUser'] = $user;
			return $user;
		} else {
			return $GLOBALS['PhpBbUser'];
		}
	}

	/**
	 * Returns all phpbb user that share a username but do not share an email with an lansuite user
	 * @return mastersearch
	 */
	public static function getPhpBBUserWithEqualLsAccount_Username_MasterSearch() {
		$versionspecific = Board2::getVersionspecificObject();
		return $versionspecific->getPhpBBUserWithEqualLsAccount_Username_MasterSearch();
	}

	/**
	 * Returns all phpbb user that share a email adress.
	 * @return mastersearch
	 */
	public static function getPhpBBUserWithEqualLsAccount_Email_MasterSearch() {
		$versionspecific = Board2::getVersionspecificObject();
		return $versionspecific->getPhpBBUserWithEqualLsAccount_Email_MasterSearch();
	}

	/**
	 * Returns all phpbb user that share neither a username nor an email adress.
	 * @return mastersearch
	 */
	public static function getPhpBBUserWithoutEqualLsAccount() {
		$versionspecific = Board2::getVersionspecificObject();
		return $versionspecific->getPhpBBUserWithoutEqualLsAccount();
	}

	/**
	 * Returns the anslations for the phpBB installation.
	 * @return, returns the ansaltions of phpbb
	 */
	public static function getPhpbbTranslations() {
		$versionspecific = Board2::getVersionspecificObject();
		return $versionspecific->getPhpbbTranslations();
	}

	/**
	 * Gets the installation url of phpbb
	 * @return unknown_type, returns the installation url of phpbb
	 */
	public static function getPhpbbInstallationUrl() {
		$versionspecific = Board2::getVersionspecificObject();
		return $versionspecific->getPhpbbInstallationUrl();
	}

	/**
	 * Number of double users.
	 * @return, number of doulbe users.
	 */
	public static function getDoubleLansuiteUserCount() {
		global $db;
			
		$data = $db->qry_first('SELECT count(*) as \'Count\' FROM %prefix%user l WHERE 1 < (SELECT count(*) FROM %prefix%user WHERE l.username = username) ORDER BY l.username');
		return $data['Count'];
	}
	/**
	 * Gets all lansuite username that occure double.
	 * @return mastersearch
	 */
	public static function getDoubleLansuiteUser() {
		global $db, $config, $auth;
			
		include_once('modules/mastersearch2/class_mastersearch2.php');
		$ms2 = new MasterSearch2('board2');

		$ms2->query['from'] = "{$config["tables"]["user"]} AS l";
		$ms2->query['where'] = "1 < (SELECT count(*) FROM {$config["tables"]["user"]} WHERE l.username = username)";
		$ms2->query['default_order_by'] = 'l.username DESC';

		$ms2->AddResultField('Username', 'l.username');
		$ms2->AddResultField('Email', 'l.email');
		$ms2->AddResultField('Name', 'l.name + l.firstname');

		$ms2->AddIconField('details', 'index.php?mod=usrmgr&action=details&userid=', t('Details'));
		if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=usrmgr&action=change&step=1&userid=', t('Editieren'));
		if ($auth['type'] >= 2) $ms2->AddIconField('delete', 'index.php?mod=usrmgr&action=delete&step=2&userid=', t('Löschen'));

		return $ms2;
	}

	public static function setDefaultBoard2Prefix() {
		global $config;
		Board2install::setConfigVariable('prefix', $config['database']['prefix'] . 'phpbb_');
	}

	public static function setDefaultBoard2Path() {
		global $config;
		Board2install::setConfigVariable('path', $config['database']['path'] . 'ext_scripts/phpBB/');
	}

	public static function setDefaultBoard2Version() {
		global $config;
		Board2install::setConfigVariable('version', '2.0.22');
	}

	/**
	 * Sets the Option 'Enable account activation' to 'Admin' to ensure that all new user register per lansuite.
	 * @return unknown_type, nothing
	 */
	public static function setAccountAdminActivation() {
		$fileintegrant = Board2::getVersionspecificObject()->setAccountAdminActivation();
	}

	public static function finishInstallation() {
		global $auth;
			
		Board2install::setConfigVariable("configured", "1");
		Board2::loginPhpBB( $auth['userid'] );
	}

	/**
	 * Checks if the integration is possible and throws an exception if it isn't
	 * @return, nothing
	 */
	public function isIntegrationPossible(){
		$versionspecific = Board2::getVersionspecificObject();
		return $versionspecific->isIntegrationPossible();
	}

	public static function deIntegrate() {
		$versionspecific = Board2::getVersionspecificObject();
		$ret =  $versionspecific->deIntegrate();
		Board2install::setConfigVariable('configured', 0);
		
		//Write the new db.xml of the usrmgr with the changes on the user table. 
		Board2install::rewriteUserDbXML();
			
		return $ret;
	}

	/**
	 * Reieves the max userid of lansuite
	 * @return, returns an int with the hightes userid.
	 */
	private static function getMaxUserID() {
		global $db;
		$query_id = $db->qry('SELECT max(userid) FROM %prefix%user');
			
		$rowdata = $db->fetch_array($query_id);
		return $rowdata[0];
	}
	
	/**
	 * Rewrites the db.xml of the usrmgr with the bew user table.
	 * @return nothing
	 */
	private static function rewriteUserDbXML() {
		include_once('modules/install/class_export.php');
		$export = new Export();
		$export->LSTableHead();
		$export->ExportTable('user', 1, 0);
		$export->ExportTable('usersettings', 1, 0);
		$export->ExportTable('user_permissions', 1, 0);
		$export->ExportTable('user_fields', 1, 0);
		$export->ExportTable('party_usergroups', 1, 0);
		$export->SaveExport('modules/usrmgr/mod_settings/db.xml');
	}
}

//
// Please make a history at the end of file of your changes !!
//

/* HISTORY
 * 17. 2. 2006 : First adaption of the file from the sample module.
 * 19. 2. 2006 : Functionality added.
 * 14. 4. 2006 : Reimplemented and added Functionallity
 *  6. 2. 2009 : Rewrite of the class, improved the modular design.
 */
?>