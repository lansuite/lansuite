<?php

//------------------------------------------------------------------------------------------------------------+
//[ PREPARE CONFIG - DO NOT CHANGE OR MOVE THIS ]

  global $lgsl_config; $lgsl_config = array();

//------------------------------------------------------------------------------------------------------------+
//[ TEXT OPTIONS - 'nmp' AND 'nnm' WILL ONLY UPDATE AFTER THE CACHE IS EMPTIED ]

  $lgsl_config['text']['vsd'] = "CLICK TO VIEW SERVER DETAILS";
  $lgsl_config['text']['slk'] = "GAME LINK";
  $lgsl_config['text']['sts'] = "Status:";
  $lgsl_config['text']['adr'] = "Address:";
  $lgsl_config['text']['cpt'] = "Connection Port:";
  $lgsl_config['text']['qpt'] = "Query Port:";
  $lgsl_config['text']['typ'] = "Type:";
  $lgsl_config['text']['gme'] = "Game:";
  $lgsl_config['text']['map'] = "Map:";
  $lgsl_config['text']['plr'] = "Players:";
  $lgsl_config['text']['npi'] = "NO PLAYER INFO";
  $lgsl_config['text']['nei'] = "NO EXTRA INFO";
  $lgsl_config['text']['ehs'] = "Setting";
  $lgsl_config['text']['ehv'] = "Value";
  $lgsl_config['text']['onl'] = "ONLINE";
  $lgsl_config['text']['onp'] = "ONLINE WITH PASSWORD";
  $lgsl_config['text']['nrs'] = "NO RESPONSE";
  $lgsl_config['text']['pen'] = "WAITING TO BE QUERIED";
  $lgsl_config['text']['zpl'] = "PLAYERS:";
  $lgsl_config['text']['mid'] = "MISSING OR INVALID SERVER ID";
  $lgsl_config['text']['nnm'] = "--";
  $lgsl_config['text']['nmp'] = "--";
  $lgsl_config['text']['tns'] = "Servers:";
  $lgsl_config['text']['tnp'] = "Players:";
  $lgsl_config['text']['tmp'] = "Max Players:";
  $lgsl_config['text']['asd'] = "PUBLIC ADDING OF SERVERS IS DISABLED";
  $lgsl_config['text']['awm'] = "THIS AREA ALLOWS YOU TO TEST AND THEN ADD ONLINE GAME SERVERS TO THE LIST";
  $lgsl_config['text']['ats'] = "Test Server";
  $lgsl_config['text']['aaa'] = "SERVER ALREADY ADDED AND NEEDS ADMIN APPROVAL";
  $lgsl_config['text']['aan'] = "SERVER ALREADY ADDED";
  $lgsl_config['text']['anr'] = "NO RESPONSE - MAKE SURE YOU ENTERED THE CORRECT DETAILS";
  $lgsl_config['text']['ada'] = "SERVER HAS BEEN ADDED FOR ADMIN APPROVAL";
  $lgsl_config['text']['adn'] = "SERVER HAS BEEN ADDED";
  $lgsl_config['text']['asc'] = "SUCCESS - PLEASE CONFIRM ITS THE CORRECT SERVER";
  $lgsl_config['text']['aas'] = "Add Server";

//------------------------------------------------------------------------------------------------------------+
//[ TEXT OPTIONS - e107 VERSION ONLY - FOR OTHERS SET PAGE AND ZONE TITLES USING THE CMS ]

  $lgsl_config['title'][0] = "Live Game Server List";
  $lgsl_config['title'][1] = "Game Server";
  $lgsl_config['title'][2] = "Game Server";
  $lgsl_config['title'][3] = "Game Server";
  $lgsl_config['title'][4] = "Game Server";
  $lgsl_config['title'][5] = "Game Server";
  $lgsl_config['title'][6] = "Game Server";
  $lgsl_config['title'][7] = "Game Server";
  $lgsl_config['title'][8] = "Game Server";

//------------------------------------------------------------------------------------------------------------+
//[ BACKGROUND COLORS - CHANGE TO MATCH YOUR THEME AND COMPLEMENT YOUR THEME FONT COLOR ]

  $lgsl_config['background'][1] = "background-color:#e4eaf2";
  $lgsl_config['background'][2] = "background-color:#f4f7fa";

//------------------------------------------------------------------------------------------------------------+
//[ SHOW TOTAL SERVERS / PLAYERS / MAX PLAYERS AT BOTTOM OF THE LIST ]

  $lgsl_config['list']['totals'] = 0;

//------------------------------------------------------------------------------------------------------------+
//[ ZONE SHOWS PLAYER NAMES - OPTIONS: 0=NO 1=YES ]

  $lgsl_config['players'][1] = 1;
  $lgsl_config['players'][2] = 1;
  $lgsl_config['players'][3] = 1;
  $lgsl_config['players'][4] = 1;
  $lgsl_config['players'][5] = 1;
  $lgsl_config['players'][6] = 1;
  $lgsl_config['players'][7] = 1;
  $lgsl_config['players'][8] = 1;

//------------------------------------------------------------------------------------------------------------+
//[ ZONE RANDOMISATION - SEE README ON HOW TO USE ]

  $lgsl_config['random'][1] = 0;
  $lgsl_config['random'][2] = 0;
  $lgsl_config['random'][3] = 0;
  $lgsl_config['random'][4] = 0;
  $lgsl_config['random'][5] = 0;
  $lgsl_config['random'][6] = 0;
  $lgsl_config['random'][7] = 0;
  $lgsl_config['random'][8] = 0;

//------------------------------------------------------------------------------------------------------------+
//[ ZONE GRID WIDTH - INCREASE TO MAKE ZONES GO SIDE BY SIDE ]

  $lgsl_config['grid'][1] = 1;
  $lgsl_config['grid'][2] = 1;
  $lgsl_config['grid'][3] = 1;
  $lgsl_config['grid'][4] = 1;
  $lgsl_config['grid'][5] = 1;
  $lgsl_config['grid'][6] = 1;
  $lgsl_config['grid'][7] = 1;
  $lgsl_config['grid'][8] = 1;

//------------------------------------------------------------------------------------------------------------+
//[ ZONE SIZING - THE BOX CONTAINING PLAYER NAMES WILL INCREASE UNTIL THE HEIGHT LIMIT IS REACHED ]

  $lgsl_config['zone']['width']     = "160"; // images will be cropped unless also resized to match
  $lgsl_config['zone']['height']    = "100"; // maximum height of the zone box containing player names
  $lgsl_config['zone']['line_size'] = "19";  // multiplied by number of players to set the zone box height

//------------------------------------------------------------------------------------------------------------+
//[ SORTING OPTIONS ]

  $lgsl_config['sort']['servers'] = "id";   // option are: id, type, zone, players, status
  $lgsl_config['sort']['players'] = "name"; // option are: name, score

//------------------------------------------------------------------------------------------------------------+
// [ HIDE OFFLINE SERVERS ON LIST AND ZONES - OPTIONS: 0=SHOW 1=HIDE ]

  $lgsl_config['hide_offline'][0] = 0;
  $lgsl_config['hide_offline'][1] = 0;
  $lgsl_config['hide_offline'][2] = 0;
  $lgsl_config['hide_offline'][3] = 0;
  $lgsl_config['hide_offline'][4] = 0;
  $lgsl_config['hide_offline'][5] = 0;
  $lgsl_config['hide_offline'][6] = 0;
  $lgsl_config['hide_offline'][7] = 0;
  $lgsl_config['hide_offline'][8] = 0;

//------------------------------------------------------------------------------------------------------------+
//[ STAND-ALONE VERSION - ADMIN DETAILS ]

  $lgsl_config['admin']['user'] = "admin";
  $lgsl_config['admin']['pass'] = "mypass";

//------------------------------------------------------------------------------------------------------------+
//[ STAND-ALONE VERSION DATABASE SETTINGS - MAINLY USED FOR THE STAND-ALONE VERSION ]

  if (file_exists('inc/base/config.php')) $config = parse_ini_file('inc/base/config.php', 1);
  if (file_exists('../../inc/base/config.php')) $config = parse_ini_file('../../inc/base/config.php', 1);
  $lgsl_config['db']['server'] = $config['database']['server'];
  $lgsl_config['db']['user']   = $config['database']['user'];
  $lgsl_config['db']['pass']   = $config['database']['passwd'];
  $lgsl_config['db']['db']     = $config['database']['database'];
  $lgsl_config['db']['table']  = $config['database']['prefix'] . "lgsl";

//------------------------------------------------------------------------------------------------------------+
//[ FEED METHOD - OPTIONS: 0=DISABLED 1=CURL OR FSOCKOPEN 2=FSOCKOPEN ONLY ]

  $lgsl_config['feed']['method'] = 0;
  $lgsl_config['feed']['url']    = "http://www.greycube.co.uk/lgsl/feed/lgsl_files/lgsl_feed.php";

//------------------------------------------------------------------------------------------------------------+
//[ ADVANCED SETTINGS - DO NOT TOUCH THESE UNLESS YOU KNOW WHAT YOUR DOING ]

  $lgsl_config['management']    = 0;           // 1=show advanced management in the admin by default
  $lgsl_config['public_add']    = 0;           // 1=servers require approval OR 2=servers shown instantly
  $lgsl_config['public_feed']   = 0;           // 1=feed requests can add servers to your list
  $lgsl_config['host_to_ip']    = 0;           // 1=show the servers ip instead of its hostname
  $lgsl_config['direct_index']  = 0;           // 1=link to index.php instead of the folder
  $lgsl_config['no_realpath']   = 0;           // 1=do not use the realpath function
  $lgsl_config['retry_offline'] = 0;           // 1=repeat query if a server does not respond the first time
  $lgsl_config['timeout']       = 0;           // 1=increase the time a server is given to respond
  $lgsl_config['live_time']     = 4;           // maximum loading delay from getting server updates
  $lgsl_config['cache_time']    = 60;          // seconds before the cached information is considered old
  $lgsl_config['url_path']      = "";          // full url to /lgsl_files/ for when auto detection fails
  $lgsl_config['cms']           = "sa";        // sets which CMS specific code to use

//------------------------------------------------------------------------------------------------------------+

?>