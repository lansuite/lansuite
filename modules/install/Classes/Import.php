<?php

namespace LanSuite\Module\Install;

class Import
{
    /**
     * @var string
     */
    private $xml_content;

    /**
     * @var string
     */
    public $xml_content_lansuite;

    /**
     * @var array
     */
    private $table_state = [];

    /**
     * @var array
     */
    public $installed_tables = [];

    /**
     * @var \LanSuite\XML
     */
    private $xml;

    public function __construct(\LanSuite\XML $xml)
    {
        global $db;

        $this->xml = $xml;

        // Get Array of installed tables
        if ($db->success) {
            $res = $db->qry('SHOW TABLES');
            while ($row = $db->fetch_array($res)) {
                array_push($this->installed_tables, $row[0]);
            }
            $db->free_result($res);
        }
    }

    /**
     * @param string $usr_file_name
     * @return bool|string
     */
    public function GetUploadFileType($usr_file_name)
    {
        $file_type = substr($usr_file_name, strrpos($usr_file_name, ".") + 1, strlen($usr_file_name));
        return $file_type;
    }

    /**
     * @param string $tmp_file_name
     * @return mixed
     */
    public function GetImportHeader($tmp_file_name)
    {
        $xml_file = fopen($tmp_file_name, "r");
        $this->xml_content = fread($xml_file, filesize($tmp_file_name));
        fclose($xml_file);

        // Get Header-Tag
        $this->xml_content_lansuite = $this->xml->getFirstTagContent("lansuite", $this->xml_content, 0);
        $header = $this->xml->getFirstTagContent("lansuite_header", $this->xml_content_lansuite, 0);
        if (!$header) {
            $header = $this->xml->getFirstTagContent("header", $this->xml_content_lansuite, 0);
        }

        if ($header) {
            $import["version"]  = $this->xml->getFirstTagContent("version", $header);
            $import["filetype"] = $this->xml->getFirstTagContent("filetype", $header);
            $import["source"]   = $this->xml->getFirstTagContent("source", $header);
            $import["date"]     = $this->xml->getFirstTagContent("date", $header);
            $import["event"]    = $this->xml->getFirstTagContent("event", $header);
        }

        return $import;
    }

    /**
     * @param boolean $rewrite
     * @return void
     */
    public function ImportXML($rewrite = null)
    {
        global $db, $config, $func;

        $tables = $this->xml->getTagContentArray("table", $this->xml_content_lansuite);
        foreach ($tables as $table) {
            // Get Table-Head-Data from XML-File
            $table_head = $this->xml->getFirstTagContent("table_head", $table, 0);
            $table_name = $this->xml->getFirstTagContent("name", $table_head);

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
            $DBPrimaryKeys = array();
            if ($table_found) {
                // Read fields from DB
                $query = $db->qry("DESCRIBE %prefix%%plain%", $table_name);
                while ($row = $db->fetch_array($query)) {
                    $db_fields[] = $row;
                    $FieldsForContent[] = $row['Field'];
                }
                $db->free_result($query);

                // Read indizes from DB
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
            $structure = $this->xml->getFirstTagContent("structure", $table, 0);
            if ($structure) {
                $fields = $this->xml->getTagContentArray("field", $structure);
                $mysql_fields = "";
                $primary_key = "";
                $unique_key = "";
  
                // Read the DB-Structure form XML-File
                if ($fields) {
                    foreach ($fields as $field) {
                        // Read XML-Entries
                        $name = $this->xml->getFirstTagContent("name", $field);
                        $type = $this->xml->getFirstTagContent("type", $field);
                        $null_xml = $this->xml->getFirstTagContent("null", $field);
                        ($null_xml != 'NULL' and $null_xml != 'YES')? $null = "NOT NULL" : $null = "NULL";
                        $key = $this->xml->getFirstTagContent("key", $field);
                        $default_xml = $this->xml->getFirstTagContent("default", $field);
                        $extra = $this->xml->getFirstTagContent("extra", $field);
                        $foreign_key = $this->xml->getFirstTagContent("foreign_key", $field);
                        $on_delete = $this->xml->getFirstTagContent("on_delete", $field);
                        $reference = $this->xml->getFirstTagContent("reference", $field);
                        $reference_condition = $this->xml->getFirstTagContent("reference_condition", $field);

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
                                        }
                                        break;
                                    }
                                }
                            }

                            // Index-Check
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
                                // Key in DB replaced/extended
                                } else {
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
                                    '
                                  DELETE FROM %prefix%ref
                                  WHERE
                                    pri_table = %string%
                                    AND pri_key = %string%
                                    AND foreign_table = %string%
                                    AND foreign_key = %string%',
                                    $table_name,
                                    $name,
                                    $foreign_table,
                                    $foreign_key_name
                                );
                                    $row['found'] = 0;
                            }
                            if (!$row['found']) {
                                $db->qry(
                                    '
                                  INSERT INTO %prefix%ref
                                  SET
                                    pri_table = %string%,
                                    pri_key = %string%,
                                    foreign_table = %string%,
                                    foreign_key = %string%,
                                    on_delete = %string%',
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
                                '
                              SELECT
                                1 AS found
                              FROM %prefix%ref
                              WHERE
                                pri_table = %string%
                                AND pri_key = %string%
                                AND foreign_table = %string%
                                AND foreign_key = %string%
                                AND foreign_condition = %string%',
                                $reference_table,
                                $reference_key,
                                $table_name,
                                $name,
                                $reference_condition
                            );
                            if (!$row['found']) {
                                $db->qry(
                                    '
                                  INSERT INTO %prefix%ref
                                  SET
                                    pri_table = %string%,
                                    pri_key = %string%,
                                    foreign_table = %string%,
                                    foreign_key = %string%,
                                    foreign_condition = %string%',
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

                    // Add to installed tables
                    array_push($this->installed_tables, $config["database"]["prefix"]. $table_name);
                }
            }

            // Import Table-Content
            $content = $this->xml->getFirstTagContent("content", $table, 0);
            $entrys = $this->xml->getTagContentArray("entry", $content);

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
                                $value = $this->xml->getFirstTagContent($field_name, $entry, 1);
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
            $db->qry_first("OPTIMIZE TABLE `%prefix%%plain%`", $table_name);
            
            // Move usersettings to user
            if ($table_name == 'user' and in_array('usersettings', $this->installed_tables)) {
                $res = $db->qry("
                  SELECT
                    s.userid,
                    s.design,
                    s.avatar_path,
                    s.signature,
                    s.show_me_in_map,
                    s.lsmail_alert,
                    u.design AS design2,
                    u.avatar_path AS avatar_path2,
                    u.signature AS signature2,
                    u.show_me_in_map AS show_me_in_map2,
                    u.lsmail_alert AS lsmail_alert2
                  FROM %prefix%user AS u
                  LEFT JOIN %prefix%usersettings AS s ON s.userid = u.userid");
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

    /**
     * @param boolean $del_db
     * @param boolean $replace
     * @param boolean $no_seat
     * @param boolean $signon
     * @param string $comment
     * @return void
     */
    public function ImportLanSuite($del_db, $replace, $no_seat, $signon, $comment)
    {
        global $db, $party, $cfg;

        // Delete User-Table
        if ($del_db) {
            $db->qry("TRUNCATE TABLE %prefix%user");
            $db->qry("TRUNCATE TABLE %prefix%usersettings");
        }

        // Getting all data in Usertags
        // Merging all <users>-Blocks togheter
        $users_blocks = $this->xml->get_tag_content_combine("users", $this->xml_content_lansuite);
        $users        = $this->xml->getTagContentArray("user", $users_blocks);

        // now transforming the array and reading the user-specific data in sub-<user> tags like name, clan etc.
        foreach ($users as $xml_user) {
            $users_to_import[] = [
                'username'      => $this->xml->getFirstTagContent("username", $xml_user),
                'firstname'     => $this->xml->getFirstTagContent("firstname", $xml_user),
                'name'          => $this->xml->getFirstTagContent("name", $xml_user),
                'clan'          => $this->xml->getFirstTagContent("clan", $xml_user),
                'type'          => $this->xml->getFirstTagContent("type", $xml_user),
                'paid'          => $this->xml->getFirstTagContent("paid", $xml_user),
                'password'      => $this->xml->getFirstTagContent("password", $xml_user),
                'email'         => $this->xml->getFirstTagContent("email", $xml_user),
                'wwclid'        => $this->xml->getFirstTagContent("wwclid", $xml_user),
                'wwclclanid'    => $this->xml->getFirstTagContent("wwclclanid", $xml_user),
                'clanurl'       => $this->xml->getFirstTagContent("homepage", $xml_user)
            ];
        }

        // Putting all <seat_blocks>-tags into an array
        $seat_blocks_blocks = $this->xml->get_tag_content_combine("seat_blocks", $this->xml_content_lansuite);
        $blocks             = $this->xml->getTagContentArray("block", $seat_blocks_blocks);

        if ($blocks) {
            foreach ($blocks as $xml_block) {
                unset($seps_to_import);
                unset($seats_to_import);

                // Seats in this block
                $seats_in_this_block = $this->xml->get_tag_content_combine("seat_seats", $xml_block);
                $seats               = $this->xml->getTagContentArray("seat", $seats_in_this_block);

                if (is_array($seats)) {
                    foreach ($seats as $xml_seat) {
                        $seats_to_import[] = [
                            'col'       => $this->xml->getFirstTagContent("col", $xml_seat),
                            'row'       => $this->xml->getFirstTagContent("row", $xml_seat),
                            'status'    => $this->xml->getFirstTagContent("status", $xml_seat),
                            'owner'     => $this->xml->getFirstTagContent("owner", $xml_seat),
                            'ipaddress' => $this->xml->getFirstTagContent("ipaddress", $xml_seat)
                        ];
                    }
                }

                // Seperators in this block
                $seps_in_this_block = $this->xml->get_tag_content_combine("seat_sep", $xml_block);
                $seps               = $this->xml->getTagContentArray("sep", $seps_in_this_block);

                if (is_array($seps)) {
                    foreach ($seps as $xml_sep) {
                        $seps_to_import[] = [
                            'orientation' => $this->xml->getFirstTagContent("orientation", $xml_sep),
                            'value' => $this->xml->getFirstTagContent("value", $xml_sep)
                        ];
                    }
                }

                // Seatblockdata
                $seat_blocks_to_import[] = [
                    'name'          => $this->xml->getFirstTagContent("name", $xml_block),
                    'rows'          => $this->xml->getFirstTagContent("rows", $xml_block),
                    'cols'          => $this->xml->getFirstTagContent("cols", $xml_block),
                    'orientation'   => $this->xml->getFirstTagContent("orientation", $xml_block),
                    'remark'        => $this->xml->getFirstTagContent("remark", $xml_block),
                    'text_tl'       => $this->xml->getFirstTagContent("text_tl", $xml_block),
                    'text_tc'       => $this->xml->getFirstTagContent("text_tc", $xml_block),
                    'text_tr'       => $this->xml->getFirstTagContent("text_tr", $xml_block),
                    'text_lt'       => $this->xml->getFirstTagContent("text_lt", $xml_block),
                    'text_lc'       => $this->xml->getFirstTagContent("text_lc", $xml_block),
                    'text_lb'       => $this->xml->getFirstTagContent("text_lb", $xml_block),
                    'text_rt'       => $this->xml->getFirstTagContent("text_rt", $xml_block),
                    'text_rc'       => $this->xml->getFirstTagContent("text_rc", $xml_block),
                    'text_rb'       => $this->xml->getFirstTagContent("text_rb", $xml_block),
                    'text_bl'       => $this->xml->getFirstTagContent("text_bl", $xml_block),
                    'text_bc'       => $this->xml->getFirstTagContent("text_bc", $xml_block),
                    'text_br'       => $this->xml->getFirstTagContent("text_br", $xml_block),
                    'seats'         => $seats_to_import,
                    'seps'          => $seps_to_import
                ];
            }
        }

        /* DB INPUT */
        if (is_array($users_to_import) == true) {
            foreach ($users_to_import as $user) {
                $email      = $user['email'];
                $username   = $this->xml->convertinputstr($user['username']);
                $name       = $this->xml->convertinputstr($user['name']);
                $firstname  = $this->xml->convertinputstr($user['firstname']);
                $clan       = $this->xml->convertinputstr($user['clan']);
                $type       = $user['type'];
                $paid       = $user['paid'];
                $password   = $user['password'];

                $wwclid     = $this->xml->convertinputstr($user['wwclid']);
                $wwclclanid = $this->xml->convertinputstr($user['wwclclanid']);

                $checkin = ($type > 1) ? "1" : "0";

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

                        // Insert new clan
                        } else {
                            $db->qry("INSERT INTO %prefix%clan SET name = %string%, url = %string% ", $clan, $clanurl);
                            $clan_id = $db->insert_id();
                        }
                    }
                    $db->qry(
                        "
                      REPLACE INTO %prefix%user
                      SET
                        email = %string%,
                        name = %string%,
                        username = %string%,
                        firstname = %string%,
                        type = %string%,
                        clanid = %int%,
                        password = %string%,
                        wwclid = %int%,
                        wwclclanid = %int%,
                        comment = %string%",
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

                    $db->qry("INSERT INTO %prefix%usersettings SET userid=%int%", $id);

                    $userids[$email] = $id;
                }
            }
        } else {
            echo "FEHLER: USER NICHT EINGETRAGEN" .HTML_NEWLINE;
        }

        if (is_array($seat_blocks_to_import) == true and !$no_seat) {
            foreach ($seat_blocks_to_import as $block) {
                $name           = $this->xml->convertinputstr($block['name']);
                $rows           = $block['rows'];
                $cols           = $block['cols'];
                $orientation    = $block['orientation'];
                $remark         = $block['remark'];
                $text_tl        = $block['text_tl'];
                $text_tc        = $block['text_tc'];
                $text_tr        = $block['text_tr'];
                $text_lt        = $block['text_lt'];
                $text_lc        = $block['text_lc'];
                $text_lb        = $block['text_lb'];
                $text_rt        = $block['text_rt'];
                $text_rc        = $block['text_rc'];
                $text_rb        = $block['text_rb'];
                $text_bl        = $block['text_bl'];
                $text_bc        = $block['text_bc'];
                $text_br        = $block['text_br'];

                $db->qry(
                    "
                  REPLACE INTO %prefix%seat_block
                  SET
                    name=%string%,
                    cols=%string%,
                    rows=%string%,
                    orientation=%string%,
                    remark=%string%,
                    text_tl=%string%,
                    text_tc=%string%,
                    text_tr=%string%,
                    text_lt=%string%,
                    text_lc=%string%,
                    text_lb=%string%,
                    text_rt=%string%,
                    text_rc=%string%,
                    text_rb=%string%,
                    text_bl=%string%,
                    text_bc=%string%,
                    text_br=%string%,
                    party_id=%int%",
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
                }

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
                }
            }
        }
    }

    /**
     * @param string $tmp_file_name
     * @param boolean $del_db
     * @param boolean $replace
     * @param boolean $signon
     * @param string $comment
     * @return array
     */
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
                $db->qry(
                    "
                  REPLACE INTO %prefix%user
                  SET
                    username = %string%,
                    clan = %string%,
                    firstname = %string%,
                    name = %string%,
                    email = %string%,
                    paid = %int%,
                    type = '1',
                    signon = %string%,
                    comment = %string%",
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

            switch ($db->get_affected_rows()) {
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
}
