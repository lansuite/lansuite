<?php

$dsp->NewContent($lang["guestlist"]["map_caption"], $lang["guestlist"]["map_subcaption"]);

// Use Googlemaps
if ($cfg['guestlist_guestmap'] == 2) {
  if (!$cfg['google_maps_api_key']) $func->information('Sie müssen sich zuerst unter http://www.google.com/apis/maps/signup.html einen Google-Maps API Key erzeugen und diesen in der Modulkonfiguration eingeben');
  else {
    $where_pid = '';
    if ($party->party_id) $where_pid = "AND (p.party_id = {$party->party_id})";

    $res = $db->query("SELECT u.*, s.avatar_path FROM {$config["tables"]["user"]} AS u
  		LEFT JOIN {$config["tables"]["party_user"]} AS p ON u.userid = p.user_id
  		LEFT JOIN {$config["tables"]["usersettings"]} AS s ON u.userid = s.userid
  		WHERE (u.plz > 0) AND u.type > 0 $where_pid
      ");

    $templ['addresses'] = '';
    while ($row = $db->fetch_array($res)) {
      $text = "<b>{$row['username']}</b>";
      if ($cfg['guestlist_shownames']) $text .= "<br>{$row['firstname']} {$row['name']}";
      $text .= "<br>{$row['plz']} {$row['city']}";

      if ($row['avatar_path']) $text .= '<br>'. sprintf('<img src=\\"%s\\" alt=\\"%s\\" border=\\"0\\">', $row["avatar_path"], '');

      if ($row['street']) $templ['guestmap']['adresses'] .= "showAddress('{$row['street']}, {$row['plz']}, Germany', \"$text\");\r\n";
      elseif ($row['plz']) $templ['guestmap']['adresses'] .= "showAddress('{$row['plz']}, Germany', \"$text\");\r\n";
    }
    $db->free_result($haus_data);

    $templ['guestmap']['apikey'] = $cfg['google_maps_api_key'];
    $dsp->AddSingleRow($dsp->FetchModTpl('guestlist', 'googlemaps'));
  }


// Use Geofreedb
} else {
  $res = $db->query("SELECT plz FROM {$config["tables"]["user"]} LEFT JOIN {$config["tables"]["party_user"]} ON userid = user_id WHERE (plz > 0) AND (party_id = {$party->party_id})");

  if ($db->num_rows($res) == 0) $dsp->AddSingleRow($lang["guestlist"]["map_err_noplz"]);
  else {

  	$map_out = '<div id="tooltip" class="tooltip" style="position: absolute; width: auto; height: auto; z-index: 100; visibility: hidden; left: 0; top: 0;"></div><script src="modules/guestlist/templates/map.js" type="text/javascript"></script><map name="deutschland">';

  	$x_start = 5.43;
  	$y_start = 46.95;
  	$x_end = 15.942;
  	$y_end = 55.155;

  	$size = GetImageSize ("modules/guestlist/map.png");
  	$img_width = $size[0];
  	$img_height = $size[1];

  	$xf = $img_width / (abs($x_start - $x_end));
  	$yf = $img_height / (abs($y_start - $y_end));

  	$res = $db->query("SELECT user.userid, user.username, user.city, user.plz, COUNT(*) AS anz, locations.laenge, locations.breite
  		FROM {$config["tables"]["user"]} AS user
  		INNER JOIN {$config["tables"]["locations"]} AS locations ON user.plz = locations.plz
  		INNER JOIN {$config["tables"]["party_user"]} AS party ON user.userid = party.user_id
  		WHERE (user.plz > 0) AND (party.party_id = {$party->party_id}) AND user.type > 0
  		GROUP BY user.plz
  		");
  	$z = 0;
  	while ($user = $db->fetch_array($res)) {
  	  $z++;

  		$kx = (int) ($xf * ($user['laenge'] - $x_start));
  		$ky = (int) ($img_height - $yf * ($user['breite'] - $y_start));

  		$size = floor(1 + 0.25 * $user['anz']);
  		if ($size > 5) $size = 5;

  		// Get list of all users with current plz
      $res2 = $db->query("SELECT u.username, u.firstname, u.name
  		FROM {$config["tables"]["user"]} AS u
  		INNER JOIN {$config["tables"]["party_user"]} AS p ON u.userid = p.user_id
  		WHERE (u.plz = {$user["plz"]}) AND (p.party_id = {$party->party_id}) AND u.type > 0
  		");
  		$users2jsarray = '';
  		while ($current_user = $db->fetch_array($res2)) {
  		  if ($auth['type'] < 2 and ($cfg['sys_internet'])) {
  		    $current_user['firstname'] = '---';
  		    $current_user['name'] = '---';
        }
        $users2jsarray .= "'{$current_user['username']}', '{$current_user['firstname']}', '{$current_user['name']}', ";
  		}
  		$db->free_result($res2);
      $users2jsarray = substr($users2jsarray, 0, strlen($users2jsarray) - 2);

  		$mouse_over_text = "javascript:user_data = new Array('{$user['plz']}', '{$user['city']}', '{$user['anz']}', $users2jsarray); TX_showToolTip(event, user_data);";

  		$map_out .= "<area name=\"point$z\" shape=\"rect\" coords=\"". ($kx-$size) .", ". ($ky-$size) .", ". ($kx+$size) .", ". ($ky+$size) ."\" onMouseOver=\"$mouse_over_text\" onMouseOut=\"javascript:TX_hideToolTip();\" />";
  		# href=\"index.php?mod=usrmgr&action=details&userid={$user["userid"]}\"
  	}
  	$db->free_result($res);
  	$map_out .= "</map>";

  	$dsp->AddSingleRow($map_out ."<img src=\"index.php?mod=guestlist&action=usermap_img&design=base\" usemap=\"#deutschland\" border=\"0\">");
  }

  $db->free_result($res);
}
$dsp->AddContent();
?>