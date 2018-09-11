<?php

$dsp->NewContent(t('Bildergalerie'), t('Hitliste'));

$MainContent .= '<ul class="Line">';
$MainContent .= '<li class="LineLeftHalf">';

$smarty->assign('caption', t('Die letzten Änderungen'));
$content = '';
$res = $db->qry('SELECT name, UNIX_TIMESTAMP(changedate) AS changedate FROM %prefix%picgallery ORDER BY changedate DESC LIMIT 10');
while ($row = $db->fetch_array($res)) {
    $smarty->assign('link', 'index.php?mod=picgallery&action=show&step=2&file=/'. $row['name'] .'&page=0');
    $smarty->assign('text', $row['name'].' ['. $func->unixstamp2date($row['changedate'], 'datetime') .']');
    $smarty->assign('text2', '');
    $content .= $smarty->fetch('modules/home/templates/show_row.htm');
}
$db->free_result($res);
$smarty->assign('content', $content);
$MainContent .= $smarty->fetch('modules/home/templates/show_item.htm');

$MainContent .= '</li>';
$MainContent .= '<li class="LineRightHalf">';

$smarty->assign('caption', t('Die meisten Hits'));
$content = '';
$res = $db->qry('SELECT name, clicks FROM %prefix%picgallery ORDER BY clicks DESC LIMIT 10');
while ($row = $db->fetch_array($res)) {
    $smarty->assign('link', 'index.php?mod=picgallery&action=show&step=2&file=/'. $row['name'] .'&page=0');
    $smarty->assign('text', $row['name'].' ['.$row['clicks'].']');
    $smarty->assign('text2', '');
    $content .= $smarty->fetch('modules/home/templates/show_row.htm');
}
$db->free_result($res);
$smarty->assign('content', $content);
$MainContent .= $smarty->fetch('modules/home/templates/show_item.htm');

$MainContent .= '</li>';
$MainContent .= '</ul>';
$MainContent .= '<ul class="Line">';
$MainContent .= '<li class="LineLeftHalf">';

$smarty->assign('caption', t('Die neusten Kommentare'));
$content = '';
$res = $db->qry('
  SELECT
    name,
    UNIX_TIMESTAMP(date) AS date
  FROM %prefix%picgallery AS p
  LEFT JOIN %prefix%comments AS c ON
    p.picid = c.relatedto_id
    AND c.relatedto_item = \'Picgallery\'
  ORDER BY c.date DESC
  LIMIT 10');
while ($row = $db->fetch_array($res)) {
    $smarty->assign('link', 'index.php?mod=picgallery&action=show&step=2&file=/'. $row['name'] .'&page=0');
    $smarty->assign('text', $row['name'].' ['. $func->unixstamp2date($row['date'], 'datetime') .']');
    $smarty->assign('text2', '');
    $content .= $smarty->fetch('modules/home/templates/show_row.htm');
}
$db->free_result($res);
$smarty->assign('content', $content);
$MainContent .= $smarty->fetch('modules/home/templates/show_item.htm');

$MainContent .= '</li>';
$MainContent .= '<li class="LineRightHalf">';

$smarty->assign('caption', t('Die meisten Kommentare'));
$content = '';
$res = $db->qry('
  SELECT
    name,
    COUNT(*) AS count
  FROM %prefix%picgallery AS p
  LEFT JOIN %prefix%comments AS c ON
    p.picid = c.relatedto_id
    AND c.relatedto_item = \'Picgallery\'
  GROUP BY c.relatedto_id
  ORDER BY count DESC
  LIMIT 10');
while ($row = $db->fetch_array($res)) {
    $smarty->assign('link', 'index.php?mod=picgallery&action=show&step=2&file=/'. $row['name'] .'&page=0');
    $smarty->assign('text', $row['name'].' ['.$row['count'].']');
    $smarty->assign('text2', '');
    $content .= $smarty->fetch('modules/home/templates/show_row.htm');
}
$db->free_result($res);
$smarty->assign('content', $content);
$MainContent .= $smarty->fetch('modules/home/templates/show_item.htm');

$MainContent .= '</li>';
$MainContent .= '</ul>';
