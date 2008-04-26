<?php

function CheckPostNew($last_post, $last_read) {
  global $db, $config, $auth, $line;

  // Delete old entries
  $db->query("DELETE FROM {$config["tables"]["board_read_state"]} WHERE last_read < ". (time() - 60 * 60 * 24 * 7));

  // Older, than one week
  if ($last_post < (time() - 60 * 60 * 24 * 7)) return 0;

  // No entry -> Thread completely new
  elseif (!$last_read) return 1;

  // Entry exists
  else {

    // The posts date is newer than the mark -> New
    if ($last_read < $last_post) return 1;

    // The posts date is older than the mark -> Old
    else return 0;
  }
}
                                                                    

$templ['home']['show']['item']['info']['caption'] = t('Aktuelles im Board') . ' <span class="small">[<a href="index.php?mod=board&action=forum&fid=&order_by=LastPost&order_dir=DESC" title="Neusten Beiträge">mehr</a>]</span>';
$templ['home']['show']['item']['control']['row'] = "";

$authtyp = $auth['type'] + 1;
$query = $db->query("SELECT f.fid, t.tid, MAX(p.pid) AS pid, t.caption, MAX(p.date) AS LastPost, (COUNT(p.pid) - 1) AS posts, r.last_read, t.closed
	FROM {$config["tables"]["board_threads"]} AS t
	LEFT JOIN {$config["tables"]["board_forums"]} AS f ON t.fid = f.fid
	LEFT JOIN {$config["tables"]["board_posts"]} AS p ON p.tid = t.tid
  LEFT JOIN {$config["tables"]["board_read_state"]} AS r ON t.tid = r.tid AND r.userid = ". (int)$auth['userid'] ."
	WHERE (f.need_type <= '{$authtyp}' AND (!f.need_group OR f.need_group = ". ((int)$auth['group_id']) ."))
	GROUP BY t.tid
	ORDER BY LastPost DESC
	LIMIT 0,{$cfg['home_item_count']}");

if ($db->num_rows($query) > 0) while($row = $db->fetch_array($query)) {
  $templ['home']['show']['row']['control']['link']	= "index.php?mod=board&action=thread&fid={$row['fid']}&tid={$row['tid']}&gotopid={$row['pid']}#pid{$row['pid']}";

  $templ['home']['show']['row']['info']['text']		= $func->CutString($row['caption'], 40) .' ['. $row['posts'] .']';
  if ($row['closed']) $templ['home']['show']['row']['info']['text'] .= ' <div id="infobox" style="display:inline"><img src="design/images/icon_locked.png" border="0" width="12" /><span class="infobox">'. t('Thread wurde geschlossen') .'</span></div>';
  
  if (CheckPostNew($row['LastPost'], $row['last_read']))
	$templ['home']['show']['item']['control']['row']	.= $dsp->FetchModTpl('home', 'show_row_new');
  else
	$templ['home']['show']['item']['control']['row']	.= $dsp->FetchModTpl('home', 'show_row');
} else $templ['home']['show']['item']['control']['row'] = "<i>". t('Keine Beiträge vorhanden') ."</i>";
?>
