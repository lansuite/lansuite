<?php

$dsp->NewContent(t('Party-Karte'), t('Partys, die Lansuite verwenden'));

if (!$cfg['google_maps_api_key']) $func->information(t('Sie müssen sich zuerst unter http://www.google.com/apis/maps/signup.html einen Google-Maps API Key erzeugen und diesen auf der %1 eingeben', '<a href="index.php?mod=install&action=modules&step=10&module=install">'. t('AdminSeite in den Allgemeinen Einstellungen').'</a>'));
else {
  $where_pid = '';
  if ($party->party_id) $where_pid = "AND (p.party_id = {$party->party_id})";

  $res = $db->query("SELECT *, UNIX_TIMESTAMP(start) AS start, UNIX_TIMESTAMP(end) AS end FROM {$config['tables']['partylist']} AS p WHERE start > NOW()");

  $templ['addresses'] = '';
  while ($row = $db->fetch_array($res)) {
    $text = "<b>{$row['name']}</b><br />- {$row['motto']} -<br>". $func->unixstamp2date($row['start'], 'datetime') .' - '. $func->unixstamp2date($row['end'], 'datetime') ."<br>{$row['street']} {$row['hnr']}, {$row['plz']} {$row['city']}";
    $templ['guestmap']['adresses'] .= "showAddress('Germany', '". addslashes($row['city']) ."', '". addslashes($row['plz']) ."', '". addslashes($row['street']) ."', '". addslashes($row['hnr']) ."', '". addslashes($text) ."');\r\n";
  }
  $db->free_result($haus_data);

  $templ['guestmap']['apikey'] = $cfg['google_maps_api_key'];
  $dsp->AddSingleRow($dsp->FetchModTpl('guestlist', 'googlemaps'));
  $templ['index']['body']['js'] = 'onload="ShowMap();"';
}
$dsp->AddContent();
?>