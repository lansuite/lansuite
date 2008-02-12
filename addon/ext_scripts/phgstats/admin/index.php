<?php

require 'ext_scripts/phgstats/settings/gametypes.inc.php';

/*
* Copyright (c) 2004-2005, woah-projekt.de
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
/*? >
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <title>phgstats - game server status</title>
    
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
   
    <style type="text/css">
    body {
        background: #4E5F71; 
	font-family: verdana, arial, sans-serif;
	font-size: 11pt;
    }
    A:link, A:visited, A:active {
	text-decoration: underline;
	color: #ff0000;
    }
    A:hover {
        text-decoration: underline;
	color: #ccc;
    }
    table {
        font-family: verdana, arial, sans-serif;
	font-size: 10pt;
	color: #ccc;
	background-color: #4E5F71;
	width: 400px;
    }
    td {
        color: #ccc;
	background-color: #4B5B6E;
    }
    td.auth {
        color: #ccc;
	background-color: #4E5F71;
    }
    th {
	color: #ffffff;
        background-color: #3B4C5C;
    }
    .arrow:link, .arrow:visited, .arrow:active {
	text-decoration: none;
        color: #4E5F71;
    }
    .arrow:hover {
        text-decoration: none;
        color: #ff0000;
    }
    </style>
<? php
*/
if (isset($_GET['edit_ip']))
{
?>
    <script type="text/javascript">
    function select () {
        document.game.gametype.options[0].value = "<?php echo $_GET['edit_gametype']; ?>";
	document.game.gametype.options[0].text = "<?php echo $_GET['edit_gametype']; ?>";
	document.game.country.options[0].value = "<?php echo $_GET['edit_country']; ?>";
	document.game.country.options[0].text = "<?php echo $_GET['edit_country']; ?>";
    }
    </script>
<?php
}
?>
  </head>
  
  <body onload="select()">
  <table align="center">
    <tr>
      <th align="center">
      phgstats admin tool / phgstats Admintool
      </th>
    </tr>
    <tr>
      <td align="center">
<?php

require('ext_scripts/phgstats/settings/access.inc.php');

if ($_GET['password'] != $password)
{
    if (isset($_GET['password']))
    {
        echo 'Password wrong / Passwort falsch<br><br>';
    }
?>      Password / Passwort:
        <form action="index.php" method="GET">
          <input type="password" name="password">
	  <input type="submit" name="submit" value="Login">
        </form>
<?php
}
else
{
?>
          <table>
            <tr>
              <th></th>
              <th>Gametype</th>
	      <th>IP</th>
	      <th>Port</th>
	      <th>Query- Port</th>
	      <th>Country</th>
	    </tr>
<?php
// include config
require('ext_scripts/phgstats/settings/config.inc.php');

// add server
if (isset($_POST['add']))
{
    // config header
    $config = '<?php' . "\n" .
              '// width of phgstats table' . "\n" .
	      '$phgtable =' . "'" . '500' . "'" . ';' . "\n\n" .
	      '/* game server variables:' . "\n";

    $i = 0;
    
    foreach ($gametypes as $type => $game)
    {
    	if ($i == 0)
	{
		$i = 13;
		$config .= "\n" . ' *';
	}
	
	$config .= ' ' . $type . ',';
	$i--;
    }																	  
	    
    $config .= "\n" . ' */' . "\n\n" .
	      '// server settings  ( ' . "'" . 'game:ip.ip.ip.ip:port:(queryport)' . "'\n" .
	      '// Read the INSTALL file for more Informations' . "\n" .
	      '$gameserver = array(' . "\n";

    for ($srv_count = 0; $srv_count < count($gameserver); $srv_count++)
    {
	$server = explode(':', $gameserver[$srv_count]);
        $land = $country[$srv_count];
	
        $serverarray = $serverarray . "'" .
	               $server[0] . ':' .
		       $server[1] . ':' .
		       $server[2] . ':' .
		       $server[3] . "',\n";

	$countryarray = $countryarray . "'" .
	                $land         . "',\n";
    }
    $serverarray = $serverarray          . "'" .
                   $_POST['gametype']    . ':' .
                   $_POST['server_ip']   . ':' .
                   $_POST['server_port'] . ':' .
                   $_POST['server_query']. "',\n";

    $countryarray = $countryarray . "'" .
                    $_POST['country']   . "',\n"; 

    $config = $config . $serverarray . '); ' . "\n\n";
    $config = $config . '// server country' . "\n" .
                        '$country = array(' . "\n";
    $config = $config . $countryarray . '); ' . "\n\n" . '?>';

    fwrite(fopen ('ext_scripts/phgstats/settings/config.inc.php', "w+"), $config);
}
// delete server
elseif (isset($_GET['id']))
{
    $config = '<?php' . "\n" .
              '// width of phgstats table' . "\n" .
	      '$phgtable =' . "'" . '500' . "'" . ';' . "\n\n" .
	      '/* game server variables:' . "\n";
    $i = 0;

    foreach ($gametypes as $type => $game)
    {
	if ($i == 0)
	{
	    $i = 13;
	    $config .= "\n" . ' *';
	}

 	$config .= ' ' . $type . ',';
	$i--;
    }

    $config .= "\n" . ' */' . "\n\n" .
	      '// server settings  ( ' . "'" . 'game:ip.ip.ip.ip:port:(queryport)' . "'\n" .
	      '// Read the INSTALL file for more Informations' . "\n" .
	      '$gameserver = array(' . "\n";
	      
    for ($srv_count = 0; $srv_count < count($gameserver); $srv_count++)
    {
        if ($srv_count != $_GET['id'])
	{
	    $server = explode(':', $gameserver[$srv_count]);
            $land = $country[$srv_count];
	
            $serverarray = $serverarray . "'" .
	                   $server[0] . ':' .
		           $server[1] . ':' .
		           $server[2] . ':' .
		           $server[3] . "',\n";
  
            $countryarray = $countryarray . "'" .
	                    $land         . "',\n";
        }
    }

    $config = $config . $serverarray . '); ' . "\n\n";
    $config = $config . '// server country' . "\n" .
                        '$country = array(' . "\n";
    $config = $config . $countryarray . '); ' . "\n\n" . '?>';

    fwrite(fopen ('ext_scripts/phgstats/settings/config.inc.php', "w+"), $config);
}
// put server one line up
elseif (isset($_GET['up']))
{
    $config = '<?php' . "\n" .
              '// width of phgstats table' . "\n" .
	      '$phgtable =' . "'" . '500' . "'" . ';' . "\n\n" .
	      '/* game server variables:' . "\n";
	      
    $i = 0;

    foreach ($gametypes as $type => $game)
    {
	if ($i == 0)
	{
	    $i = 13;
	    $config .= "\n" . ' *';
	}
        $config .= ' ' . $type . ',';
        $i--;
    }
    $config .= "\n" . ' */' . "\n\n" .
	      '// server settings  ( ' . "'" . 'game:ip.ip.ip.ip:port:(queryport)' . "'\n" .
	      '// Read the INSTALL file for more Informations' . "\n" .
	      '$gameserver = array(' . "\n";
	      
    for ($srv_count = 0; $srv_count < count($gameserver); $srv_count++)
    {
        if ($srv_count == $_GET['up'] - 1)
	{
            $changesrv = $gameserver[$srv_count];
	    $changecnt =    $country[$srv_count];

            $gameserver[$srv_count] = $gameserver[$srv_count + 1];
	    $country   [$srv_count] = $country   [$srv_count + 1];
	}
	if ($srv_count == $_GET['up'])
	{
	    $gameserver[$srv_count] = $changesrv;
	    $country   [$srv_count] = $changecnt;
	}
        $server = explode(':', $gameserver[$srv_count]);
        $land = $country[$srv_count];
	
        $serverarray = $serverarray . "'" .
	               $server[0] . ':' .
	               $server[1] . ':' .
		       $server[2] . ':' .
		       $server[3] . "',\n";
  
        $countryarray = $countryarray . "'" .
                        $land         . "',\n";

    }

    $config = $config . $serverarray . '); ' . "\n\n";
    $config = $config . '// server country' . "\n" .
                        '$country = array(' . "\n";
    $config = $config . $countryarray . '); ' . "\n\n" . '?>';

    fwrite(fopen ('ext_scripts/phgstats/settings/config.inc.php', "w+"), $config);
}
// put server one line down
elseif (isset($_GET['down']))
{
    $config = '<?php' . "\n" .
              '// width of phgstats table' . "\n" .
	      '$phgtable =' . "'" . '500' . "'" . ';' . "\n\n" .
	      '/* game server variables:' . "\n";
    $i = 0;

    foreach ($gametypes as $type => $game)
    {
        if ($i == 0)
        {
            $i = 13;
            $config .= "\n" . ' *';
        }
        $config .= ' ' . $type . ',';
        $i--;
    }

    $config .= "\n" . ' */' . "\n\n" .
	      '// server settings  ( ' . "'" . 'game:ip.ip.ip.ip:port:(queryport)' . "'\n" .
	      '// Read the INSTALL file for more Informations' . "\n" .
	      '$gameserver = array(' . "\n";
	      
    for ($srv_count = 0; $srv_count < count($gameserver); $srv_count++)
    {
	if ($srv_count == $_GET['down'] + 1)
	{
	    $gameserver[$srv_count] = $changesrv;
	    $country   [$srv_count] = $changecnt;
	}
        if ($srv_count == $_GET['down'])
	{
            $changesrv = $gameserver[$srv_count];
	    $changecnt =    $country[$srv_count];

            $gameserver[$srv_count] = $gameserver[$srv_count + 1];
	    $country   [$srv_count] = $country   [$srv_count + 1];
	}
        $server = explode(':', $gameserver[$srv_count]);
        $land = $country[$srv_count];
	
        $serverarray = $serverarray . "'" .
	               $server[0] . ':' .
	               $server[1] . ':' .
		       $server[2] . ':' .
		       $server[3] . "',\n";
  
        $countryarray = $countryarray . "'" .
                        $land         . "',\n";

    }

    $config = $config . $serverarray . '); ' . "\n\n";
    $config = $config . '// server country' . "\n" .
                        '$country = array(' . "\n";
    $config = $config . $countryarray . '); ' . "\n\n" . '?>';

    fwrite(fopen ('ext_scripts/phgstats/settings/config.inc.php', "w+"), $config);
}
elseif(isset($_POST['change']))
{
    $config = '<?php' . "\n" .
              '// html variables' .                               "\n" .
              '$btitle         = ' . "'" . $_POST['btitle']   . "';\n" .
              '// html colors:' .                                 "\n" .
              '$color          = ' . "'" . $_POST['color']    . "';\n" .
              '$bg_color       = ' . "'" . $_POST['bg_color'] . "';\n" .
              '$t_color        = ' . "'" . $_POST['t_color']  . "';\n" .
	      '$tb_color       = ' . "'" . $_POST['tb_color'] . "';\n" .
              '$td_color       = ' . "'" . $_POST['td_color'] . "';\n" .
              '$tdb_color      = ' . "'" . $_POST['tdb_color']. "';\n" .
              '$th_color       = ' . "'" . $_POST['th_color'] . "';\n" .
              '$thb_color      = ' . "'" . $_POST['thb_color']. "';\n" .
              '$h_color        = ' . "'" . $_POST['h_color']  . "';\n" .
	      '?>';
    if (fwrite(fopen ('ext_scripts/phgstats/settings/style.inc.php', "w+"), $config))
    {
        echo 'Style changed!';
    }
}

// show servers
require('ext_scripts/phgstats/settings/config.inc.php');
require('ext_scripts/phgstats/settings/style.inc.php');

for ($srv_count = 0; $srv_count < count($gameserver); $srv_count++)
{
    $server = explode(':', $gameserver[$srv_count]);
    $land = $country[$srv_count];
?>
            <tr>
	      <td>
<?php 
if ($srv_count != 0)
{
?>
              <a class="arrow" href="index.php?mod=gameserver&action=admin&up=<?php echo $srv_count; ?>&amp;password=<?php echo $_GET['password']; ?>"><img src="ext_scripts/phgstats/images/up.png" alt="up"></a>
<?php
}
?>
              <img src="ext_scripts/phgstats/images/spacer.png" alt="spacer">
<?php
if ($srv_count != count($gameserver) - 1)
{
?>
              <a class="arrow" href="index.php?mod=gameserver&action=admin&down=<?php echo $srv_count; ?>&amp;password=<?php echo $_GET['password']; ?>"><img src="ext_scripts/phgstats/images/down.png" alt="down"></a>
<?php
}
?>            </td>
	      <td><?php echo $server[0]; ?></td>
	      <td><?php echo $server[1]; ?></td>
	      <td><?php echo $server[2]; ?></td>
	      <td><?php echo $server[3]; ?></td>
	      <td><?php echo $land; ?></td>
	      <td align="right"><a href="index.php?mod=gameserver&action=admin&id=<?php echo $srv_count; ?>&amp;password=<?php echo $_GET['password']; ?>&amp;edit_ip=<?php echo $server[1]; ?>&amp;edit_port=<?php echo $server[2]; ?>&amp;edit_qport=<?php echo $server[3]; ?>&amp;edit_gametype=<?php echo $server[0]; ?>&amp;edit_country=<?php echo $land; ?>"><font color="lightgreen">Edit/Editieren</font></a><br><a href="index.php?mod=gameserver&action=admin&id=<?php echo $srv_count; ?>&amp;password=<?php echo $_GET['password']; ?>">Delete/L&ouml;schen</a></td>
	    </tr>
<?php
}
?>
          <form method="POST" action="index.php?mod=gameserver&action=admin&password=<?php echo $_GET['password']; ?>" name="game">
            <tr>
              <td></td>
	      <td>
	        <select name="gametype" <?php if (isset($_GET['edit_ip'])){ echo 'style="background-color: green"'; } ?>>
		  <option value="">[Select a game/Selektieren Sie ein Spiel]</option>
		  <?php
		  foreach ($gametypes as $type => $game)
		  {
		      echo '<option value="' . $type . '">' . $game . '</option>';
		  }
		  ?>																      
		</select>
	      </td>
	      <td>
	        <input type="text" size="15" name="server_ip" value="<?php echo $_GET['edit_ip']; ?>" <?php if (isset($_GET['edit_ip'])){ echo 'style="background-color: green"'; } ?>>
	      </td>
	      <td>
	        <input type="text" size="5" name="server_port" value="<?php echo $_GET['edit_port']; ?>" <?php if (isset($_GET['edit_ip'])){ echo 'style="background-color: green"'; } ?>>
	      </td>
	      <td>
	        <input type="text" size="5" name="server_query" value="<?php echo $_GET['edit_qport']; ?>" <?php if (isset($_GET['edit_ip'])){ echo 'style="background-color: green"'; } ?>>
	      </td>
	      <td>
                <select name="country" <?php if (isset($_GET['edit_ip'])){ echo 'style="background-color: green"'; } ?>>
		<option value="">[Select a country/Selektieren Sie ein Land]</option>
		<option>Afghanistan</option>
                <option>Albania</option>
                <option>Algeria</option>
                <option>American Samoa</option>
                <option>Andorra</option>
                <option>Angola</option>

                <option>Anguilla</option>
                <option>Antarctica</option>
                <option>Antigua and Barbuda</option>
                <option>Argentina</option>
                <option>Armenia</option>
                <option>Aruba</option>

                <option>Australia</option>
                <option>Austria</option>
                <option>Azerbaijan</option>
                <option>Bahamas</option>
                <option>Bahrain</option>
                <option>Bangladesh</option>

                <option>Barbados</option>
                <option>Belarus</option>
                <option>Belgium</option>
                <option>Belize</option>
                <option>Benin</option>
                <option>Bermuda</option>

                <option>Bhutan</option>
                <option>Bolivia</option>
                <option>Bosnia and Herzegowina</option>
                <option>Botswana</option>
                <option>Bouvet Island</option>
                <option>Brazil</option>

                <option>British Indian Ocean Territory</option>
                <option>Brunei Darussalam</option>
                <option>Bulgaria</option>
                <option>Burkina Faso</option>
                <option>Burundi</option>
                <option>Cambodia</option>

                <option>Cameroon</option>
                <option>Canada</option>
                <option>Cape Verde</option>
                <option>Cayman Islands</option>
                <option>Central African Republic</option>
                <option>Chad</option>

                <option>Chile</option>
                <option>China</option>
                <option>Christmas Island</option>
                <option>Cocos (Keeling) Islands</option>
                <option>Colombia</option>
                <option>Comoros</option>

                <option>Congo</option>
                <option>Cook Islands</option>
                <option>Costa Rica</option>
                <option>Cote D&#039;Ivoire</option>
                <option>Croatia</option>
                <option>Cuba</option>

                <option>Cyprus</option>
                <option>Czech Republic</option>
                <option>Denmark</option>
                <option>Djibouti</option>
                <option>Dominica</option>
                <option>Dominican Republic</option>

                <option>East Timor</option>
                <option>Ecuador</option>
                <option>Egypt</option>
                <option>El Salvador</option>
                <option>Equatorial Guinea</option>
                <option>Eritrea</option>

                <option>Estonia</option>
                <option>Ethiopia</option>
                <option>Falkland Islands (Malvinas)</option>
                <option>Faroe Islands</option>
                <option>Fiji</option>
                <option>Finland</option>

                <option>France</option>
                <option>France, Metropolitan</option>
                <option>French Guiana</option>
                <option>French Polynesia</option>
                <option>French Southern Territories</option>
                <option>Gabon</option>

                <option>Gambia</option>
                <option>Georgia</option>
                <option>Germany</option>
                <option>Ghana</option>
                <option>Gibraltar</option>
                <option>Greece</option>

                <option>Greenland</option>
                <option>Grenada</option>
                <option>Guadeloupe</option>
                <option>Guam</option>
                <option>Guatemala</option>
                <option>Guinea</option>

                <option>Guinea-bissau</option>
                <option>Guyana</option>
                <option>Haiti</option>
                <option>Heard and Mc Donald Islands</option>
                <option>Honduras</option>
                <option>Hong Kong</option>

                <option>Hungary</option>
                <option>Iceland</option>
                <option>India</option>
                <option>Indonesia</option>
                <option>Iran (Islamic Republic of)</option>
                <option>Iraq</option>

                <option>Ireland</option>
                <option>Israel</option>
                <option>Italy</option>
                <option>Jamaica</option>
                <option>Japan</option>
                <option>Jordan</option>

                <option>Kazakhstan</option>
                <option>Kenya</option>
                <option>Kiribati</option>
                <option>Korea, Democratic People&#039;s Republic of</option>
                <option>Korea, Republic of</option>
                <option>Kuwait</option>

                <option>Kyrgyzstan</option>
                <option>Lao People&#039;s Democratic Republic</option>
                <option>Latvia</option>
                <option>Lebanon</option>
                <option>Lesotho</option>
                <option>Liberia</option>

                <option>Libyan Arab Jamahiriya</option>
                <option>Liechtenstein</option>
                <option>Lithuania</option>
                <option>Luxembourg</option>
                <option>Macau</option>
                <option>Macedonia, The Former Yugoslav Republic of</option>

                <option>Madagascar</option>
                <option>Malawi</option>
                <option>Malaysia</option>
                <option>Maldives</option>
                <option>Mali</option>
                <option>Malta</option>

                <option>Marshall Islands</option>
                <option>Martinique</option>
                <option>Mauritania</option>
                <option>Mauritius</option>
                <option>Mayotte</option>
                <option>Mexico</option>

                <option>Micronesia, Federated States of</option>
                <option>Moldova, Republic of</option>
                <option>Monaco</option>
                <option>Mongolia</option>
                <option>Montserrat</option>
                <option>Morocco</option>

                <option>Mozambique</option>
                <option>Myanmar</option>
                <option>Namibia</option>
                <option>Nauru</option>
                <option>Nepal</option>
                <option>Netherlands</option>

                <option>Netherlands Antilles</option>
                <option>New Caledonia</option>
                <option>New Zealand</option>
                <option>Nicaragua</option>
                <option>Niger</option>
                <option>Nigeria</option>

                <option>Niue</option>
                <option>Norfolk Island</option>
                <option>Northern Mariana Islands</option>
                <option>Norway</option>
                <option>Oman</option>
                <option>Pakistan</option>

                <option>Palau</option>
                <option>Panama</option>
                <option>Papua New Guinea</option>
                <option>Paraguay</option>
                <option>Peru</option>
                <option>Philippines</option>

                <option>Pitcairn</option>
                <option>Poland</option>
                <option>Portugal</option>
                <option>Puerto Rico</option>
                <option>Qatar</option>
                <option>Reunion</option>

                <option>Romania</option>
                <option>Russian Federation</option>
                <option>Rwanda</option>
                <option>Saint Kitts and Nevis</option>
                <option>Saint Lucia</option>
                <option>Saint Vincent and the Grenadines</option>

                <option>Samoa</option>
                <option>San Marino</option>
                <option>Sao Tome and Principe</option>
                <option>Saudi Arabia</option>
                <option>Senegal</option>
                <option>Seychelles</option>

                <option>Sierra Leone</option>
                <option>Singapore</option>
                <option>Slovakia (Slovak Republic)</option>
                <option>Slovenia</option>
                <option>Solomon Islands</option>
                <option>Somalia</option>

                <option>South Africa</option>
                <option>South Georgia and the South Sandwich Islands</option>
                <option>Spain</option>
                <option>Sri Lanka</option>
                <option>St. Helena</option>
                <option>St. Pierre and Miquelon</option>

                <option>Sudan</option>
                <option>Suriname</option>
                <option>Svalbard and Jan Mayen Islands</option>
                <option>Swaziland</option>
                <option>Sweden</option>
                <option>Switzerland</option>

                <option>Syrian Arab Republic</option>
                <option>Taiwan</option>
                <option>Tajikistan</option>
                <option>Tanzania, United Republic of</option>
                <option>Thailand</option>
                <option>Togo</option>

                <option>Tokelau</option>
                <option>Tonga</option>
                <option>Trinidad and Tobago</option>
                <option>Tunisia</option>
                <option>Turkey</option>
                <option>Turkmenistan</option>

                <option>Turks and Caicos Islands</option>
                <option>Tuvalu</option>
                <option>Uganda</option>
                <option>Ukraine</option>
                <option>United Arab Emirates</option>
                <option>United Kingdom</option>

                <option>United States</option>
                <option>United States Minor Outlying Islands</option>
                <option>Uruguay</option>
                <option>Uzbekistan</option>
                <option>Vanuatu</option>
                <option>Vatican City State (Holy See)</option>

                <option>Venezuela</option>
                <option>Viet Nam</option>
                <option>Virgin Islands (British)</option>
                <option>Virgin Islands (U.S.)</option>
                <option>Wallis and Futuna Islands</option>
                <option>Western Sahara</option>

                <option>Yemen</option>
                <option>Yugoslavia</option>
                <option>Zaire</option>
                <option>Zambia</option>
                <option>Zimbabwe</option>
              </select>

              </td>
	      <td>
                <input type="submit" name="add" value="Add / Hinzuf&uuml;gen">
	      </td>
	    </tr>
	  </form>
        </table>
	<br><br>
	<form method="POST" action="index.php?password=<?php echo $_GET['password']; ?>">
	    <table>
	      <tr>
	        <td align="right" width="50%">Title / Titel: </td>
		<td width="50%"><input type="text" value="<?php echo $btitle; ?>" name="btitle"></td>
	      </tr>
	      <tr>
	        <td align="right" width="50%">Font color / Schriftfarbe: </td>
		<td width="50%"><input type="text" value="<?php echo $color; ?>" name="color"></td>
	      </tr>
	      <tr>
	        <td align="right" width="50%">Background color / Hintergrundfarbe: </td>
		<td width="50%"><input type="text" value="<?php echo $bg_color; ?>" name="bg_color"></td>
	      </tr>
	      <tr>
	        <td align="right" width="50%">Table color / Tabellenfarbe: </td>
		<td width="50%"><input type="text" value="<?php echo $t_color; ?>" name="t_color"></td>
	      </tr>
	      <tr>
	        <td align="right" width="50%">Table background color / Tabellenhintergrundfarbe: </td>
		<td width="50%"><input type="text" value="<?php echo $tb_color; ?>" name="tb_color"></td>
	      </tr>
	      <tr>
	        <td align="right" width="50%">Table data color / Tabellenzeilenfarbe: </td>
		<td width="50%"><input type="text" value="<?php echo $td_color; ?>" name="td_color"></td>
	      </tr>
	      <tr>
	        <td align="right" width="50%">Table data background color / Tabellenzeilenhintergrundfarbe: </td>
		<td width="50%"><input type="text" value="<?php echo $tdb_color; ?>" name="tdb_color"></td>
	      </tr>
	      <tr>
	        <td align="right" width="50%">Table header color / Tabellenkopffarbe: </td>
		<td width="50%"><input type="text" value="<?php echo $th_color; ?>" name="th_color"></td>
	      </tr>
	      <tr>
	        <td align="right" width="50%">Table header background color / Tabellenkopfhintergrundfarbe: </td>
		<td width="50%"><input type="text" value="<?php echo $thb_color; ?>" name="thb_color"></td>
	      </tr>
	      <tr>
	        <td align="right" width="50%">Link hover color / Link "Hover"-Farbe: </td>
		<td width="50%"><input type="text" value="<?php echo $h_color; ?>" name="h_color"></td>
	      </tr>
	    </table>
	  <br><br>
	  <input type="submit" name="change" value="Change / &Auml;ndern">
	</form>
	<?php
	}
	?>
      </td>
    </tr>
  </table>
<!--  </body>
</html>-->
