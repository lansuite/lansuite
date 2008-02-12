<?php

    
    class plugin_q3_query {
        var $_ip;
        var $_port;
        var $_socket;
        var $_timeout;
        var $_data;
        
        function plugin_q3_query($ip = "127.0.0.1", $port = "27015")
        {
            global $phpUA;
            $this->_ip = $ip;
            $this->_port = $port;
            $this->_timeout = $phpUA["CONFIG"]["TIMEOUT"];
        }
        
        function _connect()
        {
            if (!$this->_socket = @fsockopen("udp://" . $this->_ip, $this->_port, $errno, $errstr, $this->_timeout)) return false;
            return true;
        }
        
        function _disconnect()
        {
            if (!fclose($this->_socket)) return false;
            return true;
        }
        
        function _writeData($command)
        {
            // Messages are sent to the server by sending 4 consecutive bytes of 255 (32-bit integer -1)
            // and then the string command followed by a zero byte to terminate it
            if (!fwrite($this->_socket, "\xFF\xFF\xFF\xFF" . $command . "\x00")) return false;
            return true;
        }
        
        function _readData()
        {
            // The first byte of the datagram will tell us if it has been split or not.
            socket_set_timeout($this->_socket, $this->_timeout);
            $data = fread($this->_socket, 1);
            if (socket_timeout($this->_socket)) return false;
            switch (ord($data)) {
                case 255: // Just one datagram
                    $status = socket_get_status($this->_socket);
                    socket_set_timeout($this->_socket, $this->_timeout);
                    $data .= fread($this->_socket, $status["unread_bytes"]);
                    if (socket_timeout($this->_socket)) return false;
                    break;
                case 254: // More than one datagram
                    // This would have to be the only complicated part, thanks to the use of UDP.
                    // This code was made possible thanks to an artical by "Montanus" entitled
                    // "Managing Multiple UDP packet replies": http://dev.kquery.com/index.php?article=21
                    // We start by reading off the rest of the header.
                    // I still want to know what the extra 4 bytes mean (apparently a counter).
                    $status = socket_get_status($this->_socket);
                    socket_set_timeout($this->_socket, $this->_timeout);
                    fread($this->_socket, 7);
                    if (socket_timeout($this->_socket)) return false;
                    // The 9th byte tells us the datagram id and the total number of datagrams.
                    socket_set_timeout($this->_socket, $this->_timeout);
                    $data = fread($this->_socket, 1);
                    if (socket_timeout($this->_socket)) return false;
                    // We need to evaluate this in bits (so convert to binary)
                    $bits = sprintf("%08b",ord($data));
                    // The low bits denote the total number of datagrams. (1-based)
                    $count = bindec(substr($bits, -4));
                    // The high bits denote the current datagram id.
                    $x = bindec(substr($bits, 0, 4));
                    // The rest is the datagram content.
                    $status = socket_get_status($this->_socket);
                    socket_set_timeout($this->_socket, $this->_timeout);
                    $datagrams[$x] = fread($this->_socket, $status["unread_bytes"]);
                    if (socket_timeout($this->_socket)) return false;
                    // Repeat this process for each datagram.
                    // We've already done the first one, so $i = 1 to start at the next.
                    for ($i=1; $i<$count; $i++) {
                        // Skip the header.
                        socket_set_timeout($this->_socket, $this->_timeout);
                        fread($this->_socket, 8);
                        if (socket_timeout($this->_socket)) return false;
                        // Evaluate the 9th byte.
                        socket_set_timeout($this->_socket, $this->_timeout);
                        $data = fread($this->_socket, 1);
                        if (socket_timeout($this->_socket)) return false;
                        $x = bindec(substr(sprintf("%08b",ord($data)), 0, 4));
                        // Read the datagram content.
                        $status = socket_get_status($this->_socket);
                        socket_set_timeout($this->_socket, $this->_timeout);
                        $datagrams[$x] = fread($this->_socket, $status["unread_bytes"]);
                        if (socket_timeout($this->_socket)) return false;
                    }
                    // Stick all of the datagrams together and pretend that it wasn't split. :)
                    $data = "";
                    for ($i=0; $i<$count; $i++) {
                        $data .= $datagrams[$i];
                    }
                    break;
            }
            $this->_data = $data;
            return true;
        }
        
        function _getByte($bytes = 1)
        {
            $data = substr($this->_data, 0, 1);
            $this->_data = substr($this->_data, 1);
            return ord($data);
        }
        
        function _getInt16()
        {
            $data = substr($this->_data, 0, 2);
            $this->_data = substr($this->_data, 2);
            $array = @unpack("Sshort", $data);
            return $array["short"];
        }
        
        function _getInt32()
        {
            $data = substr($this->_data, 0, 4);
            $this->_data = substr($this->_data, 4);
            $array = @unpack("Lint", $data);
            return $array["int"];
        }
        
        function _getFloat32()
        {
            $data = substr($this->_data, 0, 4);
            $this->_data = substr($this->_data, 4);
            $array = @unpack("ffloat", $data);
            return $array["float"];
        }
        
        function _getString()
        {
            $data = "";
            $byte = substr($this->_data, 0, 1);
            $this->_data = substr($this->_data, 1);
            while (ord($byte) != "0" && ord($byte) != "10") {
                $data .= $byte;
                $byte = substr($this->_data, 0, 1);
                $this->_data = substr($this->_data, 1);
            }
            return $data;
        }
        
        function stripColorCodes($string)
        {
            // Basic Colors
            $string = preg_replace("/\^[0-9]/", "", $string);
            // RGB Colors
            $string = preg_replace("/\^X[0-9]{6}/", "", $string);
            // Control Commands (Bold,Blink)
            $string = preg_replace("/\^[a-z,A-Z]/", "", $string);
            // ^^ => ^
            $string = preg_replace("/\^\^/", "^", $string);
            
            return($string);
        }
        
        function getinfo()
        {
            startBenchmark(__FILE__, __FUNCTION__);
            $getinfo = array();
            if (!$this->_connect()) return false;
            if (!$this->_writeData("getinfo")) return false;
            if (!$this->_readData()) return false;
            $this->_getInt32();
            $this->_getString();
            $string = $this->_getString();
            $pair = explode("\\", $string);
            for ($i=1; $i<count($pair); $i+=2) {
                $getinfo[strtolower($pair[$i])] = $pair[$i+1];
            }
            if (!$this->_disconnect()) return false;
            endBenchmark(__FILE__, __FUNCTION__);
            return $getinfo;
        }
        
        function getstatus()
        {
            startBenchmark(__FILE__, __FUNCTION__);
            $getstatus = array();
            if (!$this->_connect()) return false;
            if (!$this->_writeData("getstatus")) return false;
            if (!$this->_readData()) return false;
            $this->_getInt32();
            $this->_getString();
            $string = $this->_getString();
            $pair = explode("\\", $string);
            for ($i=1; $i<count($pair); $i+=2) {
                $getstatus[strtolower($pair[$i])] = $pair[$i+1];
            }
            if (!$this->_disconnect()) return false;
            endBenchmark(__FILE__, __FUNCTION__);
            return $getstatus;
        }
        
        function getplayers()
        {
            startBenchmark(__FILE__, __FUNCTION__);
            $players = array();
            $info = $this->getinfo();
            if (!$this->_connect()) return false;
            if (!$this->_writeData("getstatus")) return false;
            if (!$this->_readData()) return false;
            $this->_getInt32();
            $this->_getString();
            $this->_getString(); // status vars - players follow this message
            for ($i=0; $i<$info["clients"]; $i++) {
                $player = $this->_getString();
                preg_match("=^([0-9]*) ([0-9]*) (.*)$=si", trim($player), $player_data);
                $players[$i]["frags"] = $player_data[1];
                $players[$i]["ping"] = $player_data[2];
                $players[$i]["name"] = $this->stripColorCodes(trim($player_data[3], "\""));
            }
            if (!$this->_disconnect()) return false;
            endBenchmark(__FILE__, __FUNCTION__);
            return $players;
        }
    }
?>