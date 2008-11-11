<?php // Old Half-Life Game Class
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

class hl_old
{
    var $maxlen = 2048;
    var $q_info   = "\xFF\xFF\xFF\xFF\x64\x65\x74\x61\x69\x6C\x73";
    var $q_rules  = "\xFF\xFF\xFF\xFF\x72\x75\x6C\x65\x73";
    var $q_player = "\xFF\xFF\xFF\xFF\x70\x6C\x61\x79\x65\x72\x73";
    var $s_info   = false;
    var $response = false;

    function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
 
        return ((float)$usec + (float)$sec);
    }

    function get_info($socket)
    {
        // get server info data
	socket_set_timeout($socket, 3);
        
	$time_begin = $this->microtime_float();
	
	fwrite($socket, $this->q_info);
	$this->s_info['info'] = fread($socket, $this->maxlen);

	$time_end  = $this->microtime_float();
        
	// response time
	$this->response = $time_end - $time_begin;
	$this->response = ($this->response * 1000);
	$this->response = (int)$this->response;
    }

    function get_rules($socket)
    {
        // get server rules data
        socket_set_timeout($socket, 3);
	fwrite($socket, $this->q_rules);
	$this->s_info['rules'] = fread($socket, $this->maxlen);
    }

    function get_players($socket)
    {
	socket_set_timeout($socket, 3);
	fwrite ($socket, $this->q_player);
	$this->s_info['player'] = fread($socket, $this->maxlen);
    }

    function getstream($host, $port, $queryport)
    {   
        // get the full info data from server
        $socket = fsockopen('udp://'. $host, $port, $errno, $errstr, 30);
	
	if ($socket === false)
	{
            echo "Error: $errno - $errstr<br>\n";
        }
	else
	{
	    $this->get_info($socket);
	    
	    /* dont need rules data yet
             * $this->get_rules($socket);
	     */
	    
	    $this->get_players($socket);
        }
        fclose($socket);
	
	if ($this->s_info['info'])
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
        $tmp = $this->s_info[$def][0];
	$this->s_info[$def] = substr($this->s_info[$def], 1);

	return ord($tmp);
    }
    
    function getvalue_string($def)
    {
    
        // get value (string) from raw data
        $tmp = '';
	$index = 0;
	
	while (ord($this->s_info[$def][$index]) != 0)
	{
	    $tmp .= $this->s_info[$def][$index];
	    $index++;
	}
	$this->s_info[$def] = substr($this->s_info[$def], $index+1);

	return $tmp;
    }
    
    function getvalue_sint($def)
    {
        // get value (int16) from raw data
	$tmp = substr($this->s_info[$def], 0, 2);
	$this->s_info[$def] = substr($this->s_info[$def], 2);
	$array = @unpack('Sshort', $tmp);
 
        return $array['short'];				
    }

    function getvalue_lint($def)
    {
        // get value (int32) from raw data
        $tmp = substr($this->s_info[$def], 0, 4);
	$this->s_info[$def] = substr($this->s_info[$def], 4);
	$array = @unpack('Lint', $tmp);
	
	return $array['int'];
    }

    function getvalue_float($def)
    { 
        // get value (float) from raw data
	$tmp = substr($this->s_info[$def], 0, 4);
	$this->s_info[$def] = substr($this->s_info[$def], 4);
	$array = @unpack('ffloat', $tmp);

	return $array['float'];
    }
        
    function getrules($phgdir)
    {
        $srv_rules['sets'] = false;
	
        // response time
	$srv_rules['response'] = $this->response . ' ms';
        
	// game setting pics
	$sets['pass'] = '<img src="' . $phgdir . 'privileges/pass.gif" alt="pw">';
        
	// set array key to info
        $def = 'info';
	
        // get the servertype (hl1 or source)
	$servertype = $this->s_info['info'][4];
	
	// filter the not needed code
	$this->s_info['info'] = substr($this->s_info['info'], 5);
	
	// if server running hl1 game get the following values
	if ($servertype == 'm')
	{
	    $srv_rules['gameip']      = $this->getvalue_string($def);
	    $srv_rules['hostname']    = $this->getvalue_string($def);
	    $srv_rules['mapname']     = $this->getvalue_string($def);
	    $srv_rules['gamedir']     = $this->getvalue_string($def);
	    $srv_rules['gametype']    = $this->getvalue_string($def);
	    $srv_rules['nowplayers']  = $this->getvalue_byte($def);
	    $srv_rules['maxplayers']  = $this->getvalue_byte($def);
            $srv_rules['netver']      = $this->getvalue_byte($def);
	    $srv_rules['dedicated']   = $this->getvalue_byte($def);
	    $srv_rules['os']          = $this->getvalue_byte($def);
	    $srv_rules['password']    = $this->getvalue_byte($def);
	    $srv_rules['is_mod']      = $this->getvalue_byte($def);
            $srv_rules['url_info']    = $this->getvalue_string($def);
            $srv_rules['url_down']    = $this->getvalue_string($def);
	    $srv_rules['unused']      = $this->getvalue_string($def);
	    $srv_rules['mod_version'] = $this->getvalue_lint($def);
	    $srv_rules['mod_size']    = $this->getvalue_lint($def);
            $srv_rules['sv_only']     = $this->getvalue_byte($def);
	    $srv_rules['cus_cl']      = $this->getvalue_byte($def);
            $srv_rules['secure']      = $this->getvalue_byte($def);
            $srv_rules['bots']        = $this->getvalue_byte($def);
        
	    // path to map picture
	    $srv_rules['map_path'] = 'maps/hl';
        }
	
	if ($servertype == 'I')
	{
	    $srv_rules['netver']     = $this->getvalue_byte($def);
	    $srv_rules['hostname']   = $this->getvalue_string($def);
	    $srv_rules['mapname']    = $this->getvalue_string($def);
	    $srv_rules['gamedir']    = $this->getvalue_string($def);
	    $srv_rules['gametype']   = $this->getvalue_string($def);
	    $srv_rules['app_id']     = $this->getvalue_sint($def);
	    $srv_rules['nowplayers'] = $this->getvalue_byte($def);
	    $srv_rules['maxplayers'] = $this->getvalue_byte($def);
	    $srv_rules['bots']       = $this->getvalue_byte($def);
	    $srv_rules['dedicated']  = $this->getvalue_byte($def);
	    $srv_rules['os']         = $this->getvalue_byte($def);
	    $srv_rules['password']   = $this->getvalue_byte($def);
	    $srv_rules['secure']     = $this->getvalue_byte($def);
	    $srv_rules['version']    = $this->getvalue_string($def);

            // path to map picture
	    $srv_rules['map_path'] = 'maps/hl2';
	}

	// set default map picture
	$srv_rules['map_default'] = 'default.jpg';
        
        // set gamename with gametype value (because no gametype info in hl data
	$srv_rules['gamename'] = $srv_rules['gametype'] . '<br>';
	
	// privileges pictures
	if ($srv_rules['password'] == 1)
        {
	    $srv_rules['sets'] = $sets['pass'];
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
        . '<tr valign="top"><td align="left">Gamedir:</td><td align="left">'
        . $srv_rules['gamedir']
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

        // set array key to player
	$def = 'player';

	// set html thead
	$thead = "<tr><th>Rank</th><th>Name</th><th>Frags</th><th colspan=2>Time</th></tr>";
	
        // filter the not needed code
	$this->s_info[$def] = substr($this->s_info[$def], 5);
	
	// how many player must search
	$nowplayers = $this->getvalue_byte($def);
        
	// get the data of each player
	while ($nowplayers != 0)
	{
	    $index = $this->getvalue_byte($def);
	    $nick  = $this->getvalue_string($def);
	    $frags = $this->getvalue_lint($def);
	    $time  = $this->getvalue_float($def);
	    
	    $minutes = floor($time / 60);
	    $h       = floor($minutes / 60);
	    $seconds = floor($time - ($minutes * 60));
	    $minutes = $minutes - ($h * 60);
	    
	    $time = sprintf("%02s:%02s:%02s", $h, $minutes, $seconds);

	    // scan connecting players
	    if ($time == '00:00:00')
	    {
	        $nick =  'new connection';
		$frags = '-';
	    }
	    
            $players[$nowplayers] = $frags . " " . $time . " " . "\"$nick\"";
	    $nowplayers--;
	}
	
	// check the connected players and sort the ranking
	if ($players)
	{
	    sort($players, SORT_NUMERIC);
	}
	else
	{
	    $thead .= "<tr align=center><td>--</td><td>--</td><td>--</td><td colspan=2>--</td></tr>\n";
	}

	// store the html table line to the info array
	$srv_player = $thead;

	// check how many players scanned
	$clients = count($players);
	$clients = $clients - 1;
        
	// manage the player data in the following code
	$index = 1;
	
	while ($clients != -1)
	{
	    list ($cache[$index], $player[$index]) = split ('\"', $players[$clients]);
	    list ($points[$index], $ping[$index]) =  split(' ', $cache[$index]);

	    // strip html code from player name
	    $player[$index] = htmlentities($player[$index]);

            $tdata = "<tr align=center><td>$index.</td><td>$player[$index]</td><td>$points[$index]</td><td colspan=2>$ping[$index]</td></tr>\n";
	
	    $srv_player = $srv_player . $tdata;
	    $clients--;
	    $index++;
	}
         
	return $srv_player;
    }
}
