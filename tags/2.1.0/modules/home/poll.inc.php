<?php

// Polls

	$templ['home']['show']['item']['info']['caption'] = $lang["home"]["poll_caption"];
	$templ['home']['show']['item']['control']['row'] = "";

	$query = $db->query("SELECT pollid, caption FROM {$config["tables"]["polls"]} order by changedate DESC LIMIT 0,5");
	if($db->num_rows($query) > 0) {
		while($row = $db->fetch_array($query)) {

				$pollid 	= $row["pollid"];
				$caption	= $row["caption"];

				$row_number 	= $db->query_first("SELECT count(*) as number FROM {$config["tables"]["pollvotes"]} WHERE pollid='$pollid'");
				$number_polls 	= $row_number["number"];
				
				$templ['home']['show']['row']['control']['link']	= "index.php?mod=poll&action=show&step=2&pollid=$pollid";
				$templ['home']['show']['row']['info']['text']		= $caption;
				$templ['home']['show']['row']['info']['text2']		= "(Votes: ".$number_polls.")";


			$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row");
			$templ['home']['show']['row']['info']['text2']		= "";	// set var to NULL
		} // while - news
	} // if
	else {
		$templ['home']['show']['row']['text']['info']['text'] = "<i>{$lang["home"]["poll_noentry"]}</i>";
		$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row_text");
	}

?>
