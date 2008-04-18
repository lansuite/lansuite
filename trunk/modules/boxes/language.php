<?php

$cont = '';
$cur_url = parse_url($_SERVER['REQUEST_URI']);

// Delete old 'language=' from URL
if (isset($_GET['language'])) {
  $tmpPos = strpos($_SERVER['REQUEST_URI'], 'language=') ;
  if (substr($_SERVER['REQUEST_URI'], $tmpPos-1, 1) == '&') $tmpPos = $tmpPos - 1;
  $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'],0,$tmpPos) . substr($_SERVER['REQUEST_URI'], $tmpPos + strlen('language=xx') + 1) ;
}

$res = $db->qry('SELECT cfg_value, cfg_display FROM %prefix%config_selections WHERE cfg_key = \'language\'');
while ($row = $db->fetch_array($res)) {
  if ($cur_url['query'] == '') $cont .= $dsp->FetchIcon($_SERVER['REQUEST_URI'] .'?language='. $row['cfg_value'], $row['cfg_value'], $row['cfg_display']).' ';
  else $cont .= $dsp->FetchIcon($_SERVER['REQUEST_URI'] .'&amp;language='. $row['cfg_value'], $row['cfg_value'], $row['cfg_display']).' ';
}
$db->free_result($res);
$box->Row($cont);
?>
