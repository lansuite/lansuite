<?php
	class Board2
	{
		/*
		 * Logon the user with the phpbb_user_id on the phpBB board.
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
		 * 
		 * 
		 */ 
		 function loginPhpBB( $phpbb_user_id ) {
			global $db, $board_config, $config, $lang, $gd;
			global $HTTP_COOKIE_VARS, $HTTP_GET_VARS, $SID;
		  	
		  	$temp_db = $db;
			$temp_lang = $lang;
			$tempboard_config = $board_config;
			$temp_gd = $gd;
		  	
		  	
			// Setup the phpbb environment and then
		    // run through the phpbb login process
		
			$phpbb_root_path = $this->get_absolute_path($config[board2][path]);
			
			// You may need to change the following line to reflect
			// your phpBB installation.
			include_once( $phpbb_root_path . 'config.php' );
			
			define('IN_PHPBB',true);
		
			// You may need to change the following line to reflect
			// your phpBB installation.
		  
			include_once( $phpbb_root_path . 'extension.inc' );
			include_once( $phpbb_root_path . 'common.php' );
			
			$ret = session_begin( $phpbb_user_id, $user_ip, PAGE_INDEX, FALSE, TRUE );
			
			$db = $temp_db;
			$lang = $temp_lang;
			$board_config = $tempboard_config;
			$gd = $temp_gd;
			
			return $ret;
		}
		
		/*
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
		 */ 
		function logoutPhpBB( $phpbb_user_id ) {
			global $db, $lang, $board_config, $config, $gd;
			global $HTTP_COOKIE_VARS, $HTTP_GET_VARS, $SID;
		  	
		  	$temp_db = $db;
			$temp_lang = $lang;
			$tempboard_config = $board_config;
			$temp_gd = $gd;
		  	
		  	
			// Setup the phpbb environment and then
			// run through the phpbb login process
			
			$phpbb_root_path = $this->get_absolute_path($config[board2][path]);
			
			// You may need to change the following line to reflect
			// your phpBB installation.
			include_once( $phpbb_root_path . 'config.php' );
		  
			define('IN_PHPBB',true);
		  
			// You may need to change the following line to reflect
			// your phpBB installation.
		
			include_once( $phpbb_root_path . 'extension.inc' );
			include_once( $phpbb_root_path . 'common.php' );
		
			session_end( session_id(), $phpbb_user_id );
		  
			// session_end doesn't seem to get rid of these cookies,
			// so we'll do it here just in to make certain.
			setcookie( $board_config[ 'cookie_name' ] . '_sid', '', time() - 3600, ' ' );
			setcookie( $board_config[ 'cookie_name' ] . '_mysql', '', time() - 3600, ' ' );
			
			
			$db = $temp_db;
			$lang = $temp_lang;
			$board_config = $tempboard_config;
			$gd = $temp_gd;
		}
		
		function getAnonymousUserID()
		{
			global $db, $lang, $board_config, $config, $gd;
			
			//Saves all Variables which are used by phpBB and lansuite
			$temp_db = $db;
			$temp_lang = $lang;
			$tempboard_config = $board_config;
			$temp_gd = $gd;
			
			$phpbb_root_path = $this->get_absolute_path($config[board2][path]);
			include_once($phpbb_root_path . 'config.php');
			define('IN_PHPBB',true);
			
			$phpbb_root_path = $this->get_absolute_path($config[board2][path]);
			
			include_once( $phpbb_root_path . 'includes/constants.php' );
			
			//Returns all Variables which are used by phpBB and lansuite
			$db = $temp_db;
			$lang = $temp_lang;
			$board_config = $tempboard_config;
			$gd = $temp_gd;
			return ANONYMOUS;
		}
		
		/*
		 * $file is the file url relative to the root of your site.
		 * Yourdomain.com/folder/file.inc would be passed as
		 * "folder/file.inc"
		 */ 
		function get_absolute_path($path)
        {       	
			$folder_depth = substr_count(substr($path,0,-1) , "/");
			
			if($folder_depth == false)
				$folder_depth = 1;
				
			if($folder_depth == 1)
				$folder_depth = 2;
			
         	//echo ('vorgeschlagen=' . str_repeat("../", $folder_depth - 1) . $path . '<br>');
			return (str_repeat("../", $folder_depth - 1)) . $path;
		}
	}
?>