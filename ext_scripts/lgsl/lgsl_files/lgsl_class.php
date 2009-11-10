<?php

 /*----------------------------------------------------------------------------------------------------------\
 |                                                                                                            |
 |                      [ LIVE GAME SERVER LIST ] [ © RICHARD PERRY FROM GREYCUBE.COM ]                       |
 |                                                                                                            |
 |    Released under the terms and conditions of the GNU General Public License Version 3 (http://gnu.org)    |
 |                                                                                                            |
 |-------------------------------------------------------------------------------------------------------------
 |        [ EDITOR STYLE SETTINGS: LUCIDA CONSOLE, SIZE 10, TAB = 2 SPACES, BOLD GLOBALLY TURNED OFF ]        |
 \-----------------------------------------------------------------------------------------------------------*/

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  if (!function_exists('lgsl_url_path')) { // START OF DOUBLE LOAD PROTECTION

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_bg($rotation_overide = "no")
  {
    global $lgsl_config;
    global $lgsl_bg_rotate;

    if ($rotation_overide !== "no")
    {
      $lgsl_bg_rotate = $rotation_overide ? TRUE : FALSE;
    }
    else
    {
      $lgsl_bg_rotate = $lgsl_bg_rotate ? FALSE : TRUE;
    }

    $background = $lgsl_bg_rotate ? $lgsl_config['background'][1] : $lgsl_config['background'][2];

    return $background;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_link($s)
  {
    global $lgsl_config, $lgsl_url_path;

    $index = $lgsl_config['direct_index'] ? "index.php" : "";

    if ($lgsl_config['cms'] == "e107")
    {
      $link = is_numeric($s) ? e_PLUGIN."lgsl/{$index}?s={$s}" : e_PLUGIN."lgsl/{$index}";
    }

    elseif ($lgsl_config['cms'] == "joomla")
    {
      $link = is_numeric($s) ? JRoute::_("index.php?option=com_lgsl&s={$s}") : JRoute::_("index.php?option=com_lgsl");
    }

    elseif ($lgsl_config['cms'] == "phpnuke")
    {
      $link = is_numeric($s) ? "modules.php?name=LGSL&s={$s}" : "modules.php?name=LGSL";
    }

    elseif ($lgsl_config['cms'] == "sa")
    {
      $link = is_numeric($s) ? $lgsl_url_path."../{$index}?s={$s}" : $lgsl_url_path."../{$index}";
    }

    return $link;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_database()
  {
    global $lgsl_database, $lgsl_config, $lgsl_file_path;

    if (!$lgsl_config['db']['pass'])
    {
      if ($lgsl_config['cms'] == "e107")
      {
        @include "{$lgsl_file_path}../../../e107_config.php";

        $lgsl_config['db']['server'] = $mySQLserver;
        $lgsl_config['db']['user']   = $mySQLuser;
        $lgsl_config['db']['pass']   = $mySQLpassword;
        $lgsl_config['db']['db']     = $mySQLdefaultdb;
        $lgsl_config['db']['prefix'] = $mySQLprefix;
      }

      elseif ($lgsl_config['cms'] == "joomla")
      {
        @include_once "{$lgsl_file_path}../../../configuration.php";

        $joomla_config = new JConfig();

        $lgsl_config['db']['server'] = $joomla_config->host;
        $lgsl_config['db']['user']   = $joomla_config->user;
        $lgsl_config['db']['pass']   = $joomla_config->password;
        $lgsl_config['db']['db']     = $joomla_config->db;
        $lgsl_config['db']['prefix'] = $joomla_config->dbprefix;
      }

      elseif ($lgsl_config['cms'] == "phpnuke")
      {
        @include "{$lgsl_file_path}../../../config.php";
        @include "{$lgsl_file_path}../../../conf.inc.php";
        @include "{$lgsl_file_path}../../../includes/config.php";

        $lgsl_config['db']['server'] = $dbhost;
        $lgsl_config['db']['user']   = $dbuname;
        $lgsl_config['db']['pass']   = $dbpass;
        $lgsl_config['db']['db']     = $dbname;
        $lgsl_config['db']['prefix'] = $prefix."_";
      }
    }

    $lgsl_database  = mysql_connect($lgsl_config['db']['server'], $lgsl_config['db']['user'], $lgsl_config['db']['pass']) or die(mysql_error());
    $lgsl_select_db = mysql_select_db($lgsl_config['db']['db'], $lgsl_database) or die(mysql_error());
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_query_cached($type, $ip, $c_port, $q_port, $s_port, $request)
  {
    global $lgsl_config;

    lgsl_database();

    // PROTECT THE DATABASE QUERY

    $type    = mysql_real_escape_string($type);
    $ip      = mysql_real_escape_string($ip);
    $c_port  = mysql_real_escape_string(intval($c_port));
    $q_port  = mysql_real_escape_string(intval($q_port));
    $s_port  = mysql_real_escape_string(intval($s_port));

    // GET CACHE

    $mysql_query  = "SELECT * FROM `{$lgsl_config['db']['prefix']}{$lgsl_config['db']['table']}` WHERE `type`='{$type}' AND `ip`='{$ip}' AND `q_port`='{$q_port}' LIMIT 1";
    $mysql_result = mysql_query($mysql_query) or die(mysql_error());
    $mysql_row    = mysql_fetch_array($mysql_result, MYSQL_ASSOC);

    // CHECK IF SERVER IS NOT IN THE DATABASE AND ADD IF REQUESTED

    if (!$mysql_row)
    {
      if (strpos($request, "a") !== FALSE)
      {
        $mysql_query     = "INSERT INTO `{$lgsl_config['db']['prefix']}{$lgsl_config['db']['table']}` (`type`,`ip`,`c_port`,`q_port`,`s_port`,`cache`,`cache_time`) VALUES ('{$type}','{$ip}','{$c_port}','{$q_port}','{$s_port}','','')";
        $mysql_result    = mysql_query($mysql_query) or die(mysql_error());
        $mysql_row['id'] = mysql_insert_id();
      }
      else
      {
        exit("LGSL PROBLEM: REQUESTED SERVER NOT IN DATABASE: '{$type} : {$ip} : {$c_port} : {$q_port} : {$s_port} : {$request}'");
      }
    }

    // UNPACK CACHE AND CACHE TIMES

    $cache         = unserialize(base64_decode($mysql_row['cache']));
    $cache_time    = explode("_", $mysql_row['cache_time']);
    $cache_time[0] = intval($cache_time[0]);
    $cache_time[1] = intval($cache_time[1]);
    $cache_time[2] = intval($cache_time[2]);

    // SET THE SERVER AS OFFLINE AND PENDING WHEN THERE IS NO CACHE

    if (!isset($cache['b']))
    {
      $cache      = array();
      $cache['b'] = array();
      $cache['b']['status']  = 0;
      $cache['b']['pending'] = 1;
    }

    // IF NEEDED CONVERT HOSTNAME TO IP

    if ($lgsl_config['host_to_ip'])
    {
      $ip = gethostbyname($ip);
    }

    // ALWAYS UPDATE THESE WITH THE LATEST VALUES

    $cache['b']['type']    = $type;
    $cache['b']['ip']      = $ip;
    $cache['b']['c_port']  = $c_port;
    $cache['b']['q_port']  = $q_port;
    $cache['b']['s_port']  = $s_port;
    $cache['o']['request'] = $request;
    $cache['o']['id']      = $mysql_row['id'];
    $cache['o']['zone']    = $mysql_row['zone'];
    $cache['o']['comment'] = $mysql_row['comment'];

    if (!isset($cache['s']))
    {
      $cache['s']               = array();
      $cache['s']['game']       = $type;
      $cache['s']['name']       = $lgsl_config['text']['nnm'];
      $cache['s']['map']        = $lgsl_config['text']['nmp'];
      $cache['s']['players']    = 0;
      $cache['s']['playersmax'] = 0;
      $cache['s']['password']   = 0;
    }

    if (!isset($cache['e'])) { $cache['e'] = array(); }
    if (!isset($cache['p'])) { $cache['p'] = array(); }

    // CHECK WHAT IS NEEDED

    $needed = "";

    if (strpos($request, "c") === FALSE) // CACHE ONLY REQUEST
    {
      if (strpos($request, "s") !== FALSE && time() > ($cache_time[0]+$lgsl_config['cache_time'])) { $needed .= "s"; }
      if (strpos($request, "e") !== FALSE && time() > ($cache_time[1]+$lgsl_config['cache_time'])) { $needed .= "e"; }
      if (strpos($request, "p") !== FALSE && time() > ($cache_time[2]+$lgsl_config['cache_time'])) { $needed .= "p"; }
    }

    if ($needed)
    {
      // UPDATE CACHE TIMES BEFORE QUERY - PREVENTS OTHER INSTANCES FROM QUERY FLOODING THE SAME SERVER

      $packed_times = time() + $lgsl_config['cache_time'] + 10;
      $packed_times = "{$packed_times}_{$packed_times}_{$packed_times}";
      $mysql_query  = "UPDATE `{$lgsl_config['db']['prefix']}{$lgsl_config['db']['table']}` SET `cache_time`='{$packed_times}' WHERE `id`='{$mysql_row['id']}' LIMIT 1";
      $mysql_result = mysql_query($mysql_query) or die(mysql_error());

      // GET WHAT IS NEEDED

      $live = lgsl_query_live($type, $ip, $c_port, $q_port, $s_port, $needed);

      if (!$live['b']['status'] && $lgsl_config['retry_offline'] && !$lgsl_config['feed']['method'])
      {
        $live = lgsl_query_live($type, $ip, $c_port, $q_port, $s_port, $needed);
      }

      // CHECK AND CONVERT TO UTF-8 WHERE NEEDED

      $live = lgsl_charset_convert($live, lgsl_charset_detect($live));

      // IF SERVER IS OFFLINE PRESERVE SOME OF THE CACHE AND CLEAR THE REST

      if (!$live['b']['status'])
      {
        $live['s']['game']       = $cache['s']['game'];
        $live['s']['name']       = $cache['s']['name'];
        $live['s']['map']        = $cache['s']['map'];
        $live['s']['password']   = $cache['s']['password'];
        $live['s']['players']    = 0;
        $live['s']['playersmax'] = $cache['s']['playersmax'];
        $live['e']               = array();
        $live['p']               = array();
      }

      // MERGE LIVE INTO CACHE

      if (isset($live['b'])) { $cache['b'] = $live['b']; }
      if (isset($live['s'])) { $cache['s'] = $live['s']; $cache_time[0] = time(); }
      if (isset($live['e'])) { $cache['e'] = $live['e']; $cache_time[1] = time(); }
      if (isset($live['p'])) { $cache['p'] = $live['p']; $cache_time[2] = time(); }

      // UPDATE CACHE

      $packed_cache = mysql_real_escape_string(base64_encode(serialize($cache)));
      $packed_times = mysql_real_escape_string(implode("_", $cache_time));
      $mysql_query  = "UPDATE `{$lgsl_config['db']['prefix']}{$lgsl_config['db']['table']}` SET `status`='{$cache['b']['status']}',`cache`='{$packed_cache}',`cache_time`='{$packed_times}' WHERE `id`='{$mysql_row['id']}' LIMIT 1";
      $mysql_result = mysql_query($mysql_query) or die(mysql_error());
    }

    // RETURN ONLY THE REQUESTED

    if (strpos($request, "s") === FALSE) { unset($cache['s']); }
    if (strpos($request, "e") === FALSE) { unset($cache['e']); }
    if (strpos($request, "p") === FALSE) { unset($cache['p']); }

    return $cache;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_query_cached_all($request)
  {
    global $lgsl_config;

    lgsl_database();

    $mysql_query  = "SELECT `type`,`ip`,`c_port`,`q_port`,`s_port` FROM `{$lgsl_config['db']['prefix']}{$lgsl_config['db']['table']}` WHERE `disabled`=0 ORDER BY `cache_time` ASC";
    $mysql_result = mysql_query($mysql_query) or die(mysql_error());

    $server_list  = array();

    while ($mysql_row = mysql_fetch_array($mysql_result, MYSQL_ASSOC))
    {
      if (strpos($request, "c") === FALSE && lgsl_timer("check")) { $request .= "c"; }

      $server = lgsl_query_cached($mysql_row['type'], $mysql_row['ip'], $mysql_row['c_port'], $mysql_row['q_port'], $mysql_row['s_port'], $request);

      if ($lgsl_config['hide_offline'][0] && !$server['b']['status']) { continue; }

      $server_list[] = $server;
    }

    return $server_list;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_query_cached_zone($request, $zone_number)
  {
    global $lgsl_config;

    lgsl_database();

    $zone_number = intval($zone_number);
    $zone_random = intval($lgsl_config['random'][$zone_number]);

    if ($zone_random)
    {
      $mysql_query = "SELECT `type`,`ip`,`c_port`,`q_port`,`s_port` FROM `{$lgsl_config['db']['prefix']}{$lgsl_config['db']['table']}` WHERE `zone`='{$zone_number}' AND `disabled`=0 ORDER BY rand()";
    }
    else
    {
      $mysql_query = "SELECT `type`,`ip`,`c_port`,`q_port`,`s_port` FROM `{$lgsl_config['db']['prefix']}{$lgsl_config['db']['table']}` WHERE `zone`='{$zone_number}' AND `disabled`=0 ORDER BY `cache_time` ASC";
    }

    $mysql_result = mysql_query($mysql_query) or die(mysql_error());

    $server_list  = array();

    while ($mysql_row = mysql_fetch_array($mysql_result, MYSQL_ASSOC))
    {
      if (strpos($request, "c") === FALSE && lgsl_timer("check")) { $request .= "c"; }

      $server = lgsl_query_cached($mysql_row['type'], $mysql_row['ip'], $mysql_row['c_port'], $mysql_row['q_port'], $mysql_row['s_port'], $request);

      if ($lgsl_config['hide_offline'][$zone_number] && !$server['b']['status']) { continue; }

      $server_list[] = $server;

      if ($zone_random && count($server_list) >= $zone_random) { break; }
    }

    return $server_list;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_cached_totals()
  {
    global $lgsl_config;

    lgsl_database();

    $mysql_query  = "SELECT `cache` FROM `{$lgsl_config['db']['prefix']}{$lgsl_config['db']['table']}` WHERE `disabled`=0";
    $mysql_result = mysql_query($mysql_query) or die(mysql_error());

    $total['players']         = 0;
    $total['playersmax']      = 0;
    $total['servers']         = 0;
    $total['servers_online']  = 0;
    $total['servers_offline'] = 0;

    while ($mysql_row = mysql_fetch_array($mysql_result, MYSQL_ASSOC))
    {
      $server = unserialize(base64_decode($mysql_row['cache']));

      $total['players']    += $server['s']['players'];
      $total['playersmax'] += $server['s']['playersmax'];

                                    $total['servers']         ++;
      if ($server['b']['status']) { $total['servers_online']  ++; }
      else                        { $total['servers_offline'] ++; }
    }

    return $total;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_lookup_id($id)
  {
    global $lgsl_config;

    lgsl_database();

    $id           = mysql_real_escape_string(intval($id));
    $mysql_query  = "SELECT `type`,`ip`,`c_port`,`q_port`,`s_port` FROM `{$lgsl_config['db']['prefix']}{$lgsl_config['db']['table']}` WHERE `id`='{$id}' LIMIT 1";
    $mysql_result = mysql_query($mysql_query) or die(mysql_error());
    $mysql_row    = mysql_fetch_array($mysql_result, MYSQL_ASSOC);

    return $mysql_row;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_timer($action)
  {
    global $lgsl_config;
    global $lgsl_timer;

    if (!$lgsl_timer)
    {
      $microtime  = microtime();
      $microtime  = explode(' ', $microtime);
      $microtime  = $microtime[1] + $microtime[0];
      $lgsl_timer = $microtime - 0.01;
    }

    $time_limit = intval($lgsl_config['live_time']);
    $time_php   = ini_get("max_execution_time");

    if ($time_limit > $time_php)
    {
      @set_time_limit($time_limit + 5);

      $time_php = ini_get("max_execution_time");

      if ($time_limit > $time_php)
      {
        $time_limit = $time_php - 5;
      }
    }

    if ($action == "limit")
    {
      return $time_limit;
    }

    $microtime  = microtime();
    $microtime  = explode(' ', $microtime);
    $microtime  = $microtime[1] + $microtime[0];
    $time_taken = $microtime - $lgsl_timer;

    if ($action == "check")
    {
      return ($time_taken > $time_limit) ? TRUE : FALSE;
    }
    else
    {
      return round($time_taken, 2);
    }
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_server_misc($server)
  {
    global $lgsl_config, $lgsl_url_path;

    $misc['icon_details']       = $lgsl_url_path."other/icon_details.gif";
    $misc['icon_game']          = lgsl_icon_game($server['b']['type'], $server['s']['game']);
    $misc['icon_status']        = lgsl_icon_status($server['b']['status'], $server['s']['password'], $server['b']['pending']);
    $misc['image_map']          = lgsl_image_map($server['b']['status'], $server['b']['type'], $server['s']['game'], $server['s']['map'], TRUE, $server['o']['id']);
    $misc['image_map_password'] = lgsl_image_map_password($server['b']['status'], $server['s']['password']);
    $misc['text_status']        = lgsl_text_status($server['b']['status'], $server['s']['password'], $server['b']['pending']);
    $misc['text_type_game']     = lgsl_text_type_game($server['b']['type'], $server['s']['game']);
    $misc['name_filtered']      = lgsl_name_filtered($server['s']['name']);
    $misc['software_link']      = lgsl_software_link($server['b']['type'], $server['b']['ip'], $server['b']['c_port'], $server['b']['q_port'], $server['b']['s_port']);

    return $misc;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_icon_game($type, $game)
  {
    global $lgsl_file_path, $lgsl_url_path;

    $type = preg_replace("/[^a-z0-9_]/", "_", strtolower($type));
    $game = preg_replace("/[^a-z0-9_]/", "_", strtolower($game));

    $location = array(
    "icons/{$type}/{$game}.gif",
    "icons/{$type}/{$game}.png",
    "icons/{$type}/{$type}.gif",
    "icons/{$type}/{$type}.png");

    foreach ($location as $path)
    {
      if (file_exists($lgsl_file_path.$path)) { return $lgsl_url_path.$path; }
    }

    return "{$lgsl_url_path}other/icon_unknown.gif";
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_icon_status($status, $password, $pending = 0)
  {
    global $lgsl_url_path;

    if ($pending)  { return "{$lgsl_url_path}other/icon_unknown.gif"; }
    if (!$status)  { return "{$lgsl_url_path}other/icon_no_response.gif"; }
    if ($password) { return "{$lgsl_url_path}other/icon_online_password.gif"; }

    return "{$lgsl_url_path}other/icon_online.gif";
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_image_map($status, $type, $game, $map, $check_exists = TRUE, $id = 0)
  {
    global $lgsl_file_path, $lgsl_url_path;

    $type = preg_replace("/[^a-z0-9_]/", "_", strtolower($type));
    $game = preg_replace("/[^a-z0-9_]/", "_", strtolower($game));
    $map  = preg_replace("/[^a-z0-9_]/", "_", strtolower($map));

    if ($check_exists !== TRUE) { return "{$lgsl_url_path}maps/{$type}/{$game}/{$map}.jpg"; }

    if ($status)
    {
      $location = array(
      "maps/{$type}/{$game}/{$map}.jpg",
      "maps/{$type}/{$game}/{$map}.gif",
      "maps/{$type}/{$game}/{$map}.png",
      "maps/{$type}/{$map}.jpg",
      "maps/{$type}/{$map}.gif",
      "maps/{$type}/{$map}.png",
      "maps/{$type}/map_no_image.jpg",
      "maps/{$type}/map_no_image.gif",
      "maps/{$type}/map_no_image.png",
      "other/map_no_image_{$id}.jpg",
      "other/map_no_image_{$id}.gif",
      "other/map_no_image_{$id}.png",
      "other/map_no_image.jpg");
    }
    else
    {
      $location = array(
      "maps/{$type}/map_no_response.jpg",
      "maps/{$type}/map_no_response.gif",
      "maps/{$type}/map_no_response.png",
      "other/map_no_response_{$id}.jpg",
      "other/map_no_response_{$id}.gif",
      "other/map_no_response_{$id}.png",
      "other/map_no_response.jpg");
    }

    foreach ($location as $path)
    {
      if (file_exists($lgsl_file_path.$path)) { return "{$lgsl_url_path}{$path}"; }
    }

    return "#LGSL_DEFAULT_IMAGES_MISSING#";
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_image_map_password($status, $password)
  {
    global $lgsl_url_path;

    if (!$password || !$status) { return "{$lgsl_url_path}other/map_overlay.gif"; }

    return "{$lgsl_url_path}other/map_overlay_password.gif";
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_text_status($status, $password, $pending = 0)
  {
    global $lgsl_config;

    if ($pending)  { return $lgsl_config['text']['pen']; }
    if (!$status)  { return $lgsl_config['text']['nrs']; }
    if ($password) { return $lgsl_config['text']['onp']; }

    return $lgsl_config['text']['onl'];
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_text_type_game($type, $game)
  {
    global $lgsl_config;

    return "[ {$lgsl_config['text']['typ']} {$type} ] [ {$lgsl_config['text']['gme']} {$game} ]";
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_name_filtered($name)
  {
    $name = lgsl_word_wrap($name, 20);
    $name = lgsl_string_html($name);

    return $name;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_sort_servers($server_list)
  {
    global $lgsl_config;

    if (!is_array($server_list)) { return $server_list; }

    if     ($lgsl_config['sort']['servers'] == "id")      { usort($server_list, "lgsl_sort_servers_by_id");      }
    elseif ($lgsl_config['sort']['servers'] == "zone")    { usort($server_list, "lgsl_sort_servers_by_zone");    }
    elseif ($lgsl_config['sort']['servers'] == "type")    { usort($server_list, "lgsl_sort_servers_by_type");    }
    elseif ($lgsl_config['sort']['servers'] == "status")  { usort($server_list, "lgsl_sort_servers_by_status");  }
    elseif ($lgsl_config['sort']['servers'] == "players") { usort($server_list, "lgsl_sort_servers_by_players"); }

    return $server_list;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_sort_fields($server, $fields_show, $fields_hide, $fields_other)
  {
    $fields_list = array();

    if (!is_array($server['p'])) { return $fields_list; }

    foreach ($server['p'] as $player)
    {
      foreach ($player as $field => $value)
      {
        if ($value === "") { continue; }
        if (in_array($field, $fields_list)) { continue; }
        if (in_array($field, $fields_hide)) { continue; }
        $fields_list[] = $field;
      }
    }

    $fields_show = array_intersect($fields_show, $fields_list);

    if ($fields_other == FALSE) { return $fields_show; }

    $fields_list = array_diff($fields_list, $fields_show);

    return array_merge($fields_show, $fields_list);
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_sort_servers_by_id($server_a, $server_b)
  {
    if ($server_a['o']['id'] == $server_b['o']['id']) { return 0; }

    return ($server_a['o']['id'] > $server_b['o']['id']) ? 1 : -1;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_sort_servers_by_zone($server_a, $server_b)
  {
    if ($server_a['o']['zone'] == $server_b['o']['zone']) { return 0; }

    return ($server_a['o']['zone'] > $server_b['o']['zone']) ? 1 : -1;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_sort_servers_by_type($server_a, $server_b)
  {
    $result = strcasecmp($server_a['b']['type'], $server_b['b']['type']);

    if ($result == 0)
    {
      $result = strcasecmp($server_a['s']['game'], $server_b['s']['game']);
    }

    return $result;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_sort_servers_by_status($server_a, $server_b)
  {
    if ($server_a['b']['status'] == $server_b['b']['status']) { return 0; }

    return ($server_a['b']['status'] < $server_b['b']['status']) ? 1 : -1;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_sort_servers_by_players($server_a, $server_b)
  {
    if ($server_a['s']['players'] == $server_b['s']['players']) { return 0; }

    return ($server_a['s']['players'] < $server_b['s']['players']) ? 1 : -1;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_sort_extras($server)
  {
    if (!is_array($server['e'])) { return $server; }

    ksort($server['e']);

    return $server;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_sort_players($server)
  {
    global $lgsl_config;

    if (!is_array($server['p'])) { return $server; }

    if     ($lgsl_config['sort']['players'] == "name")  { usort($server['p'], "lgsl_sort_players_by_name");  }
    elseif ($lgsl_config['sort']['players'] == "score") { usort($server['p'], "lgsl_sort_players_by_score"); }

    return $server;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_sort_players_by_score($player_a, $player_b)
  {
    if ($player_a['score'] == $player_b['score']) { return 0; }

    return ($player_a['score'] < $player_b['score']) ? 1 : -1;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_sort_players_by_name($player_a, $player_b)
  {
    // REMOVE NON ALPHA NUMERIC ASCII WHILE LEAVING UPPER UTF-8 CHARACTERS
    $name_a = preg_replace("/[\x{00}-\x{2F}\x{3A}-\x{40}\x{5B}-\x{60}\x{7B}-\x{7F}]/", "", $player_a['name']);
    $name_b = preg_replace("/[\x{00}-\x{2F}\x{3A}-\x{40}\x{5B}-\x{60}\x{7B}-\x{7F}]/", "", $player_b['name']);

    if (function_exists("mb_convert_case"))
    {
      $name_a = @mb_convert_case($name_a, MB_CASE_LOWER, "UTF-8");
      $name_b = @mb_convert_case($name_b, MB_CASE_LOWER, "UTF-8");
      return strcmp($name_a, $name_b);
    }
    else
    {
      return strcasecmp($name_a, $name_b);
    }
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_charset_detect($server)
  {
    if (!function_exists("mb_detect_encoding")) { return "AUTO"; }

    $test = $server['s']['name'];

    if (is_array($server['p']))
    {
      foreach ($server['p'] as $player)
      {
        $test .= " {$player['name']}";
      }
    }

    $charset = @mb_detect_encoding($server['s']['name'], "UTF-8, Windows-1252, ISO-8859-1, ISO-8859-15");

    return $charset ? $charset : "AUTO";
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_charset_convert($server, $charset)
  {
    if (!function_exists("mb_convert_encoding")) { return $server; }

    if (is_array($server))
    {
      foreach ($server as $key => $value)
      {
        $server[$key] = lgsl_charset_convert($value, $charset);
      }
    }
    else
    {
      $server = @mb_convert_encoding($server, "UTF-8", $charset);
    }

    return $server;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_server_html($server)
  {
    if (isset($server['e']) && is_array($server['e']))
    {
      foreach ($server['e'] as $key => $value)
      {
        $server['e'][$key] = lgsl_word_wrap($value, 90);
      }
    }

    foreach ($server as $key => $value)
    {
      $server[$key] = is_array($value) ? lgsl_server_html($value) : lgsl_string_html($value);
    }

    return $server;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_string_html($string, $xml_feed = FALSE)
  {
    if ($xml_feed != FALSE)
    {
      $string = htmlspecialchars($string, ENT_QUOTES);
    }
    elseif (function_exists("mb_convert_encoding"))
    {
      $string = htmlspecialchars($string, ENT_QUOTES);
      $string = @mb_convert_encoding($string, "HTML-ENTITIES", "UTF-8");
    }
    else
    {
      $string = htmlentities($string, ENT_QUOTES, "UTF-8");
    }

    return $string;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_word_wrap($string, $length_limit)
  {
    $words = explode(" ", $string);

    foreach ($words as $word)
    {
      $word_length = function_exists("mb_strlen") ? mb_strlen($word, "UTF-8") : strlen($word);

      if ($word_length < $length_limit)
      {
        $words_new[] = $word;
      }
      else
      {
        for ($i=0; $i<$word_length; $i+=$length_limit)
        {
          $words_new[] = function_exists("mb_substr") ? mb_substr($word, $i, $length_limit, "UTF-8") : substr($word, $i, $length_limit);
        }
      }
    }

//  return implode("&#8203;", $words_new); // INVISIBLE WRAP EXCEPT FOR IE6
    return implode(" ",       $words_new);
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_realpath($path)
  {
    // WRAPPER SO IT CAN BE DISABLED

    global $lgsl_config;

    return $lgsl_config['no_realpath'] ? $path : realpath($path);
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_file_path()
  {
    // GET THE LGSL_CLASS.PHP PATH

    $lgsl_path = __FILE__;

    // SHORTEN TO JUST THE FOLDERS AND ADD TRAILING SLASH

    $lgsl_path = dirname($lgsl_path)."/";

    // CONVERT WINDOWS BACKSLASHES TO FORWARDSLASHES

    $lgsl_path = str_replace("\\", "/", $lgsl_path);

    return $lgsl_path;
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_url_path()
  {
    // CHECK IF PATH HAS BEEN SET IN CONFIG

    global $lgsl_config;

    if ($lgsl_config['url_path'])
    {
      return $lgsl_config['url_path'];
    }

    // USE FULL DOMAIN PATH TO AVOID ALIAS PROBLEMS

    $host_path  = (!isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != "on") ? "http://" : "https://";
    $host_path .= $_SERVER['HTTP_HOST'];

    // GET FULL PATHS ( EXTRA CODE FOR WINDOWS AND IIS - NO DOCUMENT_ROOT - BACKSLASHES - DOUBLESLASHES - ETC )

    if ($_SERVER['DOCUMENT_ROOT'])
    {
      $base_path = lgsl_realpath($_SERVER['DOCUMENT_ROOT']);
      $base_path = str_replace("\\", "/", $base_path);
      $base_path = str_replace("//", "/", $base_path);
    }
    else
    {
      $file_path = $_SERVER['SCRIPT_NAME'];
      $file_path = str_replace("\\", "/", $file_path);
      $file_path = str_replace("//", "/", $file_path);

      $base_path = $_SERVER['PATH_TRANSLATED'];
      $base_path = str_replace("\\", "/", $base_path);
      $base_path = str_replace("//", "/", $base_path);
      $base_path = substr($base_path, 0, -strlen($file_path));
    }

    $lgsl_path = dirname(lgsl_realpath(__FILE__));
    $lgsl_path = str_replace("\\", "/", $lgsl_path);

    // REMOVE ANY TRAILING SLASHES

    if (substr($base_path, -1) == "/") { $base_path = substr($base_path, 0, -1); }
    if (substr($lgsl_path, -1) == "/") { $lgsl_path = substr($lgsl_path, 0, -1); }

    // USE THE DIFFERENCE BETWEEN PATHS

    if (substr($lgsl_path, 0, strlen($base_path)) == $base_path)
    {
      $url_path = substr($lgsl_path, strlen($base_path));

      return $host_path.$url_path."/";
    }

    return "/#LGSL_PATH_PROBLEM#{$base_path}#{$lgsl_path}#/";
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  } // END OF DOUBLE LOAD PROTECTION

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  global $lgsl_file_path, $lgsl_url_path;

  $lgsl_file_path = lgsl_file_path();

  if ($_GET['lgsl_debug'])
  {
    echo "<hr /><pre>".print_r($_SERVER, TRUE)."</pre>
          <hr />#d0# ".__FILE__."
          <hr />#d1# ".@realpath(__FILE__)."
          <hr />#d2# ".dirname(__FILE__)."
          <hr />#d3# {$lgsl_file_path}
          <hr />#d4# {$_SERVER['DOCUMENT_ROOT']}
          <hr />#d5# ".@realpath($_SERVER['DOCUMENT_ROOT']);
  }

  require $lgsl_file_path."lgsl_config.php";
  require $lgsl_file_path."lgsl_protocol.php";

  $lgsl_url_path = lgsl_url_path();

  if ($_GET['lgsl_debug'])
  {
    echo "<hr />#d6# {$lgsl_url_path}
          <hr />#c0# {$lgsl_config['url_path']}
          <hr />#c1# {$lgsl_config['no_realpath']}
          <hr />#c2# {$lgsl_config['feed']['method']}
          <hr />#c3# {$lgsl_config['feed']['url']}
          <hr />#c4# {$lgsl_config['cache_time']}
          <hr />#c5# {$lgsl_config['live_time']}
          <hr />#c6# {$lgsl_config['timeout']}
          <hr />#c7# {$lgsl_config['cms']}
          <hr />";
  }

  if (!isset($lgsl_config['zone']['line_size']))
  {
    exit("LGSL PROBLEM: lgsl_config.php IS OLD, CORRUPT, OR JUST FAILED TO LOAD");
  }

//------------------------------------------------------------------------------------------------------------+

?>