<?php
/*
*
**	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0.2
*	File Version:		1.0
*	Filename: 			update202.php
*	Module: 			install
*	Main editor:		marco@chuchi.tv (Genesis)
*	Last change:		15.10.2004
*	Description: 		Update from 2.0.2 to 2.0.3
*	Remarks:
*
*/



	// Search Table user and party_user
	$found_user = 0;
	$found_party_user = 0;
	$res = mysql_list_tables($config["database"]["database"]);
	while ($row = mysql_fetch_row($res)){
		if ($row[0] == $config["database"]["prefix"] . "user") {
			$found_user = 1;
		}
		if ($row[0] == $config["database"]["prefix"] . "party_user") {
			$found_party_user = 1;
		}
	}
	mysql_free_result($res);

	if($found_user == 1 && $found_party_user == 0){			
		if($db->field_exist("{$config["database"]["prefix"]}user","signon")){
			//Create Table party_user
			$db->qry("CREATE TABLE IF NOT EXISTS %prefix%party_user (
					  `party_id` int(20) NOT NULL default '0',
					  `user_id` int(20) NOT NULL default '0',
  					  `price_id` int(20) NOT NULL default '0',
  					  `paid` int(1) NOT NULL default '0',
  					  `checkin` int(15) NOT NULL default '0',
  					  `checkout` int(15) NOT NULL default '0',
                      `signondate` int(15) NOT NULL default '0'
   					   )");
			// Insert Ol Data from Table User
			$db->qry("INSERT INTO %prefix%party_user SELECT '1',userid,'0',paid,checkin,checkout,'0' FROM %prefix%user WHERE signon='1'");
			// Delete old config entrys
			
		}
		
	}

?>
