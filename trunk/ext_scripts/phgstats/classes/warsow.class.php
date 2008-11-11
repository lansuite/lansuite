<?php // WARSOW Game Class
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

class warsow
{
    var $maxlen   = 2048;
    var $write    = "\xFF\xFF\xFF\xFFgetinfo\x00";
    var $s_info   = false;
    var $g_info   = false;
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

    function splitdata()
    {
        // get rules from stream and write to g_info
	$this->s_info = substr($this->s_info, 18);
	$c_info = explode("\n", $this->s_info);
	$this->g_info = explode("\\", $c_info[0]);
        
	// get players from stream and write to p_info
	$index_old = 0;
	$index_new = 0;
	foreach ($c_info as $value)
	{
	    if ($index_old >= 1)
	    {
	    	$this->p_info[$index_new] = $value;
		$index_new++;
	    }
	    $index_old++;
	}
    }

    function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        
	return ((float)$usec + (float)$sec);
    }
    
    function getstream($host, $port, $queryport)
    {   
        // get the infostream from server
        $socket = fsockopen('udp://'. $host, $port, $errno, $errstr, 30);
	
	if ($socket === false)
	{
            echo "Error: $errno - $errstr<br>\n";
        }
	else
	{
            socket_set_timeout($socket, 3);
	    
	    $time_begin = $this->microtime_float();
	
            fwrite($socket, $this->write);
	    $this->s_info = fread($socket, $this->maxlen);
	    
	    $time_end = $this->microtime_float();
        }
        fclose($socket);
	
	// response time
	$this->response = $time_end - $time_begin;
	$this->response = ($this->response * 1000);
	$this->response = (int)$this->response;
	
	if ($this->s_info)
	{
	    // sort the infostring
	    $this->splitdata();
	    
	    return true;
	}
	else
	{
	    return false;
	}
    }

    function check_color($text, $switch)
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

        if ($switch == 1)
        { // colored string
            $search  = array (
            "/\^0/", "/\^1/", "/\^2/", "/\^3/",        //  1
            "/\^4/", "/\^5/", "/\^6/", "/\^7/",        //  2
            "/\^8/", "/\^9/", "/\^a/", "/\^b/",        //  3
            "/\^c/", "/\^d/", "/\^e/", "/\^f/",        //  4
            "/\^g/", "/\^h/", "/\^i/", "/\^j/",        //  5
            "/\^k/", "/\^l/", "/\^m/", "/\^n/",        //  6
            "/\^o/", "/\^p/", "/\^q/", "/\^r/",        //  7
            "/\^s/", "/\^t/", "/\^u/", "/\^v/",        //  8
            "/\^w/", "/\^x/", "/\^y/", "/\^z/",        //  9
            "/\^\//", "/\^\*/", "/\^\-/", "/\^\+/",    // 10
            "/\^\?/", "/\^\@/", "/\^</", "/\^>/",      // 11
            "/\^\&/", "/\^\)/", "/\^\(/", "/\^[A-Z]/", // 12
            "/\^\_/",                                  // 14
            "/&</", "/^(.*?)<\/font>/"                 // 15
            );

            $replace = array (
            "&<font color=$clr[0]>", "&<font color=$clr[1]>",   //  1
            "&<font color=$clr[2]>", "&<font color=$clr[3]>",   //  2
            "&<font color=$clr[4]>", "&<font color=$clr[5]>",   //  3
            "&<font color=$clr[6]>", "&<font color=$clr[7]>",   //  4
            "&<font color=$clr[8]>", "&<font color=$clr[9]>",   //  5
            "&<font color=$clr[10]>", "&<font color=$clr[11]>", //  6
            "&<font color=$clr[12]>", "&<font color=$clr[13]>", //  7
            "&<font color=$clr[14]>", "&<font color=$clr[15]>", //  8
            "&<font color=$clr[16]>", "&<font color=$clr[17]>", //  9
            "&<font color=$clr[18]>", "&<font color=$clr[19]>", // 10
            "&<font color=$clr[20]>", "&<font color=$clr[21]>", // 11
            "&<font color=$clr[22]>", "&<font color=$clr[23]>", // 12
            "&<font color=$clr[24]>", "&<font color=$clr[25]>", // 13
            "&<font color=$clr[26]>", "&<font color=$clr[27]>", // 14
            "&<font color=$clr[28]>", "&<font color=$clr[29]>", // 15
            "&<font color=$clr[30]>", "&<font color=$clr[31]>", // 16
            "&<font color=$clr[32]>", "&<font color=$clr[33]>", // 17
            "&<font color=$clr[34]>", "&<font color=$clr[35]>", // 18
            "&<font color=$clr[36]>", "&<font color=$clr[37]>", // 19
            "&<font color=$clr[38]>", "&<font color=$clr[39]>", // 20
            "&<font color=$clr[40]>", "&<font color=$clr[41]>", // 21
            "", "", "", "", "", "",                             // 22
            "", "</font><", "\$1"                               // 23
            );

            $ctext = preg_replace($search, $replace, $text);

            if ($ctext != $text)
            {
                $ctext = preg_replace("/$/", "</font>", $ctext);
            }

            return $ctext;
        }
        elseif ($switch == 2)
        { // colored numbers
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
    }
    
    function getrules($phgdir)
    {
        $srv_rules['sets'] = false;
       
        // response time
        $srv_rules['response'] = $this->response . ' ms';

	// privileges picture
	$sets['pass'] = '<img src="' . $phgdir . 'privileges/pass.gif" alt="pw">';
	$sets['be']   = '<img src="' . $phgdir . 'privileges/be.gif" alt="be">';

        // get the info strings from server info stream
	$srv_rules['hostname']   = $this->getvalue('sv_hostname',   $this->g_info);
	$srv_rules['gamename']   = $this->getvalue('gamename',      $this->g_info);
	$srv_rules['gametype']   = $this->getvalue('g_gametype',    $this->g_info);
	$srv_rules['version']    = $this->getvalue('version',       $this->g_info);
	$srv_rules['mapname']    = $this->getvalue('mapname',       $this->g_info);
	$srv_rules['maxplayers'] = $this->getvalue('sv_maxclients', $this->g_info);
	$srv_rules['needpass']   = $this->getvalue('g_needpass',    $this->g_info);
	$srv_rules['anticheat']  = $this->getvalue('sv_battleye',   $this->g_info);

        // scan the color tags of hostname
	$srv_rules['hostname'] = $this->check_color($srv_rules['hostname'], 1);
		
	// cut the long server system version info string 
	$srv_rules['version'] = substr($srv_rules['version'], 0, 4);
        
	// path to map picture and default info picture
	$srv_rules['map_path'] = 'maps/warsow';
	$srv_rules['map_default'] = 'default.jpg';

	// point system
	$srv_rules['points'] = 'Frags';
	
	// get the connected player
	$srv_rules['nowplayers'] = (count($this->p_info))-1;

	// set gamename and version
	$srv_rules['gamename'] = $srv_rules['gamename'] . ' ' . $srv_rules['version'];
       
        // privileges support
	// battleye anticheat (0 - off, 1 - optional, 2 - required)
	if ($srv_rules['anticheat'] == 1 || $srv_rules['anticheat'] == 2)
	{
            $srv_rules['sets'] .= $sets['be'];
	}
	if ($srv_rules['needpass'] == 1)
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
	
        // set html thead
	$thead = "<tr><th>ID</th>" .
	         "<th>Name</th>" .
	         "<th>Points</th>" .
	         "<th colspan=2>Ping</th></tr>";
        
	// how many players must search
        $nowplayers = count($this->p_info)-1;
        $nowplayers = $nowplayers - 1;
        $clients = 0;

	// get the data of each player
        while ($nowplayers != -1)
	{
	    $players[$clients] = $this->p_info[$nowplayers];
            $nowplayers--;
            $clients++;
        }
	
	// check the connected players and sort the ranking
        if ($players == false)
        {
	    $thead .= "<tr align=center><td>--</td>" .
                      "<td>--</td><td>--</td><td colspan=2>--</td></tr>\n";
        }
	
	// store the html table line to the info array
        $srv_player = $thead;

        // manage the player data in the following code
	$index = 1;

        while ($clients)
	{
	    $clients--;

	    list ($cache[$index], $player[$index], $team[$index]) = split ('\"', $players[$clients]);
	    list ($points[$index], $ping[$index], $team[$index]) =  split(' ', $cache[$index]);
            
	    $player[$index] = htmlentities($player[$index]);
	    $player[$index] = $this->check_color($player[$index], 1);
            $ping[$index]   = $this->check_color($ping[$index],   2);
	    
	    // -9999 workaround
	    if ($points[$index] == '-9999')
	    {
                $points[$index] = '0';
	    }
	    
            $tdata = "<tr align=center><td>$index.</td>" .
                     "<td>$player[$index]</td>" .
                     "<td>$points[$index]</td>" .
                     "<td colspan=2>$ping[$index]</td></tr>\n";
            
	    $srv_player = $srv_player . $tdata;
	    $index++;
	}
        
	return $srv_player;
    }
}

