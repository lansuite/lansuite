<?php

// SERVER

	$templ['home']['show']['item']['info']['caption'] = t('Neue Server');
	$templ['home']['show']['item']['control']['row'] = "";

	$query = $db->query("SELECT serverid, caption, type FROM {$config["tables"]["server"]} order by changedate DESC LIMIT 0,5");
	if($db->num_rows($query) > 0) {
		while($row = $db->fetch_array($query)) {

				$serverid 	= $row["serverid"];
				$caption	= $row["caption"];
				$type		= $row["type"];
				
				$templ['home']['show']['row']['control']['link']	= "index.php?mod=server&action=show_details&serverid=$serverid";
				$templ['home']['show']['row']['info']['text']		= $caption;
				$templ['home']['show']['row']['info']['text2']		= "(".$type.")";


			$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row");
			$templ['home']['show']['row']['info']['text2']		= "";	// set var to NULL
		} // while - news
	} // if
	else {
		$templ['home']['show']['row']['text']['info']['text'] = "<i>". t('Keine Server vorhanden') ."</i>";
		$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row_text");
	}


?>
