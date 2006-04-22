<?php

$templ['home']['show']['item']['info']['caption'] = $lang["home"]["board_caption"];
$templ['home']['show']['item']['control']['row'] = "";

$authtyp = $auth['type'] + 1;
$query = $db->query("SELECT t.tid, p.pid, t.caption
	FROM {$config["tables"]["board_threads"]} AS t
	LEFT JOIN {$config["tables"]["board_forums"]} AS f ON t.fid = f.fid
	LEFT JOIN {$config["tables"]["board_posts"]} AS p ON p.tid = t.tid
	WHERE (f.need_type <= '{$authtyp}')
	GROUP BY t.tid
	ORDER BY MAX(p.date) DESC
	LIMIT 0,5");
if ($db->num_rows($query) > 0) while($row = $db->fetch_array($query)) {
  $templ['home']['show']['row']['control']['link']	= "index.php?mod=board&action=thread&tid={$row['tid']}&pid={$row['pid']}";
  $templ['home']['show']['row']['info']['text']		= $row['caption'];
  $templ['home']['show']['item']['control']['row']	.= $dsp->FetchModTpl('home', 'show_row');
} else {
	$templ['home']['show']['row']['text']['info']['text'] = "<i>{$lang["home"]["board_noentry"]}</i>";
	$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl('home', 'show_row_text');
}

?>