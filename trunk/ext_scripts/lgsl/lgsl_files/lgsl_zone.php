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

  if (!$lgsl_zone_number) { exit("DIRECT ACCESS NOT ALLOWED"); }

  require "lgsl_class.php"; global $lgsl_config;

  $zone_width  = $lgsl_config['zone']['width']."px";
  $zone_grid   = $lgsl_config['grid'][$lgsl_zone_number];
  $zone_count  = 0;

//------------------------------------------------------------------------------------------------------------+

  $server_list = lgsl_query_cached_zone($lgsl_config['players'][$lgsl_zone_number] ? "sp" : "s", $lgsl_zone_number);
  $server_list = lgsl_sort_servers($server_list);

//------------------------------------------------------------------------------------------------------------+

  $output .= "
  <table cellpadding='0' cellspacing='0' style='width:100%; margin:auto; text-align:center'>
    <tr>";

    foreach ($server_list as $key => $server)
    {
      $server = lgsl_sort_players($server);
      $misc   = lgsl_server_misc($server);
      $server = lgsl_server_html($server);

      if ($zone_count && $zone_grid && !($zone_count % $zone_grid))
      {
        $output .= "
        </tr>
        <tr>";
      }

      $output .= "
      <td style='vertical-align:top; padding-top:5px; padding-bottom:5px; text-align:center'>";

      $zone_count ++;

//------------------------------------------------------------------------------------------------------------+

        $output .= "
        <table cellpadding='0' cellspacing='2' style='width:{$zone_width}; margin:auto; text-align:center'>

          <tr>
            <td style='text-align:center' title='{$lgsl_config['text']['slk']}'>
              <div style='width:{$zone_width}; white-space:nowrap; overflow:hidden; text-align:center'>
                <a href='{$misc['software_link']}' style='text-decoration:none'>
                  {$server['b']['ip']}:{$server['b']['c_port']}
                </a>
              </div>
            </td>
          </tr>

          <tr>
            <td style='text-align:center' title='{$server['s']['name']}'>
              <div style='width:{$zone_width}; white-space:nowrap; overflow:hidden; text-align:center'>
                {$misc['name_filtered']}
              </div>
            </td>
          </tr>

          <tr>
            <td style='background-image:url({$misc['image_map']}); background-repeat:no-repeat; background-position:center'>
              <a href='".lgsl_link($server['o']['id'])."'>
                <img alt='' src='{$misc['image_map_password']}' style='border:none; width:{$zone_width}; background:url({$misc['icon_game']}); background-repeat:no-repeat; background-position:4px 4px' title='{$misc['text_type_game']} \r\n {$lgsl_config['text']['vsd']}' />
              </a>
            </td>
          </tr>

          <tr>
            <td style='text-align:center' title='{$server['s']['map']}'>
              <div style='width:{$zone_width}; white-space:nowrap; overflow:hidden; text-align:center'>
                {$server['s']['map']}
              </div>
            </td>
          </tr>";

//------------------------------------------------------------------------------------------------------------+

        if ($server['p'] && $lgsl_config['players'][$lgsl_zone_number])
        {
          $zone_height = $lgsl_config['zone']['line_size'] * (count($server['p']) + 2);
          $zone_height = $zone_height > $lgsl_config['zone']['height'] ? $lgsl_config['zone']['height']."px" : $zone_height."px";

          $output .= "
          <tr>
            <td style='border:1px solid'>
              <div style='width:{$zone_width}; height:{$zone_height}; overflow:auto; text-align:left'>

                <span style='padding:1px; float:left'> {$lgsl_config['text']['zpl']} </span>
                <span style='padding:1px; float:right'> {$server['s']['players']} / {$server['s']['playersmax']} </span>
                <br />
                <br />";

                foreach ($server['p'] as $player)
                {
                  $output .= "
                  <div style='padding:1px; white-space:nowrap; overflow:hidden; text-align:left' title='{$player['name']}'>{$player['name']}</div>";
                }

                $output .= "
              </div>
            </td>
          </tr>";
        }
        else
        {
          $output .= "
          <tr>
            <td style='border:1px solid'>
              <span style='padding:1px; float:left'> {$lgsl_config['text']['zpl']} </span>
              <span style='padding:1px; float:right'> {$server['s']['players']} / {$server['s']['playersmax']} </span>
            </td>
          </tr>";
        }

//------------------------------------------------------------------------------------------------------------+

        $output .= "
        </table>
      </td>";

//------------------------------------------------------------------------------------------------------------+

    }

    if (!$server_list)
    {
      $output .= "
      <td style='text-align:center'>
        NO SERVERS IN ZONE {$lgsl_zone_number}
      </td>";
    }

    $output .= "
    </tr>
  </table>";

//------------------------------------------------------------------------------------------------------------+

?>