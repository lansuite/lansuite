<?php

namespace LanSuite;

use mysqli;

class DB
{
    private \mysqli|bool|null $link_id = null;

    private bool|\mysqli_result|null $query_id = null;

    private array|bool|null $record = [];

    /**
     * @var bool
     */
    public $success = false;

    /**
     * @var int
     */
    public $count_query = 0;

    private string $errors = '';

    /**
     * @var int
     */
    public $errorsFound = 0;

    /**
     * 0 = no error
     * 1 = connection error
     * 2 = database error
     *
     * @var int
     */
    public $connectfailure = 0;

    private array $QueryArgs = [];

    private string $sql_error = '';

    /**
     * @param string $msg
     * @param string $query_string_with_error
     * @return void
     */
    private function print_error($msg, $query_string_with_error)
    {
        global $config, $auth;

        $error = t('SQL-Failure. Database respondet: <b>%1</b><br /><br />Query: <br /><i>%2</i>', $msg, $query_string_with_error);

        $this->errors .= $error . '<br />';
        $this->errorsFound = 1;

        // Need to use mysql_querys here, to prevent loops!!
        $query = 'INSERT INTO '. $config['database']['prefix'] .'log SET date = NOW(), userid = '. (int)$auth['userid'] .', type = 3, description = "'. strip_tags($error) .'", sort_tag = "SQL-Fehler"';
        mysqli_query($this->link_id, $query);
        $this->count_query++;
    }

    /**
     * @param array $match
     * @return int|mixed|string
     */
    private function escape($match)
    {
        $CurrentArg = array_shift($this->QueryArgs);

        if ($match[0] == '%int%') {
            return (int)$CurrentArg;
        } elseif ($match[0] == '%string%') {
            $CurrentArg = stripslashes($CurrentArg);
            return "'". mysqli_real_escape_string($this->link_id, (string)$CurrentArg) ."'";
        } elseif ($match[0] == '%plain%') {
            return $CurrentArg;
        }

        return '';
    }

    /**
     * @param bool $save
     * @return bool
     */
    public function connect($save = false)
    {
        global $config;

        $server = $config['database']['server'];
        $user = $config['database']['user'];
        $pass = $config['database']['passwd'];
        $database = $config['database']['database'];
        $port = $config['database']['dbport'] ?? 3306;
        $charset = $config['database']['charset'];
	    

        // Try to connect to the database
        // Suppress error output, because mysqli_connect throws a PHP Warning once it is not able to connect
        $this->link_id = @mysqli_connect($server, $user, $pass, $database, $port);

        if (!$this->link_id) {
            if ($save) {
                $this->connectfailure = 1;
                $this->success = false;
                return false;
            } else {
                echo HTML_FONT_ERROR . t('Die Verbindung zur Datenbank ist fehlgeschlagen. Lansuite wird abgebrochen. Zurückgegebener MySQL-Fehler: ' . mysqli_connect_error()) . HTML_FONT_END;
                exit();
            }

        // Try to select DB
        } else {
            $ret = mysqli_select_db($this->link_id, $database);
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

        // Set encoding based on config file
        if (!empty($charset)) {
              $this->link_id->set_charset($charset);
        } else {
            $this->link_id->set_charset('utf8');
        }
        $this->success = true;
        $this->connectfailure = 0;

        return true;
    }

    /**
     * @return void
     */
    public function set_charset()
    {
        mysqli_query($this->link_id, "/*!40101 SET NAMES utf8_general_ci */;");
    }

    /**
     * @return string
     */
    public function get_host_info()
    {
        return mysqli_get_host_info($this->link_id);
    }

    /**
     * @return void
     */
    public function disconnect()
    {
        mysqli_close($this->link_id);
    }

    /**
     * Transforms an old LS-style query to a MySQLi prepared statement
     * @return mysqli_statement
    */
    private function qry2stmt($query,$args) {
        //due to the usage of %plain% to drop in parts of the SQL statement it is required to iterate through query and deal with each entry separately
        $query_split = preg_split('((%string%|%int%|%plain%))',$query,-1,PREG_SPLIT_DELIM_CAPTURE);

        $query_stmt=''; // prepared statement compatible string
        $query_param=''; //type definition for prepare statement
        $arg_iterator=0; // position in arguments
        foreach ($query_split as $query_element) {
            switch ($query_element) {
                case '%int%': //insert placeholder and write statement element with iterator
                    $query_stmt .= '?';
                    $query_param .= 'i';
                    $arg_iterator++;
                    break;
                case '%string%': //insert placeholder and write statement element with iterator
                    $query_stmt .= '?';
                    $query_param .= 's';
                    $arg_iterator++;
                    break;
                case '%plain%': //insert content as-is with iterator
                    $query_stmt .= $args[$arg_iterator];
                    unset($args[$arg_iterator]); // remove element and move forward
                    $arg_iterator++;
                    break;
                default: //insert content as-is
                    $query_stmt .= $query_element;
            }
        }

        $args = array_values($args);
        $stmt = $this->link_id->stmt_init();
        $stmt->prepare($query_stmt);
        $stmt->bind_param($query_param, ...$args); 
        return $stmt;        
    }
    
    /**
     * If the second parameter is an array, the function uses the array as value list.
     */
    public function qry(): bool|int|\mysqli_result
    {
        global $config, $debug;
        // Arguments could be passed als multiple ones, or a single array
        $this->QueryArgs = func_get_args();
        if (is_array($this->QueryArgs[0])) {
            $this->QueryArgs = $this->QueryArgs[0];
        }
        //extract query from list
        $query = array_shift($this->QueryArgs);
        // @TODO: Don't replace %prefix% within quotes!
        $query = str_replace('%prefix%', $config['database']['prefix'], $query);
        
        if (sizeof($this->QueryArgs)){
            //We have a non-native query and a replacement of elements is required
            $stmt = $this->qry2stmt($query, $this->QueryArgs);
            if (isset($debug)) {
                $debug->query_start($query_stmt);
            }
            $stmt->execute();
            // get full result set as this causes "Commands out of sync; you can't run this command now" error otherwise (See: https://stackoverflow.com/questions/614671/commands-out-of-sync-you-cant-run-this-command-now )
            $stmt->store_result();
            //MySQL returns false for unsuccessful queries, but also for queries without return values (see https://www.php.net/manual/en/mysqli-stmt.get-result.php)
            //Thus errno has to be evaluated if false
            $rslt = $stmt->get_result();
            if ($rslt){
                $this->query_id = $rslt;
            } else {
                $this->query_id = $stmt->errno == 0;
            }
            $this->sql_error = $stmt->error . $this->link_id->error;
        } else {
            //run basic query
            if (isset($debug)) {
                $debug->query_start($query_stmt);
            }
            $this->query_id = mysqli_query($this->link_id, $query);
            $this->sql_error = mysqli_error($this->link_id);
        }
        if (!$this->query_id) {
            $this->print_error($this->sql_error, $query);
        }
        $this->count_query++;
        if (isset($debug)) {
            $debug->query_stop($this->sql_error);
        }
        $this->QueryArgs = [];
        return $this->query_id;
    }

    /**
     * @param int $query_id
     * @param int $save
     */
    public function fetch_array($query_id = -1, $save = 1): ?array
    {
        global $func;

        // Mimic the original behaviour of mysqli_fetch_array
        // Returns an array representing the fetched row, null if there are no more rows in the result set, or false on failure.
        if (!$query_id) {
            return null;
        }

        if ($query_id != -1) {
            $this->query_id = $query_id;
        }

        $this->record = mysqli_fetch_array($this->query_id);

        if ($save and $this->record) {
            foreach ($this->record as $key => $value) {
                $this->record[$key] = $func->NoHTML($value);
            }
        }

        return $this->record;
    }

    /**
     * @param int $query_id
     * @return int
     */
    public function num_rows($query_id = -1)
    {
        if ($query_id != -1) {
            $this->query_id = $query_id;
        }

        // If a SQL query does not return any rows, the query function
        // returns false. We ensure that `num_rows` always returns an integer.
        return $query_id ? mysqli_num_rows($this->query_id): 0;
    }

    /**
     * @param int $query_id
     * @return int
     */
    public function get_affected_rows($query_id = -1)
    {
        if ($query_id != -1) {
            $this->query_id = $query_id;
        }

        return mysqli_affected_rows($this->link_id);
    }

    /**
     * @param int $query_id
     */
    public function insert_id($query_id = -1): int|string
    {
        if ($query_id != -1) {
            $this->query_id = $query_id;
        }

        return mysqli_insert_id($this->link_id);
    }

    /**
     * @param int $query_id
     * @return int
     */
    public function num_fields($query_id = -1)
    {
        if ($query_id != -1) {
            $this->query_id = $query_id;
        }

        return mysqli_num_fields($this->query_id);
    }

    /**
     * @param int $pos
     * @param int $query_id
     * @return mixed
     */
    public function field_name($pos, $query_id = -1)
    {
        if ($query_id != -1) {
            $this->query_id = $query_id;
        }

        $finfo = mysqli_fetch_field_direct($this->query_id, $pos);
        return $finfo->name;
    }

    /**
     * @param int $query_id
     * @return void
     */
    public function free_result($query_id = -1)
    {
        // No op if we don't had a query result
        if (!$query_id) {
            return;
        }

        if ($query_id != -1) {
            $this->query_id = $query_id;
        }

        mysqli_free_result($this->query_id);
    }

    /**
     * If the second parameter is an array, the function uses the array as value list.
     */
    public function qry_first(): array|bool|null
    {
        $this->qry($args = func_get_args());
        // For execute querys $this->query_id will not be a resource that needs to be freed.

        if ($this->query_id === true) {
            return true;
        }

		if ($this->query_id->num_rows >0) { // only try to fetch something if we got a valid result
          
			$row = $this->fetch_array();
			$this->free_result();
		} else {
			$row = false; // just return false otherwise
		}
        return $row;
    }

    public function qry_first_rows(): ?array
    {
        $this->qry($args = func_get_args());
        $row = $this->fetch_array();
        // fieldname "number" is reserved
        $row['number'] = $this->num_rows();
        $this->free_result();
        return $row;
    }

    /**
     * @return string
     */
    public function client_info()
    {
        return mysqli_get_client_info();
    }

    /**
     * Returns the version of the MySQL server
     */
    public function getServerInfo(): string|bool
    {
        if ($this->link_id) {
            return mysqli_get_server_info($this->link_id);
        }

        return false;
    }

    /**
     * @return void
     */
    public function DisplayErrors()
    {
        global $cfg, $func;

        if ($cfg['show_mysql_errors'] && $this->errors) {
            $func->error($this->errors);
            $this->errors = '';
        }
    }
}
