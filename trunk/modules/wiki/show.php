<?php

if (!$_GET['postid']) $_GET['postid'] = 1;
if (!isset($_GET['versionid'])) {
  $row = $db->qry_first('SELECT MAX(versionid) AS versionid FROM %prefix%wiki_versions WHERE postid = %int% GROUP BY postid', $_GET['postid']);
  $_GET['versionid'] = $row['versionid'];
}

$links = t('Version') .': ';
$res = $db->qry('SELECT v.versionid, v.date, u.username FROM %prefix%wiki_versions AS v LEFT JOIN %prefix%user AS u ON v.userid = u.userid
  WHERE postid = %int% ORDER BY versionid',
  $_GET['postid']);
while ($row = $db->fetch_array($res)) {
  $links .= '[<a href="index.php?mod=wiki&action=show&postid='. $_GET['postid'] .'&versionid='. $row['versionid'] .'">'. $row['versionid'];
  if ($_GET['versionid'] == $row['versionid']) $links .= ' - '. $row['username'] .'@'. $row['date'] .' ';
  $links .= '</a>';
  if ($_GET['versionid'] == $row['versionid'] and $auth['type'] > 2) $links .= ' <a href="index.php?mod=wiki&action=delete&step=10&postid='. $_GET['postid'] .'&versionid='. $_GET['versionid'] .'"><img src="design/'. $auth['design'] .'/images/arrows_delete.gif" border="0" alt="'. t('Löschen') .'" /></a> ';
  $links .= '] ';
}
$db->free_result($res);

if ($auth['login']) $links .= '[<a href="index.php?mod=wiki&action=edit&postid='. $_GET['postid'] .'">'. t('Editieren') .'</a>] ';

$links_main = '';
if ($auth['type'] > 2) $links_main .= ' <a href="index.php?mod=wiki&action=delete&step=2&postid='. $_GET['postid'] .'"><img src="design/'. $auth['design'] .'/images/arrows_delete.gif" border="0" alt="'. t('Löschen') .'" /></a>';

$row = $db->qry_first('SELECT w.name, v.text FROM %prefix%wiki AS w LEFT JOIN %prefix%wiki_versions AS v ON w.postid = v.postid
  WHERE w.postid = %int% AND v.versionid = %int%',
  $_GET['postid'], $_GET['versionid']);

$dsp->NewContent($row['name'] . $links_main, $links);
$dsp->AddSingleRow($func->Text2HTML($row['text']));
$dsp->AddContent();

?>