<?php

$xml = new xml();

class Export
{
    public $output;
    public $filename;
    public $lansuite;


    public function LSTableHead($filename = null)
    {
        global $xml;

        $xml = new xml();

        if ($filename) {
            $this->filename = $filename;
        } else {
            $this->filename = "lansuite_". date("ymd") .".xml";
        }

        $this->output = '<?xml version="1.0" encoding="UTF-8"?'.">\r\n\r\n";

        /* Header */
        $header = $xml->write_tag("filetype", "LanSuite", 2);
        $header .= $xml->write_tag("version", "2.0", 2);
        $header .= $xml->write_tag("source", "http://www.lansuite.de", 2);
        $header .= $xml->write_tag("date", date("Y-m-d h:i"), 2);
        $this->lansuite = $xml->write_master_tag("header", $header, 1);
    }


    public function LSTableFoot()
    {
        global $xml;

        $this->output .= $xml->write_master_tag("lansuite", $this->lansuite, 0);

        header('Content-Type: application/octetstream; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$this->filename}\"");
        header('Content-Length: '. strlen($this->output));
        header('Expires: 0');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: public');

        echo $this->output;
    }


    // Export Translations
    public function ExportTranslation($mod)
    {
        global $xml, $db;

        $table_head = $xml->write_tag('name', 'translation', 3);
        $tables = $xml->write_master_tag("table_head", $table_head, 2);

        $content = '';
        $res = $db->qry("SELECT * FROM %prefix%translation WHERE file = %string%", $mod);
        while ($row = $db->fetch_array($res)) {
            $entry = $xml->write_tag('id', $row['id'], 4);
            $entry .= $xml->write_tag('tid', $row['tid'], 4);
            $entry .= $xml->write_tag('org', $row['org'], 4);
            if ($row['de']) {
                $entry .= $xml->write_tag('de', $row['de'], 4);
            }
            if ($row['en']) {
                $entry .= $xml->write_tag('en', $row['en'], 4);
            }
            if ($row['es']) {
                $entry .= $xml->write_tag('es', $row['es'], 4);
            }
            if ($row['fr']) {
                $entry .= $xml->write_tag('fr', $row['fr'], 4);
            }
            if ($row['nl']) {
                $entry .= $xml->write_tag('nl', $row['nl'], 4);
            }
            if ($row['it']) {
                $entry .= $xml->write_tag('it', $row['it'], 4);
            }
            $entry .= $xml->write_tag('file', $mod, 4);
            $content .= $xml->write_master_tag("entry", $entry, 3);
        }
        $db->free_result($res);

        $tables .= $xml->write_master_tag("content", $content, 2);
        $this->lansuite .= $xml->write_master_tag("table", $tables, 1);
    }


    public function ExportTable($table, $e_struct = null, $e_cont = null)
    {
        global $db, $xml;

        if ($e_struct or $e_cont) {
            /* Table-Head */
            $table_head = $xml->write_tag("name", $table, 3);
            $tables = $xml->write_master_tag("table_head", $table_head, 2);

            /* Structure */
            if ($e_struct) {
                $structure = "";

                // Read indizes from DB
                $DBPrimaryKey = '';
                $DBUniqueKeys = array();
                $DBIndizes = array();
                $DBFulltext = array();
                $ResIndizes = $db->qry("SHOW INDEX FROM %prefix%$table");
                while ($RowIndizes = $db->fetch_array($ResIndizes)) {
                    if ($RowIndizes['Key_name'] == 'PRIMARY') {
                        $DBPrimaryKey = $RowIndizes['Column_name'];
                    } elseif ($RowIndizes['Non_unique'] == 0) {
                        $DBUniqueKeys[] = $RowIndizes['Column_name'];
                    } elseif ($RowIndizes['Non_unique'] == 1) {
                        if ($RowIndizes['Index_type'] == 'FULLTEXT') {
                            $DBFulltext[] = $RowIndizes['Column_name'];
                        } elseif ($RowIndizes['Index_type'] == 'BTREE') {
                            $DBIndizes[] = $RowIndizes['Column_name'];
                        }
                    }
                }
                $db->free_result($ResIndizes);

                $query = $db->qry("DESCRIBE %prefix%$table");
                while ($row = $db->fetch_array($query)) {
                    $field = $xml->write_tag("name", $row["Field"], 4);
                    $field .= $xml->write_tag("type", $row["Type"], 4);
                    $field .= $xml->write_tag("null", $row["Null"], 4);
                    $field .= $xml->write_tag("default", $row["Default"], 4);
                    $field .= $xml->write_tag("extra", $row["Extra"], 4);
                    if ($row["Field"] == $DBPrimaryKey) {
                        $field .= $xml->write_tag("key", 'PRI', 4);
                    } elseif (in_array($row["Field"], $DBUniqueKeys)) {
                        $field .= $xml->write_tag("key", 'UNI', 4);
                    } elseif (in_array($row["Field"], $DBIndizes)) {
                        $field .= $xml->write_tag("key", 'IND', 4);
                    } elseif (in_array($row["Field"], $DBFulltext)) {
                        $field .= $xml->write_tag("key", 'FUL', 4);
                    }
                    $structure .= $xml->write_master_tag("field", $field, 3);
                }
                $db->free_result($query);

                if ($structure) {
                    $tables .= $xml->write_master_tag("structure", $structure, 2);
                }
            }

            /* Content */
            if ($e_cont and $table != "locations") {
                $content = "";
                $query = $db->qry("SELECT * FROM %prefix%$table");
                while ($row = $db->fetch_array($query)) {
                    $entry = "";
                    for ($z = 0; $z < $db->num_fields(); $z++) {
                        $field_name = $db->field_name($z);
                        if ($row[$field_name] != "") {
                            $entry .= $xml->write_tag($field_name, $row[$field_name], 4);
                        }
                    }
                    if ($entry) {
                        $content .= $xml->write_master_tag("entry", $entry, 3);
                    }
                }
                $db->free_result($query);
                if ($content) {
                    $tables .= $xml->write_master_tag("content", $content, 2);
                }
            }

            $this->lansuite .= $xml->write_master_tag("table", $tables, 1);
        }
    }


    public function ExportMod($mod, $e_struct = null, $e_cont = null, $e_trans = null)
    {
        global $xml, $db;

        if (is_dir("modules/$mod/mod_settings/")) {
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
                    if ($table_structure != '') {
                        $this->ExportTable($table_name, $e_struct, $e_cont);
                    }
                }
            }
        }

        if ($e_trans) {
            $this->ExportTranslation($mod);
            // Export non-module-related translations
            if ($mod == 'install') {
                $this->ExportTranslation('System');
                $this->ExportTranslation('DB');
            }
        }
    }


    public function ExportAllTables($e_struct = null, $e_cont = null)
    {
        global $db;

        $this->LSTableHead();

        $res = $db->qry("SELECT * FROM %prefix%modules ORDER BY changeable DESC, caption");
        while ($row = $db->fetch_array($res)) {
            $this->ExportMod($row["name"], $e_struct, $e_cont, 0);
        }
        $db->free_result($res);

        $this->LSTableFoot();
    }


    public function SendExport($out, $name)
    {
        global $func;

        header('Content-Type: application/octetstream; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$name\"");
        header('Content-Length: '. strlen($out));
        header('Expires: 0');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: public');

        echo $out;
    }


    public function ExportCSVComplete($sep)
    {
        global $db, $config, $func, $party;

        include_once("modules/seating/class_seat.php");
        $seat2 = new seat2();

        $user_export = $config['lansuite']['version']." CSV Export\r\nParty: ". $_SESSION['party_info']['name'] ."\r\nExportdate: ".$func->unixstamp2date(time(), 'daydatetime')."\r\n\r\n";

        $user_export .= "tmp userid;email;username;name;firstname;sex;street;hnr;plz;city;md5pwd;usertype;paid;seatcontrol;clan;clanurl;wwclid;nglid;checkin;checkout;signondate;paiddate;birthday;seatblock;seat;ip;comment\r\n";

        $query = $db->qry("SELECT u.*, c.name AS clan, c.url AS clanurl, p.paid, p.checkin, p.checkout, p.signondate, p.seatcontrol, p.paiddate
			FROM %prefix%user AS u
			LEFT JOIN %prefix%party_user AS p ON p.user_id = u.userid
			LEFT JOIN %prefix%clan AS c ON u.clanid = c.clanid
			WHERE p.party_id = %int%
			", $party->party_id);
        while ($row = $db->fetch_array($query)) {
            $user_export .= $row["userid"].$sep;
            $user_export .= $row["email"].$sep;

            $username = str_replace("&gt;", "", $row["username"]);
            $username = str_replace("&lt;", "", $username);
            $username = str_replace("&gt", "", $username);
            $username = str_replace("&lt", "", $username);
            $username = trim($username);

            $user_export .= $username.$sep;
            $user_export .= $row["name"].$sep;
            $user_export .= $row["firstname"].$sep;
            $user_export .= $row["sex"].$sep;
            $user_export .= $row["street"].$sep;
            $user_export .= $row["hnr"].$sep;
            $user_export .= $row["plz"].$sep;
            $user_export .= $row["city"].$sep;

            $user_export .= $row["password"].$sep;
            $user_export .= $row["type"].$sep;
            $user_export .= $row["paid"].$sep;
#			$user_export .= $row["paidcash"].$sep;
            $user_export .= $row["seatcontrol"].$sep;

            $user_export .= $row["clan"].$sep;
            $user_export .= $row["clanurl"].$sep;
            $user_export .= $row["wwclid"].$sep;
            $user_export .= $row["nglid"].$sep;
            $user_export .= $row["checkin"].$sep;
            $user_export .= $row["checkout"].$sep;
            $user_export .= $row["signondate"].$sep;
            $user_export .= $row["paiddate"].$sep;
            $user_export .= $row["birthday"].$sep;

            // seat
            $row_seat = $db->qry_first("SELECT blockid, col, row, ip FROM %prefix%seat_seats WHERE userid=%int% AND status = 2", $row["userid"]);
            $blockid  = $row_seat["blockid"];
            if ($blockid != "") {
                $row_block    = $db->qry_first("SELECT orientation, name FROM %prefix%seat_block WHERE blockid=%int%", $blockid);
                $seatindex = $seat2->CoordinateToName($row_seat["col"] + 1, $row_seat["row"], $row_block["orientation"]);
                $user_export .= $row_block["name"].$sep;
                $user_export .= $seatindex.$sep;
            } else {
                $user_export .= $sep.$sep;
            }

            $user_export .= $row_seat["ip"].$sep;
            $user_export .= $row["comment"].$sep;
            $user_export .= "\r\n";
        }

        return $user_export;
    }



    public function ExportCSVSticker($sep)
    {
        global $db, $config, $func, $party;

        include_once("modules/seating/class_seat.php");
        $seat2 = new seat2();

        $user_export = $config['lansuite']['version']." CSV Export\r\nParty: ".$config['lanparty']['name']."\r\nExportdate: ".$func->unixstamp2date(time(), 'daydatetime')."\r\n\r\n";

        $user_export .= "username;name;firstname;clan;seatblock;seat;ip\r\n";
        $query = $db->qry("SELECT u.*, c.name AS clan, c.url AS clanurl, p.paid, p.checkin, p.checkout, p.signondate, p.seatcontrol
			FROM %prefix%user AS u
			LEFT JOIN %prefix%party_user AS p ON p.user_id = u.userid
			LEFT JOIN %prefix%clan AS c ON u.clanid = c.clanid
			WHERE p.party_id = %int%
			", $party->party_id);

        while ($row = $db->fetch_array($query)) {
            $username = str_replace("&gt;", "", $row["username"]);
            $username = str_replace("&lt;", "", $username);
            $username = str_replace("&gt", "", $username);
            $username = str_replace("&lt", "", $username);
            $username = trim($username);

            $user_export .= $username.$sep;
            $user_export .= $row["name"].$sep;
            $user_export .= $row["firstname"].$sep;
            $user_export .= $row["clan"].$sep;

            // seat
            $row_seat = $db->qry_first("SELECT blockid, col, row, ip FROM %string% WHERE userid=%int% AND status = 2'", $GLOBALS['config']['tables']['seat_seats'], $row["userid"]);
            $blockid  = $row_seat["blockid"];
            if ($blockid != "") {
                $row_block    = $db->qry_first("SELECT orientation, name FROM %string% WHERE blockid=%int%", $GLOBALS['config']['tables']['seat_block'], $blockid);
                $seatindex = $seat2->CoordinateToName($row_seat["col"] + 1, $row_seat["row"], $row_block["orientation"]);
                $user_export .= $row_block["name"].$sep;
                $user_export .= $seatindex.$sep;
            }

            $user_export .= $row_seat["ip"].$sep;
            $user_export .= "\r\n";
        } // end while

        return $user_export;
    }


    public function ExportCSVCard($sep)
    {
        global $db, $config, $func, $party;

        include_once("modules/seating/class_seat.php");
        $seat2 = new seat2();

        $user_export = $config['lansuite']['version']." CSV Export\r\nParty: ".$config['lanparty']['name']."\r\nExportdate: ".$func->unixstamp2date(time(), 'daydatetime')."\r\n\r\n";

        $user_export .= "username;name;firstname;clan;seatblock;col;row;seat;ip\n";
    
        $query = $db->qry("SELECT s.* FROM %prefix%seat_seats AS s
      LEFT JOIN %prefix%seat_block AS b ON s.blockid = b.blockid
      WHERE b.party_id = %int% AND s.status = 2
      ORDER BY s.blockid", $party->party_id);
        while ($row_seat = $db->fetch_array($query)) {
            $userid = $row_seat["userid"];

            $row = $db->qry_first("SELECT u.*, c.name AS clan, c.url AS clanurl, p.paid, p.checkin, p.checkout, p.signondate, p.seatcontrol
        FROM %prefix%user AS u
        LEFT JOIN %prefix%party_user AS p ON p.user_id = u.userid
  			LEFT JOIN %prefix%clan AS c ON u.clanid = c.clanid
        WHERE u.userid=%int%
        ", $userid);
      
            $username = str_replace("&gt;", "", $row["username"]);
            $username = str_replace("&lt;", "", $username);
            $username = str_replace("&gt", "", $username);
            $username = str_replace("&lt", "", $username);
            $username = trim($username);
            $user_export .= $username.$sep;
            $user_export .= $row["name"].$sep;
            $user_export .= $row["firstname"].$sep;
            $user_export .= $row["clan"].$sep;
      
            $blockid  = $row_seat["blockid"];
            $row_block    = $db->qry_first("SELECT orientation, name FROM %prefix%seat_block WHERE blockid=%int%", $blockid);
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


    public function ExportExtInc($filename)
    {
        include_once('ext_scripts/archive.php');

        $zip = new gzip_file($filename);
        $zip->set_options(array('basedir' => '.', 'overwrite' => 1, 'level' => 1, 'inmemory' => 1));
        $zip->add_files(array('ext_inc'));
        #$zip->exclude_files("ext_inc/.svn/*");
        $zip->create_archive();

        header('Content-Type: application/octetstream; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $zip->download_file();
    
        if (count($zip->errors) > 0) {
            return false;
        }
        return true;
    }
    
    public function SaveExport($path)
    {
        global $xml;
        
        $this->output .= $xml->write_master_tag("lansuite", $this->lansuite, 0);
        $file = fopen($path, 'w');
        fwrite($file, $this->output);
        fclose($file);
    }
} // END CLASS
