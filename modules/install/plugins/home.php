<?php

$smarty->assign('caption', t('Sonstige neue Kommentare'));
$content = "";

if (!in_array('faq', $ActiveModules)) $exclude .= ' AND relatedto_item != \'faq\'';
if (!in_array('usrmgr', $ActiveModules)) $exclude .= ' AND relatedto_item != \'User\'';
if (!in_array('poll', $ActiveModules)) $exclude .= ' AND relatedto_item != \'Poll\'';
if (!in_array('server', $ActiveModules)) $exclude .= ' AND relatedto_item != \'server\'';
if (!in_array('downloads', $ActiveModules)) $exclude .= ' AND relatedto_item != \'downloads\'';
if (!in_array('picgallery', $ActiveModules)) $exclude .= ' AND relatedto_item != \'Picgallery\'';

$query = $db->qry('SELECT relatedto_id, relatedto_item, MAX(UNIX_TIMESTAMP(date)) AS date, COUNT(*) AS cnt FROM %prefix%comments
    WHERE relatedto_item != \'BugEintrag\' AND relatedto_item != \'news\' %plain%
    GROUP BY relatedto_item, relatedto_id
    ORDER BY date DESC
    LIMIT 0, %int%',
    $exclude, $cfg['home_item_cnt_comments']);

if ($db->num_rows($query) > 0) while($row = $db->fetch_array($query)) {
  switch($row['relatedto_item']) {
    case 'faq':
      $row2 = $db->qry_first('SELECT caption FROM %prefix%faq_item WHERE itemid = %int%', $row['relatedto_id']);
      $link = 'index.php?mod=faq&action=comment&itemid='. (int)$row['relatedto_id'];
      $title = 'FAQ: '. $row2['caption'];
    break;
    case 'User':
      $row2 = $db->qry_first('SELECT username FROM %prefix%user WHERE userid = %int%', $row['relatedto_id']);
      $link = 'index.php?mod=usrmgr&action=details&userid='. (int)$row['relatedto_id'] .'&headermenuitem=3';
      $title = 'User: '. $row2['username'];
    break;
    case 'Poll':
      $row2 = $db->qry_first('SELECT caption FROM %prefix%polls WHERE pollid = %int%', $row['relatedto_id']);
      $link = 'index.php?mod=poll&action=show&step=2&pollid='. (int)$row['relatedto_id'];
      $title = 'Poll: '. $row2['caption'];
    break;
    case 'server':
      $row2 = $db->qry_first('SELECT caption FROM %prefix%server WHERE serverid = %int%', $row['relatedto_id']);
      $link = 'index.php?mod=server&action=show_details&serverid='. (int)$row['relatedto_id'];
      $title = 'Server: '. $row2['caption'];
    break;
    case 'downloads':
      $link = 'index.php?mod=downloads';
      $title = 'Download';
    break;
    case 'Picgallery':
      $row2 = $db->qry_first('SELECT name FROM %prefix%picgallery WHERE picid = %int%', $row['relatedto_id']);
      $link = 'index.php?mod=picgallery&action=show&step=2&file=/'. $row2['name'];
      $title = 'Gallery: '. $row2['name'];
    break;
    default:
      $link = '';
      $title = '';
    break;
  }
  $smarty->assign('link', $link);
  $smarty->assign('text', $func->CutString($title, 40));
  $smarty->assign('text2', ' ['. $row['cnt'] .']');
  #if ($func->CheckNewPosts($row['date'], 'wiki', $row['postid'])) $content .= $smarty->fetch('modules/home/templates/show_row_new.htm');
  #else $content .= $smarty->fetch('modules/home/templates/show_row.htm');
  $content .= $smarty->fetch('modules/home/templates/show_row.htm');
} else $content = "<i>". t('Keine Einträge vorhanden') ."</i>";
?>