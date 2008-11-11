<?php // Armagetronad Game Class
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

class atron
{
    var $maxlen   = 2048;
    var $write    = false;
    var $s_info   = false;
    var $ss_info  = false;
    var $g_info   = false;
    var $p_info   = false;
    var $response = false;
    var $ppos     = false;

    function get_string($term)
    {
        // search in server infostream array and put into string
        while ($this->ppos <= count($this->ss_info) && $this->ss_info[$this->ppos] != $term)
        {
            $res_string .= $this->ss_info[$this->ppos];
            $this->ppos++;
        }

        // convert string from utf-8 into iso-8859-1
        return mb_convert_encoding($res_string, 'UTF-8', 'ISO-8859-1');
    }

    function split_bytes()
    {
        // split server infostream into array
        for ($count = 0; $count < strlen($this->s_info); $count = $count + 2)
        {
            $this->ss_info[$count] = $this->s_info[$count + 1];
            $this->ss_info[$count + 1] = $this->s_info[$count];
        }
    }
    function convert_color($hex_string) 
    {
        // convert hexadecimal colors from string into html font-tags with color-attributes
        $hex_string .= '0xRESETT';
        $fc = 0;

        while (($i = strpos($hex_string, '0x')) !== false)
        {
            $r .= substr($hex_string, 0, $i);

            if (!strcasecmp(substr($hex_string, $i + 2, 6), 'RESETT'))
            {
                for (; $fc > 0; --$fc)
                {
                    $r .= '</font>';
                }
            }
            else
            {
                $Cr = hexdec(substr($hex_string, $i + 2, 2));
                $Cg = hexdec(substr($hex_string, $i + 4, 2));
                $Cb = hexdec(substr($hex_string, $i + 6, 2));
                $r .= '<font color="#' . substr('0'.dechex($Cr), -2) . substr('0'.dechex($Cg), -2) . substr('0'.dechex($Cb), -2) . '"';

                if (max($Cr, $Cg, $Cb) < 0x7f || ($Cr + $Cg + $Cb) < 0xff)
                    $r .= ' class="darktext"';
                    $r .= '>';
                }

                $hex_string = substr($hex_string, $i + 8);
                ++$fc;
        }
    $r .= $hex_string;
    return $r;
    }

    function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());

        return ((float)$usec + (float)$sec);
    }

    function getstream($host, $port, $queryport)
    {
        $this->write = pack("xCxxxxxC", 0x35, 0x11);

        // get the infostream from server
        $socket = fsockopen('udp://'. $host, $port, $errno, $errstr, 30);

        if ($socket === false)
        {
            echo "Error: $errno - $errstr<br>\n";
        }
        else
        {
            socket_set_timeout($socket, 1);

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
            $this->split_bytes();

            return true;
        }
        else
        {
            return false;
        }
    }

    function getrules($phgdir)
    {
        // response time
        $srv_rules['response'] = $this->response . ' ms';

        // hostname
        $this->ppos = 16;
        $srv_rules['hostname'] = $this->convert_color(htmlspecialchars($this->get_string(pack("x"))));

        $this->ppos++;
        $players1 = ord($this->ss_info[$this->ppos]);
        $this->ppos++;
        $players2 = ord($this->ss_info[$this->ppos]);

        // players
        if ($players1 != 0)
        {
            $srv_rules['nowplayers'] = $players1;
            $this->ppos--;
        }
        elseif ($players2 != 0)
        {
            $srv_rules['nowplayers'] = $players2;
        }
        else
        {
            $srv_rules['nowplayers'] = 0;
        }

        // version
        $this->ppos += 14;
        if (ord($this->ss_info[$this->ppos - 1]) != 0)
        {
            $this->ppos--;
        }
        $srv_rules['gamename'] = 'Armagetronad' . "<br>" . $this->get_string(pack("x"));

        // playernames
        $this->ppos += 8;
        if (ord($this->ss_info[$this->ppos - 1]) != 0)
        {
            $this->ppos--;
        }
        $playernames = $this->get_string(chr(0));
        $this->players = explode("\n", $playernames);
        unset($this->players[count($this->players) - 1]);

        // url
        $this->ppos += 8;
        if (ord($this->ss_info[$this->ppos - 1]) != 0){
            $this->ppos--;
        }
        $srv_rules['url'] = $this->get_string(pack("x"));

	// if url does not begin with http:// leave it empty or add tp:// to convert correct read error
        if (substr($srv_rules['url'],0, 7) != 'http://')
        {
            if (substr($srv_rules['url'], 0, 5) == 'tp://')
            {
                $srv_rules['url'] = 'ht' . $srv_rules['url'];
		$srv_rules['url'] = '<a href="' . $srv_rules['url'] . '" target="_blank">' 
		                                . $srv_rules['url'] . '</a>';
            }
            else
            {
                $srv_rules['url'] = '-';
            }
        }

	// if no players on the server, changed 0 to 'none'
	if ($srv_rules['nowplayers'] == 0)
	{
	    $srv_rules['nowplayers'] = 'none';
	}

	// mapname: only one map in this game
	$srv_rules['mapname'] = 'Armagetronad';

        // map picture
        $srv_rules['game'];
        $srv_rules['map_path'] = 'maps/atron';
        $srv_rules['map_default'] = 'default.jpg';

        // server privileges
        $srv_rules['sets'] = '-';
        
	// server general info
        $srv_rules['htmlinfo'] = 
	'<tr><td align="left">Mapname:</td><td align="left">'
	. $srv_rules['mapname']
	. '</td></tr>' ."\n"
	. '<tr><td align="left">Players:</td><td align="left">'
	. $srv_rules['nowplayers']
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
	. '<tr valign="top"><td align="left">Mapname:</td><td align="left">'
	. $srv_rules['mapname']
	. '</td></tr>' . "\n"
	. '<tr valign="top"><td align="left">Players:</td><td align="left">'
	. $srv_rules['nowplayers']
	. '</td></tr>' . "\n"
	. '<tr valign="top"><td align="left">Response:</td><td align="left">'
	. $srv_rules['response']
	. '</td></tr>' . "\n"
	. '<tr valign="top"><td align="left">Privileges:</td><td align="left">'
	. $srv_rules['sets']
	. '</td></tr>' . "\n"
	. '<tr valign="top"><td align="left">Provider:</td><td align="left">'
        . $srv_rules['url']
	. '</td></tr>' . "\n";
        
        // return all server rules
        return $srv_rules;
        }

    function getplayers()
    {
        // set html thead
        $thead = "<tr><th>ID</th>" .
                 "<th>Name</th></tr>";

        // set -- playerline when 0 players online
        if (count($this->players) == 0)
        {
            $srv_player = '<tr align=center><td>--</td><td>--</td>' . "\n";
        }

        // set players sorted in lines
        for ($player = 0; $player < count($this->players); $player++)
        {
            $srv_player = $srv_player . '<tr align=center><td>' . ($player + 1) . '</td><td>' . htmlentities($this->players[$player]) . '</td></tr>';
        }

        // return html playerstring
        return $thead . $srv_player;
    }
}
