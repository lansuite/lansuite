<?php // Battlefield Game Class
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

class bf
{
    var $host     = false;
    var $port     = false;
    var $socket   = false;
    var $maxlen   = 2048;
    var $s_info   = false;
    var $g_info   = false;
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

    function splitdata()
    {
        // get rules from stream and write to g_info
        $c_info = explode("\n", $this->s_info);
        $this->g_info = explode("\\", $c_info[0]);
    }

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
    
    function get_status()
    {
        if ($this->connect() === false)
	{
	    return false;
	}
        
	socket_set_timeout($this->socket, 4);
	
	$time_begin = $this->microtime_float();
        
        fwrite($this->socket, "\\status\\");
        $info = fread($this->socket, 1);
        
	$time_end = $this->microtime_float();

	if (empty($info))
	{
	    return false;
	}

        while (substr($info, -6) != "\final")
        {
            $info .= fread($this->socket, 1);
        }
        
	// response time
	$this->response = $time_end - $time_begin;
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
            $this->port = $port + 8433;
        }
        else
        {
            $this->port = $queryport;
        }
        
        $this->host = $host;

        // get the infostream from server
	$this->s_info .= $this->get_status();
	
	if ($this->s_info)
	{
	    $this->splitdata();
	
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
	
        // bf setting pics
	$sets['pb']      = '<img src="' . $phgdir . 'privileges/pb.gif" alt="pb">';
	$sets['pass']    = '<img src="' . $phgdir . 'privileges/pass.gif" alt="pw">';
        
	// get the info strings from server info stream
	$srv_rules['hostname']   = $this->getvalue('hostname',      $this->g_info);
	$srv_rules['gametype']   = $this->getvalue('gametype',      $this->g_info);
	$srv_rules['gamename']   = $this->getvalue('gamename',      $this->g_info);
	$srv_rules['version']    = $this->getvalue('gamever',       $this->g_info);
	$srv_rules['mapname']    = $this->getvalue('mapname',       $this->g_info);
	$srv_rules['maxplayers'] = $this->getvalue('maxplayers',    $this->g_info);
	$srv_rules['punkbuster'] = $this->getvalue('sv_punkbuster', $this->g_info);
	$srv_rules['needpass']   = $this->getvalue('password',      $this->g_info);
	
	// map picture and default info picture
	$srv_rules['map_default'] = 'default.jpg';
	
	// get the connected player
	$srv_rules['nowplayers'] = $this->getvalue('numplayers', $this->g_info);
        
	// complete the gamename
	$srv_rules['gamename'] = 'Battlefield 1942<br>' . $srv_rules['version'];
	$srv_rules['map_path'] = 'maps/bf';
	
	// server privileges
	if ($srv_rules['punkbuster'] == 1)
	{
	    $srv_rules['sets'] .= $sets['pb'];
	
	}
	if ($srv_rules['needpass'] == 1)
	{
            $srv_rules['sets'] .= $sets['pass'];
	}

	if ($srv_rules['sets'] === false)
	{
	    $srv_rules['sets'] = '-';
	}
        
	// server general info (added by balgo 05.01.2006 only for test)
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

        // server detail info (added by balgo 05.01.2006 only for test)
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
	         '<th>Score</th>'    .
		 '<th>Team</th>'     .
		 '<th>Deaths</th>'   .
		 '<th>Kills</th>'    .
		 '<th>Ping</th></tr>';
	
        // how many players must search
        $nowplayers = $this->getvalue('numplayers', $this->g_info);
	$nowplayers = $nowplayers - 1;
        $clients = 0;
       
        // get the data of each player and add the team status
        while ($nowplayers != -1)
        {
	    $pl = $this->getvalue("playername_$clients", $this->g_info);
	    if ($pl == '')
	    {
	        $pl = 'Player Bot';
		$pl_score  = '-';
		$pl_team   = '-';
	        $pl_deaths = '-';
		$pl_kills  = '-';
		$pl_ping   = '-';
	    }
	    else
	    {
	        $pl_score  = $this->getvalue("score_$clients",  $this->g_info);
	        $pl_team   = $this->getvalue("team_$clients",   $this->g_info);
	        $pl_deaths = $this->getvalue("deaths_$clients", $this->g_info);
	        $pl_kills  = $this->getvalue("kills_$clients",  $this->g_info);
	        $pl_ping   = $this->getvalue("ping_$clients",   $this->g_info);
	    }
	    
            $players[$clients] = $pl_score  . ' ' . 
	                         $pl_team   . ' ' .
				 $pl_deaths . ' ' .
				 $pl_kills  . ' ' .
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

	while ($clients)
	{
	     $clients--;
	     
	     list ($cache[$index], $player[$index]) = split ('\"', $players[$clients]);
	     list ($score[$index],
	           $team[$index],
		   $deaths[$index],
		   $kills[$index],
		   $ping[$index])  = split (' ',  $cache[$index]);
             
	     $player[$index] = htmlentities($player[$index]);
	     $ping[$index]   = $this->check_color($ping[$index]);
	     
	     $tdata = "<tr align=center><td>$index.</td>" .
	              "<td>$player[$index]</td>" .
	              "<td>$score[$index]</td>"  .
		      "<td>$team[$index]</td>"   .
		      "<td>$deaths[$index]</td>"  .
		      "<td>$kills[$index]</td>"  .
	              "<td>$ping[$index]</td></tr>\n";
	                  
	     $srv_player = $srv_player . $tdata;
	     $index++;
	}

        return $srv_player;
    }
}
