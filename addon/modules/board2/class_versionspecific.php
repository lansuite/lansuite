<?php

/*
 * This class is the base class for all version specific integration classes.
 */
abstract class Versionspecific
{
	/**
	 * Checks if ther are lansuite Accouts that have reserved phpBB usernames.
	 * @return, array with usernames.
	 */
	public abstract function checkForSpecialUser();

	/**
	 * Gets an array with the phpBB translation of its installation.
	 * @return unknown_type, nothing
	 */
	public abstract function getPhpbbTranslations();

	/**
	 * Gets an int array with all bot user_id's
	 * @return, an int array containing the user_id's of the bot user
	 */
	public abstract function getAllPhpbbBotUser();

	/**
	 * Gets the installation url of phpbb
	 * @return, returns the installation url of phpbb
	 */
	public abstract function getPhpbbInstallationUrl();

	/**
	 * Prepares lansuite for the integration and alters the user table e.g. to add the necessary columns.
	 * @return nothing
	 */
	public abstract function preparePhpbbIntegration();

	/**
	 * Removes the phpBB user table and creates a view for phpBB that points to the lansuite user table.
	 * @return unknown_type, nothing
	 */
	public abstract function integratePhpbb();

	/**
	 * Creates a new user in lansuite with the data of the phpBB user.
	 * @param $phpbbUserID, The id of the user in phpBB
	 * @return Returns the id of the new user in lansuite.
	 */
	public abstract function transferPhpbbUserToLansuite($phpbbUserID);

	/**
	 * Copies the phpBB specific user info to the lansuite user table.
	 * @param $phpbbUserID, the id of the user in phpBB
	 * @param $lsUserID, the id of the user in lansuite
	 * @return unknown_type, nothing
	 */
	public abstract function transferPhpBBUserInfoToLansuite($phpbbUserID, $lsUserID);
	
	/**
	 * Adds the delta to all phpBB user id's to ensure that the lansuite and the phpBB user id's compete.
	 * @param $delta, the number about the phpBB users should be shifted.
	 * @return unknown_type, nothing
	 */
	public abstract function shiftAllPhpBBUser($delta);

	/**
	 * Changes the phpbb user_id of the specified phpBB user.
	 * @return unknown_type, nothing
	 */
	public abstract function shiftPhpBBUserID($oldPhpBBID, $newPhpBBID);

	/**
	 * Checks if the integration is possible and throws an exception if it isn't
	 * @return, nothing
	 */
	public abstract function isIntegrationPossible();

	/**
	 * Returns all phpbb user that share a username but do not share an email with an lansuite user
	 * @return array with UserIntegrationItem objects.
	 */
	public abstract function getPhpBBUserWithEqualLsAccount_Username();

	/**
	 * Returns all phpbb user that share a email adress.
	 * @return array with UserIntegrationItem objects.
	 */
	public abstract function getPhpBBUserWithEqualLsAccount_Email();

	/**
	 * Returns all phpbb user that share neither a username nor an email adress.
	 * @return array with UserIntegrationItem objects.
	 */
	public abstract function getPhpBBUserWithoutEqualLsAccount();

	/**
	 * Sets the Option 'Enable account activation' to 'Admin' to ensure that all new user register per lansuite.
	 * @return unknown_type, nothing
	 */
	public abstract function setAccountAdminActivation();

	/**
	 * Sets the user to a phpBB Administrator.
	 * @param $lsUserID, the id of the user.
	 * @return unknown_type, nothing
	 */
	public abstract function setPhpbbAdmin($lsUserID);

	/**
	 * Sets the user active in phpBB
	 * @param $lsUserID, the id of the user.
	 * @return unknown_type, nothing
	 */
	public abstract function setActive($lsUserID);

	/**
	 * Deintegrates the phpBB forum from lansuite.
	 * Removes the phpBB_users view, creates the pbpBB_users table and fills the table with ls user.
	 * @return unknown_type, nothing
	 */
	public abstract function deIntegrate();

	/**
	 * Logs the user with the phpbb user id on the phpBB board.
	 * @param $phpbbUserID
	 * @return unknown_type
	 */
	public abstract function loginPhpbb($phpbbUserID);

	/**
	 * Logoff the user with the phpbb_user_id of the phpBB board.
	 * @param $phpbbUserID
	 * @return unknown_type
	 */
	public abstract function logoutPhpbb($phpbbUserID);

	/**
	 * Gets the id of the anonymous user.
	 * @return unknown_type, returns the id of the user.
	 */
	public abstract function getAnonymousUserID();

	/**
	 * Sets the anonymous user id of phpbb
	 * @return, returns TRUE if everything went fine, otherwise false.
	 */
	public abstract function setAnonymousUserID();
}
//
// Please make a history at the end of file of your changes !!
//

/* HISTORY
 * 06. 2. 2009 : Created the file.
 */
?>