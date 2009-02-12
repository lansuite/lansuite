<?php
/*
 * Created on 14.04.2006
 */

/*
 * This class represents an user of the phpbb  board that should be integrated in lansuite.
 */
class UserIntegrationItem
{
	private $_lansuiteUserID;
	private $_lansuiteUsername;
	private $_lansuiteEmail;
	private $_phpbbUserID;
	private $_phpbbUsername;
	private $_phpbbEmail;
		
	public function UserIntegrationItem($phpbbUserID, $phpbbUsername, $phpbbEmail, $lansuiteUserID = '', $lansuiteUsername = '', $lansuiteEmail = '') {
		$this->_phpbbUserID = $phpbbUserID;
		$this->_phpbbUsername = $phpbbUsername;
		$this->_phpbbEmail = $phpbbEmail;

		$this->_lansuiteUserID = $lansuiteUserID;
		$this->_lansuiteUsername = $lansuiteUsername;
		$this->_lansuiteEmail = $lansuiteEmail;
	}
		
	public function getLansuiteUserID() {
		return $this->_lansuiteUserID;
	}

	public function getLansuiteUsername() {
		return $this->_lansuiteUsername;
	}

	public function getLansuiteEmail() {
		return $this->_lansuiteEmail;
	}

	public function getPhpbbUserID() {
		return $this->_phpbbUserID;
	}

	public function getPhpbbUsername() {
		return $this->_phpbbUsername;
	}

	public function getPhpbbEmail() {
		return $this->_phpbbEmail;
	}

	public function setLansuiteUserID($lansuiteUserID) {
		$this->_lansuiteUserID = $lansuiteUserID;
	}
		
	public function setLansuiteUsername($lansuiteUsername) {
		$this->_lansuiteUsername = $lansuiteUsername;
	}

	public function setLansuiteEmail($lansuiteEmail) {
		$this->_lansuiteEmail = $lansuiteEmail;
	}

	public function setPhpbbUserID($phpbbUserID) {
		$this->_phpbbUserID = $phpbbUserID;
	}

	public function setPhpbbUsername($phpbbUsername) {
		$this->_phpbbUsername = $phpbbUsername;
	}

	public function setPhpbbEmail($phpbbEmail) {
		$this->_phpbbEmail = $phpbbEmail;
	}
}
//
// Please make a history at the end of file of your changes !!
//

/* HISTORY
 * 06. 2. 2009 : Created the file.
 */
?>
