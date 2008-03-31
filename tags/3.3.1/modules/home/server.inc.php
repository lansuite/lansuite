<?php
$templ['home']['show']['item']['info']['caption'] = t('Neue Server');
$templ['home']['show']['item']['control']['row'] = "";

if (!$cfg['server_sortmethod']) $cfg['server_sortmethod'] = 'changedate';
$query = $db->query("SELECT serverid, caption, type FROM {$config["tables"]["server"]} ORDER BY {$cfg['server_sortmethod']} DESC LIMIT 0,{$cfg['home_item_count']}");
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
	}
}
else $templ['home']['show']['item']['control']['row'] = "<i>". t('Keine Server vorhanden') ."</i>";
?>
