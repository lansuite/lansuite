<?php // Unreal Tournament Game Class
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

class ut
{
    var $host     = false;
    var $port     = false;
    var $socket   = false;
    var $g_info   = false;
    var $r_info   = false;
    var $p_info   = false;
    var $response = false;
      
    function getvalue($srv_value, $srv_data)
    {
        // search the value of selected rule and return it
        $srv_value = array_search ($srv_value, $srv_data);

        if ($srv_value === false)
        {
            return false;
        }
        else
        {
            $srv_value = $srv_data[$srv_value+1];

            return $srv_value;
        }
    }

    function splitdata($stream)
    {
        $cut = strpos($stream, "player_");
	$p_info = substr($stream, $cut);
        $this->p_info = explode("\\", $p_info);	
        $this->g_info = explode("\\", $stream);
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

    function get_info()
    {
        $write = "\\info\\";
        $stream = $this->get_status($write);
        
	return $stream;
    }
    function get_rules()
    {
        $write = "\\status\\";
	$stream = $this->get_status($write);

	return $stream;
    }

    function get_players()
    {
        
        $write = "\\players\\";
        $stream = $this->get_status($write);
        
	return $stream;
    }

    function get_queryid($stream)
    {
        if (stristr($stream, "\\final"))
	{
	    $cut = strpos($stream, "\\final");
	    $stream = substr($stream, 0, $cut);
	}
	    
	$cache = substr($stream, -1, 1);
	
	return $cache;
    }

    function rm_queryid($stream)
    {
        $end = strpos($stream, "\\final");
	if ($end == false)
	{
            $end = strpos($stream, "queryid\\");
	}
	$cache = substr($stream, 0, $end);

	return $cache;
    }
    
    function get_status($write)
    {
        $packets = array();

        $ready   = false;
	$timeout = false;
	
	$packets[0] = false;
	$packets[1] = false;
	$packets[2] = false;
	$packets[3] = false;
	$packets[4] = false;
	$packets[5] = false;
	
	$info = '';
	$cache = '';
	$finalid = 0;
	
        if ($this->connect() === false)
	{
	    return false;
	}
	
        socket_set_timeout($this->socket, 1);

	$time_begin = microtime();

        fwrite($this->socket, $write);

	while ($ready == false && $timeout == false)
	{
            $first = fread($this->socket, 1);
	    $this->response =  microtime() - $time_begin;
	    
            $status = socket_get_status($this->socket);
            $length = $status['unread_bytes'];
            
	    if ($length > 0)
            {
                $cache = fread($this->socket, $length);
            }
	    
	    $id = $this->get_queryid($cache);

	    if (stristr($cache, "\\final") && $id == 1)
	    {
	        $packets[0] = $cache;
                
		$ready = true;
	    }
	    elseif (stristr($cache, "\\final\\") && $id > 1)
	    {
	        $finalid = substr($id, -1, 1);
	        $packets[5] = $cache;
                
		$ready = false;
	    }
	    elseif ($finalid > 0)
	    {
		$packets[$id] = $cache;
		
		if (count($packets) == $finalid)
		{
		    $ready = true;
		}
		else
		{
		    $ready = false;
		}
	    }
	    else
	    {
	        $packets[$id] = $cache;
		
                $ready = false;
	    }

	    if ($status['timed_out'] == true)
	    {
	        $timeout = true;
	    }
        }
		
        $info  = $this->rm_queryid($packets[0]);
	$info .= $this->rm_queryid($packets[1]);
	$info .= $this->rm_queryid($packets[2]);
	$info .= $this->rm_queryid($packets[3]);
	$info .= $this->rm_queryid($packets[4]);
	$info .= $this->rm_queryid($packets[5]);
	
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
	    $this->port = $port + 1;
	}
	else
	{
	    $this->port = $queryport;
	}

	$this->host = $host;

        // get the infostream from server
	$this->r_info = $this->get_rules();
	
	if ($this->r_info)
	{
	    $this->splitdata($this->r_info);
	    
	    return true;
	}
	else
	{
	    return false;
	}
    }

    function check_color($text)
    {
        $clr = array ( // colors
        "\"#000000\"", "\"#DA0120\"", "\"#00B906\"", "\"#E8FF19\"", //  1
        "\"#170BDB\"", "\"#23C2C6\"", "\"#E201DB\"", "\"#FFFFFF\"", //  2
        "\"#CA7C27\"", "\"#757575\"", "\"#EB9F53\"", "\"#106F59\"", //  3
        "\"#5A134F\"", "\"#035AFF\"", "\"#681EA7\"", "\"#5097C1\"", //  4
        "\"#BEDAC4\"", "\"#024D2C\"", "\"#7D081B\"", "\"#90243E\"", //  5
        "\"#743313\"", "\"#A7905E\"", "\"#555C26\"", "\"#AEAC97\"", //  6
        "\"#C0BF7F\"", "\"#000000\"", "\"#DA0120\"", "\"#00B906\"", //  7
        "\"#E8FF19\"", "\"#170BDB\"", "\"#23C2C6\"", "\"#E201DB\"", //  8
        "\"#FFFFFF\"", "\"#CA7C27\"", "\"#757575\"", "\"#CC8034\"", //  9
        "\"#DBDF70\"", "\"#BBBBBB\"", "\"#747228\"", "\"#993400\"", // 10
        "\"#670504\"", "\"#623307\""                                // 11
        );

        // colored numbers
        if ($text <= 39)
        {
            $ctext = "<font color=$clr[7]>$text</font>";
        }
        elseif ($text <= 69)
        {
            $ctext = "<font color=$clr[5]>$text</font>";
        }
        elseif ($text <= 129)
        {
            $ctext = "<font color=$clr[8]>$text</font>";
        }
        elseif ($text <= 399)
        {
            $ctext = "<font color=$clr[9]>$text</font>";
        }
        else
        {
            $ctext = "<font color=$clr[1]>$text</font>";
        }

            return $ctext;
    }
    
    function getrules($phgdir)
    {
	$srv_rules['sets'] = false;
	
        // response time
	$srv_rules['response'] = $this->response . ' ms';
	
        // ut setting pics
	$sets['pass']    = '<img src="' . $phgdir . 'privileges/pass.gif" alt="pw">';
        
	// get the info strings from server info stream
	$srv_rules['hostname']     = $this->getvalue('hostname',   $this->g_info);
	$srv_rules['gametype']     = $this->getvalue('gametype',   $this->g_info);
	$srv_rules['gamename']     = $this->getvalue('gamename',   $this->g_info);
	$srv_rules['mapname']      = $this->getvalue('mapname',    $this->g_info);
	$srv_rules['maxplayers']   = $this->getvalue('maxplayers', $this->g_info);
	$srv_rules['version']      = $this->getvalue('gamever',    $this->g_info);
	$srv_rules['needpass']     = $this->getvalue('password',   $this->g_info);
        
	// path to map picture and default info picture
	$srv_rules['map_path'] = 'maps/ut';
	$srv_rules['map_default'] = 'default.jpg';
	
	// get the connected player
	$srv_rules['nowplayers'] = $this->getvalue('numplayers', $this->g_info);
        
	// complete the gamename
	$srv_rules['gamename'] = 'Unreal Tournament<br>' . 'Version ' . $srv_rules['version'];
	
	// server privileges
	if ($srv_rules['needpass'] == 'True')
	{
	    $srv_rules['sets'] .= $sets['pass'];
	}
	
	if ($srv_rules['sets'] === false)
	{
            $srv_rules['sets'] = '-';
	}
	
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

	// set html thead for playerlist without teams
	$thead = '<tr><th>Rank</th>' .
	         '<th>Name</th>'     .
	         '<th>Points</th>'   .
		 '<th>Team</th>'     .
		 '<th>Ping</th></tr>';
	
        // how many players must search
        $nowplayers = $this->getvalue('numplayers', $this->g_info);
        
	$clients = 0; 
        
	// get the data of each player and add the team status
        while ($nowplayers != 0)
        {
            $pl       = $this->getvalue("player_$clients", $this->p_info);
            $pl_frags = $this->getvalue("frags_$clients",  $this->p_info);
            $pl_team  = $this->getvalue("team_$clients",   $this->p_info);
	    $pl_ping  = $this->getvalue("ping_$clients",   $this->p_info);
	    
	    // UT Version 451 fixed team blank
	    if ($pl_ping[0] != ' ')
	    {
                $pl_team .= ' ';
	    }
            
            $players[$clients] = 
	    $pl_frags  . ' ' .
	    $pl_team   .
            $pl_ping   . ' ' .
            "\"$pl\"";

	    $nowplayers--;
	    $clients++;
        }
        
	// check the connected players and sort the ranking
	if ($players == false)
	{
	    $thead .= 
	    '<tr align=center><td>--</td>' .
	    '<td>--</td>' .
	    '<td>--</td>' .
	    '<td>--</td>' .
	    '<td>--</td></tr>' . "\n";
	}
	else
	{
	    sort($players, SORT_NUMERIC);
	}

	// store the html table line to the info array
	$srv_player = $thead;
        
	// manage the player data in the following code
	$index = 1;
	$clients = $clients - 1;
	
	while ($clients != -1)
	{   
	     list ($cache[$index], $player[$index]) = split ('\"', $players[$clients]);
	     list ($frags[$index],
		   $team[$index],
		   $ping[$index])  = split (' ',  $cache[$index]);

	     $player[$index] = htmlentities($player[$index]);
	     $ping[$index]   = $this->check_color($ping[$index]);
	     
	     $tdata = "<tr align=center><td>$index.</td>" .
	              "<td>$player[$index]</td>" .
	              "<td>$frags[$index]</td>"  .
		      "<td>$team[$index]</td>"   .
	              "<td>$ping[$index]</td></tr>\n";
	                  
	     $srv_player = $srv_player . $tdata;
	     $index++;
	     $clients--;
	}

        return $srv_player;
    }
}
