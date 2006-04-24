<?php

class board_func {

	function getboardrank($posts) {
		global $lang;

		switch (TRUE){
			case ($posts < 10): 	 		 		 return $lang['board']['user_rank'][0]; break;
			case ($posts >= 10 AND $posts < 20): 	 return $lang['board']['user_rank'][1];  break;
			case ($posts >= 20 AND $posts < 50): 	 return $lang['board']['user_rank'][2]; break;
			case ($posts >= 50 AND $posts < 100): 	 return $lang['board']['user_rank'][3]; break;
			case ($posts >= 100): 			 		 return $lang['board']['user_rank'][4]; break;
		}
	}

	function getuserinfo($userid) {
		global $db, $cfg, $lang, $config;

		$row_poster = $db->query_first("SELECT username, posts, type FROM {$config["tables"]["user"]} WHERE userid='$userid'");
		$row_poster_settings = $db->query_first("SELECT avatar_path, signature FROM {$config["tables"]["usersettings"]} WHERE userid='$userid'");

		$html_image= '<img src="ext_inc/avatare/%s" alt="%s" border="0">';

		$user["username"]   =$row_poster["username"];
		$user["avatar"]     =($row_poster_settings["avatar_path"] != "") ? sprintf($html_image, $row_poster_settings["avatar_path"], "") : "";
		$user["signature"]   = $row_poster_settings["signature"];

		if ($cfg['board_ranking'] == TRUE) $user["rank"] = $this->getboardrank($row_poster["posts"]);
		$user["posts"] = $row_poster["posts"];

		switch($row_poster["type"]) {
			case 1:	$user["type"] = $lang['board']['user'][0]; 	break;
			case 2: $user["type"] = $lang['board']['user'][1];break;
			case 3: $user["type"] = $lang['board']['user'][2]; 	break;
		}

		return $user;
	}

	function get_fid($tid){
		global $db, $config;
		
		$row = $db->query_first("SELECT fid FROM {$config['tables']['board_threads']} WHERE tid='$tid'");
		$fid = $row["fid"];

		return $fid;
	}

	function get_fid_by_pid($pid){
		global $db, $config;

		$tid = $this->get_tid($pid);
		$row = $db->query_first("SELECT fid FROM {$config['tables']['board_threads']} WHERE tid='$tid'");
		$fid = $row["fid"];

		return $fid;
	}
	function get_tid($pid){
		global $db, $config;

		$row = $db->query_first("SELECT tid FROM {$config['tables']['board_posts']} WHERE pid='$pid'");
		$tid = $row["tid"];

		return $tid;
	}

	function check_fid($fid){
		global $db, $config;

		$row = $db->query_first("SELECT fid FROM {$config['tables']['board_forums']} WHERE fid='$fid'");
		$fid = $row["fid"];

		if ($fid != "") return TRUE;
		else return FALSE;
	}

	function check_tid($tid){
		global $db, $config;

		$row = $db->query_first("SELECT tid FROM {$config['tables']['board_threads']} WHERE tid='$tid'");
		$tid = $row["tid"];

		if ($tid != "") return TRUE;
		else return FALSE;
	}
	
	function CloseThread($tid) {
		global $db, $config;
	  $db->query("UPDATE {$config['tables']['board_threads']} SET closed = 1 WHERE tid = ". (int)$tid);
	}

	function OpenThread($tid) {
		global $db, $config;
	  $db->query("UPDATE {$config['tables']['board_threads']} SET closed = 0 WHERE tid = ". (int)$tid);
	}
}

?>
