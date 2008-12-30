<?php

$dsp->NewContent(t('Bildergalerie'), t('Hitliste'));

$MainContent .= '<ul class="Line">';
$MainContent .= '<li class="LineLeftHalf">';

$templ['home']['show']['item']['info']['caption'] = t('Die letzten Änderungen');
$content = '';
$res = $db->qry('SELECT name, UNIX_TIMESTAMP(changedate) AS changedate FROM %prefix%picgallery ORDER BY changedate DESC LIMIT 10');
while ($row = $db->fetch_array($res)) {
  $templ['home']['show']['row']['control']['link'] = 'index.php?mod=picgallery&action=show&step=2&file=/'. $row['name'] .'&page=0';
  $templ['home']['show']['row']['info']['text'] = $row['name'].' ['. $row['changedate'] .']';
  $templ['home']['show']['row']['info']['text2'] = '';
  $content .= $dsp->FetchModTpl('home', 'show_row');
}
$db->free_result($row);
$MainContent .= $smarty->fetch('modules/home/templates/show_item.htm');

$MainContent .= '</li>';
$MainContent .= '<li class="LineRightHalf">';

$templ['home']['show']['item']['info']['caption'] = t('Die meisten Hits');
$content = '';
$res = $db->qry('SELECT name, clicks FROM %prefix%picgallery ORDER BY clicks DESC LIMIT 10');
while ($row = $db->fetch_array($res)) {
  $templ['home']['show']['row']['control']['link'] = 'index.php?mod=picgallery&action=show&step=2&file=/'. $row['name'] .'&page=0';
  $templ['home']['show']['row']['info']['text'] = $row['name'].' ['.$row['clicks'].']';
  $templ['home']['show']['row']['info']['text2'] = '';
  $content .= $dsp->FetchModTpl('home', 'show_row');
}
$db->free_result($row);
$MainContent .= $smarty->fetch('modules/home/templates/show_item.htm');

$MainContent .= '</li>';
$MainContent .= '</ul>';
$MainContent .= '<ul class="Line">';
$MainContent .= '<li class="LineLeftHalf">';

$templ['home']['show']['item']['info']['caption'] = t('Die neusten Kommentare');
$content = '';
$res = $db->qry('SELECT name, UNIX_TIMESTAMP(date) as date FROM %prefix%picgallery AS p
  LEFT JOIN %prefix%comments AS c ON p.picid = c.relatedto_id AND c.relatedto_item = \'Picgallery\'
  ORDER BY c.date DESC
  LIMIT 10');
while ($row = $db->fetch_array($res)) {
  $templ['home']['show']['row']['control']['link'] = 'index.php?mod=picgallery&action=show&step=2&file=/'. $row['name'] .'&page=0';
  $templ['home']['show']['row']['info']['text'] = $row['name'].' ['. $row['date'] .']';
  $templ['home']['show']['row']['info']['text2'] = '';
  $content .= $dsp->FetchModTpl('home', 'show_row');
}
$db->free_result($row);
$MainContent .= $smarty->fetch('modules/home/templates/show_item.htm');

$MainContent .= '</li>';
$MainContent .= '<li class="LineRightHalf">';

$templ['home']['show']['item']['info']['caption'] = t('Die meisten Kommentare');
$content = '';
$res = $db->qry('SELECT name, COUNT(*) AS count FROM %prefix%picgallery AS p
  LEFT JOIN %prefix%comments AS c ON p.picid = c.relatedto_id AND c.relatedto_item = \'Picgallery\'
  GROUP BY c.relatedto_id
  ORDER BY count DESC
  LIMIT 10');
while ($row = $db->fetch_array($res)) {
  $templ['home']['show']['row']['control']['link'] = 'index.php?mod=picgallery&action=show&step=2&file=/'. $row['name'] .'&page=0';
  $templ['home']['show']['row']['info']['text'] = $row['name'].' ['.$row['count'].']';
  $templ['home']['show']['row']['info']['text2'] = '';
  $content .= $dsp->FetchModTpl('home', 'show_row');
}
$db->free_result($row);
$MainContent .= $smarty->fetch('modules/home/templates/show_item.htm');

$MainContent .= '</li>';
$MainContent .= '</ul>';

?>