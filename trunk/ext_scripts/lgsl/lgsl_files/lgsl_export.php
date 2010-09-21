<?php

 /*----------------------------------------------------------------------------------------------------------\
 |                                                                                                            |
 |                      [ LIVE GAME SERVER LIST ] [ � RICHARD PERRY FROM GREYCUBE.COM ]                       |
 |                                                                                                            |
 |    Released under the terms and conditions of the GNU General Public License Version 3 (http://gnu.org)    |
 |                                                                                                            |
 \-----------------------------------------------------------------------------------------------------------*/

//------------------------------------------------------------------------------------------------------------+

  require "lgsl_class.php";

  lgsl_database();

//------------------------------------------------------------------------------------------------------------+

  $xml         = empty($_GET['xml'])         ? FALSE : TRUE;
  $online      = empty($_GET['online'])      ? FALSE : TRUE;
  $nodisabled  = empty($_GET['nodisabled'])  ? FALSE : TRUE;
  $download    = empty($_GET['download'])    ? FALSE : TRUE;
  $sort        = empty($_GET['sort'])        ? FALSE : $_GET['sort'];
  $randomzones = empty($_GET['randomzones']) ? FALSE : intval($_GET['randomzones']);

//------------------------------------------------------------------------------------------------------------+

  $output       = "";
  $mysql_filter = "";
  $mysql_where  = array();

  if ($nodisabled)  { $mysql_where[] = "`disabled`=0"; } // ONLY LIST ENABLED
  if ($online)      { $mysql_where[] = "`status`=1"; }   // ONLY LIST ONLINE
  if ($mysql_where) { $mysql_filter  = "WHERE ".implode(" AND ", $mysql_where); }

  if     ($sort == "ip")   { $mysql_filter .= " ORDER BY CONCAT(`ip`, `c_port`) ASC"; }
  elseif ($sort == "type") { $mysql_filter .= " ORDER BY `type` ASC"; }
  else                     { $mysql_filter .= " ORDER BY `id` ASC"; }

//------------------------------------------------------------------------------------------------------------+

  $mysql_result = mysql_query("SELECT * FROM `{$lgsl_config['db']['prefix']}{$lgsl_config['db']['table']}` {$mysql_filter}");

  while($mysql_row = mysql_fetch_array($mysql_result, MYSQL_ASSOC))
  {
    if ($randomzones) { $mysql_row['zone'] = rand(1, $randomzones); } // FILL ZONES WITH RANDOM NUMBERS ( 1 TO $randomzones )

    if ($xml)
    {
      $output .= "
      <server>
        <type>{$mysql_row['type']}</type>
        <ip>{$mysql_row['ip']}</ip>
        <c_port>{$mysql_row['c_port']}</c_port>
        <q_port>{$mysql_row['q_port']}</q_port>
        <s_port>{$mysql_row['s_port']}</s_port>
        <zone>{$mysql_row['zone']}</zone>
        <disabled>{$mysql_row['disabled']}</disabled>
      </server>";
    }
    else
    {
      $output .= "{$mysql_row['type']} : {$mysql_row['ip']} : {$mysql_row['c_port']} : {$mysql_row['q_port']} : {$mysql_row['s_port']} : {$mysql_row['zone']} : {$mysql_row['disabled']} \r\n";
    }
  }

//------------------------------------------------------------------------------------------------------------+

  if ($download)
  {
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"servers.txt\"");
    echo $output;
    exit;
  }

  if ($xml)
  {
    header("content-type: text/xml");
    echo "<?xml version='1.0' encoding='UTF-8' ?>
    <servers>{$output}</servers>";
    exit;
  }

//------------------------------------------------------------------------------------------------------------+
?>


<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>

<html xmlns='http://www.w3.org/1999/xhtml'>
  <head>
    <title>Live Game Server List</title>
    <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
    <meta http-equiv='content-style-type' content='text/css' />
    <link rel='stylesheet' href='lgsl_style.css' type='text/css' />
  </head>

  <body>
    <pre><?php echo $output; ?></pre>
  </body>
</html>
