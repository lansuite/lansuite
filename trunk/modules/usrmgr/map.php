<?php

$dsp->NewContent(t('Benutzerkarte'), t('Auf dieser Karte finden Sie alle Benutzer, die eine PLZ eingegeben haben'));

if (!$cfg['google_maps_api_key']) $func->information('Sie müssen sich zuerst unter http://www.google.com/apis/maps/signup.html einen Google-Maps API Key erzeugen und diesen in der Modulkonfiguration eingeben');
else {
  $res = $db->query("SELECT u.*, s.avatar_path FROM {$config["tables"]["user"]} AS u
		LEFT JOIN {$config["tables"]["usersettings"]} AS s ON u.userid = s.userid
		WHERE u.plz > 0 AND u.type > 0
    ");
    # AND s.show_me_in_map = 1

  $templ['addresses'] = '';
  while ($row = $db->fetch_array($res)) {
    $text = "<b>{$row['username']}</b>";
    $text .= "<br>{$row['firstname']} {$row['name']}";
    $text .= "<br>{$row['plz']} {$row['city']}";

    if ($row['avatar_path']) $text .= '<br>'. sprintf('<img src=\\"%s\\" alt=\\"%s\\" border=\\"0\\">', $row["avatar_path"], '');

    $templ['guestmap']['adresses'] .= "showAddress('Germany', '{$row['city']}', '{$row['plz']}', '{$row['street']}', '{$row['hnr']}', '$text');\r\n";
#      if ($row['street']) $templ['guestmap']['adresses'] .= "showAddress('{$row['street']}, {$row['plz']}, Germany', \"$text\");\r\n";
#      elseif ($row['plz']) $templ['guestmap']['adresses'] .= "showAddress('{$row['plz']}, Germany', \"$text\");\r\n";
  }
  $db->free_result($haus_data);

  $templ['guestmap']['apikey'] = $cfg['google_maps_api_key'];
  $dsp->AddSingleRow($dsp->FetchModTpl('guestlist', 'googlemaps'));
}
$dsp->AddContent();
?>