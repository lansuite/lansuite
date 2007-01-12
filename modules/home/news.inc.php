<?php

	$templ['home']['show']['item']['info']['caption'] = t('Aktuelle News') . ' <span class="small">[<a href="ext_inc/newsfeed/news.xml" class="menu" title="XML Newsfeed">RSS</a>]</span>';
	$templ['home']['show']['item']['control']['row'] = "";
	
  $query = $db->query("SELECT n.newsid, n.caption, n.priority, COUNT(c.relatedto_id) AS comments FROM {$config["tables"]["news"]} AS n
    LEFT JOIN {$config["tables"]["comments"]} AS c ON (c.relatedto_id = n.newsid AND c.relatedto_item = 'news')
    GROUP BY c.relatedto_id
    ORDER BY n.top DESC, n.date DESC
    LIMIT 0,{$cfg['home_item_count']}
    ");
	if($db->num_rows($query) > 0) {
		while($row = $db->fetch_array($query)) {

				$newsid 	= $row["newsid"];
				$caption	= $row["caption"];
				$prio		= $row["priority"];
				
				$templ['home']['show']['row']['control']['link']	= "index.php?mod=news&action=comment&newsid=$newsid";
				$templ['home']['show']['row']['info']['text']		= $caption.' ['.$row['comments'].']';

				if ($prio == 1) { 
					$templ['home']['show']['row']['info']['text2']		= "<strong>!!!</strong>";
				 }				

			$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row");

			$templ['home']['show']['row']['info']['text']		= "";	// set var to NULL
			$templ['home']['show']['row']['info']['text2']		= "";	// set var to NULL
		} // while - news
	} // if
	else {
		$templ['home']['show']['row']['text']['info']['text'] = "<i>". t('Keine News bisher vorhanden') ."</i>";
		$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row_text");
	}

?>
