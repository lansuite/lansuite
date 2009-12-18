<?php

class db {
  var $mysqli = 0;
  var $link_id = 0;
  var $query_id = 0;
  var $record   = array();
  var $print_sql_error;
  var $success = false;
  var $count_query = 0;
  var $querys = array();
  var $errors = '';
  var $errorsFound = 0;
  var $connectfailure = 0;  //0= no error, 1=connection error, 2=database error

  // Construktor
  function db() {
    if (extension_loaded("mysqli")) $this->mysqli = 1;
    elseif (!extension_loaded("mysql")) echo HTML_FONT_ERROR . t('Das MySQL-PHP Modul ist nicht geladen. Bitte fügen Sie die mysql.so Erweiterung zur php.ini hinzu und restarten Sie den Webserver neu. Lansuite wird abgebrochen') . HTML_FONT_END;
  }


  #### Internal only ####

  function print_error($msg, $query_string_with_error) {
    global $func, $config, $auth;

    $error = t('SQL-Failure. Database respondet: <b>%1</b><br/><br/>Query: <br><i>%2</i><br/><br/>Script: <a href="%3">%3</a><br/>Referrer: <a href="%4">%4</a><br/>', $msg, $query_string_with_error, $_SERVER["REQUEST_URI"], $func->internal_referer);

    $this->errors .= $error;
    $this->errorsFound = 1;
    // Need to use mysql_querys here, to prevent loops!!
    $query = 'INSERT INTO '. $config['database']['prefix'] .'log SET date = NOW(), userid = '. (int)$auth['userid'] .', type = 3, description = "'. strip_tags($error) .'", sort_tag = "SQL-Fehler"';
    if ($this->mysqli) mysqli_query($this->link_id, $query);
    else mysql_query($query, $this->link_id);

    $this->count_query++;
  }

  function escape($match) {
    global $CurrentArg;

    if ($match[0] == '%int%') return (int)$CurrentArg;
    elseif ($match[0] == '%string%') {
      $CurrentArg = stripslashes($CurrentArg);
      if ($this->mysqli) return "'". mysqli_real_escape_string($this->link_id, (string)$CurrentArg) ."'";
      else return "'". mysql_real_escape_string((string)$CurrentArg, $this->link_id) ."'";
    } elseif ($match[0] == '%plain%') return $CurrentArg;
  }


  #### Connection related ####

  function connect($save = false) {
    global $config;

    $server = $config['database']['server'];
    $user = $config['database']['user'];
    $pass = $config['database']['passwd'];
    $database = $config['database']['database'];

    // Try to connect
    if ($this->mysqli) $this->link_id=@mysqli_connect($server, $user, $pass);
    else $this->link_id=@mysql_connect($server, $user, $pass);
    if (!$this->link_id) {
      if ($save) {
      	$this->connectfailure = 1;
        $this->success = false;
        return false;
      } else  {
        echo HTML_FONT_ERROR . t('Die Verbindung zur Datenbank ist fehlgeschlagen. Lansuite wird abgebrochen') . HTML_FONT_END;
        exit();
      }

    // Try to select DB
    } else {
      if ($this->mysqli) $ret = @mysqli_select_db($this->link_id, $database);
      else $ret = @mysql_select_db($database, $this->link_id);
      if (!$ret) {
        if ($save) {
          $this->connectfailure = 2;
          $this->success = false;
          return false;
        } else {
          echo HTML_FONT_ERROR . t("Die Datenbank '%1' konnte nicht ausgewählt werden. Lansuite wird abgebrochen", $database) . HTML_FONT_END;
          exit();
        }
      }
    }

    if ($this->mysqli) @mysqli_query($this->link_id, "/*!40101 SET NAMES utf8_general_ci */;");
    else @mysql_query("/*!40101 SET NAMES utf8_general_ci */;", $this->link_id);
    $this->success = true;
    $this->connectfailure = 0;
    return true;
  }
  
  function set_charset()
  {
  	if ($this->mysqli) @mysqli_query($this->link_id, "/*!40101 SET NAMES utf8_general_ci */;");
    else @mysql_query("/*!40101 SET NAMES utf8_general_ci */;", $this->link_id);
  }
  
  function get_host_info() {
    if ($this->mysqli) return @mysqli_get_host_info($this->link_id);
    else return @mysql_get_host_info($this->link_id);
  }

  function disconnect() {
    if ($this->mysqli) mysqli_close($this->link_id);
    else mysql_close($this->link_id);
  }


  #### Queries ####

  function query($query_string) {
  }

	
  /**
   * If the second parameter is an array, the function uses the array as value list.
   * @return unknown_type
   */
  function qry() {
    global $config, $CurrentArg;

    $query_start = microtime(true);

    $args = func_get_args();
    if (is_array($args[0])) $args = $args[0]; // Arguments could be passed als multiple ones, or a single array

    $query = array_shift($args);
    $query = str_replace('%prefix%', $config['database']['prefix'], $query);

    if (is_array($args[0]))
    	$args = $args[0];

    foreach ($args as $CurrentArg) $query = preg_replace_callback('#(%string%|%int%|%plain%)#sUi', array('db', 'escape'), $query, 1);

    if ($this->mysqli) {
      $this->query_id = mysqli_query($this->link_id, $query);
      $this->sql_error = @mysqli_error($this->link_id);
    } else {
      $this->query_id = mysql_query($query, $this->link_id);
      $this->sql_error = @mysql_error($this->link_id);
    }
    if (!$this->query_id) $this->print_error($this->sql_error, $query);

    $this->count_query++;
    $query_end = microtime(true);
    $this->querys[] = array($query, round(($query_end - $query_start) *1000, 4));

    return $this->query_id;
  }

  function fetch_array($query_id = -1, $save = 1) {
    global $func;

    if ($query_id != -1) $this->query_id = $query_id;
    
    if ($this->mysqli) $this->record = @mysqli_fetch_array($this->query_id);
    else $this->record = @mysql_fetch_array($this->query_id);

    if ($save and $this->record) foreach ($this->record as $key => $value) $this->record[$key] = $func->NoHTML($value);

    return $this->record;
  }

  function num_rows($query_id =- 1) {
    if ($query_id != -1) $this->query_id=$query_id;

    if ($this->mysqli) return @mysqli_num_rows($this->query_id);
    else return @mysql_num_rows($this->query_id);
  }

  function get_affected_rows($query_id =- 1) {
    if ($query_id != -1) $this->query_id=$query_id;

    if ($this->mysqli) return @mysqli_affected_rows($this->link_id);
    else return @mysql_affected_rows($this->link_id);
  }

  function insert_id($query_id =- 1) {
    if ($query_id != -1) $this->query_id=$query_id;

    if ($this->mysqli) return @mysqli_insert_id($this->link_id);
    return @mysql_insert_id($this->link_id);
  }

  function num_fields($query_id =- 1) {
    if ($query_id != -1) $this->query_id=$query_id;

    if ($this->mysqli) return mysqli_num_fields($this->query_id);
    else return mysql_num_fields($this->query_id);
  }

  function field_name($pos, $query_id =- 1) {
    if ($query_id !=- 1) $this->query_id=$query_id;

    if ($this->mysqli) {
      $finfo = mysqli_fetch_field_direct($this->query_id, $pos);
      return $finfo->name;
    } else return mysql_field_name($this->query_id, $pos);
  }

  function free_result($query_id = -1) {
    if ($query_id != -1) $this->query_id = $query_id;

    if ($this->mysqli) return @mysqli_free_result($this->query_id);
    else return @mysql_free_result($this->query_id);
  }


  #### Special ####
  
  /**
   * If the second parameter is an array, the function uses the array as value list.
   * @return unknown_type
   */
  function qry_first() {
    $this->qry($args = func_get_args());
    $row = $this->fetch_array();
    $this->free_result();
    return $row;
  }

  function qry_first_rows() {
    $this->qry($args = func_get_args());
    $row = $this->fetch_array();
    $row['number'] = $this->num_rows(); // fieldname "number" is reserved
    $this->free_result();
    return $row;
  }


  #### Misc ####
  
  function client_info() {
    if ($this->mysqli) {
      if (function_exists('mysqli_get_client_info')) return mysqli_get_client_info();
      else return false;
    } else {
      if (function_exists('mysql_get_client_info')) return mysql_get_client_info();
      else return false;
    }
  }

  function DisplayErrors() {
    global $config, $func;

    if ($config['database']['display_debug_errors'] and $this->errors) {
      $func->error($this->errors);
      $this->errors = '';
    } 
  }

  function field_exist($table, $field) {
    $fields = mysql_list_fields($this->database, $table);
    if ($this->mysqli) $columns = mysqli_num_fields($fields);
    else $columns = mysql_num_fields($fields);
    $found = 0;
    for ($i = 0; $i < $columns; $i++) {
      if ($this->mysqli) {
        if (trim($field) == trim(mysqli_field_name($fields, $i))) $found = 1;
      } else {
        if (trim($field) == trim(mysql_field_name($fields, $i))) $found = 1;
      }
    }
   return $found;
  }

/*
// Could be deleted soon ... KnoX 17.11.2009

  // Old: All "$config['tables'][table_name]" should be replaced by "$config['database']['prefix']. 'table_name'"
  // Afterwards this could be deleted
  function SetTableNames() {
    global $config;

    // Importent Tables
    $config['tables']['config'] = $config['database']['prefix'].'config';
    $config['tables']['user'] = $config['database']['prefix'].'user';

    $res = $this->qry('SHOW TABLES'); //"SELECT name FROM {$config["database"]["prefix"]}table_names"
    while ($row = $this->fetch_array($res)){
      if ($config['database']['prefix'] == substr($row[0], 0, strlen($config['database']['prefix']))){
	      $table_name = substr($row[0], strlen($config['database']['prefix']), strlen($row[0]));
	      $config['tables'][$table_name] = $row[0];
      }
    }
        
    $this->free_result($res);
  }
  */
}
?>
