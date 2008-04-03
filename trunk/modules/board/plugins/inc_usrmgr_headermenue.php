<?
// This File is a Part of the LS-Pluginsystem. It will be included in
// modules/usrmgr/details.php to generate Modulspezific Headermenue 
// for Userdetails

// ADD HERE MODULSPECIFIC INCLUDES

// ADD HERE MODULPUGINCODE

// forumposts
$dsp->AddDoubleRow(t('Board Posts'), $user_data['posts'].$count_rows['count']);

// Threads
$get_board_threads = $db->query("SELECT b.tid, b.date, t.caption FROM {$config['tables']['board_posts']} AS b
	LEFT JOIN {$config['tables']['board_threads']} AS t ON b.tid = t.tid
	LEFT JOIN {$config['tables']['board_forums']} AS f ON t.fid = f.fid
	WHERE b.userid = '{$_GET['userid']}' AND (f.need_type <= '{$auth['type']}' OR f.need_type = '1')
	GROUP BY b.tid
	ORDER BY b.date DESC
	LIMIT 20
	");
while($row_threads = $db->fetch_array($get_board_threads)) {
    $threads .= $func->unixstamp2date($row_threads['date'], "datetime")." - <a href=\"index.php?mod=board&action=thread&tid={$row_threads['tid']}\">{$row_threads['caption']}</a>". HTML_NEWLINE;
}
$db->free_result($get_board_threads);
$dsp->AddDoubleRow(t('Letzte 10 Threads'), $threads);

?>