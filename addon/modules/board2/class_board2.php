<?php

class Board2
{
	/**
	 * Returns all supported integration Versions from the board2/integration folder
	 * @return unknown_type, returns an array with all supported versions: array[minversion]='fromversion-toversion'
	 */
	public static function getSupportedVersions() {
		$phpbbVersions = array();

		$phpbbVersions['2.0.19'] .= 'phpBB2 2.0.19';
		$phpbbVersions['2.0.20'] .= 'phpBB2 2.0.20';
		$phpbbVersions['2.0.21'] .= 'phpBB2 2.0.21';
		$phpbbVersions['2.0.22'] .= 'phpBB2 2.0.22';
		$phpbbVersions['2.0.23'] .= 'phpBB2 2.0.23';
		$phpbbVersions['2.0.22_Plus_1.5.3'] .= 'phpBB2 Plus 1.53';
		//$phpbbVersions['3.0.4'] .= 'phpBB3 3.0.4';

		return $phpbbVersions;
	}

	/**
	 * Gets the integration object corresponding to specified minversion
	 * This object is responsible for the actual integration, e.g. SQL - Views....
	 * @param $minversion, the min. supported version of the integration object. If the param is '' the default version will be used.
	 * @return unknown_type, the integration object.
	 */
	public static function getVersionspecificObject($version = '') {
		global $config;

		if ($version == '') $version = $config['board2']['version'];
			
		switch ($version) {
			case '2.0.19':
			case '2.0.20':
			case '2.0.21':
			case '2.0.22':
			case '2.0.23':
				include_once('modules/board2/integration/class_versionspecific_2_0_19.php');
				return new versionspecific_2_0_19();
			case '2.0.22_Plus_1.5.3':
				include_once('modules/board2/integration/class_versionspecific_2_0_22_plus_1_5_3.php');
				return new versionspecific_2_0_22_plus_1_5_3();
			case '3.0.4':
				include_once('modules/board2/integration/class_versionspecific_3_0_4.php');
				return new versionspecific_3_0_4();
		}

		throw new Exception(t('Die ausgew&auml;hlte phpBB Version wird nicht unterst&uuml;tzt.'));
	}

	/**
	 * Logon the user with the phpbb_user_id on the phpBB board.
	 * @param $phpbbUserID
	 * @return unknown_type
	 */
	public static function loginPhpbb($phpbbUserID) {
		return Board2::getVersionspecificObject()->loginphpbb($phpbbUserID);
	}

	/*
	 * Logoff the user with the phpbb_user_id of the phpbb board.
	 * @param $phpbbUserID
	 * @return unknown_type
	 */
	public static function logoutPhpbb($phpbbUserID) {
		return Board2::getVersionspecificObject()->logoutPhpbb($phpbbUserID);
	}

	/**
	 * Gets the id of the anonymous user.
	 * @return unknown_type, returns the id of the user.
	 */
	public static function getAnonymousUserID() {
		return Board2::getVersionspecificObject()->getAnonymousUserID();
	}


	/**
	 * $file is the file url relative to the root of your site.
	 * Yourdomain.com/folder/file.inc would be passed as
	 * "folder/file.inc"
	 * @param $path, the relative path
	 * @return, the absolut path of the $path.
	 */
	public static function getPathToRoot() {
		$path = substr($_SERVER["PHP_SELF"], 0, strrpos($_SERVER["PHP_SELF"], '/')+1);
		return $path;
	}

	public static function getPathFromRootToScript() {
		$path = substr($_SERVER["PHP_SELF"], 0, strrpos($_SERVER["PHP_SELF"], '/')+1);
		return $path;
	}
	//
	// Please make a history at the end of file of your changes !!
	//

	/* HISTORY
	 * 06. 2. 2009 : Major changes of the versionspecific object handling.
	 */
}
?>