<?php

/**
 * Class debug
 *
 * Class to provide functions like timer, debugvar output, servervar output.
 *
 * Mode:
 *      0 = off
 *      1 = just generate HTML
 *      2 = Generate HTML & Write to File
 *
 * By using the filemode you have to protect the output directory.
 *
 * Example:
 *      $debug = new debug(1);
 *      $debug->tracker("BEFOREINCLUDE");            // Set Timerpoint
 *      $debug->addvar('$cfg Serverconfig',$cfg);    // Add an Debugvar (Arrays posible)
 *      $debug->timer_start('function sortarray');
 *      $array = sortarray($array)
 *      $debug->timer_stop('function sortarray');
 *      echo $sys_debug->show();                     // Show() generates simple HTML-Output
 *
 * @todo Add percentual display for Tracker/Timer
 * @todo Solve different Config options ($config/$cfg) (index.php problem)
 * @todo Add Start/Stop-Timer
 * @todo Improve HTML-Output (Use just 1 function for better overview)
 * @todo Improve File-Output (write during runtime for better debugging)
 */
class debug
{
    /**
     * Helpvar Timer
     *
     * @var string
     */
    public $timer_first = '';

    /**
     * Helpvar Timer
     *
     * @var string
     */
    public $timer_last = '';

    /**
     * Helpvar Timer (Outputstring)
     *
     * @var string
     */
    public $timer_out = '';

    /**
     * @var string
     */
    public $timer_all;

    /**
     * Uservars to show
     *
     * @var array
     */
    public $debugvars = [];

    /**
     * Debug mode
     *
     * @var string
     */
    public $mode = '';

    /**
     * Debugpath for Filedebug
     *
     * @var string
     */
    public $debug_path = '';

    /**
     * debug constructor.
     * @param string $mode          0 = off, 1 = normal, 2 = file
     * @param string $debug_path    Path for filedebug
     */
    public function __construct($mode = "0", $debug_path = "")
    {
        // TODO Debugbacktrace
        $this->mode = $mode;
        $this->debug_path = $debug_path;

        // Sets first Timer point
        $this->tracker("INIT DEBUG-CLASS");
        if ($this->mode > 0) {
            @ini_set('display_errors', 1);
        }
    }

    /**
     * Set Timerpoint for debug output.
     * Shows memory usage also.
     *
     * @param string $event
     * @return void
     */
    public function tracker($event)
    {
        if ($this->mode > 0) {
            $time = array_sum(explode(" ", microtime()));

            $mem = sprintf("MemAct: %05d KB &nbsp;&nbsp;", memory_get_usage() / 1024);
            $memmax = sprintf("MemPeak: %05d KB &nbsp;&nbsp;", memory_get_peak_usage() / 1024);

            if (!$this->timer_first || !$event) {
                $this->timer_first = $time;
            }
            if (!$this->timer_last || !$event) {
                $this->timer_last = $time;
            }

            $tmp_out = sprintf(
                "Step: %07.1f ms &nbsp; Total: %07.1f ms &nbsp;&nbsp;".$mem.$memmax." => [%s]<br />\n",
                ($time - $this->timer_last) * 1000,
                ($time - $this->timer_first)*1000,
                $event
            );

            $this->timer_out .= $tmp_out;
            $this->timer_all = $time - $this->timer_first;
            $this->timer_last = $time;
        }
    }

    /**
     * @return string
     */
    public function timer_show()
    {
        if ($this->mode > 0) {
            return $this->timer_out;
        }
    }

    /**
     * Add userdefined debug variable
     * E.g. addvar('$anz', $anz)
     *
     * @param string $key       Name of the Variable
     * @param string $value
     * @return void
     */
    public function addvar($key, $value)
    {
        if ($this->mode > 0) {
            if (is_string($key)) {
                $this->debugvars[$key] = $value;

            } else {
                $this->debugvars["debugvar_".count($this->debugvars)] = $value;
            }
        }
    }

    /**
     * Generate and sort querylist
     *
     * @return string
     */
    public function query_fetchlist()
    {
        if (($this->mode > 0) && is_array($this->sql_query_list)) {
            $this->sql_query_list = $this->sort_array_by_col($this->sql_query_list);

            $sql_query_debug = '';
            foreach ($this->sql_query_list as $debug_query) {
                $sql_query_debug .= debug::row_double(sprintf("<b>%8.4f ms</b>", $debug_query[0]), $debug_query[1]);
                if (!($debug_query[2]=="")) {
                    $sql_query_debug .= debug::row_double("", "<span style=\"color:red\"><b>Error : ".$debug_query[2]."</b></span>");
                }
            }

            return $sql_query_debug;
        }
    }

    /**
     * Sort array by first column
     *
     * @param array $array
     * @return array
     */
    public function sort_array_by_col($array)
    {
        function compare($wert_a, $wert_b) {
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
     *
     * @param string $name  Table heading
     * @return string       HTML-Row for table (<tr><td>...</td></tr>)
     */
    public function row_top($name)
    {
        $out = "<tr><td width=\"100%\" colspan=\"2\" bgcolor=\"#C0C0C0\">".$name."</td></tr>\n";
        return $out;
    }

    /**
     * Generate single table row (HTML)
     *
     * @param string $name  Text
     * @return string       HTML-Row for table (<tr><td>...</td></tr>)
     */
    public function row_single($name)
    {
        $out = "<tr><td width=\"100%\" colspan=\"2\" align=\"left\">".$name."</td></tr>";
        $out .= "<tr><td width=\"100%\" height=\"1\" bgcolor=\"#C0C0C0\" colspan=\"2\"></td></tr>\n";
        return $out;
    }

    /**
     * Generate doublerow (HTML)
     *
     * @param string $key       Description
     * @param string $value     Variable
     * @return string           HTML-Row for Table (<tr><td>...</td></tr>)
     */
    public function row_double($key, $value)
    {
        $out = "<tr><td width=\"20%\" align=\"left\">".$key."</td><td width=\"80%\" align=\"left\">".wordwrap($value, 65, "<br />\n", true)."&nbsp;</td></tr>";
        $out .= "<tr><td width=\"100%\" height=\"1\" bgcolor=\"#C0C0C0\" colspan=\"2\"></td></tr>\n";
        return $out;
    }

    /**
     * Print Array as Tablerows (HTML).
     * Recursive calls possible.
     *
     * @param array     $array
     * @param string    $array_node     For recursive calls
     * @param int       $array_level    For recursive calls
     * @return string
     */
    public function row_array($array, $array_node = null, $array_level = 0)
    {
        $out = '';
        if ($array_level == 0) {
            $out .= debug::row_double("<b>Key</b>", "<b>Value</b>");
        }

        foreach ($array as $key => $value) {
            $shift = str_repeat("&nbsp;&nbsp;", $array_level);

            if ($array_level==0) {
                $caption = $key;

            } else {
                $caption = "[".$key."]";
            };

            if (is_array($value)) {
                $out .= debug::row_double($shift.$array_node.$caption, "(".gettype($value).")");
                $out .= $this->row_array($value, $array_node.$caption, $array_level+1);

            } elseif (is_object($value)) {
                $out .= debug::row_double($shift.$array_node.$caption, "(".gettype($value).")&nbsp;");
                $out .= $this->row_array(get_object_vars($value), $array_node.$caption, $array_level+1);

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
     *
     * @todo Make functions for automatic add/generate sections ala Timer, Vars, etc.
     * @todo Add "Jump Top" Links
     *
     * @return string   HTML-Table
     */
    public function show()
    {
        if ($this->mode > 0) {
            $this->tracker("END DEBUG-CLASS");
            $out = "<div align=\"left\"><table width=\"100%\" border=\"0\" cols=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
            $out .= debug::row_top("<a name=\"debugtracker\"><b>Debugtracker</b></a>");
            $out .= debug::row_single($this->timer_show());
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
        }

        return '';
    }
}
