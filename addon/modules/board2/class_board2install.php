<?php
 	class Board2install
	{
		/*
		 * Returns all supported integration Versions from the board2/integration folder
		 */
		function getSupportedIntegVersions()
		{
	  		$phpbbVersions = array();
	  
	  		$folder=dir('modules/board2/integration'); 	  
	  		while($folderEntry=$folder->read())
	  		{ 
				if ($folderEntry != '.' && $folderEntry != '..' && $folderEntry != 'CVS')
				{
					require_once('modules/board2/integration/'.$folderEntry);
				 	$fileintegrat = new FileIntegrationPhpBB();
		  			$phpbbVersions[] .= '<option value="'.$folderEntry. '">' . $fileintegrat->version . '</option>';
				}
	  		}
	  		$folder->close(); 
	  
      		return $phpbbVersions;
		}
    
		/*
	 	 * Integrates phpBB in lansuite by calling the function of the integrate file
	 	 */
    	function integratePhpBB($phpintegration)
    	{
    		global $config;
	      	$this->setConfigVariable("version", str_replace('_', '.', substr($phpintegration, 5, strlen($phpintegration) -9)));
	      	
	      	$path = substr($_SERVER["PHP_SELF"], 0, strrpos($_SERVER["PHP_SELF"], '/')+1) . 'ext_scripts/phpBB/';
	      	$path = substr($path, 1);
	      	
 			$this->setConfigVariable('path', $path);
 				 	
		 	$fileintegrat = $this->getFileIntegration();
		  	$fileintegrat->integrateNewPhpBB();
   		}
	
		/*
		 * Integrates phpBB in lansuite without installing phpBB
		 */
		function integratePhpBBOnly($phpintegration)
		{
			$_SESSION['phpbbUser'] = $phpintegration;
			global $db, $cvg, $config, $func;
			
		 	$fileintegrat = $this->getFileIntegration();
			
			$lsMaxID = 0;		//Larges UserID of lansuite
			$curMaxID = 0;		//Largest UserID of lansuite and the phpBB Board
			
			$db->query("START TRANSACTION");
			$phpBBMaxID = $fileintegrat->getHighestphpBBUserID();
			$curMaxID = $this->getHighestLSphpBBUserID();

			$fileintegrat->addToAllPhpBBUserIDs($phpBBMaxID+1);
			
			$lsMaxID = $this->getHighestLansuiteUserID();
			$phpBBMaxID = $fileintegrat->getHighestphpBBUserID();
			$curMaxID = $this->getHighestLSphpBBUserID();
			
			$processedLsUserIDs = array();
			foreach ($phpintegration as $phpBBID => $lsUserID)
			{
				//Tests if 1 lansuiteUser has more then one Relation with an phpBB User
				foreach ($processedLsUserIDs as $id => $userID)
				{
					if ($userID == $lsUserID && $userID != "new")
					{	
						$db->query("ROLLBACK");
						return 0;
					}
				}
				$processedLsUserIDs[] .= $lsUserID;
				
				if ($phpBBID == $lsUserID)
				{	//The value doesn't have to be changed.'
					$fileintegrat->transferPhpBBUserInfoToLSUserInfo($phpBBID, $lsUserID);
					continue;
				}
				else if ($lsUserID == "new")
				{	//A new record with the phpBB User has to be created.
					if ($phpBBID < $lsMaxID)
					{
						$fileintegrat->changePhpBBUserID($phpBBID, $curMaxID);
					}
					$fileintegrat->createsLansuiteUserFromPhpBBUser($phpBBID);
					
					$lsMaxID ++;
					$curMaxID ++;
				}
				else
				{	//The userid from the lansuite user and from the phpBB user have to be set the same.
					
					$fileintegrat->transferPhpBBUserInfoToLSUserInfo($phpBBID, $lsUserID);
					$fileintegrat->changePhpBBUserID($phpBBID, $lsUserID);
					
					$curMaxID ++;
				}
			}
			
			//Drops the Table and creates the view.
			$fileintegrat->integratePhpBB();
			
			//Saves all changes on the Databse if everything no errors were detected.
			$db->query("COMMIT");
			return 1;
		}
		
		/*
		 * Returns the highest UserID of the lansuiteDB
		 */
		function getHighestLansuiteUserID()
		{
			global $config, $db;
			$data = ($db->query_first('SELECT max(userid) FROM ' . $config["database"]["prefix"] . 'user'));
			return $data[0];
		}
		
		/*
		 * Returns the highest UserID of the lansuite DB and the phpBB DB
		 */
		function getHighestLSphpBBUserID()
		{
			global $config, $db;
			$fileintegrat = new FileIntegrationPhpBB();
			
			$phpBBMaxID =  $fileintegrat->getHighestphpBBUserID();
			$lsMaxID = $this->getHighestLansuiteUserID();
			
			if ($lsMaxID < $phpBBMaxID)	//biggest userid of both, lansuite and phpBB
				$curMaxID = $phpBBMaxID;
			else
				$curMaxID = $lsMaxID;
				
			return $curMaxID;
		}
		
		/*
		 * Returns all Lansuite Users in this Style for the DropDownField:
		 * <option value="{userid}"> {username} - {email} </option>
		 */
		function getLansuiteUser($error = null)
		{
			global $config, $db;
			
			if ($error == null)
			{
				$query_id = $db->query('select userid, username, email from ' . $config["database"]["prefix"] . 'user');
				
				$lansuiteUser = array();
				
				$lansuiteUser[] .= '<option value="new">' . $lang['board2']['integrateonly']['new'] . '</option>';
				while($rowdata = $db->fetch_array($query_id))
				{ 
				 	$lansuiteUser[] .= '<option value="'.$rowdata[0]. '">' . $rowdata[1] . ' - ' . $rowdata[2] . '</option>';
				}
				$db->free_result($query_id);
				$GLOBALS['lansuiteUser'] = $lansuiteUser;
				return $lansuiteUser;
			}
			else
				return $GLOBALS['lansuiteUser'];
		}
		
		/*
		 * This function sets the board2 installed in the config-file
		 */
	    function setConfigVariable($variable, $value) {
	  		global $db, $config, $install;
		
	  		$config['board2'][$variable] = $value;
      		$install->WriteConfig();
		}
		
		/*
		 * Returns the FileIntegration Object for the phpBB Version
		 */
		function getFileIntegration() {
			global $config;
			
			$version = $config['board2']['version'];
			
			if ($version = '2.0.20') $version = '2.0.19';

			$filename = 'phpbb' . str_replace('.', '_', $version) . '.php';
			require_once('modules/board2/integration/'.$filename);
		 	$fileintegrat = new FileIntegrationPhpBB();
		 	return $fileintegrat;
		}
		
		/*
		 * Returns all PhpBB Users in this Style for the DropDownField:
		 * array of class_userlistitem.php
		 * $phpbbUser[{userid}] = userlistitem
		 * userlistitem->username = {username} - {email}
		 */
		function getPhpBBUser($error = null)
		{
			if ($error == null)
			{
			 	$fileintegrant = $this->getFileIntegration();
			 	$user = $fileintegrant->getPhpBBUser();
			 	$GLOBALS['PhpBBUser'] = $user;
			  	return $user;
			}
			else
				return $GLOBALS['PhpBbUser'];
		}
		
		/*
		 * Returns the language file from phpBB 
		 */
		function getPhpBBLang()
		{
		 	$fileintegrant = $this->getFileIntegration();
		  	return $fileintegrant->getPhpBBLang();
		}
		
		function getDoublePhpBBUser()
		{
			$fileintegrant = $this->getFileIntegration();
		  	return $fileintegrant->getDoublePhpBBUser();
		}
		
		function setBoard2Prefix()
		{
			global $config;
			$this->setConfigVariable('prefix', $config['database'][prefix] . 'phpbb_');
		}
		
		function setAccountAdminActivation()
		{
			$fileintegrant = $this->getFileIntegration();
		  	return $fileintegrant->setAccountAdminActivation();
		}
		
		function finishInstallation()
		{
			global $auth;
			$this->setConfigVariable("configured", "1");
			
			require_once ('class_board2.php');
		  	$board2 = new Board2();
		  	$board2->loginPhpBB( $auth['userid'] );
		}
		
		function deIntegrate()
		{
			$fileintegrant = $this->getFileIntegration();
		  	$ret =  $fileintegrant->deIntegrate();
		  	$this->setConfigVariable('configured', 0);
		  	return $ret;
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