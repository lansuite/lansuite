<?php

$xml = new xml();

class Import
{
    public $xml_content;
    public $xml_content_lansuite;
    public $table_state = array();
    public $installed_tables = array();

    // Constructor
    public function __construct()
    {
        global $db;

        // Get Array of installed tables
        if ($db->success) {
            $res = $db->qry('SHOW TABLES');
            while ($row = $db->fetch_array($res)) {
                array_push($this->installed_tables, $row[0]);
            }
            $db->free_result($res);
        }
    }

    public function GetUploadFileType($usr_file_name)
    {
        $file_type = substr($usr_file_name, strrpos($usr_file_name, ".") + 1, strlen($usr_file_name));
        return $file_type;
    }



    public function GetImportHeader($tmp_file_name)
    {
        global $xml;

        ## Open XML-File
        $xml_file    = fopen($tmp_file_name, "r");
#		$this->xml_content = utf8_encode(fread($xml_file, filesize($tmp_file_name)));
        $this->xml_content = fread($xml_file, filesize($tmp_file_name));
        fclose($xml_file);

    // Due to a bug in PHP "5.2.4-2ubuntu5.7", fread seams to replace all ' with ''. So lets fix this:
    // 120428: Removed Fix again, since it cause Bugs with new MySQL Versions
    // See: http://lansuite.orgapage.de/index.php?mod=bugtracker&bugid=1059
    #$this->xml_content = str_replace("''", "'", $this->xml_content);

        ## Get Header-Tag
        $header = "";
        $this->xml_content_lansuite = $xml->getFirstTagContent("LANsurfer", $this->xml_content, 0);
        if ($this->xml_content_lansuite) {
            $header = $xml->getFirstTagContent("LANsurfer_header", $this->xml_content_lansuite, 0);
        } else {
            $this->xml_content_lansuite = $xml->getFirstTagContent("lansuite", $this->xml_content, 0);
            $header = $xml->getFirstTagContent("lansuite_header", $this->xml_content_lansuite, 0);
            if (!$header) {
                $header = $xml->getFirstTagContent("header", $this->xml_content_lansuite, 0);
            }
        }

        if ($header) {
            $import["version"]    = $xml->getFirstTagContent("version", $header);
            $import["filetype"] = $xml->getFirstTagContent("filetype", $header);
            $import["source"]    = $xml->getFirstTagContent("source", $header);
            $import["date"]    = $xml->getFirstTagContent("date", $header);
            $import["event"]    = $xml->getFirstTagContent("event", $header);
        }

        return $import;
    }


    public function ImportXML($rewrite = null)
    {
        global $xml, $db, $config, $func;

        $tables = $xml->getTagContentArray("table", $this->xml_content_lansuite);
        foreach ($tables as $table) {
            // Get Table-Head-Data from XML-File
            $table_head = $xml->getFirstTagContent("table_head", $table, 0);
            $table_name = $xml->getFirstTagContent("name", $table_head);
            #$this->table_names[] = $table_name;

            $table_found = false;

            // If Rewrite: Drop current table
            if ($rewrite) {
                $db->qry_first("DROP TABLE IF EXISTS %prefix%%plain%", $table_name);
            } else {
                // Search current XML-Table in installed tables
                $table_found = in_array($config['database']['prefix'] . $table_name, $this->installed_tables);
                if ($table_found) {
                    $this->table_state[] = "exist";
                }
            }

            // Get current table-structure from DB, to compare with XML-File
            $db_fields = array();
            $FieldsForContent = array();
            if ($table_found) {
                // Read fields from DB
                $query = $db->qry("DESCRIBE %prefix%%plain%", $table_name);
                while ($row = $db->fetch_array($query)) {
                    $db_fields[] = $row;
                    $FieldsForContent[] = $row['Field'];
                }
                $db->free_result($query);

                // Read indizes from DB
                $DBPrimaryKeys = array();
                $DBUniqueKeys = array();
                $DBIndizes = array();
                $DBFulltext = array();
                $ResIndizes = $db->qry("SHOW INDEX FROM %prefix%%plain%", $table_name);
                while ($RowIndizes = $db->fetch_array($ResIndizes)) {
                    if ($RowIndizes['Key_name'] == 'PRIMARY') {
                        $DBPrimaryKeys[] = $RowIndizes['Column_name'];
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
            }


            // Import Table-Structure
            $field_names = array();
            $structure = $xml->getFirstTagContent("structure", $table, 0);
            if ($structure) {
                $fields = $xml->getTagContentArray("field", $structure);
                $mysql_fields = "";
                $primary_key = "";
                $unique_key = "";
  
                // Read the DB-Structure form XML-File
                if ($fields) {
                    foreach ($fields as $field) {
                    // Read XML-Entries
                        $name = $xml->getFirstTagContent("name", $field);
                        $type = $xml->getFirstTagContent("type", $field);
                        $null_xml = $xml->getFirstTagContent("null", $field);
                        ($null_xml != 'NULL' and $null_xml != 'YES')? $null = "NOT NULL" : $null = "NULL";
                        $key = $xml->getFirstTagContent("key", $field);
                        $default_xml = $xml->getFirstTagContent("default", $field);
                        $extra = $xml->getFirstTagContent("extra", $field);
                        $foreign_key = $xml->getFirstTagContent("foreign_key", $field);
                        $on_delete = $xml->getFirstTagContent("on_delete", $field);
                        $reference = $xml->getFirstTagContent("reference", $field);
                        $reference_condition = $xml->getFirstTagContent("reference_condition", $field);

                    // Set default value to 0 or '', if NOT NULL and not autoincrement
                        if ($null == 'NOT NULL' and $extra == '') {
                            if (substr($type, 0, 3) == 'int' or substr($type, 0, 7) == 'tinyint' or substr($type, 0, 9) == 'mediumint'
                            or substr($type, 0, 8) == 'smallint' or substr($type, 0, 6) == 'bigint'
                            or substr($type, 0, 7) == 'decimal' or substr($type, 0, 5) == 'float' or substr($type, 0, 6) == 'double') {
                                    $default = 'default '. (int)$default_xml;
                            } elseif ($type == 'timestamp') {
                                $default = 'default CURRENT_TIMESTAMP';
                                $default_xml = 'CURRENT_TIMESTAMP';
                                $extra = 'on update CURRENT_TIMESTAMP';
                            } elseif ($type == 'datetime' or $type == 'date' or $type == 'time' or $type == 'blob') {
                                $default = '';
                            } elseif ($type == 'text' or $type == 'tinytext' or $type == 'mediumtext' or $type == 'longtext') {
                                $default = '';
                            } else {
                                $default = "default '$default_xml'";
                            }
                        } else {
                            $default = '';
                        }

                    // Create MySQL-String to import
                        if ($key == "PRI") {
                            $primary_key .= "$name, ";
                        }
                        if ($key == "UNI") {
                            $unique_key .= ", UNIQUE KEY $name ($name)";
                        }
                        $mysql_fields .= "$name $type $null $default $extra, ";

                    // Safe Field-Names to know which fields to import content for, in the next step.
                        $field_names[] = $name;

                    // If table exists, compare XML-File with DB and check weather the DB has to be updated
                        $found_in_db = 0;
                        if ($table_found) {
                        // Search for fiels, which exist in the XML-File, but dont exist in the DB yet.
                            if ($db_fields) {
                                foreach ($db_fields as $db_field) {
                                    if ($db_field["Field"] == $name) {
                                        $found_in_db = 1;

                                // Check wheather the field in the DB differs from the one in the XML-File
                                // Change it
                                        if ($db_field['Null'] == 'NO') {
                                            $db_field['Null'] = 'NOT NULL';
                                        } // Some MySQL-Versions return 'NO' instead of ''

                                // Handle special type changes
                                // Changing int() to datetime
                                        if ($type == 'datetime' and substr($db_field["Type"], 0, 3) == 'int') {
                                            $db->qry("ALTER TABLE %prefix%$table_name CHANGE %plain% %plain%_lstmp INT", $name, $name);
                                            $db->qry("ALTER TABLE %prefix%%plain% ADD %plain% DATETIME", $table_name, $name);
                                            $db->qry("UPDATE %prefix%%plain% SET %plain% = FROM_UNIXTIME(%plain%_lstmp)", $table_name, $name, $name);
                                            $db->qry("ALTER TABLE %prefix%%plain% DROP %plain%_lstmp", $table_name, $name);


                                            // Handle structure changes in general
                                        } elseif ($db_field["Type"] != $type
                                        or (!($db_field["Null"] == $null or ($db_field["Null"] == 'YES' and $null == 'NULL')))
                                        or ($db_field["Default"] != $default_xml and !($db_field["Default"] == 0 and $default_xml == '') and !($db_field["Default"] == '' and $default_xml == 0))
                                        or $db_field["Extra"] != $extra) {
                                            $db->qry("ALTER TABLE %prefix%%plain% CHANGE %plain% %plain% %plain% %plain% %plain% %plain%", $table_name, $name, $name, $type, $null, $default, $extra);
                    /*
                                             // Differece-Report
                                             #echo "ALTER TABLE $table_name CHANGE $name $name $type $null $default $extra";
                                             if ($db_field["Type"] != $type) $func->information($db_field["Type"] ."=". $type ." Type in $table_name $name<br>");
                                             if ($db_field["Null"] != $null) $func->information($db_field["Null"] ."=". $null ." Null in $table_name $name<br>");
                                             if ($db_field["Default"] != $default_xml) $func->information($db_field["Default"] ."=". $default_xml ." Def in $table_name $name<br>");
                                             if ($db_field["Extra"] != $extra) $func->information($db_field["Extra"] ."=". $extra ." Extra in $table_name $name<br>");
                    */
                                        }
                                        break;
                                    }
                                }
                            }

                        //// Index-Check
                        // Drop keys, which no longer exist in XML
                            if ($key == '') {
                                if (in_array($name, $DBPrimaryKeys)) {
                                    $db->qry('ALTER TABLE %prefix%%plain% DROP PRIMARY KEY', $table_name);
                                    array_splice($DBPrimaryKeys, array_search($name, $DBPrimaryKeys));
                                }
                                if (in_array($name, $DBUniqueKeys) or in_array($name, $DBIndizes) or in_array($name, $DBFulltext)) {
                                    $db->qry("ALTER TABLE %prefix%%plain% DROP INDEX %plain%", $table_name, $name);
                                }

                                // Drop keys, which have changed type in XML. They will be re-created beneath
                            } elseif ($key == 'PRI') {
                                if (in_array($name, $DBUniqueKeys) or in_array($name, $DBIndizes) or in_array($name, $DBFulltext)) {
                                    $db->qry("ALTER TABLE %prefix%%plain% DROP INDEX %plain%", $table_name, $name);
                                }
                            } elseif ($key == 'UNI') {
                                if (in_array($name, $DBPrimaryKeys)) {
                                    $db->qry('ALTER TABLE %prefix%%plain% DROP PRIMARY KEY', $table_name);
                                    array_splice($DBPrimaryKeys, array_search($name, $DBPrimaryKeys));
                                }
                                if (in_array($name, $DBIndizes) or in_array($name, $DBFulltext)) {
                                    $db->qry("ALTER TABLE %prefix%%plain% DROP INDEX %plain%", $table_name, $name);
                                }
                            } elseif ($key == 'IND') {
                                if (in_array($name, $DBPrimaryKeys)) {
                                    $db->qry('ALTER TABLE %prefix%%plain% DROP PRIMARY KEY', $table_name);
                                    array_splice($DBPrimaryKeys, array_search($name, $DBPrimaryKeys));
                                }
                                if (in_array($name, $DBUniqueKeys) or in_array($name, $DBFulltext)) {
                                    $db->qry("ALTER TABLE %prefix%%plain% DROP INDEX %plain%", $table_name, $name);
                                }
                            } elseif ($key == 'FUL') {
                                if (in_array($name, $DBPrimaryKeys)) {
                                    $db->qry('ALTER TABLE %prefix%%plain% DROP PRIMARY KEY', $table_name);
                                    array_splice($DBPrimaryKeys, array_search($name, $DBPrimaryKeys));
                                }
                                if (in_array($name, $DBUniqueKeys) or in_array($name, $DBIndizes)) {
                                    $db->qry("ALTER TABLE %prefix%%plain% DROP INDEX %plain%", $table_name, $name);
                                }
                            }
                        // Primary Key in XML but not in DB
                        // Attention when adding a double-primary-key it is added one after another. So some lines will be droped!
                            if ($key == 'PRI' and !in_array($name, $DBPrimaryKeys)) {
                                // No key in DB, yet
                                $DBPrimaryKeys[] = $name;
                                // count = 1, because added to var one line before, IGNORE is to drop non-uniqe lines
                                if (count($DBPrimaryKeys) == 1) {
                                    $db->qry("ALTER IGNORE TABLE %prefix%%plain% ADD PRIMARY KEY (%plain%)", $table_name, $name);
                                } // Key in DB replaced/extended
                                else {
                                    $priKeys = implode(', ', $DBPrimaryKeys);
                                    $db->qry("ALTER IGNORE TABLE %prefix%%plain% DROP PRIMARY KEY, ADD PRIMARY KEY (%plain%)", $table_name, $priKeys);
                                }
                            }

                        // Unique keys in XML but not in DB
                            if ($key == 'UNI' and !in_array($name, $DBUniqueKeys)) {
                                if (in_array($name, $DBIndizes) or in_array($name, $DBFulltext)) {
                                    $db->qry("ALTER TABLE %prefix%%plain% DROP INDEX %plain%", $table_name, $name);
                                }
                                // IGNORE is to drop non-uniqe lines
                                $db->qry("ALTER IGNORE TABLE %prefix%%plain% ADD UNIQUE (%plain%)", $table_name, $name);
                            }

                        // Index in XML but not in DB
                            if ($key == 'IND' and !in_array($name, $DBIndizes)) {
                                if (in_array($name, $DBUniqueKeys) or in_array($name, $DBFulltext)) {
                                    $db->qry("ALTER TABLE %prefix%%plain% DROP INDEX %plain%", $table_name, $name);
                                }
                                $db->qry("ALTER TABLE %prefix%%plain% ADD INDEX (%plain%)", $table_name, $name);
                            }

                        // Fulltext in XML but not in DB
                            if ($key == 'FUL' and !in_array($name, $DBFulltext)) {
                                if (in_array($name, $DBUniqueKeys) or in_array($name, $DBIndizes)) {
                                    $db->qry("ALTER TABLE %prefix%%plain% DROP INDEX %plain%", $table_name, $name);
                                }
                                ## TODO: if ($type == 'text' or $type == 'longtext' or substr($type, 0, 7) == 'varchar')
                                $db->qry("ALTER TABLE %prefix%%plain% ADD FULLTEXT (%plain%)", $table_name, $name);
                            }


                        // If a key was not found in the DB, but in the XML-File -> Add it!
                            if (!$found_in_db) {
                                // If auto_increment is used for this key, add this key as primary, unique key
                                if ($extra == "auto_increment") {
                                    $db->qry(
                                        "ALTER TABLE %prefix%%plain% ADD %plain% %plain% %plain% %plain% %plain%, ADD PRIMARY KEY (%plain%), ADD UNIQUE (%plain%)",
                                        $table_name,
                                        $name,
                                        $type,
                                        $null,
                                        $default,
                                        $extra,
                                        $name,
                                        $name
                                    );
                                } else {
                                    $db->qry("ALTER TABLE %prefix%%plain% ADD %plain% %plain% %plain% %plain% %plain%", $table_name, $name, $type, $null, $default, $extra);
                                }
                            }
                        }
                
                    // Foreign Key references
                        if ($foreign_key) {
                            list($foreign_table, $foreign_key_name) = explode('.', $foreign_key, 2);
                            $row = $db->qry_first(
                                'SELECT 1 AS found, on_delete FROM %prefix%ref WHERE
              pri_table = %string% AND pri_key = %string% AND foreign_table = %string% AND foreign_key = %string%',
                                $table_name,
                                $name,
                                $foreign_table,
                                $foreign_key_name
                            );
                            if ($row['on_delete'] != $on_delete) {
                                $db->qry(
                                    'DELETE FROM %prefix%ref WHERE
                pri_table = %string% AND pri_key = %string% AND foreign_table = %string% AND foreign_key = %string%',
                                    $table_name,
                                    $name,
                                    $foreign_table,
                                    $foreign_key_name
                                );
                                    $row['found'] = 0;
                            }
                            if (!$row['found']) {
                                $db->qry(
                                    'INSERT INTO %prefix%ref SET
              pri_table = %string%, pri_key = %string%, foreign_table = %string%, foreign_key = %string%, on_delete = %string%',
                                    $table_name,
                                    $name,
                                    $foreign_table,
                                    $foreign_key_name,
                                    $on_delete
                                );
                            }
                        }
                        if ($reference) {
                            list($reference_table, $reference_key) = explode('.', $reference, 2);

                            $row = $db->qry_first(
                                'SELECT 1 AS found FROM %prefix%ref WHERE
  				    pri_table = %string% AND pri_key = %string% AND foreign_table = %string% AND foreign_key = %string% AND foreign_condition = %string%',
                                $reference_table,
                                $reference_key,
                                $table_name,
                                $name,
                                $reference_condition
                            );
                            if (!$row['found']) {
                                $db->qry(
                                    'INSERT INTO %prefix%ref SET
              pri_table = %string%, pri_key = %string%, foreign_table = %string%, foreign_key = %string%, foreign_condition = %string%',
                                    $reference_table,
                                    $reference_key,
                                    $table_name,
                                    $name,
                                    $reference_condition
                                );
                            }
                        }
                    }
                }

                // Search for fields, which exist in the XML-File no more, but still in DB.
                // Delete them from the DB
                if ($table_found and $db_fields) {
                    foreach ($db_fields as $db_field) {
                        if (!in_array($db_field['Field'], $field_names)) {
                            $db->qry("ALTER TABLE %prefix%%plain% DROP `%plain%`", $table_name, $db_field["Field"]);
                        }
                    }
                }

                if (!$table_found) {
                    $mysql_fields = substr($mysql_fields, 0, strlen($mysql_fields) - 2);
                    if ($primary_key) {
                        $primary_key = ", PRIMARY KEY (". substr($primary_key, 0, strlen($primary_key) - 2) .")";
                    }

                    // Create a new table, if it does not exist yet, or has been dropped above, due to rewrite
                    $db->qry("CREATE TABLE IF NOT EXISTS %prefix%%plain% ($mysql_fields %plain% $unique_key) ENGINE = MyISAM CHARACTER SET utf8", $table_name, $primary_key);
                    #$db->qry("REPLACE INTO %prefix%table_names SET name = %string%", $table_name);

                    // Set Table-Charset to UTF-8
                    # Needed??
                    // Do not use, for some old MySQL-Versions are causing trouble, with that statement.
#          			$db->qry_first("ALTER TABLE %prefix%%plain% DEFAULT CHARACTER SET utf8", $table_name);

                    // Add to installed tables
                    array_push($this->installed_tables, $config["database"]["prefix"]. $table_name);
                }
            }

            // Import Table-Content
            $content = $xml->getFirstTagContent("content", $table, 0);
            $entrys = $xml->getTagContentArray("entry", $content);

            if ($entrys) {
                // Update Content only, if no row exists, or table has PrimKey set
                $EntriesFound = array();
                $qry = $db->qry("SELECT * FROM %prefix%%plain%", $table_name);
                if (count($DBPrimaryKeys) > 0 or $db->num_rows($qry) == 0) {
                    if (count($DBPrimaryKeys) > 0) {
                        while ($row = $db->fetch_array($qry)) {
                            $EntriesFound[] = $row[$DBPrimaryKeys[0]];
                        }
                    }

                    foreach ($entrys as $entry) {
                        $mysql_entries = '';
                        $FoundValueInDB = 0;
                        if (!$field_names) {
                            $field_names = $FieldsForContent;
                        } // Get names from DB, if not in XML-Structure
                        if ($field_names) {
                            foreach ($field_names as $field_name) {
                                $value = $xml->getFirstTagContent($field_name, $entry, 1);
                                if ($value != '') {
                                    $mysql_entries .= "$field_name = '". $func->escape_sql($value) ."', ";
                                }
                                if ($field_name == $DBPrimaryKeys[0] and in_array($value, $EntriesFound)) {
                                    $FoundValueInDB = 1;
                                }
                            }
                        }

                        if (!$FoundValueInDB) {
                            $mysql_entries = substr($mysql_entries, 0, strlen($mysql_entries) - 2);
                            $db->qry_first("REPLACE INTO %prefix%%plain% SET %plain%", $table_name, $mysql_entries);
                        }
                    }
                }
                $db->free_result($qry);
            }

            if ($rewrite) {
                $this->table_state[] = "rewrite";
            }

            // Optimize table
            $db->qry_first("OPTIMIZE TABLE `%plain%`", $table_name);
            
            // Move usersettings to user
            if ($table_name == 'user' and in_array('usersettings', $this->installed_tables)) {
                $res = $db->qry("SELECT s.userid, s.design, s.avatar_path, s.signature, s.show_me_in_map, s.lsmail_alert,
          u.design AS design2, u.avatar_path AS avatar_path2, u.signature AS signature2, u.show_me_in_map AS show_me_in_map2, u.lsmail_alert AS lsmail_alert2
          FROM %prefix%user AS u
          LEFT JOIN %prefix%usersettings AS s ON s.userid = u.userid
          ");
                while ($row = $db->fetch_array($res)) {
                    if ($row['design'] != '' and $row['design2'] == '') {
                        $db->qry('UPDATE %prefix%user SET design = %string% WHERE userid = %int%', $row['design'], $row['userid']);
                        $db->qry('UPDATE %prefix%usersettings SET design = \'\' WHERE userid = %int%', $row['userid']);
                    }
                    if ($row['avatar_path'] != '' and $row['avatar_path2'] == '') {
                        $db->qry('UPDATE %prefix%user SET avatar_path = %string% WHERE userid = %int%', $row['avatar_path'], $row['userid']);
                        $db->qry('UPDATE %prefix%usersettings SET avatar_path = \'\' WHERE userid = %int%', $row['userid']);
                    }
                    if ($row['signature'] != '' and $row['designsignature2'] == '') {
                        $db->qry('UPDATE %prefix%user SET signature = %string% WHERE userid = %int%', $row['signature'], $row['userid']);
                        $db->qry('UPDATE %prefix%usersettings SET signature = \'\' WHERE userid = %int%', $row['userid']);
                    }
                    if ($row['show_me_in_map'] != '' and $row['show_me_in_map2'] == '') {
                        $db->qry('UPDATE %prefix%user SET show_me_in_map = %int% WHERE userid = %int%', $row['show_me_in_map'], $row['userid']);
                        $db->qry('UPDATE %prefix%usersettings SET show_me_in_map = 0 WHERE userid = %int%', $row['userid']);
                    }
                    if ($row['lsmail_alert'] != '' and $row['lsmail_alert2'] == '') {
                        $db->qry('UPDATE %prefix%user SET lsmail_alert = %int% WHERE userid = %int%', $row['lsmail_alert'], $row['userid']);
                        $db->qry('UPDATE %prefix%usersettings SET lsmail_alert = 0 WHERE userid = %int%', $row['userid']);
                    }
                }
            }
        }
    }


    public function ImportLanSurfer($del_db, $replace, $no_seat, $signon, $comment)
    {
        global $xml, $db, $config, $party, $cfg;

        // Delete User-Table
        if ($del_db) {
            $db->qry("TRUNCATE TABLE %prefix%user");
            $db->qry("TRUNCATE TABLE %prefix%usersettings");
        }

        // Getting all data in Usertags
        // Mergeing all <users>-Blocks togheter
        $users_blocks    = $xml->get_tag_content_combine("users", $this->xml_content_lansuite);
        $users        = $xml->getTagContentArray("user", $users_blocks);

        // now transforming the array and reading the user-specific data in sub-<user> tags like name, clan etc.
        foreach ($users as $xml_user) {
            $users_to_import[] = array(    username =>        $xml->getFirstTagContent("username", $xml_user),
            firstname =>        $xml->getFirstTagContent("firstname", $xml_user),
            name =>        $xml->getFirstTagContent("name", $xml_user),
            clan =>        $xml->getFirstTagContent("clan", $xml_user),
            type =>        $xml->getFirstTagContent("type", $xml_user),
            paid =>        $xml->getFirstTagContent("paid", $xml_user),
            password =>        $xml->getFirstTagContent("password", $xml_user),
            email =>        $xml->getFirstTagContent("email", $xml_user),
            wwclid =>        $xml->getFirstTagContent("wwclid", $xml_user),
            wwclclanid =>    $xml->getFirstTagContent("wwclclanid", $xml_user),
            clanurl =>        $xml->getFirstTagContent("homepage", $xml_user));
        } // foreach - users

        // Putting all <seat_blocks>-tags into an array
        $seat_blocks_blocks    = $xml->get_tag_content_combine("seat_blocks", $this->xml_content_lansuite);
        $blocks        = $xml->getTagContentArray("block", $seat_blocks_blocks);

        if ($blocks) {
            foreach ($blocks as $xml_block) {
                unset($seps_to_import);
                unset($seats_to_import);

            // Seats in this block
                $seats_in_this_block    = $xml->get_tag_content_combine("seat_seats", $xml_block);
                $seats            = $xml->getTagContentArray("seat", $seats_in_this_block);

                if (is_array($seats)) {
                    foreach ($seats as $xml_seat) {
                        $seats_to_import[] = array(col =>        $xml->getFirstTagContent("col", $xml_seat),
                        row =>        $xml->getFirstTagContent("row", $xml_seat),
                        status =>    $xml->getFirstTagContent("status", $xml_seat),
                        owner =>    $xml->getFirstTagContent("owner", $xml_seat),
                        ipaddress =>    $xml->getFirstTagContent("ipaddress", $xml_seat));
                    }
                } // foreach - seats

            // Sepeartors in this block
                $seps_in_this_block    = $xml->get_tag_content_combine("seat_sep", $xml_block);
                $seps            = $xml->getTagContentArray("sep", $seps_in_this_block);

                if (is_array($seps)) {
                    foreach ($seps as $xml_sep) {
                        $seps_to_import[] = array(    orientation =>    $xml->getFirstTagContent("orientation", $xml_sep),
                        value =>    $xml->getFirstTagContent("value", $xml_sep));
                    }
                } // foreach - seperators

            // Seatblockdata
                $seat_blocks_to_import[] = array(    name =>        $xml->getFirstTagContent("name", $xml_block),
                rows =>            $xml->getFirstTagContent("rows", $xml_block),
                cols =>            $xml->getFirstTagContent("cols", $xml_block),
                orientation =>        $xml->getFirstTagContent("orientation", $xml_block),
                remark =>        $xml->getFirstTagContent("remark", $xml_block),
                text_tl =>        $xml->getFirstTagContent("text_tl", $xml_block),
                text_tc =>        $xml->getFirstTagContent("text_tc", $xml_block),
                text_tr =>        $xml->getFirstTagContent("text_tr", $xml_block),
                text_lt =>        $xml->getFirstTagContent("text_lt", $xml_block),
                text_lc =>        $xml->getFirstTagContent("text_lc", $xml_block),
                text_lb =>        $xml->getFirstTagContent("text_lb", $xml_block),
                text_rt =>        $xml->getFirstTagContent("text_rt", $xml_block),
                text_rc =>        $xml->getFirstTagContent("text_rc", $xml_block),
                text_rb =>        $xml->getFirstTagContent("text_rb", $xml_block),
                text_bl =>        $xml->getFirstTagContent("text_bl", $xml_block),
                text_bc =>        $xml->getFirstTagContent("text_bc", $xml_block),
                text_br =>        $xml->getFirstTagContent("text_br", $xml_block),
                seats =>        $seats_to_import,
                seps =>            $seps_to_import);
            }
        } // foreach - seatblocks


        /* DB INPUT */
        if (is_array($users_to_import) == true) {
            #			$db->qry("DELETE FROM lansuite_usersettings");

            foreach ($users_to_import as $user) {
                $email        = $user['email'];
                $username    = $xml->convertinputstr($user['username']);
                $name        = $xml->convertinputstr($user['name']);
                $firstname    = $xml->convertinputstr($user['firstname']);
                $clan        = $xml->convertinputstr($user['clan']);

                $type        = $user['type'];
                $paid        = $user['paid'];
                $password    = $user['password'];

                $wwclid    = $xml->convertinputstr($user['wwclid']);
                $wwclclanid    = $xml->convertinputstr($user['wwclclanid']);

                $checkin =($type > 1) ? "1" : "0";

                $skip = 0;
                $res = $db->qry("SELECT username FROM %prefix%user WHERE email = %string%", $email);
                if (($db->num_rows($res) > 0) && (!$replace)) {
                    $skip = 1;
                }

                if (!$skip) {
                    $clan_id = 0;
                    if ($clan != '') {
                        // Search clan
                        $search_clan = $db->qry_first("SELECT clanid FROM %prefix%clan WHERE name = %string%", $clan);
                        if ($search_clan['clanid'] != '') {
                            $clan_id = $search_clan['clanid'];
                        } // Insert new clan
                        else {
                            $db->qry("INSERT INTO %prefix%clan SET name = %string%, url = %string% ", $clan, $clanurl);
                            $clan_id = $db->insert_id();
                        }
                    }
                    $db->qry(
                        "REPLACE INTO %prefix%user SET email = %string%, name = %string%, username = %string%, firstname = %string%, type = %string%, clanid = %int%,
								password = %string%, wwclid = %int%, wwclclanid = %int%, comment = %string%",
                        $email,
                        $name,
                        $username,
                        $firstname,
                        $type,
                        $clan_id,
                        $password,
                        $wwclid,
                        $wwclclanid,
                        $comment
                    );
                    $id = $db->insert_id();

                    // Update Party-Signon
                    if ($signon) {
                        $party->add_user_to_party($id, 1, $paid, $checkin);
                    } else {
                        $party->delete_user_from_party($id);
                    }

                    $default_design = $config['lansuite']['default_design'];
                    $db->qry("INSERT INTO %prefix%usersettings SET userid=%int%", $id);

                    $userids[$email] = $id;
                }
            } // foreach - $users_to_import
            $confirmation .= HTML_NEWLINE . HTML_NEWLINE ."- User eintragen beendet";
        } // is array
        else {
            echo "FEHLER: USER NICHT EINGETRAGEN" .HTML_NEWLINE;
        }

        if (is_array($seat_blocks_to_import) == true and !$no_seat) {
            foreach ($seat_blocks_to_import as $block) {
                $name        = $xml->convertinputstr($block['name']);
                $rows        = $block['rows'];
                $cols        = $block['cols'];
                $orientation    = $block['orientation'];
                $remark    = $block['remark'];
                $text_tl    = $block['text_tl'];
                $text_tc    = $block['text_tc'];
                $text_tr    = $block['text_tr'];
                $text_lt    = $block['text_lt'];
                $text_lc    = $block['text_lc'];
                $text_lb    = $block['text_lb'];
                $text_rt    = $block['text_rt'];
                $text_rc    = $block['text_rc'];
                $text_rb    = $block['text_rb'];
                $text_bl    = $block['text_bl'];
                $text_bc    = $block['text_bc'];
                $text_br    = $block['text_br'];

                $db->qry(
                    "REPLACE INTO %prefix%seat_block SET name=%string%, cols=%string%, rows=%string%, orientation=%string%, remark=%string%, text_tl=%string%, text_tc=%string%,
  text_tr=%string%, text_lt=%string%, text_lc=%string%, text_lb=%string%, text_rt=%string%, text_rc=%string%, text_rb=%string%, text_bl=%string%, text_bc=%string%,
  text_br=%string%, party_id=%int%",
                    $name,
                    $cols,
                    $rows,
                    $orientation,
                    $remark,
                    $text_tl,
                    $text_tc,
                    $text_tr,
                    $text_lt,
                    $text_lc,
                    $text_lb,
                    $text_rt,
                    $text_rc,
                    $text_rb,
                    $text_bl,
                    $text_bc,
                    $text_br,
                    $cfg['signon_partyid']
                );
                $blockid = $db->insert_id();

                if (is_array($block['seps'])) {
                    foreach ($block['seps'] as $sep) {
                        $orientation    = $sep['orientation'];
                        $value        = $sep['value'];
                        $db->qry(
                            "REPLACE INTO %prefix%seat_sep SET blockid=%int%, orientation=%string%, value=%string%",
                            $blockid,
                            $orientation,
                            $value
                        );
                    }
                } // foreach - seps

                if (is_array($block['seats'])) {
                    foreach ($block['seats'] as $seat) {
                        $col        = $seat['col'];
                        $row        = $seat['row'];
                        $status    = $seat['status'];
                        $owner        = $seat['owner'];
                        $ipaddress    = $seat['ipaddress'];
                        $userid    = $userids[$owner];
                        if ($owner == "") {
                            $userid  = 0;
                        }

                        $db->qry(
                            "REPLACE INTO %prefix%seat_seats SET blockid=%int%, col=%string%, row=%string%, status=%string%, userid=%int%, ip=%string%",
                            $blockid,
                            $col,
                            $row,
                            $status,
                            $userid,
                            $ipaddress
                        );
                    }
                } // foreach - seats
            } // foreach - $seat_blocks_to_import
        } // if array
    }



    public function ImportCSV($tmp_file_name, $del_db, $replace, $signon, $comment)
    {
        global $db;

        // Delete User-Table
        if ($del_db) {
            $db->qry("TRUNCATE TABLE %prefix%user");
            $db->qry("TRUNCATE TABLE %prefix%usersettings");
        }

        $csv_file = file($tmp_file_name);
        $import = array("error" => 0, "nothing" => 0, "insert" => 0, "replace" => 0);

        foreach ($csv_file as $csv_line) {
            $csv_line = chop($csv_line);
            $csv_line = trim($csv_line);
            $csv_line = str_replace("\"", "", $csv_line);
            $csv_line = str_replace("'", "", $csv_line);
                        
            $user = explode(";", $csv_line);
            ($user[5] == "Not Paid") ? $user_paid = 0 : $user_paid = 1;

            $skip = 0;
            $res = $db->qry("SELECT username FROM %prefix%user WHERE email = %string%", $email);
            if (($db->num_rows($res) > 0) && (!$replace)) {
                $skip = 1;
            }

            if (!$skip) {
                $replace_user = $db->qry(
                    "REPLACE INTO %prefix%user SET username = %string%, clan = %string%, firstname = %string%, name = %string%, email = %string%, paid = %int%,
  type = '1', signon = %string%, comment = %string%",
                    $user[0],
                    $user[1],
                    $user[2],
                    $user[3],
                    $user[4],
                    $user_paid,
                    $signon,
                    $comment
                );
                $id = $db->insert_id();
                $db->qry("INSERT INTO %prefix%usersettings SET userid=%int%", $id);
            }

            switch (mysql_affected_rows($db->link_id)) {
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
    
    public function ImportExtInc($filename)
    {
        include_once('ext_scripts/archive.php');

        $zip = new gzip_file($filename);
        $zip->set_options(array('basedir' => '.', 'overwrite' => 1));
        $zip->extract_files();
    }
}
