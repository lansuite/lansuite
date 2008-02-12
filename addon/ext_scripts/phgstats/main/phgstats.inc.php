<?php
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
if (!defined('PHGDIR')) { exit(); }

if (file_exists('install/index.php')) { die('install/index.php must be deleted!<br>Not installed yet? Click <a href="install/index.php">here</a>!'); }

include_once(PHGDIR . 'settings/config.inc.php');     // phgstats configuration
include_once(PHGDIR . 'includes/daytime.inc.php');    // time function
include_once(PHGDIR . 'includes/dns.inc.php');        // dns function
include_once(PHGDIR . 'classes/phgstats.class.php');  // main phgstats class

$phgdir = PHGDIR;

// get data of server
function info($phgdir, $sh_srv, $game, $host, $port, $queryport, $country) 
{
    // create server the object
    $phgstats = phgstats::query($game[$sh_srv]);
    
    // resolve ip adress
    $host[$sh_srv] = dns($host[$sh_srv]);

    // get the serverinfo string
    $server = $phgstats->getstream($host[$sh_srv], $port[$sh_srv], $queryport[$sh_srv]);

    if ($server === true) 
    {
        // get the server rules
        $srv_rules  = $phgstats->getrules($phgdir);
        $srv_rules['playerlist'] = $phgstats->getplayers();     
        
	// full path to the map picture
	$srv_rules['map'] = $phgdir . $srv_rules['map_path'] . '/' . $srv_rules['mapname'] . '.jpg';
	
        if (!(file_exists($srv_rules['map'])))
	{ // set default map if no picture found
	    $srv_rules['map'] = $phgdir . $srv_rules['map_path'] . '/' . $srv_rules['map_default'];
	}
    }
    else
    {
        // default values if no response
        $msg = 'No Response';
	$srv_rules['playerlist'] = '';
	
	$srv_rules['hostname']    = $msg;
	$srv_rules['gamename']    = $msg . "<br>";
	$srv_rules['map']         = $phgdir . 'maps/no_response.jpg';
	$srv_rules['mapname']     = 'no response';
	$srv_rules['sets']        = '-';
	$srv_rules['htmlinfo']    = '<tr valign="top"><td align="left">No</td><td align="left">Response</td></tr>' . "\n";
	$srv_rules['htmldetail'] = '<tr valign="top"><td align="left">No</td><td align="left">Response</td></tr>' . "\n";
    }

    // get server day time
    $srv_rules['time']= daytime();
    
    // get server country / location
    $srv_rules['country'] = $country[$sh_srv];

    // get server adress
    $srv_rules['adress'] = $host[$sh_srv] . ':' . $port[$sh_srv];
     
    return $srv_rules;
}

// show the data of one server
function srv_info ($sh_srv, $srv_rules, $use_file, $use_bind, $only, $phgtable)
{   
    if ($only)
    {   // html: one server: refresh button
        $bar = '<table border="0" cellpadding="2" cellspacing="2" width="' . $phgtable . '">'
	. '<tr><th colspan="2">' . '<a href="' . $use_file . $use_bind . '">Refresh</a></th></tr></table>' . "\n";
    }
    else
    {   // html: more server: resfresh and serverlist button
        $bar = '<table border="0" cellpadding="2" cellspacing="2" width="' . $phgtable . '">'
	.'<tr><th colspan="2">' . '<a href="' . $use_file . '">Serverlist</a> | '
        . '<a href="' . $use_file . $use_bind . 'sh_srv=' . $sh_srv . '">Refresh</a></th></tr></table>' . "\n";
    }
    
    // html: menu bar top
    echo $bar;
    
    // html: table to show server infos
    echo '<table border="0" cellpadding="2" cellspacing="2" width="' . $phgtable . '">' . "\n";
    
    // html: hostname, country and daytime 
    echo '<tr>' . '<th colspan="2">'
    . $srv_rules['hostname'] . '<br>' 
    . $srv_rules['country'] . ', ' 
    . $srv_rules['time'] 
    . '</th>' . '</tr>' . "\n";
    
    // html: titels (server info)
    echo '<tr><th width="60%">Server</th><th width="40%">Map</th></tr>' . "\n";
    
    // html: adress, game, gametype, mapname, players, privileges
    echo '<tr><td>' . "\n"
    . '<table border="0" cellpadding="3" cellspacing="0">' . "\n"
    . '<tr valign="top"><td align="left">IP:</td><td align="left">'         . $srv_rules['adress']     . '</td></tr>' . "\n"
   
    . $srv_rules['htmldetail'] // now the details generate from game class by balgo on 05.01.2006
    
    /* removed for more flexibly of game rules by balgo on 05.01.2006
    . '<tr valign="top"><td align="left">Gamename:</td><td align="left">'   . $srv_rules['gamename']   . '</td></tr>' . "\n"
    . '<tr valign="top"><td align="left">Gametype:</td><td align="left">'   . $srv_rules['gametype']   . '</td></tr>' . "\n"
    . '<tr valign="top"><td align="left">Mapname:</td><td align="left">'    . $srv_rules['mapname']    . '</td></tr>' . "\n"
    . '<tr valign="top"><td align="left">Players:</td><td align="left">'    . $srv_rules['nowplayers'] 
                                                                            . $srv_rules['maxplayers'] . '</td></tr>' . "\n"
    . '<tr valign="top"><td align="left">Response:</td><td align="left">'   . $srv_rules['response']   . '</td></tr>' . "\n"
    . '<tr valign="top"><td align="left">Privileges:</td><td align="left">' . $srv_rules['sets']       . '</td></tr>' . "\n"
    */
    . '</table></td>' . "\n";
    
    // html: map picture
    echo '<td width="60%" align="center">' . "\n"
    . '<img alt="' . $srv_rules['mapname'] . '" src="' . $srv_rules['map'] . '" border="0">' . "\n"
    . '</td>' . "\n"
    . '</tr>' . "\n";

    // html: close info table
    echo '</table>' . "\n";
    
    // html: open playerlist table
    echo '<table border="0" cellpadding="2" cellspacing="2" width="' . $phgtable . '">' . "\n";
    
    // html: playerlist
    echo $srv_rules['playerlist'];

    // html: close playerlist table
    echo '</table>';

    // html: menu bar bottom
    echo $bar;
}

// show the data of two or more server
function srv_list ($sh_srv, $srv_rules, $use_file, $use_bind)
{
        // html: server ip and gamename
	echo '<tr valign="top">' . "\n"
	. '<td align="left">'
	. '<a href="' . $use_file . $use_bind . 'sh_srv=' . $sh_srv . '">'
	. $srv_rules['adress']
	. '</a>'
	. '<br><br>'
	. $srv_rules['gamename']
	. '<br><br>'
	. '</td>'
	. "\n";

	// html: server info link
	echo '<td align="left">'
	. '<a href="' . $use_file . $use_bind . 'sh_srv='  . $sh_srv 
	. '">'        . $srv_rules['hostname'] 	. '</a>' . "\n";
	
	    // html: server details table
	    echo '<table border="0" cellpadding="3" cellspacing="0">' . "\n"
	    . $srv_rules['htmlinfo'] // now the info generate from game class by balgo on 05.01.2006

	    /* removed for more flexibly of game rules by balgo on 05.01.2006
	    . '<tr><td align="left">Mapname:</td><td align="left">'    . $srv_rules['mapname']    . '</td></tr>' ."\n"
	    . '<tr><td align="left">Players:</td><td align="left">'    . $srv_rules['nowplayers']
	                                                               . $srv_rules['maxplayers'] . '</td></tr>' . "\n"
	    . '<tr><td align="left">Response:</td><td align="left">'   . $srv_rules['response']   . '</td></tr>' . "\n"
	    . '<tr><td align="left">Privileges:</td><td align="left">' . $srv_rules['sets']       . '</td></tr>' . "\n"
	    */
	    
	    . '</table>' . "\n";
	
	echo '</td>' . "\n";
	
	// html: map picture
	echo '<td align="right">'
	. '<img height="50" width="50" src="'
	. $srv_rules['map'] . '" border="0" alt="'
	. $srv_rules['mapname'] . '"><br>'
	. '</td>' . "\n"
	. '</tr>' . "\n";      
}

echo '<center>' . "\n";


// how much server must scan
$index = count($gameserver);

while($index)
{
  $index--;

  list($game[$index], $host[$index], $port[$index], $queryport[$index]) = split(':', $gameserver[$index]);
}

//if ($host[$HTTP_GET_VARS["sh_srv"]])
if ( IsSet($_GET['sh_srv']) ) 
{   
    $sh_srv = $_GET["sh_srv"];

    // gameserver data
    $srv_rules = info($phgdir, $sh_srv, $game, $host, $port, $queryport, $country);    

    srv_info($sh_srv, $srv_rules, $use_file, $use_bind, 0, $phgtable);
}
else
{
    $sh_srv = count($host);
        
    if ($sh_srv > 1)
    {   // html: open table to show more server
        echo '<table border="0" cellpadding="2" cellspacing="2" width="' . $phgtable . '">'
	   . '<tr><th colspan="2">' . '<a href="' . $use_file . '">Refresh</a></th></tr></table>' . "\n";
		
        
        echo '<table border="0" cellpadding="2" cellspacing="2" width="' . $phgtable . '">' . "\n";
	
	// html: titles (game, hostname, players, map)
	echo '<tr>'
	. '<th>Server</th><th>Info</th><th>Map</th>'
	. '</tr>'
	. "\n";
        
	// html: gameserver list data
	while ($sh_srv)
	{
	    $sh_srv--;
	    $srv_rules = info($phgdir, $sh_srv, $game, $host, $port, $queryport, $country);
	    srv_list($sh_srv, $srv_rules, $use_file, $use_bind);
	    flush();
	}
        
	// html: close table with server info
	echo '</table>' . "\n";

	echo '<table border="0" cellpadding="2" cellspacing="2" width="' . $phgtable . '">'
	   . '<tr><th colspan="2">' . '<a href="' . $use_file . '">Refresh</a></th></tr></table>' . "\n";
		
    }
    else
    {
        $sh_srv--;
	
	// gameserver data
	$srv_rules = info($phgdir, $sh_srv, $game, $host, $port, $queryport, $country);
        srv_info($sh_srv, $srv_rules, $use_file, $use_bind, 1, $phgtable);
    }
}

// html: our copyright, dont remove !
echo '<table cellpadding="0" cellspacing="0" border="0">'. "\n"
   . '<tr><td class="auth" align="center">'
   . '<a href="http://phgstats.sourceforge.net/" target="_blank">phgstats 0.6.9</a><font color="'
   . $color
   . '"> &copy; 2004-2006 </font>'
   . '<a href="http://woah-projekt.de/" target="_blank">woah-projekt</a>'
   . '</td></tr>' . "\n"
   . '</table>' . "\n";
   
echo '</center>';				    
?>
