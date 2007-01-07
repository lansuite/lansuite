<?php

$templ['home']['show']['item']['info']['caption'] = t('Aktuelles im Board');
$templ['home']['show']['item']['control']['row'] = "";

$authtyp = $auth['type'] + 1;
$query = $db->query("SELECT f.fid, t.tid, MAX(p.pid) AS pid, t.caption, MAX(p.date) AS LastPost, COUNT(p.pid) AS posts
	FROM {$config["tables"]["board_threads"]} AS t
	LEFT JOIN {$config["tables"]["board_forums"]} AS f ON t.fid = f.fid
	LEFT JOIN {$config["tables"]["board_posts"]} AS p ON p.tid = t.tid
	WHERE (f.need_type <= '{$authtyp}')
	GROUP BY t.tid
	ORDER BY LastPost DESC
	LIMIT 0,5");
if ($db->num_rows($query) > 0) while($row = $db->fetch_array($query)) {
  $templ['home']['show']['row']['control']['link']	= "index.php?mod=board&action=thread&fid={$row['fid']}&tid={$row['tid']}&gotopid={$row['pid']}#pid{$row['pid']}";
  $templ['home']['show']['row']['info']['text']		= $row['caption'] .' ('.$row['posts'].')';
  $templ['home']['show']['item']['control']['row']	.= $dsp->FetchModTpl('home', 'show_row');
} else {
	$templ['home']['show']['row']['text']['info']['text'] = "<i>". t('Keine Beiträge vorhanden') ."</i>";
	$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl('home', 'show_row_text');
}

?>
