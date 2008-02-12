<?php

	$templ['home']['show']['item']['info']['caption'] = $lang["home"]["board_caption"];
	$templ['home']['show']['item']['control']['row'] = "";
	
// BOARD
	$authtyp = $_SESSION['auth']['type'] + 1;
	$query = $db->query("SELECT threads.tid, threads.fid, threads.caption
	FROM {$config["tables"]["board_threads"]} AS threads
	INNER JOIN {$config["tables"]["board_forums"]} AS forums ON threads.fid = forums.fid
	WHERE (forums.need_type <= '{$authtyp}')
	GROUP BY threads.tid
	ORDER BY threads.last_pid DESC, date DESC
	LIMIT 0,5");
	if($db->num_rows($query) > 0) {
		while($row = $db->fetch_array($query)) {

				$tid 	 = $row["tid"];
				$fid 	 = $row["fid"];
				$caption = $row["caption"];

				$templ['home']['show']['row']['control']['link']	= "index.php?mod=board&action=thread&tid=$tid&fid=$fid";
				$templ['home']['show']['row']['info']['text']		= $caption;
				$templ['home']['show']['item']['control']['row']	.= $dsp->FetchModTpl("home", "show_row");
		} // while - board
	} //if
	else {
		$templ['home']['show']['row']['text']['info']['text'] = "<i>{$lang["home"]["board_noentry"]}</i>";
		$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row_text");
	}

?>
