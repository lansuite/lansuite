<?php // Teamspeak Game Class
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

class ts
{
    var $maxlen   = 1024;
    var $response = false;
    var $s_info = '';

    function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        
        return ((float)$usec + (float)$sec);
    }

    function splitdata()
    {
        $this->s_info    = explode('OK', $this->s_info);
	$this->s_info[0] = str_replace ('[TS]', '', $this->s_info[0]);
	$this->s_info[0] = str_replace ("\n", '', $this->s_info[0]);

	$this->p_info    = explode("\n", $this->s_info[1]);

	$this->d_info    = explode("\n", $this->s_info[2]);
	$this->s_info[3] = explode("\n", $this->s_info[3]);
    }

    function getstream($host, $port, $queryport)
    {
	// get the infostream from server

	if (empty($queryport))
	{
		$queryport = '51234';
	}
	
        $socket = @fsockopen($host, $queryport, $errno, $errstr, 30);

	if ($socket === false)
	{
            #echo "Error: $errno - $errstr<br>\n";
        }
	else
	{
            socket_set_timeout($socket, 3);
	    
	    $time_begin = $this->microtime_float();
	    
	    fwrite($socket, 'ver ' . "\n");
	    fwrite($socket, 'pl ' . $port . "\n");
	    fwrite($socket, 'si ' . $port . "\n");
	    fwrite($socket, 'cl ' . $port . "\n");
	    fwrite($socket, 'quit' . "\n");

	    while(!feof($socket))
	    {
	    	$this->s_info .= fgets($socket, $this->maxlen);						
	    }

	    $time_end = $this->microtime_float();
            fclose($socket);

	    // response time
	    $this->response = $time_end - $time_begin;
	    $this->response = ($this->response * 1000);
            $this->response = (int)$this->response;			    
        }

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

    function getchannel($channel_id)
    {
	for ($c_nr = 2; $c_nr < count($this->s_info[3]) - 1; $c_nr++)
	{
	    $channel_info = explode("\t", $this->s_info[3][$c_nr]);
	    $channel_info[0] = str_replace(' ', '', $channel_info[0]);

	    if ($channel_info[0] == $channel_id)
	    {
		$channel_info[5] = str_replace('"', '', $channel_info[5]);
		return $channel_info[5];
	    }
	}
    }

    function getprivileges($player_info)
    {
        $c_privs = '';
        $s_privs = '';

	$player_info[10] = str_replace(' ', '', $player_info[10]);

	switch($player_info[10])
	{
	    case 1:
	    {
		$c_privs = 'CA';
		break;
            }
	    case 2:
	    {
		$c_privs = 'O';
		break;
	    } 
	    case 3:
	    {
		$c_privs = 'CA O';
		break;
	    }
	    case 4:
	    {
		$c_privs = 'V';
		break;
	    } 
	    case 5:
	    {
		$c_privs = 'CA V';
		break;
	    } 
	    case 6:
	    {
		$c_privs = 'O V';
		break;
	    } 
	    case 7:
	    {
		$c_privs = 'CA O V';
		break;
	    } 
	    case 8:
	    {
		$c_privs = 'AO';
		break;
	    } 
	    case 9:
	    {
		$c_privs = 'CA AO';
		break;
	    } 
	    case 10:
	    {
		$c_privs = 'O AO';
		break;
	    } 
	    case 11:
	    {
		$c_privs = 'CA O AO';
		break;
	    } 
	    case 12:
	    {
		$c_privs = 'V AO';
		break;
	    } 
	    case 13:
	    {
		$c_privs = 'CA V AO';
		break;
	    } 
	    case 14:
	    {
		$c_privs = 'O V AO';
		break;
	    } 
	    case 15:
	    {
		$c_privs = 'CA O V AO';
		break;
	    } 
	    case 16:
	    {
		$c_privs = 'AV';
		break;
	    } 
	    case 17:
	    {
		$c_privs = 'CA AV';
		break;
	    } 
	    case 18:
	    {
		$c_privs = 'O AV';
		break;
	    } 
	    case 19:
	    {
		$c_privs = 'CA O AV';
		break;
	    }
	    case 20:
	    {
		$c_privs = 'V AV';
		break;
	    } 
	    case 21:
	    {
		$c_privs = 'CA V AV';
		break;
	    } 
	    case 22:
	    {
		$c_privs = 'O V AV';
		break;
	    } 
	    case 23:
	    {
		$c_privs = 'CA V AV';
		break;
	    }
 	    case 24:
	    {
		$c_privs = 'AO AV';
		break;
	    }
	    case 25:
	    {
		$c_privs = 'CA AO AV';
		break;
	    } 
	    case 26:
	    {
		$c_privs = 'O AO AV';
		break;
	    } 
	    case 27:
	    {
		$c_privs = 'CA O AO AV';
		break;
	    }
 	    case 28:
	    {
		$c_privs = 'V AO AV';
		break;
	    } 
	    case 29:
	    {
		$c_privs = 'CA V AO AV';
		break;
	    }
 	    case 30:
	    {
		$c_privs = 'O V AO AV';
		break;
	    }
 	    case 31:
	    {
		$c_privs = 'CA O V AO AV';
		break;
	    } 
	}
	switch($player_info[11])
	{
	    case 0:
	    {
                $s_privs = 'U';
		break;
	    }
	    case 1:
	    {
		$s_privs = 'SA U';
		break;
            }
	    case 2:
	    {
		$s_privs = 'U';
		break;
	    } 
	    case 3:
	    {
		$s_privs = 'SA U';
		break;
	    }
	    case 4:
	    {
		$s_privs = 'R';
		break;
	    } 
	    case 5:
	    {
		$s_privs = 'SA R';
		break;
	    } 
	    case 6:
	    {
		$s_privs = 'R';
		break;
	    } 
	    case 7:
	    {
		$s_privs = 'SA R';
		break;
	    } 
	    case 8:
	    {
		$s_privs = 'U';
		break;
	    } 
	    case 9:
	    {
		$s_privs = 'SA U';
		break;
	    } 
	    case 10:
	    {
		$s_privs = 'U';
		break;
	    } 
	    case 11:
	    {
		$s_privs = 'SA U';
		break;
	    } 
	    case 12:
	    {
		$s_privs = 'R';
		break;
	    } 
	    case 13:
	    {
		$s_privs = 'SA R';
		break;
	    } 
	    case 14:
	    {
		$s_privs = 'R';
		break;
	    } 
	    case 15:
	    {
		$s_privs = 'SA R';
		break;
	    } 
	    case 16:
	    {
		$s_privs = 'R';
		break;
	    } 
	    case 17:
	    {
		$s_privs = 'SA U';
		break;
	    } 
	    case 18:
	    {
		$s_privs = 'R';
		break;
	    } 
	    case 19:
	    {
		$s_privs = 'SA U';
		break;
	    }
	    case 20:
	    {
		$s_privs = 'R';
		break;
	    } 
	    case 21:
	    {
		$s_privs = 'SA R';
		break;
	    } 
	    case 22:
	    {
		$s_privs = 'R';
		break;
	    } 
	    case 23:
	    {
		$s_privs = 'SA R';
		break;
	    }
 	    case 24:
	    {
		$s_privs = 'U';
		break;
	    }
	    case 25:
	    {
		$s_privs = 'SA U';
		break;
	    } 
	    case 26:
	    {
		$s_privs = 'U';
		break;
	    } 
	    case 27:
	    {
		$s_privs = 'SA U';
		break;
	    }
 	    case 28:
	    {
		$s_privs = 'R';
		break;
	    } 
	    case 29:
	    {
		$s_privs = 'SA R';
		break;
	    }
 	    case 30:
	    {
		$s_privs = 'R';
		break;
	    }
 	    case 31:
	    {
		$s_privs = 'SA R';
		break;
	    } 
	}
	return $s_privs . ' ' . $c_privs;
    }

    function getstatus($player_info)
    {
	$player_info[12] = str_replace(' ', '', $player_info[12]);

	switch ($player_info[12]) 
	{
	    case "1":
	    {
		$status_img = '<img src="images/commander.gif" width="16" height="16" border="0">'; 
   		break;
	    }
	    case "3": 
	    {
                $status_img = '<img src="images/commander.gif" width="16" height="16" border="0">'; 
		break;
            }
	    case "5": 
	    {
                $status_img = '<img src="images/commander.gif" width="16" height="16" border="0">'; 
		break;
            }
	    case "7": 
	    {
		$status_img = '<img src="images/commander.gif" width="16" height="16" border="0">'; 
		break;		
	    }
	    case "8":
            {
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }		
	    case "9":
	    { 
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "10":
            { 
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "11": 
            {
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "12":
            { 
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "13":
            { 
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
            }
	    case "14":
            { 
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "15": 
            {
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;			
	    }
	    case "16":
            {
		$status_img = '<img src="images/nomic.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "17":
            {
		$status_img = '<img src="images/nomic.gif" width="16" height="16" border="0">';
		break;
	    }
            case "18":
            { 
		$status_img = '<img src="images/nomic.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "19":
            { 
		$status_img = '<img src="images/nomic.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "20": 
            {
		$status_img = '<img src="images/nomic.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "21":
            { 
		$status_img = '<img src="images/nomic.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "22": 
            {
		$status_img = '<img src="images/nomic.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "23":
	    { 
		$status_img = '<img src="images/nomic.gif" width="16" height="16" border="0">';
		break;		
	    }
	    case "24":
            { 
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "25":
            {
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "26": 
            {
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
            case "27":
            { 
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "28": 
            {
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "29":
            { 
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "30": 
            {
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "31":
            { 
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;		
	    }
	    case "32":
            {
		$status_img = '<img src="images/nosnd.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "33": 
	    {
		$status_img = '<img src="images/nosnd.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "34":
	    { 
		$status_img = '<img src="images/nosnd.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "35": 
            {
		$status_img = '<img src="images/nosnd.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "36":
	    { 
		$status_img = '<img src="images/nosnd.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "37":
	    { 
		$status_img = '<img src="images/nosnd.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "38":
	    { 
		$status_img = '<img src="images/nosnd.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "39": 
	    {
		$status_img = '<img src="images/nosnd.gif" width="16" height="16" border="0">';
		break;		
	    }
	    case "40": 
	    {
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "41": 
	    {
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "42": 
	    {
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "43": 
	    {
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "44":
	    { 
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "45":
	    { 
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "46": 
	    {
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "47": 
	    {
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;		
	    }
	    case "48":
            { 
		$status_img = '<img src="images/nosnd.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "49": 
            {
		$status_img = '<img src="images/nosnd.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "50":
            { 
		$status_img = '<img src="images/nosnd.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "51":
            { 
		$status_img = '<img src="images/nosnd.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "52": 
	    {
		$status_img = '<img src="images/nosnd.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "53":
	    { 
		$status_img = '<img src="images/nosnd.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "54": 
	    {
		$status_img = '<img src="images/nosnd.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "55": 
	    {
		$status_img = '<img src="images/nosnd.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "56":
            { 
		$status_img = '<img src="images/nosnd.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "57": 
	    {
		$status_img = '<img src="images/nosnd.gif" width="16" height="16" border="0">';
		break;		
	    }
	    case "58": 
            {
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "59": 
            {
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;		
	    }
            case "60":
	    { 
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;
	    }
	    case "61": 
	    {
		$status_img = '<img src="images/away.gif" width="16" height="16" border="0">';
		break;		
	    }
	    default:
	    {
   		$status_img = '<img src="images/user.gif" width="16" height="16" border="0">';
		break;
	    }	
	}

	return $status_img;
    }

    function getrules($phgdir)
    {
        $srv_rules['sets'] = false;

	$srv_rules['mapname'] = 'Teamspeak';
	$srv_rules['map_path'] = 'maps/ts';
	$srv_rules['map_default'] = 'default.jpg';

	// ts setting pics
	$sets['pass']    = '<img src="' . $phgdir . 'privileges/pass.gif" alt="pw">';

	// server hostname
	$srv_rules['hostname'] = substr($this->d_info[2], 12);

	// server version
	$srv_rules['version'] = $this->s_info[0];	
	$srv_rules['gamename'] = 'Teamspeak<br>' . $srv_rules['version'];
	// server channels
	$srv_rules['channels'] = substr($this->d_info[30], 23);

        // response time
	$srv_rules['response'] = $this->response . ' ms';

	// server type
	if (substr($this->d_info[8], 19, 1) == 1)
	{
		$srv_rules['type'] = 'Clanserver';
	}
	else
	{
		$srv_rules['type'] = 'Publicserver';
	}

	// players
	$srv_rules['nowplayers'] = str_replace(' ', '', substr($this->d_info[29], 20));
	$srv_rules['maxplayers'] = str_replace(' ', '', substr($this->d_info[10], 16));

	if (substr($this->d_info[7], 16, 1) == 1)
	{
	    $srv_rules['sets'] .= $sets['pass'];
	}
	else
	{
	    $srv_rules['sets'] = '-';
	}

	// server general info (added by balgo 05.01.2006 only for test)
        $srv_rules['htmlinfo'] =
        '<tr><td align="left">Version:</td><td align="left">'
        . $srv_rules['version']
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
        '<tr valign="top"><td align="left">Version:</td><td align="left">'
        . $srv_rules['gamename']
        . '</td></tr>' . "\n"
        . '<tr valign="top"><td align="left">Type:</td><td align="left">'
        . $srv_rules['type']
        . '</td></tr>' . "\n"
        . '<tr valign="top"><td align="left">Channels:</td><td align="left">'
        . $srv_rules['channels']
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
	$players = 0;
	$tdata = '';

	$player = array();
	for ($count = 2; $count < count($this->p_info) - 1; $count++)
	{
		$player_data = explode ("\t", $this->p_info[$count]);

		$players++;
                $player_name = str_replace('"', '', $player_data[14]);
                /* removed by balgo 17/06/06: disable login name display
                if (strlen(str_replace('"', '', $player_data[15])) != 0)
                {
                        $player_name .= ' (' . str_replace('"', '', $player_data[15]) . ')';
                }
                */
                array_push($player, $this->getchannel(
                $player_data[1]) . "\00" . $this->getstatus($player_data). "\00" .
                $player_name . "\00" . $this->getprivileges($player_data) . "\00" . $player_data[7]
                );
	}

	asort($player);

	foreach ($player as $player_info)
	{
		$player_data = explode ("\00", $player_info);

		$tdata .= '<tr align=center><td>' . $player_data[1] . '</td>' .
	                  '<td>' . $player_data[2] .
			  '</td><td>' . $player_data[0] . '</td>' .
			  '<td>' . $player_data[3] . '</td>' .
	                  '<td>' . $player_data[4] . '</td></tr>' . "\n";
	}
        // get team variable
        $thead = '<tr><th>#</th>' .
	         '<th>Name</th>' .
	         '<th>Channel</th>' .
		 '<th>Privileges</th>' .
	         '<th colspan=2>Ping</th></tr>';

	if ($players == 0)
	{
	    $thead .= 
	    "<tr align=center><td>--</td>" .
	    "<td>--</td><td>--</td><td>--</td><td>--</td></tr>\n";
	}

	// store the html table line to the info array
	$srv_player = $thead . $tdata;

        return $srv_player;
    }
}
