<?php
$templ['home']['show']['item']['info']['caption'] = t('Aktuelle Umfragen');
$templ['home']['show']['item']['control']['row'] = "";

$query = $db->query("SELECT p.pollid, p.caption, COUNT(v.pollid) AS votes FROM {$config["tables"]["polls"]} AS p
  LEFT JOIN {$config["tables"]["pollvotes"]} AS v on p.pollid = v.pollid
  GROUP BY p.pollid
  ORDER BY p.changedate DESC
  LIMIT 0,{$cfg['home_item_count']}
  ");
if ($db->num_rows($query) > 0) {
	while($row = $db->fetch_array($query)) {
		$templ['home']['show']['row']['control']['link']	= 'index.php?mod=poll&action=show&step=2&pollid='. $row['pollid'];
		$templ['home']['show']['row']['info']['text']		= $row['caption'];
		$templ['home']['show']['row']['info']['text2']		= '(Votes: '. $row['votes'] .')';
		$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row");
		$templ['home']['show']['row']['info']['text2']		= "";	// set var to NULL
	}
} else $templ['home']['show']['item']['control']['row'] = "<i>". t('Keine Umfragen vorhanden') ."</i>";
?>