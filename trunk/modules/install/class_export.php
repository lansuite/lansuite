<?php

class Export {
	var $output;
	var $filename;
	var $lansuite;


	function LSTableHead($filename = NULL){
		global $xml;

		include_once("inc/classes/class_xml.php");
		$xml = new xml;

		if ($filename) $this->filename = $filename;
		else $this->filename = "lansuite_". date("ymd") .".xml";

		$this->output = '<?xml version="1.0" encoding="UTF-8"?'.">\r\n\r\n";

		/* Header */
		$header = $xml->write_tag("filetype", "LanSuite", 2);
		$header .= $xml->write_tag("version", "2.0", 2);
		$header .= $xml->write_tag("source", "http://www.lansuite.de", 2);
		$header .= $xml->write_tag("date", date("Y-m-d h:i"), 2);
		$this->lansuite = $xml->write_master_tag("header", $header, 1);
	}


	function LSTableFoot(){
		global $xml;

		$this->output .= $xml->write_master_tag("lansuite", $this->lansuite, 0);

    header('Content-Type: application/octetstream; charset=utf-8');
    header("Content-Disposition: attachment; filename=\"{$this->filename}\"" );
    header('Content-Length: '. strlen($this->output));
    header('Expires: 0');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: public');

		echo $this->output;
	}


	function ExportTable($table, $e_struct = NULL, $e_cont = NULL){
		global $db, $config, $xml;

		/* Table-Head */
		$table_head = $xml->write_tag("name", $table, 3);
		$tables = $xml->write_master_tag("table_head", $table_head, 2);

		/* Structure */
		if ($e_struct) {
			$structure = "";
			$query = $db->query("DESCRIBE {$config["database"]["prefix"]}$table");
			while ($row = $db->fetch_array($query)) {
				$field = $xml->write_tag("name", $row["Field"], 4);
				$field .= $xml->write_tag("type", $row["Type"], 4);
				$field .= $xml->write_tag("null", $row["Null"], 4);
				$field .= $xml->write_tag("key", $row["Key"], 4);
				$field .= $xml->write_tag("default", $row["Default"], 4);
				$field .= $xml->write_tag("extra", $row["Extra"], 4);
				$structure .= $xml->write_master_tag("field", $field, 3);
			}
			$db->free_result($query);
			if ($structure) $tables .= $xml->write_master_tag("structure", $structure, 2);
		}

		/* Content */
		if ($e_cont and $table != "locations") {
			$content = "";
			$query = $db->query("SELECT * FROM {$config["database"]["prefix"]}$table");
			while ($row = $db->fetch_array($query)) {
				$entry = "";
				for ($z = 0; $z < $db->num_fields(); $z++) {
					$field_name = $db->field_name($z);
					if ($row[$field_name] != "") $entry .= $xml->write_tag($field_name, $row[$field_name], 4);
				}
				if ($entry) $content .= $xml->write_master_tag("entry", $entry, 3);
			}
			$db->free_result($query);
			if ($content) $tables .= $xml->write_master_tag("content", $content, 2);
		}

		$this->lansuite .= $xml->write_master_tag("table", $tables, 1);
	}


	function ExportMod($mod, $e_struct = NULL, $e_cont = NULL, $e_trans = NULL){
		global $xml, $db, $config;

		if (is_dir("modules/$mod/mod_settings/")){

			// Read DB-Names from db.xml
			$file = "modules/$mod/mod_settings/db.xml";
			if (file_exists($file)) {
				$xml_file = fopen($file, "r");
				$xml_content = fread($xml_file, filesize($file));
				fclose($xml_file);

				$lansuite = $xml->get_tag_content("lansuite", $xml_content);
				$tables = $xml->get_tag_content_array("table", $lansuite);
				foreach ($tables as $table) {
					$table_head = $xml->get_tag_content("table_head", $table);
					$table_name = $xml->get_tag_content("name", $table_head);
					$table_structure = $xml->get_tag_content("structure", $table);
					if ($table_structure != '') $this->ExportTable($table_name, $e_struct, $e_cont);
				}
			}
			
			// Export Translations
  		if ($e_trans) {
  			$table_head = $xml->write_tag('name', 'translation', 3);
    		$tables = $xml->write_master_tag("table_head", $table_head, 2);
  
        $content = '';
        $res = $db->query("SELECT * FROM {$config["database"]["prefix"]}translation WHERE file = '$mod'");
  			while ($row = $db->fetch_array($res)) {
    			$entry = $xml->write_tag('id', $row['id'], 4);
    			$entry .= $xml->write_tag('tid', $row['tid'], 4);
    			$entry .= $xml->write_tag('org', $row['org'], 4);
    			$entry .= $xml->write_tag('en', $row['en'], 4);
    			$entry .= $xml->write_tag('file', $mod, 4);
    		  $content .= $xml->write_master_tag("entry", $entry, 3);
        }  		  
        $db->free_result($res);
  
    		$tables .= $xml->write_master_tag("content", $content, 2);
    		$this->lansuite .= $xml->write_master_tag("table", $tables, 1);
    	}
		}
	}


	function ExportAllTables($e_struct = NULL, $e_cont = NULL, $e_trans = NULL){
		global $db, $config;

		$this->LSTableHead();

		$res = $db->query("SELECT * FROM {$config["tables"]["modules"]} ORDER BY changeable DESC, caption");
		while ($row = $db->fetch_array($res)) $this->ExportMod($row["name"], $e_struct, $e_cont, $e_trans);
		$db->free_result($res);

		// !!TODO: Export general Translations

		$this->LSTableFoot();
	}


	function SendExport($out, $name){
		global $func;

    header('Content-Type: application/octetstream; charset=utf-8');
    header("Content-Disposition: attachment; filename=\"$name\"" );
    header('Content-Length: '. strlen($out));
    header('Expires: 0');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: public');

		echo $out;
	}


	function ExportCSVComplete($sep){
		global $db, $config, $func, $party, $seat2;

		$user_export = $config['lansuite']['version']." CSV Export\r\nParty: ". $_SESSION['party_info']['name'] ."\r\nExportdate: ".$func->unixstamp2date(time(),'daydatetime')."\r\n\r\n";

		$user_export .= "tmp userid;email;username;name;firstname;sex;street;hnr;plz;city;passnr/misc;md5pwd;usertype;paid;seatcontrol;clan;clanurl;wwclid;nglid;checkin;checkout;signondate;seatblock;seat;ip;comment\r\n";

		$query = $db->query("SELECT u.*, p.paid, p.checkin, p.checkout, p.signondate, p.seatcontrol
			FROM {$config["tables"]["user"]} AS u
			LEFT JOIN {$config["tables"]["party_user"]} AS p ON p.user_id = u.userid
			WHERE p.party_id = {$party->party_id}
			");
		while($row = $db->fetch_array($query)) {
			$user_export .= $row["userid"].$sep;
			$user_export .= $row["email"].$sep;

			$username = str_replace("&gt;","",$row["username"]);
			$username = str_replace("&lt;","",$username);
			$username = str_replace("&gt","",$username);
			$username = str_replace("&lt","",$username);
			$username = trim($username);

			$user_export .= $username.$sep;
			$user_export .= $row["name"].$sep;
			$user_export .= $row["firstname"].$sep;
			$user_export .= $row["sex"].$sep;
			$user_export .= $row["street"].$sep;
			$user_export .= $row["hnr"].$sep;
			$user_export .= $row["plz"].$sep;
			$user_export .= $row["city"].$sep;
			$user_export .= $row["passnr"].$sep;

			$user_export .= $row["password"].$sep;
			$user_export .= $row["type"].$sep;
			$user_export .= $row["paid"].$sep;
#			$user_export .= $row["paidcash"].$sep;
			$user_export .= $row["seatcontrol"].$sep;

			$user_export .= $row["clan"].$sep;
			$user_export .= $row["clanurl"].$sep;
			$user_export .= $row["wwclid"].$sep;
			$user_export .= $row["nglid"].$sep;
			$user_export .= ($row["checkin"] > "0") ? $func->unixstamp2date($row["checkin"],"datetime").$sep : $sep;
			$user_export .= ($row["checkout"] > "0") ? $func->unixstamp2date($row["checkout"],"datetime").$sep : $sep;
			$user_export .= ($row["signondate"] > "0") ? $func->unixstamp2date($row["signondate"],"datetime").$sep : $sep;

			// seat
			$row_seat = $db->query_first("SELECT blockid, col, row, ip FROM {$config['tables']['seat_seats']} WHERE userid='{$row["userid"]}' AND status = 2");
			$blockid  = $row_seat["blockid"];
			if ($blockid != "") {
				$row_block    = $db->query_first("SELECT orientation, name FROM {$config['tables']['seat_block']} WHERE blockid='$blockid'");
				$seatindex = $seat2->CoordinateToName($row_seat["col"] + 1, $row_seat["row"], $row_block["orientation"]);
				$user_export .= $row_block["name"].$sep;
				$user_export .= $seatindex.$sep;
			} else $user_export .= $sep.$sep;

			$user_export .= $row_seat["ip"].$sep;
			$user_export .= $row["comment"].$sep;
			$user_export .= "\r\n";
		}

		return $user_export;
	}



	function ExportCSVSticker($sep){
		global $db, $config, $func, $party, $seat2;

		$user_export = $config['lansuite']['version']." CSV Export\r\nParty: ".$config['lanparty']['name']."\r\nExportdate: ".$func->unixstamp2date(time(),'daydatetime')."\r\n\r\n";

		$user_export .= "username;name;firstname;clan;seatblock;seat;ip\r\n";
		$query = $db->query("SELECT u.*, p.paid, p.checkin, p.checkout, p.signondate, p.seatcontrol
			FROM {$config["tables"]["user"]} AS u
			LEFT JOIN {$config["tables"]["party_user"]} AS p ON p.user_id = u.userid
			WHERE p.party_id = {$party->party_id}
			");

		while($row = $db->fetch_array($query)) {
			$username = str_replace("&gt;","",$row["username"]);
			$username = str_replace("&lt;","",$username);
			$username = str_replace("&gt","",$username);
			$username = str_replace("&lt","",$username);
			$username = trim($username);

			$user_export .= $username.$sep;
			$user_export .= $row["name"].$sep;
			$user_export .= $row["firstname"].$sep;
			$user_export .= $row["clan"].$sep;

			// seat
			$row_seat = $db->query_first("SELECT blockid, col, row, ip FROM {$GLOBALS['config']['tables']['seat_seats']} WHERE userid='{$row["userid"]} AND status = 2'");
			$blockid  = $row_seat["blockid"];
			if($blockid != "") {
				$row_block    = $db->query_first("SELECT orientation, name FROM {$GLOBALS['config']['tables']['seat_block']} WHERE blockid='$blockid'");
				$seatindex = $seat2->CoordinateToName($row_seat["col"] + 1, $row_seat["row"], $row_block["orientation"]);
				$user_export .= $row_block["name"].$sep;
				$user_export .= $seatindex.$sep;
			}

			$user_export .= $row_seat["ip"].$sep;
			$user_export .= "\r\n";
		} // end while

		return $user_export;
	}


	function ExportCSVCard($sep){
		global $db, $config, $func, $party, $seat2;

		$user_export = $config['lansuite']['version']." CSV Export\r\nParty: ".$config['lanparty']['name']."\r\nExportdate: ".$func->unixstamp2date(time(),'daydatetime')."\r\n\r\n";

    $user_export .= "username;name;firstname;clan;seatblock;col;row;seat;ip\n";
    
    $query = $db->query("SELECT s.* FROM {$config["tables"]["seat_seats"]} AS s
      LEFT JOIN {$config['tables']['seat_block']} AS b ON s.blockid = b.blockid
      WHERE b.party_id = {$party->party_id} AND s.status = 2
      ORDER BY s.blockid");      
    while ($row_seat = $db->fetch_array($query)) {
      $userid = $row_seat["userid"];

      $row = $db->query_first("SELECT u.*, p.paid, p.checkin, p.checkout, p.signondate, p.seatcontrol
        FROM {$config["tables"]["user"]} AS u
        LEFT JOIN {$config["tables"]["party_user"]} AS p ON p.user_id = u.userid
        WHERE u.userid='$userid'
        ");
      
      $username = str_replace("&gt;","",$row["username"]);
      $username = str_replace("&lt;","",$username);
      $username = str_replace("&gt","",$username);
      $username = str_replace("&lt","",$username);
      $username = trim($username);
      $user_export .= $username.$sep;
      $user_export .= $row["name"].$sep;
      $user_export .= $row["firstname"].$sep;
      $user_export .= $row["clan"].$sep;
      
      $blockid  = $row_seat["blockid"];
      $row_block    = $db->query_first("SELECT orientation, name FROM {$config['tables']['seat_block']} WHERE blockid='$blockid'");
  		$seatindex = $seat2->CoordinateToName($row_seat["col"] + 1, $row_seat["row"], $row_block["orientation"]);
      $user_export .= $row_block["name"].$sep;
      $user_export .= $row_seat["col"].$sep;
      $user_export .= $row_seat["row"].$sep;
      $user_export .= $seatindex.$sep;
      $user_export .= $row_seat["ip"];
      
      $user_export .= "\r\n";
    } // end while
		return $user_export;
	}


	function ExportExtInc($filename) {
    include_once('ext_scripts/archive.php');

    $zip = new gzip_file($filename);
    $zip->set_options(array('basedir' => '.', 'overwrite' => 1, 'level' => 1, 'inmemory' => 1));
    $zip->add_files(array('ext_inc'));
    #$zip->exclude_files("ext_inc/CVS/*");
    $zip->create_archive();

    header('Content-Type: application/octetstream; charset=utf-8');
    header("Content-Disposition: attachment; filename=\"$filename\"" );
    $zip->download_file();
    
    if (count($zip->errors) > 0) return false;
    return true;
  }

} // END CLASS
?>
