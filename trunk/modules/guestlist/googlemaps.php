<?php

$dsp->NewContent('Karte');
$dsp->SetForm("index.php?mod=home&action=karte");
$dsp->AddTextFieldRow('plz', 'PLZ', $_POST['plz'], '');
$dsp->AddFormSubmitRow("search");

$where = '';
if ($_POST['plz']) $where = 'WHERE haus.PLZ = '. (int)$_POST['plz'];

$haus_data = $db->query("SELECT u.* FROM {$config["tables"]["user"]} AS u
  ORDER BY RAND()
  LIMIT 10000
  ");

$templ['addresses'] = '';
while ($cur_haus = $db->fetch_array($haus_data)) {
  $templ['addresses'] .= "showPoint({$cur_haus['lat']}, {$cur_haus['lon']}, \"{$cur_haus['HNR']}\", \"{$cur_haus['NAME1']}\", \"{$cur_haus['STRASSE']}\", \"{$cur_haus['PLZ']}\", \"{$cur_haus['ORT']}\");\r\n";
}
$db->free_result($haus_data);

$dsp->AddSingleRow($dsp->FetchModTpl('home', 'map_all'));
$dsp->AddBackButton('index.php?mod=home&action=show');
$dsp->AddContent();

?>