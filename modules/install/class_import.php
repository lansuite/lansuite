<?php

class Import {
	var $xml_content;
	var $xml_content_lansuite;
	var $table_names = array();
	var $table_state = array();

	function GetUploadFileType($usr_file_name){
		$file_type = substr($usr_file_name, strrpos($usr_file_name, ".") + 1, strlen($usr_file_name));
		return $file_type;
	}



	function GetImportHeader($tmp_file_name){
		global $xml;

		## Open XML-File
		$xml_file    = fopen($tmp_file_name, "r");
		$this->xml_content = fread($xml_file, filesize($tmp_file_name));
		fclose($xml_file);

		## Get Header-Tag
		$header = "";
		$this->xml_content_lansuite = $xml->get_tag_content("LANsurfer", $this->xml_content, 0);
		if ($this->xml_content_lansuite) $header = $xml->get_tag_content("LANsurfer_header", $this->xml_content_lansuite, 0);
		else {
			$this->xml_content_lansuite = $xml->get_tag_content("lansuite", $this->xml_content, 0);
			$header = $xml->get_tag_content("lansuite_header", $this->xml_content_lansuite, 0);
			if (!$header) $header = $xml->get_tag_content("header", $this->xml_content_lansuite, 0);
		}

		if ($header) {
			$import["version"] 	= $xml->get_tag_content("version", $header);
			$import["filetype"] = $xml->get_tag_content("filetype", $header);
			$import["source"] 	= $xml->get_tag_content("source", $header);
			$import["date"] 	= $xml->get_tag_content("date", $header);
			$import["event"] 	= $xml->get_tag_content("event", $header);
		}

		return $import;
	}


	function ImportXML($rewrite = NULL){
		global $xml, $db, $config, $func;

    // Get Array of installed tables
		$res = $db->query("SHOW TABLES");
		$installed_tables = array();
		while ($row = $db->fetch_array($res)) array_push($installed_tables, $row[0]); 
		$db->free_result($res);

		$tables = $xml->get_tag_content_array("table", $this->xml_content_lansuite);
		foreach ($tables as $table) {

			// Get Table-Head-Data from XML-File
			$table_head = $xml->get_tag_content("table_head", $table, 0);
			$table_name = $xml->get_tag_content("name", $table_head);
			$this->table_names[] = $table_name;

			// Search current XML-Table in installed tables
			$table_found = in_array($config['database']['prefix'] . $table_name, $installed_tables);

			// Just temporary becauso of errors in the update 2.0.1 -> 2.0.2
			if ($table_name == "config_selections") {
        $table_found = 0;
        $db->query_first("DROP TABLE IF EXISTS {$config["database"]["prefix"]}$table_name");
      }

			if ($table_found) $this->table_state[] = "exist";

			// If Rewrite: Drop current table
			if ($rewrite){
				$db->query_first("DROP TABLE IF EXISTS {$config["database"]["prefix"]}$table_name");
				$table_found = false;
			}

			// Get current table-structure from DB, to compare with XML-File
			$pri_key_count = 0;
			$db_fields = array();
			if ($table_found) {
				$query = $db->query("DESCRIBE {$config["database"]["prefix"]}$table_name");
				while ($row = $db->fetch_array($query)) {
          $db_fields[] = $row;
					if ($row["Key"] == "PRI") $pri_key_count++;  // If primary key in DB, increase counter
				}
				$db->free_result($query);
			}

			// Import Table-Structure
			$structure = $xml->get_tag_content("structure", $table, 0);
			$fields = $xml->get_tag_content_array("field", $structure);
			$mysql_fields = "";
			$primary_key = "";
			$unique_key = "";
			$field_names = array();

			// Search for fields, which exist in the XML-File no more, but still in DB.
			// Delete them from the DB
			if ($table_found and $db_fields) foreach ($db_fields as $db_field) if ($fields) {
				$found_in_xml = 0;
				foreach ($fields as $field) {
					if ($db_field["Field"] == $xml->get_tag_content("name", $field)) {
						$found_in_xml = 1;
						break;
					}
				}
				if (!$found_in_xml and $db_field["Field"] != "") {
					$db->query("ALTER TABLE {$config["database"]["prefix"]}$table_name DROP {$db_field["Field"]}");
				}
			}

			// Read the DB-Structure form XML-File
			if ($fields) foreach ($fields as $field) {

				// Read XML-Entries
				$name = $xml->get_tag_content("name", $field);
				$type = $xml->get_tag_content("type", $field);
				$null_xml = $xml->get_tag_content("null", $field);
				$key = $xml->get_tag_content("key", $field);
				$default_xml = $xml->get_tag_content("default", $field);
				$extra = $xml->get_tag_content("extra", $field);

        // Set default value to 0 or '', if NOT NULL and not autoincrement
        if ($null_xml == '' and $extra == '') {
          if (substr($type, 0, 3) == 'int' or substr($type, 0, 7) == 'tinyint') $default = 'default '. (int)$default_xml;
          else $default = "default '$default_xml'";
        } else $default = '';

				// Create MySQL-String to import
				($null_xml == '')? $null = "NOT NULL" : $null = "NULL";
				($default_xml != '')? $default = "default '$default_xml'" : $default = "";
				if ($key == "PRI") $primary_key .= "$name, ";
				if ($key == "UNI" or $key == "PRI") $unique_key .= ", UNIQUE KEY $name ($name)";
				$mysql_fields .= "$name $type $null $default $extra, ";

				// Safe Field-Names to know which fields to import content for, in the next step.
				$field_names[] = $name;

				// If table exists, compare XML-File with DB and check weather the DB has to be updated
				if ($table_found) {

					// Search for fiels, which exist in the XML-File, but dont exist in the DB yet.
					$found_in_db = 0;
					if ($db_fields) foreach ($db_fields as $db_field) if ($db_field["Field"] == $name) {

						// Check wheather the field in the DB differs from the one in the XML-File
						// Change it
						if ($null_xml == '' and $db_field['Null'] = 'NO') $null_xml = $db_field['Null']; // Some MySQL-Versions return 'NO' instead of ''
						if ($db_field["Type"] != $type or $db_field["Null"] != $null_xml or $db_field["Default"] != $default_xml or $db_field["Extra"] != $extra) {
							$db->query("ALTER TABLE {$config["database"]["prefix"]}$table_name CHANGE $name $name $type $null $default $extra");
						}

						// Has an Index been removed / added?
						if ($db_field["Key"] != $key) {

							// Index in DB, but not in XML -> Drop it!
							if ($db_field["Key"] == "PRI") {
							  $db->query("ALTER TABLE {$config["database"]["prefix"]}$table_name DROP PRIMARY KEY");
							  $pri_key_count = 0;
							} elseif ($db_field["Key"] == "UNI") $db->query("ALTER TABLE {$config["database"]["prefix"]}$table_name DROP INDEX $name");
							elseif ($db_field["Key"] == "MUL") $db->query("ALTER TABLE {$config["database"]["prefix"]}$table_name DROP INDEX $name");


							// Index in XML, but not in DB -> Add it!
							if ($key == "MUL") {
							
                // If type is string, or text use FULLTEXT as index, otherwise use simple INDEX							
                if ($type == 'text' or $type == 'longtext' or substr($type, 0, 7) == 'varchar')
                  $db->query("ALTER TABLE {$config["database"]["prefix"]}$table_name ADD FULLTEXT ($name)"); 							
								else $db->query("ALTER TABLE {$config["database"]["prefix"]}$table_name ADD INDEX ($name)");

							} elseif ($key == "UNI") $db->query("ALTER TABLE {$config["database"]["prefix"]}$table_name ADD UNIQUE ($name)");
							elseif ($key == "PRI") {
							
                // If primary key is in DB, drop it first, otherwise just add the new one							
                if ($pri_key_count == 0) $db->query("ALTER TABLE {$config["database"]["prefix"]}$table_name ADD PRIMARY KEY ($name)");
                else $db->query("ALTER TABLE {$config["database"]["prefix"]}$table_name DROP PRIMARY KEY, ADD PRIMARY KEY ($name)");
                $pri_key_to_add = $name;
							}
						}
/*
						// Differece-Report
						if ($db_field["Type"] != $type) echo $db_field["Type"] ."=". $type ." Type in $table_name $name<br>";
						if ($db_field["Null"] != $null_xml) echo $db_field["Null"] ."=". $null_xml ." Null in $table_name $name<br>";
						if ($db_field["Key"] != $key) echo $db_field["Key"] ."=". $key ." Key in $table_name $name<br>";
						if ($db_field["Default"] != $default_xml) echo $db_field["Default"] ."=". $default_xml ." Def in $table_name $name<br>";
						if ($db_field["Extra"] != $extra) echo $db_field["Extra"] ."=". $extra ." Extra in $table_name $name<br>";
*/
						$found_in_db = 1;
						break;
					}

					// If a key was not found in the DB, but in the XML-File -> Add it!
					if (!$found_in_db) {
						// If auto_increment is used for this key, add this key as primary, unique key
						if ($extra == "auto_increment") $db->query("ALTER TABLE {$config["database"]["prefix"]}$table_name ADD $name $type $null $default $extra , ADD PRIMARY KEY ($name), ADD UNIQUE ($name)");
						else $db->query("ALTER TABLE {$config["database"]["prefix"]}$table_name ADD $name $type $null $default $extra");
					}
				}
			}
			
			$mysql_fields = substr($mysql_fields, 0, strlen($mysql_fields) - 2);
			if ($primary_key) $primary_key = ", PRIMARY KEY (". substr($primary_key, 0, strlen($primary_key) - 2) .")";

			// Create a new table, if it does not exist yet, or has been dropped above, due to rewrite
			$db->query("CREATE TABLE IF NOT EXISTS {$config["database"]["prefix"]}$table_name ($mysql_fields $primary_key $unique_key) TYPE = MyISAM  CHARACTER SET utf8 COLLATE utf8_general_ci");
			$db->query("REPLACE INTO {$config["database"]["prefix"]}table_names SET name = '$table_name'");

			// Import Table-Content
			$content = $xml->get_tag_content("content", $table, 0);
			$entrys = $xml->get_tag_content_array("entry", $content);

			if ($entrys) {
				// Update Content only, if no row exists
				$qry = $db->query("SELECT * FROM {$config["database"]["prefix"]}$table_name LIMIT 1");
				$num_rows = $db->num_rows($qry);
				$db->free_result($qry);

				foreach ($entrys as $entry) {
					$mysql_entries = "";
					if ($field_names) foreach ($field_names as $field_name) {
						$value = $xml->get_tag_content($field_name, $entry);
						if ($value) $mysql_entries .= "$field_name = '". $func->escape_sql($value) ."', ";
					}
					$mysql_entries = substr($mysql_entries, 0, strlen($mysql_entries) - 2);
					if ($num_rows == 0) $db->query_first("REPLACE INTO {$config["database"]["prefix"]}$table_name SET $mysql_entries");
				}
			}

			if ($rewrite) $this->table_state[] = "rewrite";
			
			// Optimize table
			$db->query_first("OPTIMIZE TABLE $table_name");
		}
	}


	function ImportLanSurfer($del_db, $replace, $no_seat, $signon, $comment){
		global $xml, $db, $config, $party, $cfg;

		// Delete User-Table
		if ($del_db){
			$db->query("TRUNCATE TABLE {$config["tables"]["user"]}");
			$db->query("TRUNCATE TABLE {$config["tables"]["usersettings"]}");
		}

		// Getting all data in Usertags
		// Mergeing all <users>-Blocks togheter
		$users_blocks 	= $xml->get_tag_content_combine("users", $this->xml_content_lansuite);
		$users 		= $xml->get_tag_content_array("user", $users_blocks);

		// now transforming the array and reading the user-specific data in sub-<user> tags like name, clan etc.
		foreach ($users AS $xml_user) {
			$users_to_import[] = array(	username => 		$xml->get_tag_content("username", $xml_user),
										firstname =>		$xml->get_tag_content("firstname", $xml_user),
										name =>		$xml->get_tag_content("name", $xml_user),
										clan =>		$xml->get_tag_content("clan", $xml_user),
										type =>		$xml->get_tag_content("type", $xml_user),
										paid =>		$xml->get_tag_content("paid", $xml_user),
										password =>		$xml->get_tag_content("password", $xml_user),
										email =>		$xml->get_tag_content("email", $xml_user),
										wwclid =>		$xml->get_tag_content("wwclid", $xml_user),
										wwclclanid =>	$xml->get_tag_content("wwclclanid", $xml_user),
										clanurl =>		$xml->get_tag_content("homepage", $xml_user));
		} // foreach - users

		// Putting all <seat_blocks>-tags into an array
		$seat_blocks_blocks 	= $xml->get_tag_content_combine("seat_blocks",$this->xml_content_lansuite);
		$blocks 		= $xml->get_tag_content_array("block",$seat_blocks_blocks);

		if ($blocks) foreach ($blocks AS $xml_block) {
			unset($seps_to_import);
			unset($seats_to_import);

			// Seats in this block
			$seats_in_this_block 	= $xml->get_tag_content_combine("seat_seats",$xml_block);
			$seats 			= $xml->get_tag_content_array("seat",$seats_in_this_block);

			if(is_array($seats)) foreach ($seats AS $xml_seat) {
				$seats_to_import[] = array (col =>		$xml->get_tag_content("col", $xml_seat),
											row =>		$xml->get_tag_content("row", $xml_seat),
											status =>	$xml->get_tag_content("status", $xml_seat),
											owner =>	$xml->get_tag_content("owner", $xml_seat),
											ipaddress =>	$xml->get_tag_content("ipaddress", $xml_seat));
			} // foreach - seats

			// Sepeartors in this block
			$seps_in_this_block 	= $xml->get_tag_content_combine("seat_sep",$xml_block);
			$seps 			= $xml->get_tag_content_array("sep",$seps_in_this_block);

			if(is_array($seps)) foreach ($seps AS $xml_sep) {
				$seps_to_import[] = array (	orientation => 	$xml->get_tag_content("orientation",$xml_sep),
											value =>	$xml->get_tag_content("value",$xml_sep));
			} // foreach - seperators

			// Seatblockdata
			$seat_blocks_to_import[] = array (	name => 		$xml->get_tag_content("name",$xml_block),
												rows =>			$xml->get_tag_content("rows",$xml_block),
												cols =>			$xml->get_tag_content("cols",$xml_block),
												orientation =>		$xml->get_tag_content("orientation",$xml_block),
												remark =>		$xml->get_tag_content("remark",$xml_block),
												text_tl =>		$xml->get_tag_content("text_tl",$xml_block),
												text_tc =>		$xml->get_tag_content("text_tc",$xml_block),
												text_tr =>		$xml->get_tag_content("text_tr",$xml_block),
												text_lt =>		$xml->get_tag_content("text_lt",$xml_block),
												text_lc =>		$xml->get_tag_content("text_lc",$xml_block),
												text_lb =>		$xml->get_tag_content("text_lb",$xml_block),
												text_rt =>		$xml->get_tag_content("text_rt",$xml_block),
												text_rc =>		$xml->get_tag_content("text_rc",$xml_block),
												text_rb =>		$xml->get_tag_content("text_rb",$xml_block),
												text_bl =>		$xml->get_tag_content("text_bl",$xml_block),
												text_bc =>		$xml->get_tag_content("text_bc",$xml_block),
												text_br =>		$xml->get_tag_content("text_br",$xml_block),
												seats =>		$seats_to_import,
												seps =>			$seps_to_import);
		} // foreach - seatblocks


		/* DB INPUT */
		if(is_array($users_to_import) == TRUE) {
#				$db->query("DELETE FROM lansuite_usersettings");

			foreach($users_to_import as $user) {
				$email 		= $user['email'];
				$username	= $xml->convertinputstr($user['username']);
				$name 		= $xml->convertinputstr($user['name']);
				$firstname 	= $xml->convertinputstr($user['firstname']);
				$clan 		= $xml->convertinputstr($user['clan']);

				$type 		= $user['type'];	
				$paid 		= $user['paid'];
				$password 	= $user['password'];	

				$wwclid 	= $xml->convertinputstr($user['wwclid']);	
				$wwclclanid 	= $xml->convertinputstr($user['wwclclanid']);

				$checkin =($type > 1) ? "1" : "0";

				$skip = 0;
				$res = $db->query("SELECT username FROM {$config["tables"]["user"]} WHERE email = '$email'");
				if (($db->num_rows($res) > 0) && (!$replace)) $skip = 1;

				if (!$skip){
				  $clan_id = 0;
				  if ($clan != '') {
  					$db->query("REPLACE INTO {$config["tables"]["clan"]} SET
  							clan = '$clan',
  							clanurl = '$clanurl'
  							");
  					$clan_id = $db->insert_id();
          }
					$db->query("REPLACE INTO {$config["tables"]["user"]} SET
							email = '$email',
							name = '$name',
							username = '$username',
							firstname = '$firstname',
							type = '$type',
							clanid = ". (int)$clan_id .",
							password = '$password',
							wwclid = '$wwclid',
							wwclclanid = '$wwclclanid',
							comment = '$comment'
							");
					$id = $db->insert_id();

					// Update Party-Signon
					if ($signon) $party->add_user_to_party($id, 1, $paid, $checkin);
					else $party->delete_user_from_party($id);	

					$default_design = $config['lansuite']['default_design'];
					$db->query("INSERT INTO {$config["tables"]["usersettings"]} SET userid='$id'");

					$userids[$email] = $id;
				}
			} // foreach - $users_to_import
			$confirmation .= HTML_NEWLINE . HTML_NEWLINE ."- User erfolgreich eingetragen";
		} // is array
		else echo "FEHLER: USER NICHT EINGETRAGEN" .HTML_NEWLINE;

		if(is_array($seat_blocks_to_import) == TRUE AND !$no_seat) {
			foreach($seat_blocks_to_import as $block) {
				$name 		= $xml->convertinputstr($block['name']);
				$rows 		= $block['rows'];
				$cols 		= $block['cols'];
				$orientation 	= $block['orientation'];
				$remark 	= $block['remark'];
				$text_tl	= $block['text_tl'];
				$text_tc	= $block['text_tc'];
				$text_tr	= $block['text_tr'];
				$text_lt	= $block['text_lt'];
				$text_lc	= $block['text_lc'];
				$text_lb	= $block['text_lb'];
				$text_rt	= $block['text_rt'];
				$text_rc	= $block['text_rc'];
				$text_rb	= $block['text_rb'];
				$text_bl	= $block['text_bl'];
				$text_bc	= $block['text_bc'];
				$text_br	= $block['text_br'];

				$db->query("REPLACE INTO {$config["tables"]["seat_block"]} SET 
							name='$name',
							cols='$cols',
							rows='$rows',
							orientation='$orientation',
							remark='$remark',
							text_tl='$text_tl',
							text_tc='$text_tc',
							text_tr='$text_tr',
							text_lt='$text_lt', 
							text_lc='$text_lc',  
							text_lb='$text_lb', 
							text_rt='$text_rt',
							text_rc='$text_rc',
							text_rb='$text_rb',
							text_bl='$text_bl',
							text_bc='$text_bc',
							text_br='$text_br',
							party_id={$cfg['signon_partyid']}");
				$blockid = $db->insert_id();

				if(is_array($block['seps'])) foreach($block['seps'] as $sep) {
					$orientation 	= $sep['orientation'];
					$value 		= $sep['value'];
					$db->query("REPLACE INTO {$config["tables"]["seat_sep"]} SET
								blockid='$blockid',
								orientation='$orientation',
								value='$value'"); 
				} // foreach - seps

				if(is_array($block['seats'])) foreach($block['seats'] as $seat) {
					$col 		= $seat['col'];		
					$row 		= $seat['row'];		
					$status 	= $seat['status'];	
					$owner 		= $seat['owner'];	
					$ipaddress 	= $seat['ipaddress'];
					$userid 	= $userids[$owner];
					if($owner == "") $userid  = 0;

					$db->query("REPLACE INTO {$config["tables"]["seat_seats"]} SET
								blockid='$blockid',
								col='$col',
								row='$row',
								status='$status',
								userid='$userid',
								ip='$ipaddress'"); 
				} // foreach - seats
			} // foreach - $seat_blocks_to_import
		} // if array
	}



	function ImportCSV($tmp_file_name, $del_db, $replace, $signon, $comment){
		global $db, $config;

		// Delete User-Table
		if ($del_db){
			$db->query("TRUNCATE TABLE {$config["tables"]["user"]}");
			$db->query("TRUNCATE TABLE {$config["tables"]["usersettings"]}");
		}

		$csv_file = file($tmp_file_name);
		$import = Array("error" => 0, "nothing" => 0, "insert" => 0, "replace" => 0);

		foreach ($csv_file as $csv_line) {
			$csv_line = chop($csv_line);
			$csv_line = trim($csv_line);
			$csv_line = str_replace("\"", "", $csv_line);
			$csv_line = str_replace("'", "", $csv_line);
						
			$user = explode(";", $csv_line);
			($user[5] == "Not Paid") ? $user_paid = 0 : $user_paid = 1;

			$skip = 0;
			$res = $db->query("SELECT username FROM {$config["tables"]["user"]} WHERE email = '$email'");
			if (($db->num_rows($res) > 0) && (!$replace)) $skip = 1;

			if (!$skip){
				$replace_user = $db->query("REPLACE INTO {$config["tables"]["user"]}
											SET username	= '{$user[0]}',
												clan		= '{$user[1]}',
												firstname	= '{$user[2]}',
												name		= '{$user[3]}',
												email		= '{$user[4]}',
												paid		= '$user_paid',
												type		= '1',
												signon		= '$signon',
												comment		= '$comment'
												");
				$id = $db->insert_id();
				$db->query("INSERT INTO {$config["tables"]["usersettings"]} SET userid='$id'");
			}

			switch(mysql_affected_rows($db->link_id)) {
				case "-1":
					$import["error"]++;
				break;
				
				case "0":
					$import["nothing"]++;
				break;
				
				case "1":
					$import["insert"]++;
				break;
				
				case "2":
					$import["replace"]++;
				break;
			}
		}

		return $import;
	}
	
	function ImportExtInc($filename) {
    include_once('ext_scripts/archive.php');

    $zip = new gzip_file($filename);
    $zip->set_options(array('basedir' => '.', 'overwrite' => 1));
    $zip->extract_files();
  }
	
}
?>
