<?php

    
    // Gamespy Query Protocol.
    
    class plugin_gs_query {
        var $_ip;
        var $_port;
        var $_socket;
        var $_timeout;
        
        function plugin_gs_query($ip="127.0.0.1", $port)
        {
            global $phpUA;
            $this->_ip = $ip;
            $this->_port = $port;
            $this->_timeout = $phpUA["CONFIG"]["TIMEOUT"];
        }
        
        function connect()
        {
            if ($this->_socket = @fsockopen("udp://" . $this->_ip, $this->_port, $errno, $errstr, $this->_timeout)) return true;
            return false;
        }
        
        function disconnect()
        {
            if (!fclose($this->_socket)) return false;
            return true;
        }
        
        // The SDK allows the following query commands:
        // basic, info, rules, players, status,
        
        function info()
        {
            startBenchmark(__FILE__, __FUNCTION__);
            if (!$this->connect()) return false;
            fwrite($this->_socket, "\\info\\");
            $buffer = "";
            while ( substr($buffer,-6) != "\final" ) {
                socket_set_timeout($this->_socket, $this->_timeout);
                $buffer .= fread($this->_socket,1);
                if ( socket_timeout($this->_socket) ) return false;
            }
            $this->disconnect();
            $pair = explode("\\", $buffer);
            for ($i=1; $i<count($pair)-2; $i++) { // -2 because the last pair is "final"
                if ( $pair[$i] != "queryid" ) {
                    $info[$pair[$i]] = $pair[$i+1];
                }
                $i++;
            }
            endBenchmark(__FILE__, __FUNCTION__);
            return $info;
        }
        
        function status()
        {
            startBenchmark(__FILE__, __FUNCTION__);
            if (!$this->connect()) return false;
            fwrite($this->_socket, "\\status\\");
            $buffer = "";
            while ( substr($buffer,-6) != "\final" ) {
                socket_set_timeout($this->_socket, $this->_timeout);
                $buffer .= fread($this->_socket,1);
                if ( socket_timeout($this->_socket) ) return false;
            }
            $this->disconnect();
            $pair = explode("\\", $buffer);
            for ($i=1; $i<count($pair)-2; $i++) { // -2 because the last pair is "final"
                if ( $pair[$i] != "queryid" ) {
                    $status[$pair[$i]] = $pair[$i+1];
                }
                $i++;
            }
            endBenchmark(__FILE__, __FUNCTION__);
            return $status;
        }
        
        function rules()
        {
            startBenchmark(__FILE__, __FUNCTION__);
            if (!$this->connect()) return false;
            $buffer = "";
            fwrite($this->_socket, "\\rules\\");
            while ( substr($buffer,-7) != "\final\\" ) {
                socket_set_timeout($this->_socket, $this->_timeout);
                $buffer .= fread($this->_socket,1);
                if ( socket_timeout($this->_socket) ) return false;
            }
            $this->disconnect();
            $pair = explode("\\", $buffer);
            for ($i=1; $i<count($pair)-2; $i++) { // -2 because the last pair is "final"
                if ( $pair[$i] != "queryid" ) {
                    $rules[$pair[$i]] = $pair[$i+1];
                }
                $i++;
            }
            endBenchmark(__FILE__, __FUNCTION__);
            return $rules;
        }
        
        function players()
        {
            startBenchmark(__FILE__, __FUNCTION__);
            if (!$this->connect()) return false;
            $buffer = "";
            fwrite($this->_socket, "\\players\\");
            while ( substr($buffer,-7) != "\final\\" ) {
                socket_set_timeout($this->_socket, $this->_timeout);
                $buffer .= fread($this->_socket,1);
                if ( socket_timeout($this->_socket) ) return false;
            }
            $this->disconnect();
            $players = "";
            $pair = explode("\\", $buffer);
            reset($pair);
            each($pair);
            for ($i=1; $i<count($pair)-2; $i++) { // -2 because the last pair is "final"
                $array = each($pair);
                $rule = $array[1];
                if ( $rule != "queryid" ) {
                    $array = each($pair);
                    $value = $array[1];
                    $array = explode("_", $rule);
                    $players[$array[0]][$array[1]] = $value;
                } else {
                    each($pair);
                }
                $i++;
            }
            endBenchmark(__FILE__, __FUNCTION__);
            if ( empty($players) ) return true;
            else return $players;
        }
    }
?>