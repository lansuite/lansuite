<?php

class db {
    var $link_id = 0;
    var $query_id = 0;
    var $record   = array();
    var $print_sql_error;
    var $success = false;
    var $count_query = 0;
    var $querys = array();

    // Konstruktor
    function db() {
        global $lang;

        if (!extension_loaded("mysqli")) echo(HTML_FONT_ERROR . t('Das MySQL-PHP Modul ist nicht geladen. Bitte fügen Sie die mysql.so Extension zur php.ini hinzu und restarten Sie Apache.') . HTML_FONT_END);
    }


    function connect($save = false) {
        global $config, $lang;

        $this->querys_count = 0;

        $this->dbserver = $config["database"]["server"];
        $this->dbuser   = $config["database"]["user"];
        $this->dbpasswd = $config["database"]["passwd"];
        $this->database = $config["database"]["database"];

        $this->link_id=@mysqli_connect($this->dbserver,$this->dbuser,$this->dbpasswd);
        if (!$this->link_id) {
            if ($save == true) {
                $this->success = false;
                return false;
            } else  {
                echo HTML_FONT_ERROR . t('Die Verbindung zur Datenbank ist fehlgeschlagen. Lansuite wird abgebrochen.') . HTML_FONT_END;
                exit();
            }
        } elseif (!@mysqli_select_db($this->link_id, $this->database)) {
            if ($save == true) {
                $this->success = false;
                return false;
            } else {
                echo HTML_FONT_ERROR . str_replace("%DB%", $this->database, t('Die Datenbank /\'/%DB%/\'/ konnte nicht ausgewählt werden. Lansuite wird abgebrochen.'))  . HTML_FONT_END;
                exit();
            }
        } else $GLOBALS['db_link_id'] = $this->link_id;

        @mysqli_query($GLOBALS['db_link_id'], "/*!40101 SET NAMES utf8_general_ci */;");
        $this->success = true;
        return true;
    }


    function query($query_string) {
    // Escape bad mysql
    $query_test_string = str_replace("\'", '', strtolower($query_string)); # Cut out escaped ' and convert to lower string
    $query_test_string = ereg_replace("'[^']*'", "", strtolower($query_test_string)); # Cut out strings within '-quotes
    // No UNION
    if (!strpos($query_test_string, 'union ') === false) $query_string = '___UNION_STATEMENT_IS_FORBIDDEN_WITHIN_LANSUITE___'; 

    // No INTO OUTFILE
    elseif (!strpos($query_test_string, 'into outfile') === false) $query_string = '___INTO OUTFILE_STATEMENT_IS_FORBIDDEN_WITHIN_LANSUITE___'; 
      $this->querys[] = $query_string;
      $this->querys_count++;
      $this->query_id = mysqli_query($GLOBALS['db_link_id'], $query_string);
      $this->sql_error = @mysqli_error($GLOBALS['db_link_id']);
      $this->count_query++;
      if (!$this->query_id) $this->print_error($this->sql_error, $query_string);
      return $this->query_id;
    }

  function escape($match) {
    global $CurrentArg;

    $CurrentArg = stripslashes($CurrentArg);    
    if ($match[0] == '%int%') return (int)$CurrentArg;
    elseif ($match[0] == '%string%') return "'". mysqli_real_escape_string($GLOBALS['db_link_id'], (string)$CurrentArg) ."'";
    elseif ($match[0] == '%plain%') return $CurrentArg;
  }

  function qry() {
    global $config, $CurrentArg;

    $args = func_get_args();
    $query = array_shift($args);
    $query = str_replace('%prefix%', $config['database']['prefix'], $query);
    foreach ($args as $CurrentArg) $query = preg_replace_callback('#(%string%|%int%|%plain%)#sUi', array('db', 'escape'), $query, 1);
    return $this->query($query);
  }

    function disconnect() {
    mysqli_close($this->link_id);
  }

    function get_affected_rows() {
        return @mysqli_affected_rows($GLOBALS['db_link_id']);
    }


    function fetch_array($query_id=-1) {
      global $func;

        if ($query_id != -1) $this->query_id = $query_id;

        $this->record = @mysqli_fetch_array($this->query_id);
        if ($this->record) foreach ($this->record as $key => $value) {
           $this->record[$key] = $func->NoHTML($value);
        }
        
        return $this->record;
    }


    function free_result($query_id = -1) {
      if ($query_id != -1) $this->query_id = $query_id;
      return @mysqli_free_result($this->query_id);
    }


    function query_first($query_string) {
        $this->query($query_string);
        $row = $this->fetch_array($this->query_id);
        $this->free_result($this->query_id);

        return $row;
    }

    function qry_first() {
    global $config, $CurrentArg;

    $args = func_get_args();
    (string)$query = array_shift($args);
    (string)$query = str_replace('%prefix%', $config['database']['prefix'], $query);

    foreach ($args as $CurrentArg) $query = preg_replace_callback('#(%string%|%int%|%plain%)#sUi', array('db', 'escape'), $query, 1);
    $this->query($query);

        $row = $this->fetch_array($this->query_id);
        $this->free_result($this->query_id);
        return $row;
  }


    function query_first_rows($query_string) { // fieldname "number" is reserved
        $this->query($query_string);
        $row = $this->fetch_array($this->query_id);
        $row['number'] = $this->num_rows($this->query_id);
        $this->free_result($this->query_id);

        return $row;
    }


    function num_rows($query_id=-1) {
        if ($query_id!=-1) $this->query_id=$query_id;

        return @mysqli_num_rows($this->query_id);
    }


    function insert_id() {
        return @mysqli_insert_id($GLOBALS['db_link_id']);
    }


    function stat() {
    return @mysqli_stat($GLOBALS['db_link_id']);
  }

    function get_host_info() {
        return @mysqli_get_host_info($GLOBALS['db_link_id']);
    }


    function print_error($msg, $query_string_with_error) {
        global $func, $config, $auth, $lang;

        $url_array = parse_url($_SERVER['REQUEST_URI']);

        $error_msg = str_replace("%SCRIPT%", '?' . $url_array['query'] . $url_array['fragment'], str_replace("%ERROR%", $msg, str_replace("%QUERY%", $query_string_with_error, str_replace("%SCRIPT%", $_SERVER["REQUEST_URI"], str_replace("%REFERRER%", $func->internal_referer, t('SQL-Fehler in PHP-Skript /\'/%SCRIPT%/\'/ (Referrer: /\'/%REFERRER%/\'/)<br />SQL-Fehler-Meldung: %ERROR%<br />Query: %QUERY%'))))));
    if ($config['database']['display_debug_errors']) echo '<font color="red">' . $error_msg . '</font><br /><br />';

        $error_msg = $func->escape_sql($error_msg);

        $current_time = date("U");
        @mysqli_query($GLOBALS['db_link_id'], "INSERT INTO {$config["tables"]["log"]} SET date = '$current_time',  userid = '{$auth["userid"]}', type='3', description = '$error_msg', sort_tag = 'SQL-Fehler'");   
        $this->count_query++;
    }
    
    function field_exist($table,$field) {
        $fields = mysql_list_fields($this->database, $table);
        $columns = mysqli_num_fields($fields);
        $found = 0;
        for ($i = 0; $i < $columns; $i++) {
           if ( trim($field) == trim(mysqli_field_name($fields, $i)) ) $found = 1;
        }
       return $found;
    } 


    function num_fields() {
    return mysqli_num_fields($this->query_id);
  }

    function field_name($pos) {
    $finfo = mysqli_fetch_field_direct($this->query_id, $pos);
    return $finfo->name;
  }
  
    function get_mysqli_stmt() {
        $prep = $link_id->stmt_init();
        return $prep;
    }
}
?>
