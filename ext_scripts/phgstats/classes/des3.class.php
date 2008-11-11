<?php // Descent 3 Game Class
/*
* Copyright (c) 2004-2006, woah-projekt.de
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions
* are met:
*
* * Redistributions of source code must retain the above copyright
*   notice, this list of conditions and the following disclaimer.
* * Redistributions in binary form must reproduce the above copyright
*   notice, this list of conditions and the following disclaimer
*   in the documentation and/or other materials provided with the
*   distribution.
* * Neither the name of the phgstats project (woah-projekt.de)
*   nor the names of its contributors may be used to endorse or
*   promote products derived from this software without specific
*   prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
* COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
* POSSIBILITY OF SUCH DAMAGE.
*/

class descent3
{
    var $host     = false;
    var $port     = false;
    var $socket   = false;
    var $g_info   = false;
    var $r_info   = false;
    var $p_info   = false;
    var $response = false;
				
    function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
 
        return ((float)$usec + (float)$sec);
    }

    function connect()
    {
        if (($this->socket = fsockopen('udp://'. $this->host, $this->port, $errno, $errstr, 30)))
        {
            return true;
        }

        return false;
    }

    function disconnect()
    {
        if ((fclose($this->socket)))
        {
            return true;
        }

        return false;
    }

    function get_rules()
    {
        $write = "\x01\x1e\x0b\x00\x1b\x2f\xf4\x41\x09\x00\x00\x00";
        $stream = $this->get_status($write);

        return $stream;
    }

    function get_players()
    {

        $write = "\x01\x72\x03\x00";
        $stream = $this->get_status($write);

        return $stream;
    }

    function get_status($write)
    {
        $info = '';

        if ($this->connect() === false)
        {
            return false;
        }

        socket_set_timeout($this->socket, 2);

        $time_begin = microtime();

        fwrite($this->socket, $write);

        $first = fread($this->socket, 1);
        $this->response =  microtime() - $time_begin;

        $status = socket_get_status($this->socket);
        $length = $status['unread_bytes'];

        if ($length > 0)
        {
            $info = $first.fread($this->socket, $length);
        }

        // response time
        $this->response = ($this->response * 1000);
        $this->response = (int)$this->response;

        if ($this->disconnect() === false)
        {
            return false;
        }

        return $info;
    }

    function getstream($host, $port, $queryport)
    {
        if (empty($queryport))
        {
            $this->port = $port;
        }
        else
        {
            $this->port = $queryport;
        }

        $this->host = $host;

        // get the infostream from server
        $this->r_info = $this->get_rules();
        $this->p_info = $this->get_players();

        if ($this->r_info)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function getvalue_byte($def)
    {
        // get value (byte) from raw data
        $tmp = $def[0];
	$this->r_info = substr($def, 1);

	return ord($tmp);
    }
    
    function getvalue_string($def)
    {
    
        // get value (string) from raw data
        $tmp = '';
	$index = 0;
	
	while (ord($def[$index]) != 0)
	{
	    $tmp .= $def[$index];
	    $index++;
	}
	$this->r_info = substr($def, $index+1);

	return $tmp;
    }
    
    function getrules($phgdir)
    {
        // response time
	$srv_rules['response'] = $this->response . ' ms';
        
	// get the rules values
	$srv_rules['cache']       = $this->getvalue_byte($this->r_info);   // 1 byte
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // 1 byte
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // 1 byte
	
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // x00
        $srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // x00
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // x00
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // x00
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // x00
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // x00
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // x00
	
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // 1 byte
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // 1 byte
	
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // x00
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // x00
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // x00
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // x00
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // x00
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // x00
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // x00
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // x00
	
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // 1 byte
	$srv_rules['hostname']    = $this->getvalue_string($this->r_info); // string (hostname)
	
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // x00
	$srv_rules['mapfile']     = $this->getvalue_string($this->r_info); // string (mapfile)
	
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // x00
	$srv_rules['mapname']     = $this->getvalue_string($this->r_info);  // string (mapname)
	
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // x00
	$srv_rules['gametype']    = $this->getvalue_string($this->r_info); // string (gametype)
	
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // 1 byte
	
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // x00
	$srv_rules['nowplayers']  = $this->getvalue_byte($this->r_info);   // 1 byte (nowplayers)
	
	$srv_rules['cache']      .= $this->getvalue_byte($this->r_info);   // x00
	$srv_rules['maxplayers']  = $this->getvalue_byte($this->r_info);   // 1 byte (nowplayers)
		
        // path to map picture
	$srv_rules['map_path'] = 'maps/descent3';
	

	// set default map picture
	$srv_rules['map_default'] = 'default.jpg';
	$srv_rules['mapname'] = $srv_rules['mapfile'];
        
        // set gamename
	$srv_rules['gamename'] = "Descent 3";
	
	// no privileges
	$srv_rules['sets'] = '-';

        // server general info
        $srv_rules['htmlinfo'] =
        '<tr><td align="left">Mapname:</td><td align="left">'
        . $srv_rules['mapname']
        . '</td></tr>' ."\n"
        . '<tr><td align="left">Players:</td><td align="left">'
        . $srv_rules['nowplayers'] . ' / ' . $srv_rules['maxplayers']
        . '</td></tr>' . "\n"
        . '<tr><td align="left">Response:</td><td align="left">'
        . $srv_rules['response']
        . '</td></tr>' . "\n"
        . '<tr><td align="left">Privileges:</td><td align="left">'
        . $srv_rules['sets']
        . '</td></tr>' . "\n";

        // server detail info
        $srv_rules['htmldetail'] =
        '<tr valign="top"><td align="left">Gamename:</td><td align="left">'
        . $srv_rules['gamename']
        . '</td></tr>' . "\n"
        . '<tr valign="top"><td align="left">Gametype:</td><td align="left">'
        . $srv_rules['gametype']
        . '</td></tr>' . "\n"
        . '<tr valign="top"><td align="left">Mapname:</td><td align="left">'
        . $srv_rules['mapname']
        . '</td></tr>' . "\n"
        . '<tr valign="top"><td align="left">Players:</td><td align="left">'
        . $srv_rules['nowplayers'] . ' / ' . $srv_rules['maxplayers']
        . '</td></tr>' . "\n"
        . '<tr valign="top"><td align="left">Response:</td><td align="left">'
        . $srv_rules['response']
        . '</td></tr>' . "\n"
        . '<tr valign="top"><td align="left">Privileges:</td><td align="left">'
        . $srv_rules['sets']
        . '</td></tr>' . "\n";
	
	// return all server rules
	return $srv_rules;
    }
    
    function getplayers()
    {
        $players = array();
	
	// set html thead
	$thead = "<tr><th>ID</th><th>Name</th></tr>";
	
        // filter the not needed code & create array
	$cache = substr($this->p_info, 4, -1);
	$players = explode("\x00", $cache);
	
	// how many player must search
	$nowplayers = count($players) - 1;
        
	// check the connected players and sort the ranking
	if ($nowplayers == 0)
	{
	    $thead .= "<tr align=center><td>--</td><td>--</td></tr>\n";
	}

	// store the html table line to the info array
	$srv_player = $thead;

	// manage the player data in the following code
	$index = 1;
	$client = 0;
	
	while ($nowplayers != 0)
	{
	    // strip html code from player name
	    $players[$client] = htmlentities($players[$client]);

            $tdata = "<tr align=center><td>$index.</td><td>$players[$client]</td></tr>\n";
	
	    $srv_player = $srv_player . $tdata;
	    $nowplayers--;
	    $client++;
	    $index++;
	}
         
	return $srv_player;
    }
}
