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

  // Construktor
  function db() {
    global $lang;

    if (extension_loaded("mysqli")) $this->mysqli = 1;
    elseif (!extension_loaded("mysql")) echo(HTML_FONT_ERROR . t('Das MySQL-PHP Modul ist nicht geladen. Bitte fügen Sie die mysql.so Extension zur php.ini hinzu und restarten Sie Apache.') . HTML_FONT_END);
  }

  function connect($save = false) {
    global $config, $lang;

    $this->dbserver = $config["database"]["server"];
    $this->dbuser   = $config["database"]["user"];
    $this->dbpasswd = $config["database"]["passwd"];
    $this->database = $config["database"]["database"];

    // Try to connect
    if ($this->mysqli) $this->link_id=@mysqli_connect($this->dbserver,$this->dbuser,$this->dbpasswd);
    else $this->link_id=@mysql_connect($this->dbserver,$this->dbuser,$this->dbpasswd);
    if (!$this->link_id) {
      if ($save == true) {
        $this->success = false;
        return false;
      } else  {
        echo HTML_FONT_ERROR . t('Die Verbindung zur Datenbank ist fehlgeschlagen. Lansuite wird abgebrochen.') . HTML_FONT_END;
        exit();
      }

    // Try to select DB
    } else {
      if ($this->mysqli) $ret = @mysqli_select_db($this->link_id, $this->database);
      else $ret = @mysql_select_db($this->database, $this->link_id);
      if (!$ret) {
        if ($save == true) {
          $this->success = false;
          return false;
        } else {
          echo HTML_FONT_ERROR . t('Die Datenbank /\'/%1/\'/ konnte nicht ausgewählt werden. Lansuite wird abgebrochen.', $this->database)  . HTML_FONT_END;
          exit();
        }
      }
    }

    if ($this->mysqli) @mysqli_query($this->link_id, "/*!40101 SET NAMES utf8_general_ci */;");
    else @mysql_query("/*!40101 SET NAMES utf8_general_ci */;", $this->link_id);
    $this->success = true;
    return true;
  }

  function disconnect() {
    if ($this->mysqli) mysqli_close($this->link_id);
    else mysql_close($this->link_id);
  }

  function query($query_string, $noErrorLog = 0) {
    #// Escape bad mysql
    #$query_test_string = str_replace("\'", '', strtolower($query_string)); # Cut out escaped ' and convert to lower string
    #$query_test_string = ereg_replace("'[^']*'", "", strtolower($query_test_string)); # Cut out strings within '-quotes
    #// No UNION
    #if (!strpos($query_test_string, 'union ') === false) $query_string = '___UNION_STATEMENT_IS_FORBIDDEN_WITHIN_LANSUITE___'; 
    #// No INTO OUTFILE
    #elseif (!strpos($query_test_string, 'into outfile') === false) $query_string = '___INTO OUTFILE_STATEMENT_IS_FORBIDDEN_WITHIN_LANSUITE___'; 

    $query_start = microtime(true);
    if ($this->mysqli) {
      $this->query_id = mysqli_query($this->link_id, $query_string);
      $this->sql_error = @mysqli_error($this->link_id);
    } else {
      $this->query_id = mysql_query($query_string, $this->link_id);
      $this->sql_error = @mysql_error($this->link_id);
    }
    if (!$this->query_id and !$noErrorLog) $this->print_error($this->sql_error, $query_string);
    $this->count_query++;
    $query_end = microtime(true);
    $this->querys[] = array($query_string, round(($query_end - $query_start) *1000, 4));

    return $this->query_id;
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

  function qry() {
    global $config, $CurrentArg;

    $args = func_get_args();
    $query = array_shift($args);
    $query = str_replace('%prefix%', $config['database']['prefix'], $query);
    foreach ($args as $CurrentArg) $query = preg_replace_callback('#(%string%|%int%|%plain%)#sUi', array('db', 'escape'), $query, 1);
    return $this->query($query);
  }

  function get_affected_rows() {
    if ($this->mysqli) return @mysqli_affected_rows($this->link_id);
    else return @mysql_affected_rows($this->link_id);
  }

  function fetch_array($query_id=-1, $save=1) {
    global $func;
    
    if ($query_id != -1) $this->query_id = $query_id;
    
    if ($this->mysqli) $this->record = @mysqli_fetch_array($this->query_id);
    else $this->record = @mysql_fetch_array($this->query_id);
    
    if ($this->record) foreach ($this->record as $key => $value) {
      if ($save) $this->record[$key] = $func->NoHTML($value);
      else $this->record[$key] = $value;
    }    
    return $this->record;
  }

  function free_result($query_id = -1) {
    if ($query_id != -1) $this->query_id = $query_id;
    if ($this->mysqli) return @mysqli_free_result($this->query_id);
    else return @mysql_free_result($this->query_id);
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
    $query = array_shift($args);
    $query = str_replace('%prefix%', $config['database']['prefix'], $query);
    foreach ($args as $CurrentArg) $query = preg_replace_callback('#(%string%|%int%|%plain%)#sUi', array('db', 'escape'), $query, 1);
    $this->query($query);
    
    $row = $this->fetch_array($this->query_id);
    $this->free_result($this->query_id);
    return $row;
  }

  function qry_first_rows($query_string) { // fieldname "number" is reserved
    $this->qry($query_string);
    $row = $this->fetch_array($this->query_id);
    $row['number'] = $this->num_rows($this->query_id);
    $this->free_result($this->query_id);
    
    return $row;
  }

  function num_rows($query_id=-1) {
    if ($query_id!=-1) $this->query_id=$query_id;
    if ($this->mysqli) return @mysqli_num_rows($this->query_id);
    else return @mysql_num_rows($this->query_id);
  }

  function insert_id() {
    if ($this->mysqli) return @mysqli_insert_id($this->link_id);
    return @mysql_insert_id($this->link_id);
  }

  function get_host_info() {
    if ($this->mysqli) return @mysqli_get_host_info($this->link_id);
    else return @mysql_get_host_info($this->link_id);
  }

  function client_info() {
    if ($this->mysqli) {
      if (function_exists('mysqli_get_client_info')) return mysqli_get_client_info();
      else return false;
    } else {
      if (function_exists('mysql_get_client_info')) return mysql_get_client_info();
      else return false;
    }
  }

  function print_error($msg, $query_string_with_error) {
    global $func, $config, $auth, $lang;

    $error = t('SQL-Failure. Database respondet: <font color="red"><b>%1</b></font><br/>Your query was: <i>%2</i><br/><br/> Script: %3<br/>Referrer: %4', $msg, $query_string_with_error, $_SERVER["REQUEST_URI"], $func->internal_referer);

    if ($config['database']['display_debug_errors']) echo $error;
    $this->qry('INSERT INTO %prefix%log SET date = NOW(), userid = %int%, type = 3, description = %string%, sort_tag = \'SQL-Fehler\'', $auth["userid"], $error);
    $this->count_query++;
  }
    
  function field_exist($table,$field) {
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

  function num_fields() {
    if ($this->mysqli) return mysqli_num_fields($this->query_id);
    else return mysql_num_fields($this->query_id);
  }

  function field_name($pos) {
    if ($this->mysqli) {
      $finfo = mysqli_fetch_field_direct($this->query_id, $pos);
      return $finfo->name;
    } else return mysql_field_name($this->query_id, $pos);
  }

  function get_mysqli_stmt() {
    $prep = $link_id->stmt_init();
    return $prep;
  }
  
  function SetTableNames() {
    global $config;

    // Importent Tables
    $config['tables']['config'] = $config['database']['prefix'].'config';
    $config['tables']['user'] = $config['database']['prefix'].'user';

    $res = $this->qry('SHOW TABLES'); //"SELECT name FROM {$config["database"]["prefix"]}table_names"
    while ($row = $this->fetch_array($res)){
      $table_name = substr($row[0], strlen($config['database']['prefix']), strlen($row[0]));
      $config['tables'][$table_name] = $row[0];
    }
    $this->free_result($res);
  }
}
?>