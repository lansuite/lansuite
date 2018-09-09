<?php

if ($_GET['name']) {
    $row = $db->qry_first('SELECT postid FROM %prefix%wiki WHERE name = %string%', $_GET['name']);
    $_GET['postid'] = $row['postid'];
}
if (!$_GET['postid']) {
    $_GET['postid'] = 1;
}
if (!isset($_GET['versionid'])) {
    $row = $db->qry_first('
      SELECT
        MAX(versionid) AS versionid
      FROM
        %prefix%wiki_versions
      WHERE
        postid = %int%
      GROUP BY postid', $_GET['postid']);
    $_GET['versionid'] = $row['versionid'];
}

$links = t('Version') .': ';
$start_versionid = $_GET['versionid'] - 4;
if ($start_versionid < 0) {
    $start_versionid = 0;
}
$res = $db->qry('
  SELECT
    v.versionid,
    v.date,
    u.username
  FROM %prefix%wiki_versions AS v
  LEFT JOIN %prefix%user AS u ON v.userid = u.userid
  WHERE
    postid = %int%
  ORDER BY versionid
  LIMIT %int%, 7', $_GET['postid'], $start_versionid);
while ($row = $db->fetch_array($res)) {
    $links .= '[<a href="index.php?mod=wiki&action=show&postid='. $_GET['postid'] .'&versionid='. $row['versionid'] .'">'. $row['versionid'];
    if ($_GET['versionid'] == $row['versionid']) {
        $links .= ' - '. $row['username'] .'@'. $row['date'] .' ';
    }
    $links .= '</a>';
    if ($_GET['versionid'] == $row['versionid'] and $auth['type'] > 2) {
        $links .= ' <a href="index.php?mod=wiki&action=delete&step=10&postid='. $_GET['postid'] .'&versionid='. $_GET['versionid'] .'" rel="nofollow" class="icon_delete" title="'. t('Löschen') .'"> </a> ';
    }
    $links .= '] ';
}
$db->free_result($res);

if ($auth['login']) {
    $links .= '[<a href="index.php?mod=wiki&action=edit&postid='. $_GET['postid'] .'">'. t('Editieren') .'</a>] ';
}

$links_main = '';
if ($auth['type'] > 2) {
    $links_main .= ' <a href="index.php?mod=wiki&action=delete&step=2&postid='. $_GET['postid'] .'" class="icon_delete" title="'. t('Löschen') .'"> </a>';
}

$row = $db->qry_first('
  SELECT
    w.postid,
    w.name,
    v.text
  FROM %prefix%wiki AS w 
  LEFT JOIN %prefix%wiki_versions AS v ON w.postid = v.postid
  WHERE
    w.postid = %int%
    AND v.versionid = %int%', $_GET['postid'], $_GET['versionid']);

$func->SetRead('wiki', $row['postid']);
$framework->AddToPageTitle($row["name"]);
$framework->AddToPageTitle('V'. (int)$_GET['versionid']);

$dsp->NewContent($row['name'] . $links_main, $links);
$dsp->AddSingleRow($func->Text2Wiki($row['text']), '', 'textContent');
