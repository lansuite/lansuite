<?php
/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-----------------------------------------------
*
*	(c) 2001-2003 by One-Network.Org
*
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 			class_db_mysql.php
*	Module: 			Framework
*	Main editor: 		raphael@lansuite.de
*	Last change: 		22.09.2002 19:39
*	Description: 		
*	Remarks: 		
*
**************************************************************************/

class db {
	var $link_id = 0;
	var $query_id = 0;
	var $record   = array();
	var $print_sql_error;
	var $success = false;
	var $count_query = 0;

    // Konstruktor
	function db() {
		global $lang;

		if (!extension_loaded("mysql")) echo(HTML_FONT_ERROR . $lang['class_db_mysql']['no_mysql'] . HTML_FONT_END);
	}


	function connect($save = false) {
		global $config, $lang;

        $this->querys_count = 0;

		$this->dbserver	= $config["database"]["server"];
		$this->dbuser   = $config["database"]["user"];
		$this->dbpasswd = $config["database"]["passwd"];
		$this->database = $config["database"]["database"];

		$this->link_id=@mysql_connect($this->dbserver,$this->dbuser,$this->dbpasswd);
		if (!$this->link_id) {
			if ($save == true) return false;
			else  {
				echo HTML_FONT_ERROR . $lang['class_db_mysql']['no_connection'] . HTML_FONT_END;
				exit();
			}
   		} elseif (!@mysql_select_db($this->database, $this->link_id)) {
			if ($save == true) return false; 
			else {
				echo HTML_FONT_ERROR . str_replace("%DB%", $this->database, $lang['class_db_mysql']['no_db'])  . HTML_FONT_END;
				exit();
			}
		} else $GLOBALS['db_link_id'] = $this->link_id;

		@mysql_query("/*!40101 SET NAMES latin1 */;", $GLOBALS['db_link_id']);

		return true;
	}


	function query($query_string) {
   		$this->querys[] = $query_string;
		$this->querys_count++;
		$this->query_id = @mysql_query($query_string, $GLOBALS['db_link_id']);
		$this->sql_error = @mysql_error($GLOBALS['db_link_id']);
		$this->count_query++;
		if (!$this->query_id) $this->print_error($this->sql_error, $query_string);
		return $this->query_id;
	}


	function get_affected_rows() {
		return @mysql_affected_rows($GLOBALS['db_link_id']);
	}


	function fetch_array($query_id=-1) {
		if ($query_id != -1) $this->query_id = $query_id;

		$this->record = @mysql_fetch_assoc($this->query_id);
		return $this->record;
	}


	function free_result($query_id = -1) {
		if ($query_id != -1) $this->query_id = $query_id;

		return @mysql_free_result($this->query_id);
  	}


	function query_first($query_string) {
   		$this->query($query_string);
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

		return @mysql_num_rows($this->query_id);
  	}


	function insert_id() {
		return @mysql_insert_id($GLOBALS['db_link_id']);
  	}


	function get_host_info() {
		return @mysql_get_host_info();
	}


	function print_error($msg, $query_string_with_error) {
		global $func, $config, $auth, $lang;

		echo str_replace("%LINE%", __LINE__, str_replace("%ERROR%", $msg, str_replace("%QUERY%", $query_string_with_error, str_replace("%SCRIPT%", $func->internal_referer, $lang['class_db_mysql']['sql_error']))));

		$msg = str_replace("'", "", $msg);
		$post_this_error = str_replace("%LINE%", __LINE__, str_replace("%ERROR%", $msg, str_replace("%QUERY%", $query_string_with_error, str_replace("%SCRIPT%", $func->internal_referer, $lang['class_db_mysql']['sql_error_log']))));

		$current_time = date("U");
		@mysql_query("INSERT INTO {$config["tables"]["log"]} SET date = '$current_time',  userid = '{$auth["userid"]}', type='3', description = '$post_this_error', sort_tag = 'SQL-Fehler'");	
		$this->count_query++;
	}
	
	function field_exist($table,$field) {
    	$fields = mysql_list_fields($this->database, $table);
    	$columns = mysql_num_fields($fields);
    	$found = 0;
    	for ($i = 0; $i < $columns; $i++) {
     	   if ( trim($field) == trim(mysql_field_name($fields, $i)) ) $found = 1;
      	}
       return $found;
	} 
}
?>
