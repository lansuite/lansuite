<?php

 /*----------------------------------------------------------------------------------------------------------\
 |                                                                                                            |
 |                      [ LIVE GAME SERVER LIST ] [ � RICHARD PERRY FROM GREYCUBE.COM ]                       |
 |                                                                                                            |
 |    Released under the terms and conditions of the GNU General Public License Version 3 (http://gnu.org)    |
 |                                                                                                            |
 |-------------------------------------------------------------------------------------------------------------
 |        [ EDITOR STYLE SETTINGS: LUCIDA CONSOLE, SIZE 10, TAB = 2 SPACES, BOLD GLOBALLY TURNED OFF ]        |
 \-----------------------------------------------------------------------------------------------------------*/

//------------------------------------------------------------------------------------------------------------+

  require "lgsl_class.php";

//------------------------------------------------------------------------------------------------------------+
// THIS ALLOWS YOU TO CONTROL THE FIELDS DISPLAYED AND THEIR ORDER

  $fields_show  = array("name", "score", "deaths", "team", "ping", "bot", "time"); // THESE FIELDS ARE ORDERED FIRST
  $fields_hide  = array("teamindex", "pid", "pbguid"); // THESE FIELDS ARE REMOVED
  $fields_other = TRUE; // FALSE WILL HIDE FIELDS NOT IN $fields_show

//------------------------------------------------------------------------------------------------------------+
// GET THE SERVER DETAILS AND PREPARE IT FOR DISPLAY

  $lookup = lgsl_lookup_id($_GET['s']);

  if (!$lookup)
  {
    $output .= "<div style='margin:auto; text-align:center'> {$lgsl_config['text']['mid']} </div>"; return;
  }

  $server = lgsl_query_cached($lookup['type'], $lookup['ip'], $lookup['c_port'], $lookup['q_port'], $lookup['s_port'], "sep");
  $fields = lgsl_sort_fields($server, $fields_show, $fields_hide, $fields_other);
  $server = lgsl_sort_players($server);
  $server = lgsl_sort_extras($server);
  $misc   = lgsl_server_misc($server);
  $server = lgsl_server_html($server);

//------------------------------------------------------------------------------------------------------------+

  $output .= "
  <div style='margin:auto; text-align:center'>";

  $output .="
  <div style='".lgsl_bg(TRUE)."; width:90%; margin:auto; text-align:center; height:6px; border:1px solid'><br /></div>
  <div style='height:10px'><br /></div>";

//------------------------------------------------------------------------------------------------------------+
// SHOW THE STANDARD INFO

  $output .= "
  <table cellpadding='2' cellspacing='2' style='margin:auto'>
    <tr>
      <td colspan='3' style='text-align:center'>
        <b> {$server['s']['name']} </b><br /><br />
      </td>
    </tr>
    <tr>
      <td colspan='2' style='text-align:center'>
        <table cellpadding='4' cellspacing='2' style='width:100%; margin:auto'>
          <tr><td style='".lgsl_bg(TRUE)."; text-align:center'><a href='{$misc['software_link']}'>{$lgsl_config['text']['slk']}</a></td></tr>
        </table>
      </td>
      <td rowspan='2' style='text-align:center' >
        <div style='background-image:url({$misc['image_map']}); background-repeat:no-repeat; background-position:center'>
          <img alt='' src='{$misc['image_map_password']}' style='border:none; width:{$zone_width}; background:url({$misc['icon_game']}); background-repeat:no-repeat; background-position:4px 4px' />
        </div>
      </td>
    </tr>
    <tr>
      <td style='text-align:center'>
        <table cellpadding='4' cellspacing='2' style='margin:auto'>
          <tr style='".lgsl_bg().";white-space:nowrap'><td> <b> {$lgsl_config['text']['sts']} </b></td><td style='white-space:nowrap'> {$misc['text_status']}                                   </td></tr>
          <tr style='".lgsl_bg().";white-space:nowrap'><td> <b> {$lgsl_config['text']['adr']} </b></td><td style='white-space:nowrap'> {$server['b']['ip']}                                     </td></tr>
          <tr style='".lgsl_bg().";white-space:nowrap'><td> <b> {$lgsl_config['text']['cpt']} </b></td><td style='white-space:nowrap'> {$server['b']['c_port']}                                 </td></tr>
          <tr style='".lgsl_bg().";white-space:nowrap'><td> <b> {$lgsl_config['text']['qpt']} </b></td><td style='white-space:nowrap'> {$server['b']['q_port']}                                 </td></tr>
        </table>
      </td>
      <td style='text-align:center'>
        <table cellpadding='4' cellspacing='2' style='margin:auto'>
          <tr style='".lgsl_bg().";white-space:nowrap'><td> <b> {$lgsl_config['text']['typ']} </b></td><td style='white-space:nowrap'> {$server['b']['type']}                                   </td></tr>
          <tr style='".lgsl_bg().";white-space:nowrap'><td> <b> {$lgsl_config['text']['gme']} </b></td><td style='white-space:nowrap'> {$server['s']['game']}                                   </td></tr>
          <tr style='".lgsl_bg().";white-space:nowrap'><td> <b> {$lgsl_config['text']['map']} </b></td><td style='white-space:nowrap'> {$server['s']['map']}                                    </td></tr>
          <tr style='".lgsl_bg().";white-space:nowrap'><td> <b> {$lgsl_config['text']['plr']} </b></td><td style='white-space:nowrap'> {$server['s']['players']} / {$server['s']['playersmax']} </td></tr>
        </table>
      </td>
    </tr>
  </table>";

//------------------------------------------------------------------------------------------------------------+

  $output .= "
  <div style='height:10px'><br /></div>
  <div style='".lgsl_bg(TRUE)."; width:90%; margin:auto; text-align:center; height:6px; border:1px solid'><br /></div>
  <div style='height:10px'><br /></div>";

//------------------------------------------------------------------------------------------------------------+
// SHOW THE PLAYERS

  $output .= "
  <div style='margin:auto; overflow:auto; text-align:center; padding:10px'>";

  if (!$server['p'] || !is_array($server['p']))
  {
    $output .= "
    <table cellpadding='4' cellspacing='2' style='margin:auto'>
      <tr style='".lgsl_bg(FALSE)."'>
        <td> {$lgsl_config['text']['npi']} </td>
      </tr>
    </table>";
  }
  else
  {
    $output .= "
    <table cellpadding='4' cellspacing='2' style='margin:auto'>
      <tr style='".lgsl_bg(FALSE)."'>";

      foreach ($fields as $field)
      {
        $field = ucfirst($field);
        $output .= "
        <td> <b>{$field}</b> </td>";
      }

      $output .= "
      </tr>";

      foreach ($server['p'] as $player_key => $player)
      {
        $output .= "
        <tr style='".lgsl_bg()."'>";

        foreach ($fields as $field)
        {
          $output .= "<td> {$player[$field]} </td>";
        }

        $output .= "
        </tr>";
      }

    $output .= "
    </table>";
  }

  $output .= "
  </div>";

//------------------------------------------------------------------------------------------------------------+

  $output .= "
  <div style='height:10px'><br /></div>
  <div style='".lgsl_bg(TRUE)."; width:90%; margin:auto; text-align:center; height:6px; border:1px solid'><br /></div>
  <div style='height:20px'><br /></div>";

//------------------------------------------------------------------------------------------------------------+
// SHOW THE SETTINGS

  if (!$server['e'] || !is_array($server['e']))
  {
    $output .= "
    <table cellpadding='4' cellspacing='2' style='margin:auto'>
      <tr style='".lgsl_bg(FALSE)."'>
        <td> {$lgsl_config['text']['nei']} </td>
      </tr>
    </table>";
  }
  else
  {
    $output .= "
    <table cellpadding='4' cellspacing='2' style='margin:auto'>
      <tr style='".lgsl_bg(FALSE)."'>
        <td> <b>{$lgsl_config['text']['ehs']}</b> </td>
        <td> <b>{$lgsl_config['text']['ehv']}</b> </td>
      </tr>";

    foreach ($server['e'] as $field => $value)
    {
      $color = lgsl_bg();

      $output .= "
      <tr>
        <td style='{$color}'> {$field} </td>
        <td style='{$color}'> {$value} </td>
      </tr>";
    }

    $output .= "
    </table>";
  }

//------------------------------------------------------------------------------------------------------------+

  $output .= "
  <div style='height:10px'><br /></div>
  <div style='".lgsl_bg(TRUE)."; width:90%; margin:auto; text-align:center; height:6px; border:1px solid'><br /></div>
  <div style='height:20px'><br /></div>";

  $output .= "
  </div>";

//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//------  PLEASE MAKE A DONATION OR SIGN THE GUESTBOOK AT GREYCUBE.COM IF YOU REMOVE THIS CREDIT ---------------------------------------------------------------------------------------------------+
  $output .= "<div style='text-align:center; font-family:tahoma; font-size:9px'><br /><br /><br /><a href='http://www.greycube.com' style='text-decoration:none'>".lgsl_version()."</a><br /></div>";
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+

?>