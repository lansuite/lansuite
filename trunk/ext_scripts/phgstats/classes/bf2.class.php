<?php // Battlefield 2 / 2142 Game Class
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

class bf2
{
    var $host     = false;
    var $port     = false;
    var $socket   = false;
    var $write    = "\xFE\xFD\x09\x10\x20\x30\x40";
    var $write2	  = "\xFE\xFD\x00\x10\x20\x30\x40";
    var $split    = "\xFF\xFF\xFF\x01";
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
	$g_end  = strpos($this->s_info, 'player_');
	$g_info = substr($this->s_info, 0, $g_end);
	$this->g_info = explode("\x00", $g_info);
	
	// now get the player data
	$p_end  = strlen($this->s_info);
	$p_info = substr($this->s_info, $g_end, $p_end);
	
	$this->p_info = $p_info;
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
        if (fclose($this->socket))
	{
	    return true;
	}

	return false;
    }

    function get_split($info)
    {
        $end = strpos($info, "num") +3;
	$split = substr($info, $end+1, 2);
	$number = ord($split);
	
	return $number;
    }

    function rm_split($info)
    {
        $info = substr($info, 15);
	
	return $info;
    }
    
    function rm_end($info)
    {
        $reverse = '';
	$cache   = '';
	
        $cache = substr($info, 0, -1);
	$end  = strlen($cache);
        
	while ($end != -1)
        {
	    if ($end--)
	    {
                $reverse .= $cache[$end];
	    }
	}
	
	$cut = substr($reverse, strpos($reverse, "\x00"));
        $end = strlen($cut);
        
	$info = '';
        
	while ($end != -1)
        {
	    if ($end--)
	    {
                $info .= $cut[$end];
	    }
        }
	
	return $info;
    }
    
    function get_status()
    {
	$timeout = false;
	$ready   = 0;
        
	$data[0] = false;
	$data[1] = false;
	$data[2] = false;

	$packets[0]   = false;
	$packets[1]   = false;
	$packets[128] = false;
	$packets[129] = false;
	$packets[130] = false;
	
        if ($this->connect() === false)
	{
	    return false;
	}
        
	socket_set_timeout($this->socket, 3);
	
	$time_begin = $this->microtime_float();
	fwrite($this->socket, $this->write);

	$info = fread($this->socket, 20);
	$time_end = $this->microtime_float();
	$this->response = $time_end - $time_begin;

	$cache = substr($info, 5, -1);
	$cache = pack("H*", dechex($cache));

	fwrite($this->socket, $this->write2 . $cache . $this->split);

        while ($ready == false && $timeout == false)
        {
            $info = fread($this->socket, 1);

            $status = socket_get_status($this->socket);
            $length = $status['unread_bytes'];

            if ($length > 0)
            {
               $info = fread($this->socket, $length);
            }

            // first of more packets (1/n)
            if ($this->get_split($info) == 0)
            {
                $info = $this->rm_split($info);

                $data[0] = $this->rm_end($info);
                $packets[0] = true;

                if ($packets[129] == true)
                {
                    $ready = true;
                }
                elseif ($packets[1] == true && $packets[130] == true)
                {
                    $ready = true;
                }
                else
                {
                    $ready = false;
                }
            }
            // second packet of three (2/3)
            elseif ($this->get_split($info) == 1)
            {
                $info = $this->rm_split($info);
                $info = substr($info, strpos($info, "\x00")+1);
                
		$data[1] = $this->rm_end($info);

                $packets[1] = true;

                if ($packets[0] == true && $packets[130] == true)
                {
                    $ready = true;
                }
                else
                {
                    $ready = false;
                }
            }
            // only this one packet (1/1)
            elseif ($this->get_split($info) == 128)
            {
               $info = $this->rm_split($info);
                $data[0] = $info;

                $ready = true;
            }
            // second packet of two (2/2)
            elseif ($this->get_split($info) == 129)
            {
               $info = $this->rm_split($info);
               $info = substr($info, strpos($info, "\x00")+1);
               $data[1] = $info;

               $packets[129] = true;

               if ($packets[0] == true)
               {
                   $ready = true;
               }
               else
               {
                   $ready = false;
               }
            }
            // third packet of three (3/3)
            elseif ($this->get_split($info) == 130)
            {
                $info = $this->rm_split($info);
                $info = substr($info, strpos($info, "\x00")+1);
                $data[2] = $info;

                $packets[130] = true;

                if ($packets[0] == true && $packets[1] == true)
                {
                   $ready = true;
                }
                else
                {
                    $ready = false;
                }
            }

            if ($status['timed_out'] == true)
            {
                $timeout = true;
            }
        }

        $info = $data[0] . $data[1] . $data[2];

        if (empty($info) || $info > 100)
	{
	    return false;
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
            $this->port = $port + 13333;
        }
        else
        {
            $this->port = $queryport;
        }
        
        $this->host = $host;

        // get the infostream from server
	$this->s_info = $this->get_status();
        
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
	
        // bfv setting pics
	$sets['pb']      = '<img src="' . $phgdir . 'privileges/pb.gif" alt="pb">';
	$sets['pass']    = '<img src="' . $phgdir . 'privileges/pass.gif" alt="pw">';
        
	// get the info strings from server info stream
	$srv_rules['hostname']    = $this->getvalue('hostname',      $this->g_info);
	$srv_rules['gametype']    = $this->getvalue('gametype',      $this->g_info);
	$srv_rules['gamevariant'] = $this->getvalue('gamevariant',   $this->g_info);
	$srv_rules['version']     = $this->getvalue('gamever',       $this->g_info);
	$srv_rules['mapname']     = $this->getvalue('mapname',       $this->g_info);
	$srv_rules['maxplayers']  = $this->getvalue('maxplayers',    $this->g_info);
	$srv_rules['anticheat']   = $this->getvalue('bf2_anticheat', $this->g_info);
	$srv_rules['needpass']    = $this->getvalue('password',      $this->g_info);
        
	// path to map picture and default info picture
	if ($srv_rules['gamevariant'] == 'bf2')
	{
	    $srv_rules['map_path'] = 'maps/bf2';
	    $srv_rules['gamename'] = 'Battlefield 2';
	}
	else
	{
	    $srv_rules['map_path'] = 'maps/bf2142';
	    $srv_rules['gamename'] = 'Battlefield 2142';
	}

	$srv_rules['map_default'] = 'default.jpg';
	
	// get the connected player
	$srv_rules['nowplayers'] = $this->getvalue('numplayers', $this->g_info);
        
	// complete the gamename
	$srv_rules['gamename'] .= '<br>(' . $srv_rules['version'] .')';
	
	// server privileges
	if ($srv_rules['needpass'] == 1)
	{
	    $srv_rules['sets'] .= $sets['pass'];
	}
	if ($srv_rules['anticheat'] == 1)
	{
            $srv_rules['sets'] .= $sets['pb'];
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
	$clients = 0;
	
	// set html thead for playerlist without teams
	$thead = '<tr><th>Rank</th>' .
	         '<th>Name</th>'     .
		 '<th>Score</th>'    .
		 '<th>Team</th>'     .
		 '<th>Death</th>'    .
		 '<th>Skill</th>'    .
		 '<th>Ping</th></tr>';
	
        // how many players must search
        $nowplayers = $this->getvalue('numplayers', $this->g_info);
	if ($nowplayers == 0)
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
	    $nowplayers = $nowplayers - 1;
        
	    // sort the data to can scan it better
	    $cache    = explode ("\x00\x00", $this->p_info);

            $c_pl     = explode("\x00", $cache[1]);
	    $c_score  = explode("\x00", $cache[3]);
	    $c_ping   = explode("\x00", $cache[5]);
	    $c_team   = explode("\x00", $cache[7]);
	    $c_deaths = explode("\x00", $cache[9]);
	    $c_pid    = explode("\x00", $cache[11]);
	    $c_skill  = explode("\x00", $cache[13]);
	
            // get the data of each player and add the team status
            while ($nowplayers != -1)
            {   
	        $pl        = $c_pl[$nowplayers];
	        $pl_score  = $c_score[$nowplayers];
	        $pl_ping   = $c_ping[$nowplayers];
	        $pl_team   = $c_team[$nowplayers];
	        $pl_deaths = $c_deaths[$nowplayers];
	        $pl_pid    = $c_pid[$nowplayers];
	        $pl_skill  = $c_skill[$nowplayers];
	    
	        // no player data
	        if ($pl == '')
	        {
	            $pl        = '-';
		    $pl_score  = '-';
		    $pl_ping   = '-';
		    $pl_team   = '-';
		    $pl_deaths = '-';
		    $pl_pid    = '-';
		    $pl_skill  = '-';
	        }
	        else
	        {
	            $players[$clients] =
		        $pl_score  . ' ' . 
	                $pl_team   . ' ' .
		        $pl_deaths . ' ' .
		        $pl_skill  . ' ' .
		        $pl_ping   . ' ' .
		        "\"$pl\"";
                }
		
	        $nowplayers--;
	        $clients++;
	    }
	    sort($players, SORT_NUMERIC);
	}

	// store the html table line to the info array
	$srv_player = $thead;
        
	// manage the player data in the following code
	$index = 1;

	while ($clients != 0)
	{
	     $clients--;
	     
	     list ($cache[$index], $player[$index]) = split ('\"', $players[$clients]);
	     list ($score[$index],
		   $team[$index],
		   $deaths[$index],
		   $skill[$index],
		   $ping[$index])  = split (' ',  $cache[$index]);
             
	     $player[$index] = htmlentities($player[$index]);
	     $ping[$index]   = $this->check_color($ping[$index]);
	     
	     $tdata = "<tr align=center><td>$index.</td>" .
	              "<td>$player[$index]</td>" .
	              "<td>$score[$index]</td>"  .
		      "<td>$team[$index]</td>"  .
		      "<td>$deaths[$index]</td>"    .
		      "<td>$skill[$index]</td>"  .
	              "<td>$ping[$index]</td></tr>\n";
	                  
	     $srv_player = $srv_player . $tdata;
	     $index++;
	}

        return $srv_player;
    }
}
