<?php

$query = $db->query("SELECT fid, name, description, need_type FROM {$config["tables"]["board_forums"]} WHERE need_type <= '". ($auth['type'] + 1) ."' ORDER BY name");
if ($db->num_rows($query) == 0) $func->error($lang['board']['show_err_noboards'], "");
else {
	$templ['board']['overview']['case']['info']['caption_text'] = $lang['board']['overview_caption'];
	$templ['board']['overview']['case']['info']['forum_text'] = $lang['board']['forum'];
	$templ['board']['overview']['case']['info']['topic_text'] = $lang['board']['topic'];
	$templ['board']['overview']['case']['info']['posts_text'] = $lang['board']['posts'];
	$templ['board']['overview']['case']['info']['last_post_text'] = $lang['board']['last_post'];

	while( $row = $db->fetch_array($query) ) {
		$fid = $row["fid"];

		$row_posts = $db->query_first("SELECT t.tid AS tid, t.caption AS caption, count(p.tid) AS number, max(p.date) AS last_post, max(t.date) AS last_thread
			FROM {$config["tables"]["board_threads"]} AS t
			INNER JOIN {$config["tables"]["board_posts"]} AS p ON t.fid = p.fid
			WHERE t.fid='$fid'
			GROUP BY t.tid
			ORDER BY t.last_pid DESC, t.date DESC
			");

		$row_threads = $db->query_first("SELECT count(*) AS number FROM {$config["tables"]["board_threads"]} WHERE fid='$fid' GROUP BY fid");

		$linktothread = "<a href=\"index.php?mod=board&action=thread&tid={$row_posts['tid']}\"><b>{$row_posts['caption']}</b></a>" . HTML_NEWLINE;
		$templ['board']['overview']['row']['last_post'] .= $linktothread;
		if ($row_posts["last_post"] == 0) $templ['board']['overview']['row']['last_post'] = $lang['board']['no_posts'];
		elseif ($row_posts["last_thread"] > $row_posts["last_post"])
			$templ['board']['overview']['row']['last_post'] .= $func->unixstamp2date( $row_posts["last_thread"], "daydatetime");
		else $templ['board']['overview']['row']['last_post'] .= $func->unixstamp2date( $row_posts["last_post"], "daydatetime");
	
		$templ['board']['overview']['row']['posts'] = ($row_posts["number"] == "") ? "0" : $row_posts["number"];
		$templ['board']['overview']['row']['topics'] = ($row_threads["number"] == "") ? "0" : $row_threads["number"];
	
		$templ["board"]["overview"]["row"]["forum_link"] = "index.php?mod=board&action=forum&fid=$fid";
		$templ["board"]["overview"]["row"]["name"] = $row["name"];
		$templ["board"]["overview"]["row"]["description"] = $func->db2text2html($row["description"]);

		$templ['board']['overview']['case']['control']['rows'] .= $dsp->FetchModTpl("board","board_overview_row"); 
		unset($templ['board']['overview']['row']['last_post']);
	}

	$row_threads = $db->query_first("SELECT count(*) AS number, max(date) as last_thread FROM {$config["tables"]["board_threads"]}");	
	$row_posts   = $db->query_first("SELECT count(*) AS number, max(date) AS last_post FROM {$config["tables"]["board_posts"]}");	

	$templ['board']['overview']['case']['info']['topics'] = $lang['board']['topics_total'] . " " . $row_threads["number"];
	$templ['board']['overview']['case']['info']['posts']  = $lang['board']['posts_total'] . " " . $row_posts["number"];

	if ($row_posts["last_post"] == 0) $templ['board']['overview']['case']['info']['last_post'] = $lang['board']['no_posts'];
	elseif ($row_threads["last_thread"] > $row_posts["last_post"])
		$templ['board']['overview']['case']['info']['last_post'] = $lang['board']['last_post']	. " " .$func->unixstamp2date( $row_threads["last_thread"], "daydatetime" );
	else $templ['board']['overview']['case']['info']['last_post'] = $lang['board']['last_post']	. " " .$func->unixstamp2date( $row_posts["last_post"], "daydatetime" );

	$dsp->AddSingleRow($dsp->FetchModTpl("board", "board_overview_case")); 
	$dsp->AddContent();
}

?>
