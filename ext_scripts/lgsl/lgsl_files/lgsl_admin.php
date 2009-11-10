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

  if (!defined("LGSL_ADMIN")) { exit("DIRECT ACCESS NOT ALLOWED"); }

  require "lgsl_class.php"; global $lgsl_config;

  lgsl_database();
  $lgsl_type_list = lgsl_type_list(); asort($lgsl_type_list);
  $lgsl_protocol_list = lgsl_protocol_list();
  $last_type = "source";

//------------------------------------------------------------------------------------------------------------+

  if (!function_exists("fsockopen") && !$lgsl_config['feed']['method'])
  {
    if ((function_exists("curl_init") && function_exists("curl_setopt") && function_exists("curl_exec")))
    {
      $output = "<div style='text-align:center'><br /><br /><b>FSOCKOPEN IS DISABLED - YOU MUST ENABLE THE FEED OPTION</b><br /><br /></div>".lgsl_help_info(); return;
    }
    else
    {
      $output = "<div style='text-align:center'><br /><br /><b>FSOCKOPEN AND CURL ARE DISABLED - LGSL WILL NOT WORK ON THIS HOST</b><br /><br /></div>".lgsl_help_info(); return;
    }
  }

//------------------------------------------------------------------------------------------------------------+

  if ($_POST && get_magic_quotes_gpc()) { $_POST = lgsl_stripslashes_deep($_POST); }

  if (function_exists("mysql_set_charset"))
  {
    @mysql_set_charset("utf8");
  }
  else
  {
    @mysql_query("SET NAMES 'utf8'");
  }

//------------------------------------------------------------------------------------------------------------+
// THIS WILL UPGRADE THE DATABASE TABLE EXCEPT IF EMPTY

  $mysql_result = mysql_query("SELECT * FROM `{$lgsl_config['db']['prefix']}{$lgsl_config['db']['table']}` LIMIT 1");
  $mysql_row    = mysql_fetch_array($mysql_result, MYSQL_ASSOC);

  if ($mysql_row && !isset($mysql_row['comment']))
  {
    $mysql_query  = "ALTER TABLE `{$lgsl_config['db']['prefix']}{$lgsl_config['db']['table']}` ADD `comment` VARCHAR(255) NOT NULL DEFAULT ''";
    $mysql_result = mysql_query($mysql_query) or die(mysql_error());
  }

//------------------------------------------------------------------------------------------------------------+

  if ($_POST['lgsl_update'])
  {
    $db = array();
    $mysql_result = mysql_query("SELECT * FROM `{$lgsl_config['db']['prefix']}{$lgsl_config['db']['table']}`");
    while($mysql_row = mysql_fetch_array($mysql_result, MYSQL_ASSOC))
    {
      $db[$mysql_row['type'].":".$mysql_row['ip'].":".$mysql_row['q_port']] = $mysql_row;
    }
    $mysql_result = mysql_query("TRUNCATE `{$lgsl_config['db']['prefix']}{$lgsl_config['db']['table']}`") or die(mysql_error());

    if ($_POST['lgsl_advanced'])
    {
      $form_lines = explode("\r\n", $_POST['form_list']);

      foreach ($form_lines as $form_key => $form_line)
      {
        list($_POST['form_type']    [$form_key],
             $_POST['form_ip']      [$form_key],
             $_POST['form_c_port']  [$form_key],
             $_POST['form_q_port']  [$form_key],
             $_POST['form_s_port']  [$form_key],
             $_POST['form_zone']    [$form_key],
             $_POST['form_disabled'][$form_key],
             $_POST['form_comment'] [$form_key]) = explode(":", $form_line);
      }
    }

    foreach ($_POST['form_type'] as $form_key => $not_used)
    {
      // COMMENTS ARE LEFT IN THEIR NATIVE ENCODING AND JUST HTML SYMBOLS ARE CONVERTED
      $_POST['form_comment'][$form_key] = htmlspecialchars($_POST['form_comment'][$form_key], ENT_QUOTES, "UTF-8", FALSE);

      $type       = mysql_real_escape_string(strtolower(trim($_POST['form_type']    [$form_key])));
      $ip         = mysql_real_escape_string(           trim($_POST['form_ip']      [$form_key]));
      $c_port     = mysql_real_escape_string(    intval(trim($_POST['form_c_port']  [$form_key])));
      $q_port     = mysql_real_escape_string(    intval(trim($_POST['form_q_port']  [$form_key])));
      $s_port     = mysql_real_escape_string(    intval(trim($_POST['form_s_port']  [$form_key])));
      $zone       = mysql_real_escape_string(    intval(trim($_POST['form_zone']    [$form_key])));
      $disabled   = mysql_real_escape_string(    intval(trim($_POST['form_disabled'][$form_key])));
      $comment    = mysql_real_escape_string(           trim($_POST['form_comment'] [$form_key]));

      // THE VALUES ARE INDEXED BY TYPE:IP:Q_PORT SO CHANGES TO THESE WIPES THE CACHE
      $status     = mysql_real_escape_string(intval($db[$type.":".$ip.":".$q_port]['status']));
      $cache      = mysql_real_escape_string(       $db[$type.":".$ip.":".$q_port]['cache']);
      $cache_time = mysql_real_escape_string(       $db[$type.":".$ip.":".$q_port]['cache_time']);

      // THIS PREVENTS PORTS OR WHITESPACE BEING PUT IN THE IP WHILE ALLOWING IPv6
      if     (preg_match("/(\[[0-9a-z\:]+\])/iU", $ip, $match)) { $ip = $match[1]; }
      elseif (preg_match("/([0-9a-z\.\-]+)/i", $ip, $match))    { $ip = $match[1]; }

      list($c_port, $q_port, $s_port) = lgsl_port_conversion($type, $c_port, $q_port, $s_port);

      if     (!$ip)                           { continue; }
      elseif ($c_port < 1 || $c_port > 99999) { $disabled = 1; $c_port = 0; }
      elseif ($q_port < 1 || $q_port > 99999) { $disabled = 1; $q_port = 0; }
      elseif (!$lgsl_protocol_list[$type])    { $disabled = 1; }

      $mysql_query  = "INSERT INTO `{$lgsl_config['db']['prefix']}{$lgsl_config['db']['table']}` (`type`,`ip`,`c_port`,`q_port`,`s_port`,`zone`,`disabled`,`comment`,`status`,`cache`,`cache_time`) VALUES ('{$type}','{$ip}','{$c_port}','{$q_port}','{$s_port}','{$zone}','{$disabled}','{$comment}','{$status}','{$cache}','{$cache_time}')";
      $mysql_result = mysql_query($mysql_query) or die(mysql_error());
    }
  }

//------------------------------------------------------------------------------------------------------------+

  if ($_POST['lgsl_empty_cache'])
  {
    $mysql_result = mysql_query("UPDATE `{$lgsl_config['db']['prefix']}{$lgsl_config['db']['table']}` SET `status`='0',`cache`='',`cache_time`=''");
  }

//------------------------------------------------------------------------------------------------------------+

  if ($_POST['lgsl_map_image_paths'])
  {
    $server_list = lgsl_query_cached_all("s");

    foreach ($server_list as $server)
    {
      if (!$server['b']['status']) { continue; }

      $image_map = lgsl_image_map($server['b']['status'], $server['b']['type'], $server['s']['game'], $server['s']['map'], FALSE);

      $output .= "
      <div>
        <a href='{$image_map}'> {$image_map} </a>
      </div>";
    }

    $output .= "
    <form method='post' action=''>
      <div>
        <br />
        <br />
        ".($_POST['lgsl_advanced'] ? "<input type='hidden' name='lgsl_advanced' value='1' />" : "")."
        <input type='submit' name='lgsl_return' value='RETURN TO ADMIN' />
        <br />
        <br />
      </div>
    </form>";

    return;
  }

//------------------------------------------------------------------------------------------------------------+

  if ((($_POST['lgsl_advanced'] || $lgsl_config['management']) && !$_POST['lgsl_normal']) || $_POST['lgsl_switch_advanced'])
  {
    $output .= "
    <form method='post' action=''>
      <div style='text-align:center'>
        <b>TYPE : IP : C PORT : Q PORT : S PORT : ZONE : DISABLED : COMMENT</b>
        <br />
        <br />
      </div>
      <div style='text-align:center'>
        <textarea name='form_list' cols='90' rows='30' wrap='off' spellcheck='false' style='width:95%; height:500px'>";

//---------------------------------------------------------+
        $mysql_result = mysql_query("SELECT * FROM `{$lgsl_config['db']['prefix']}{$lgsl_config['db']['table']}` ORDER BY `id` ASC");

        while($mysql_row = mysql_fetch_array($mysql_result, MYSQL_ASSOC))
        {
          $output .=
          lgsl_string_html(str_pad($mysql_row['type'],     15, " ")) .":".
          lgsl_string_html(str_pad($mysql_row['ip'],       30, " ")) .":".
          lgsl_string_html(str_pad($mysql_row['c_port'],   6,  " ")) .":".
          lgsl_string_html(str_pad($mysql_row['q_port'],   6,  " ")) .":".
          lgsl_string_html(str_pad($mysql_row['s_port'],   7,  " ")) .":".
          lgsl_string_html(str_pad($mysql_row['zone'],     3,  " ")) .":".
          lgsl_string_html(str_pad($mysql_row['disabled'], 3,  " ")) .":".
                           str_pad($mysql_row['comment'],  0,  " ")  ."\r\n";
        }
//---------------------------------------------------------+
        $output .= "
        </textarea>
      </div>
      <div style='text-align:center'>
        <input type='hidden' name='lgsl_advanced' value='1' />
        <table cellspacing='20' cellpadding='0' style='text-align:center;margin:auto'>
          <tr>
            <td><input type='submit' name='lgsl_update'          value='Update' />           </td>
            <td><input type='submit' name='lgsl_empty_cache'     value='Empty Cache' />      </td>
            <td><input type='submit' name='lgsl_map_image_paths' value='Map Image Paths' />  </td>
            <td><input type='submit' name='lgsl_normal'          value='Normal Management' /></td>
          </tr>
        </table>
      </div>
    </form>";

    $output .= lgsl_help_info();

    return $output;
  }

//------------------------------------------------------------------------------------------------------------+

  $output .= "
  <form method='post' action=''>
    <div style='text-align:center; overflow:auto'>
      <table cellspacing='5' cellpadding='0' style='margin:auto'>
        <tr>
          <td style='text-align:center; white-space:nowrap'>[ ID ]             </td>
          <td style='text-align:center; white-space:nowrap'>[ Game Type ]      </td>
          <td style='text-align:center; white-space:nowrap'>[ IP ]             </td>
          <td style='text-align:center; white-space:nowrap'>[ Connection Port ]</td>
          <td style='text-align:center; white-space:nowrap'>[ Query Port ]     </td>
          <td style='text-align:center; white-space:nowrap'>[ Software Port ]  </td>
          <td style='text-align:center; white-space:nowrap'>[ Zone ]           </td>
          <td style='text-align:center; white-space:nowrap'>[ Disabled ]       </td>
          <td style='text-align:center; white-space:nowrap'>[ Comment ]        </td>
        </tr>";

//---------------------------------------------------------+

      $mysql_result = mysql_query("SELECT * FROM `{$lgsl_config['db']['prefix']}{$lgsl_config['db']['table']}` ORDER BY `id` ASC");

      while($mysql_row = mysql_fetch_array($mysql_result, MYSQL_ASSOC))
      {
        $id = $mysql_row['id']; // ID USED AS [] ONLY RETURNS TICKED CHECKBOXES

        $output .= "
        <tr>
          <td>
            <a href='".lgsl_link($id)."' style='text-decoration:none'>{$id}</a>
          </td>
          <td>
            <select name='form_type[{$id}]'>";
//---------------------------------------------------------+
            foreach ($lgsl_type_list as $type => $description)
            {
              $output .= "
              <option ".($type == $mysql_row['type'] ? "selected='selected'" : "")." value='{$type}'>{$description}</option>";
            }
//---------------------------------------------------------+
            if (!$lgsl_type_list[$mysql_row['type']])
            {
              $output .= "
              <option selected='selected' value='".lgsl_string_html($mysql_row['type'])."'>".lgsl_string_html($mysql_row['type'])."</option>";
            }
//---------------------------------------------------------+
            $output .= "
            </select>
          </td>
          <td style='text-align:center'><input type='text' name='form_ip[{$id}]'     value='".lgsl_string_html($mysql_row['ip'])."'     size='15' maxlength='255' /></td>
          <td style='text-align:center'><input type='text' name='form_c_port[{$id}]' value='".lgsl_string_html($mysql_row['c_port'])."' size='5'  maxlength='5'   /></td>
          <td style='text-align:center'><input type='text' name='form_q_port[{$id}]' value='".lgsl_string_html($mysql_row['q_port'])."' size='5'  maxlength='5'   /></td>
          <td style='text-align:center'><input type='text' name='form_s_port[{$id}]' value='".lgsl_string_html($mysql_row['s_port'])."' size='5'  maxlength='5'   /></td>
          <td>
            <select name='form_zone[$id]'>";
//---------------------------------------------------------+
            for ($i=0; $i<=8; $i++)
            {
              $output .= "
              <option ".($mysql_row['zone'] == $i ? "selected='selected'" : "")." value='{$i}'>{$i}</option>";
            }
//---------------------------------------------------------+
            if ($mysql_row['zone'] > 8)
            {
              $output .= "
              <option selected='selected' value='{$mysql_row['zone']}'>{$mysql_row['zone']}</option>";
            }
//---------------------------------------------------------+
            $output .= "
            </select>
          </td>
          <td style='text-align:center'><input type='checkbox' name='form_disabled[{$id}]' value='1' ".($mysql_row['disabled'] ? "checked='checked'" : "")." /></td>
          <td style='text-align:center'><input type='text'     name='form_comment[{$id}]'  value='{$mysql_row['comment']}' size='20' maxlength='255' /></td>
        </tr>";

        $last_type = $mysql_row['type']; // SET LAST TYPE ( $mysql_row EXISTS ONLY WITHIN THE LOOP )
      }
//---------------------------------------------------------+
        $id ++; // NEW SERVER ID CONTINUES ON FROM LAST

        $output .= "
        <tr>
          <td>NEW</td>
          <td>
            <select name='form_type[{$id}]'>";
//---------------------------------------------------------+
            foreach ($lgsl_type_list as $type => $description)
            {
              $output .= "
              <option ".($type == $last_type ? "selected='selected'" : "")." value='{$type}'>{$description}</option>";
            }
//---------------------------------------------------------+
            $output .= "
            </select>
          </td>
          <td style='text-align:center'><input type='text' name='form_ip[{$id}]'     value=''  size='15' maxlength='255' /></td>
          <td style='text-align:center'><input type='text' name='form_c_port[{$id}]' value=''  size='5'  maxlength='5'   /></td>
          <td style='text-align:center'><input type='text' name='form_q_port[{$id}]' value=''  size='5'  maxlength='5'   /></td>
          <td style='text-align:center'><input type='text' name='form_s_port[{$id}]' value='0' size='5'  maxlength='5'   /></td>
          <td>
            <select name='form_zone[{$id}]'>";
//---------------------------------------------------------+
            for ($i=0; $i<=8; $i++)
            {
              $output .= "
              <option ".($mysql_row['zone'] == $i ? "selected='selected'" : "")." value='{$i}'>{$i}</option>";
            }
//---------------------------------------------------------+
            $output .= "
            </select>
          </td>
          <td style='text-align:center'><input type='checkbox' name='form_disabled[{$id}]' value='' /></td>
          <td style='text-align:center'><input type='text'     name='form_comment[{$id}]'  value='' size='20' maxlength='255' /></td>
        </tr>
      </table>

      <input type='hidden' name='lgsl_normal' value='1' />
      <table cellspacing='20' cellpadding='0' style='text-align:center;margin:auto'>
        <tr>
          <td><input type='submit' name='lgsl_update'          value='Update' />             </td>
          <td><input type='submit' name='lgsl_empty_cache'     value='Empty Cache' />        </td>
          <td><input type='submit' name='lgsl_map_image_paths' value='Map Image Paths' />    </td>
          <td><input type='submit' name='lgsl_switch_advanced' value='Advanced Management' /></td>
        </tr>
      </table>
    </div>
  </form>";

  $output .= lgsl_help_info();

//------------------------------------------------------------------------------------------------------------+

  function lgsl_help_info()
  {
    return "
    <div style='text-align:center'>
      <br /><br />
      <a href='http://www.greycube.com/help/readme/lgsl/'>[ LGSL ONLINE README ]</a>  <br /><br />
      - To remove a server, delete the IP, then click update.                         <br /><br />
      - If you leave the query port blank then LGSL will try to fill it in for you.   <br /><br />
      - The software port is only needed for a few games so it being set 0 is normal. <br /><br />
      - Edit the lgsl_config.php to set the background colors and other options.      <br /><br />
      <table cellspacing='10' cellpadding='0' style='border:1px solid; margin:auto; text-align:left'>
        <tr>
          <td> <a href='http://php.net/fsockopen'>FSOCKOPEN</a>           </td>
          <td> Enabled: ".(function_exists("fsockopen") ? "YES" : "NO")." </td>
          <td> ( Required for direct querying of servers )                </td>
        </tr>
        <tr>
          <td> <a href='http://php.net/curl'>CURL</a>                                                                                         </td>
          <td> Enabled: ".((function_exists("curl_init") && function_exists("curl_setopt") && function_exists("curl_exec")) ? "YES" : "NO")." </td>
          <td> ( Used for the feed when fsockopen is disabled )                                                                               </td>
        </tr>
        <tr>
          <td> <a href='http://php.net/mbstring'>MBSTRING</a>                       </td>
          <td> Enabled: ".(function_exists("mb_convert_encoding") ? "YES" : "NO")." </td>
          <td> ( Used to show UTF-8 server and player names correctly )             </td>
        </tr>
        <tr>
          <td> <a href='http://php.net/bzip2'>BZIP2</a>                      </td>
          <td> Enabled: ".(function_exists("bzdecompress") ? "YES" : "NO")." </td>
          <td> ( Used to show Source server settings over a certain size )   </td>
        </tr>
        <tr>
          <td> <a href='http://php.net/zlib'>ZLIB</a>                        </td>
          <td> Enabled: ".(function_exists("gzuncompress") ? "YES" : "NO")." </td>
          <td> ( Required for America's Army 3 )                             </td>
        </tr>
      </table>
      <br /><br />
      <br /><br />
    </div>";
  }

//------------------------------------------------------------------------------------------------------------+

  function lgsl_stripslashes_deep($value)
  {
    $value = is_array($value) ? array_map('lgsl_stripslashes_deep', $value) : stripslashes($value);
    return $value;
  }

//------------------------------------------------------------------------------------------------------------+

?>