<?php

	$templ['home']['show']['item']['info']['caption'] = $lang["home"]["news_caption"] . ' <span class="small">[<a href="ext_inc/newsfeed/news.xml" class="menu" title="XML Newsfeed">RSS</a>]</span>';
	$templ['home']['show']['item']['control']['row'] = "";
	
	
	$query = $db->query("SELECT newsid, caption, priority FROM {$config["tables"]["news"]} order by top DESC, date DESC LIMIT 0,5");
	if($db->num_rows($query) > 0) {
		while($row = $db->fetch_array($query)) {

    $comments = $db->query_first("SELECT COUNT(*) AS n FROM {$config["tables"]["comments"]}
      WHERE relatedto_item = 'news' AND relatedto_id = {$row["newsid"]}
      GROUP BY relatedto_id
      ");
    

				$newsid 	= $row["newsid"];
				$caption	= $row["caption"];
				$prio		= $row["priority"];
				
				$templ['home']['show']['row']['control']['link']	= "index.php?mod=news&action=comment&newsid=$newsid";
				$templ['home']['show']['row']['info']['text']		= $caption.' ('.$comments['n'].')';

				if ($prio == 1) { 
					$templ['home']['show']['row']['info']['text2']		= "<strong>!!!</strong>";
				 }				

			$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row");

			$templ['home']['show']['row']['info']['text']		= "";	// set var to NULL
			$templ['home']['show']['row']['info']['text2']		= "";	// set var to NULL
		} // while - news
	} // if
	else {
		$templ['home']['show']['row']['text']['info']['text'] = "<i>{$lang["home"]["news_noentry"]}</i>";
		$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row_text");
	}

?>
