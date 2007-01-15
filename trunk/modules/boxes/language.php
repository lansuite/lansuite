<?php

$cur_url = parse_url($_SERVER['REQUEST_URI']);
$res = $db->query("SELECT cfg_value, cfg_display FROM {$config["tables"]["config_selections"]} WHERE cfg_key = 'language'");
while ($row = $db->fetch_array($res)) {
  if ($cur_url['query'] == '') $templ['box']['rows'] .= $dsp->FetchIcon($_SERVER['REQUEST_URI'] .'?language='. $row['cfg_value'], $row['cfg_value'], $row['cfg_display']).' ';
  else $templ['box']['rows'] .= $dsp->FetchIcon($_SERVER['REQUEST_URI'] .'&language='. $row['cfg_value'], $row['cfg_value'], $row['cfg_display']).' ';
}
$db->free_result($res);

?>