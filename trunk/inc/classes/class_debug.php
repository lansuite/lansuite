<?php
/**
 * Containerfile for Debug-Class
 *
 * @author  bytekilla@synergy-lan.de
 * @version 0.02
 * @package lansuite_core
 */

/**
* Magic Debugfunction
* You can use it 3 ways.
* <code>
* d($config); // Just an variable. Variables name will be unknown in the output
* d('$config'); // Variable as string. This way the variables name can be written as well.
* d('Any Text', $config); // Write the variables name as a string
* </code>
*
*/
function d() {
    global $debug, $func;

    $arg_vars = func_get_args();
    if (!isset($debug)) $debug = new debug(1);

    if ($arg_vars[1]) {
      $title = $arg_vars[0];
      $val = $arg_vars[1];
    } elseif (is_string($arg_vars[0]) and substr($arg_vars[0], 0, 1) == '$') {
      $title = $arg_vars[0];
      eval('global '. $arg_vars[0] .'; $val = '. $arg_vars[0] .';');
    } else {
      $title = 'Variable';
      $val = $arg_vars[0];
    }

    $func->information($title .':<br>"'. nl2br(str_replace(' ', '&nbsp;', htmlentities(print_r($val, true)))) .'"', NO_LINK);

    if ($title == 'Variable') {
      if (is_numeric($val)) $title = $val;
      elseif (is_string($val)) $title = substr($val, 0, 10);
      else $title = 'No title given';
    } 
    $debug->tracker('Debug point: '. $title);
}

/**
 * Debugclass to provide functions like timer, debugvar output, servervar output
 *
 * Mode : 0 = off, 1 = just generate HTML, 2 = Generate HTML & Write to File
 * By use the Filemode you have do protect the outputdirectory
 * example :
 * <code>
 * $debug = new debug(1);
 * $debug->tracker("BEFOREINCLUDE");            // Set Timerpoint
 * $debug->addvar('$cfg Serverconfig',$cfg);    // Add an Debugvar (Arrays posible)
 * $debug->timer_start('function sortarray');
 * $array = sortarray($array)
 * $debug->timer_stop('function sortarray');
 * echo $sys_debug->show();                     // Show() generates simple HTML-Output                       
 * </code>
 *
 * @author  bytekilla@synergy-lan.de
 * @version 0.02
 * @package lansuite_core
 * @todo Add Procentual Display for Tracker/Timer
 * @todo Solve Different Configoptions ($config/$cfg) (index.php problem)
 * @todo Add Start/Stopp-Timer
 * @todo Improve HTML-Output (Use just 1 Funktion for better overview)
 * @todo Improve File-Output (scheiben zur Laufzeit fuer besseres Debuging)
 */
class debug {

  /**#@+
   * Intern Variables
   * @access private
   * @var mixed
   */
  var $timer_first = "";    // Helpvar Timer
  var $timer_last = "";     // Helpvar Timer
  var $timer_out = "";      // Helpvar Timer (Outputstring)
  var $debugvars = array(); // Uservars to show
  var $mode = "";           // Debugmode
  var $debug_path = "";     // Debugpath for Filedebug
  /**#@-*/

/**
 * Constructor Debug-Class
 * Sets Debugmode and Path for Filedebug
 *
 * @param string Debugmode (0=off, 1=normal, 2=file)
 * @param string Path for Filedebug
 * @return void
 */
  function debug($mode = "0", $debug_path = "") {
    // TODO : argvars verwenden um einfache Variablenübergabe zu ermöglischen.
    // TODO : Debugbacktrace
    $this->mode = $mode;
    $this->debug_path = $debug_path;
    $this->tracker("INIT DEBUG-CLASS");  // Sets first Timerpoint
    if ($this->mode > 0) @ini_set('display_errors', 1);
  }

  // Set Timerpoint for Debugoutput. Shows Memoryusage also, if available
  // @param string Name of Event
  function tracker($event) {
    if ($this->mode > 0) {
      $time = array_sum(explode(" ", microtime()));

      // Prepare for Memory, just if functions avail
      if (function_exists('memory_get_usage')) $mem = sprintf("MemAct: %05d KB &nbsp;&nbsp;", memory_get_usage() / 1024);
      else $mem = "";
      if (function_exists('memory_get_peak_usage')) $memmax = sprintf("MemPeak: %05d KB &nbsp;&nbsp;", memory_get_peak_usage() / 1024);
      else $memmax = "";

      if (!$this->timer_first or !$event) $this->timer_first = $time;
      if (!$this->timer_last or !$event) $this->timer_last = $time;

      $tmp_out = sprintf("Step: %07.1f ms &nbsp; Total: %07.1f ms &nbsp;&nbsp;".$mem.$memmax." => [%s]<br />\n",
        ($time - $this->timer_last) * 1000, ($time - $this->timer_first)*1000, $event);

      $this->timer_out .= $tmp_out;
      $this->timer_all = $time - $this->timer_first;
      $this->timer_last = $time;
    }
  }

  function timer_show() {
    if ($this->mode > 0) return $this->timer_out;
  }

  function timer_all() {
    if ($this->mode > 0) return sprintf("%8.4f", $this->timer_all);
  }

  /**
   * Add Userdefined Debugvariable e.g. addvar('$anz',$anz)
   * Caution : Use single quote vor Varcaption like $var
   * @param string  Name of the Variable
   * @param mixed   Variable
   */
  function addvar($key, $value){
    if ($this->mode > 0) {
      if (is_string($key)) $this->debugvars[$key] = $value;
      else $this->debugvars["debugvar_".count($this->debugvars)] = $value;
    }
  }

  /**
   * Start Timer for testing. Always use with timer_stop()
   * Shows measured Time in Debugoutput. You can alwas only start and stop just 1 time
   * Example :
   * <code>
   * $debug->timer_start('function sortarray');
   * $array = sortarray($array)
   * $debug->timer_stop('function sortarray');
   * </code>
   * @param string  String to Identify Timer
   * @todo Write it
   */
  //
  function timer_start($caption){
    if ($this->mode > 0) {
      // Diverse Ueberpruefungen, evtl mehrfach start/stop erlauben
    }
  }

  /**
   * Stop Timer for testing. Always use with timer_start()
   * For Instructions see timer_start())
   * @param string  String to Identify Timer
   * @todo Write it
   */
  function timer_stop($caption){
    if ($this->mode > 0) {
    }
  }

  /**
   * Start Timer for Querys. Always use with query_stop()
   * @param string  Executet Querystring
   */
  //
  function query_start($query){
      if (($this->mode > 0) AND (!$this->sql_query_running)) {
          $this->sql_query_running = true;
          $this->sql_query_start = microtime(true);
          $this->sql_query_string = $query;
      }
  }

  /**
   * Stop Timer for Querys. Always use with query_start()
   * @param string  Executet Querystring
   */
  function query_stop($error = null){
      if (($this->mode > 0) AND ($this->sql_query_running)) {
          $this->sql_query_running = false;
          $sql_query_end = microtime(true);
          $this->sql_query_list[] = array(round(($sql_query_end - $this->sql_query_start) *1000, 4),$this->sql_query_string, $error);
      }
  }

  /**
   * Generate and Sort Querylist
   * @return array Sortet HTML-Querylist
   * @access private
   */
  function query_fetchlist(){
      if (($this->mode > 0) AND is_array($this->sql_query_list)) {
          $this->sql_query_list = $this->sort_array_by_col($this->sql_query_list);
          foreach($this->sql_query_list as $debug_query) {
              $sql_query_debug .= debug::row_double(sprintf("<b>%8.4f ms</b>", $debug_query[0]), $debug_query[1]);
              if (!($debug_query[2]=="")) $sql_query_debug .= debug::row_double("", "<span style=\"color:red\"><b>Error : ".$debug_query[2]."</b></span>");
          }
          return $sql_query_debug;
      }
  }
  
  /**
   * Sort Array by first Column
   * @param  array Array with Columns
   * @return array Sortet Array
   * @access private
   */
  function sort_array_by_col($array){
      function compare($wert_a, $wert_b) {
          // Sortierung nach dem zweiten Wert des Array (Index: 1)
          $a = $wert_a[0];
          $b = $wert_b[0];
          if ($a == $b) {
             return 0;
          }
          return ($a > $b) ? -1 : +1;
      }
      usort($array, 'compare');
      return $array;
  }

  /**
   * Generate table heading (HTML)
   * @param  string table heading
   * @return string HTML-Row for table (<tr><td>...</td></tr>)
   * @access private
   */
  function row_top($name){
      $out = "<tr><td width=\"100%\" colspan=\"2\" bgcolor=\"#C0C0C0\">".$name."</td></tr>\n";
      return $out;
  }

  /**
   * Generate single table row (HTML)
   * @param  string text
   * @return string HTML-Row for table (<tr><td>...</td></tr>)
   * @access private
   */
  function row_single($name){
      $out = "<tr><td width=\"100%\" colspan=\"2\" align=\"left\">".$name."</td></tr>";
      $out .= "<tr><td width=\"100%\" height=\"1\" bgcolor=\"#C0C0C0\" colspan=\"2\"></td></tr>\n";
      return $out;
  }

  /**
   * Generate doublerow (HTML)
   * @param  string Keydescription
   * @param  string Variable
   * @return string HTML-Row for Table (<tr><td>...</td></tr>)
   * @access private
   */
  function row_double($key, $value){
      $out = "<tr><td width=\"20%\" align=\"left\">".$key."</td><td width=\"80%\" align=\"left\">".wordwrap( $value, 65, "<br />\n", true )."&nbsp;</td></tr>";
      $out .= "<tr><td width=\"100%\" height=\"1\" bgcolor=\"#C0C0C0\" colspan=\"2\"></td></tr>\n";
      return $out;
  }

  /**
   * Print Array as Tablerows (HTML) Recursive calls posible
   * @param  string Vararray
   * @param  string Arraynode (for recursive Calls)
   * @param  string Levelcounter (for recursive Calls)
   * @return string HTML-Rows for table (<tr><td>...</td></tr>) multiline
   * @access private
   */
  function row_array($array, $array_node = NULL, $array_level = 0 ){
      if ($array_level == 0) $out .= debug::row_double("<b>Key</b>", "<b>Value</b>");
      foreach ($array as $key => $value) {
          $shift = str_repeat("&nbsp;&nbsp;", $array_level); 
          if ($array_level==0) {$caption = $key;} else {$caption = "[".$key."]";};            
          // walk types
          if (is_array($value)){
              $out .= debug::row_double($shift.$array_node.$caption,"(".gettype($value).")");
              $out .= $this->row_array($value,$array_node.$caption,$array_level+1);
          } elseif (is_object($value)) {
              $out .= debug::row_double($shift.$array_node.$caption, "(".gettype($value).")&nbsp;");
              $out .= $this->row_array(get_object_vars($value),$array_node.$caption,$array_level+1);                
          } elseif (is_scalar($value)) {
              $out .= debug::row_double($shift.$array_node.$caption, "(".gettype($value).")&nbsp;".htmlentities($value));
          } else {
              $out .= debug::row_double($shift.$array_node.$caption, "(".gettype($value).")&nbsp;Error: Can not display Debug- Value!!!");
          }
      }
      return $out;
  }

  /**
   * Generating Debug-Table (Simple HTML)
   * @todo Make Funktions for automatic add/generate Sections ala Timer, Vars, etc.
   * @todo Add "Jump Top" Links
   * @return string HTML-Table
   */
  function show() {
      if ($this->mode > 0) {
          $this->tracker("END DEBUG-CLASS");
          $out .= "<div align=\"left\"><table width=\"100%\" border=\"0\" cols=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
/*          $out .= debug::row_top("<a name=\"debug_top\"><b>Quicknavi</b></a>");
          $out .= debug::row_single("<a href=\"#debugtracker\">Debugtracker</a>&nbsp;|&nbsp;"
                                   ."<a href=\"#debugvars\">User-Debugvars</a>&nbsp;|&nbsp;"
                                   ."<a href=\"#post\">\$_POST</a>&nbsp;|&nbsp;"
                                   ."<a href=\"#get\">\$_GET</a>&nbsp;|&nbsp;"
                                   ."<a href=\"#cookie\">\$_COOKIE</a>&nbsp;|&nbsp;"
                                   ."<a href=\"#session\">\$_SESSION</a>&nbsp;|&nbsp;"
                                   ."<a href=\"#server\">\$_SERVER</a>&nbsp;|&nbsp;"
                                   ."<a href=\"#files\">\$_FILES</a>&nbsp;|&nbsp;"
                                   ."<a href=\"#sql_querys\">SQL-Querys</a>");*/
          $out .= debug::row_top("<a name=\"debugtracker\"><b>Debugtracker</b></a>");
          $out .= debug::row_single($this->timer_show());
/*
          $out .= debug::row_top("<a name=\"debugvars\"><b>Userdefined Debugvars</b></a>");
          $out .= debug::row_array($this->debugvars);
          $out .= debug::row_top("<a name=\"post\"><b>\$_POST-Data</b></a>");
          $out .= debug::row_array($_POST);
          $out .= debug::row_top("<a name=\"get\"><b>\$_GET-Data</b></a>");
          $out .= debug::row_array($_GET);
          $out .= debug::row_top("<a name=\"cookie\"><b>\$_COOKIE-Data</b></a>");
          $out .= debug::row_array($_COOKIE);
          $out .= debug::row_top("<a name=\"session\"><b>\$_Session-Data</b></a>");
          $out .= debug::row_array($_SESSION);
          $out .= debug::row_top("<a name=\"server\"><b>\$_SERVER-Data</b></a>");
          $out .= debug::row_array($_SERVER);
          $out .= debug::row_top("<a name=\"files\"><b>\$_FILES-Data</b></a>");
          $out .= debug::row_array($_FILES);
*/
          $out .= debug::row_top("<a name=\"sql_querys\"><b>SQL-Querys (".count($this->sql_query_list).")</b></a>");
          $out .= $this->query_fetchlist();
          $out .= "</table></div>";
          // Mode 2 write complete Debugvars to a file. The directory has to be protected.
          if ($this->mode == "2") {
              echo $this->mode;
              $file_handle = fopen($this->debug_path."debug_".time().".htm", "a");
              fputs($file_handle, $out);
              fclose($file_handle);   
          }
          return $out;
      } else {
          return "";

      }
  }
}