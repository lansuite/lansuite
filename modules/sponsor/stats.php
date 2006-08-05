<?php

$dsp->NewContent($lang['sponsor']['stats_caption'], $lang['sponsor']['stats_sub_caption']);
$sponsoren = $db->query("SELECT * FROM {$config['tables']['sponsor']}");
while ($sponsor = $db->fetch_array($sponsoren)){
  $dsp->AddFieldsetStart("<a href=\"{$sponsor["url"]}\" traget=\"_blank\">{$sponsor["name"]}</a>");
  if ($sponsor['views']) $percentage = round($sponsor['hits'] / $sponsor['views'], 4) * 100 .'%';
  else $percentage = '---';
	$dsp->AddDoubleRow($lang['sponsor']['add_sponsor'], $sponsor['views'] .'x '. $lang['sponsor']['stats_views'].
    ', '.$sponsor['hits'] .'x '. $lang['sponsor']['stats_hits'].
    ' ['. $lang['sponsor']['stats_rate'] .': '. $percentage .']');

  if ($sponsor['views_banner']) $percentage = round($sponsor['hits_banner'] / $sponsor['views_banner'], 4) * 100 .'%';
  else $percentage = '---';
	$dsp->AddDoubleRow($lang['sponsor']['add_banner'], $sponsor['views_banner'] .'x '. $lang['sponsor']['stats_views'].
    ', '.$sponsor['hits_banner'] .'x '. $lang['sponsor']['stats_hits'].
    ' ['. $lang['sponsor']['stats_rate'] .': '. $percentage .']');

  if ($sponsor['views_box']) $percentage = round($sponsor['hits_box'] / $sponsor['views_box'], 4) * 100 .'%';
  else $percentage = '---';
	$dsp->AddDoubleRow($lang['sponsor']['add_active'], $sponsor['views_box'] .'x '. $lang['sponsor']['stats_views'].
    ', '.$sponsor['hits_box'] .'x '. $lang['sponsor']['stats_hits'].
    ' ['. $lang['sponsor']['stats_rate'] .': '. $percentage .']');
  $dsp->AddFieldsetEnd();
}
$db->free_result($sponsoren);
$dsp->AddBackButton('index.php?mod=sponsor', 'sponsor/show');
$dsp->AddContent();
?>
