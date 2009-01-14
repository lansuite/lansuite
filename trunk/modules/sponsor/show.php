<?php

if (!$cfg["sponsor_picwidth"]) $cfg["sponsor_picwidth"] = 400;

$dsp->NewContent(t('Unsere Sponsoren'), t('Bei den folgenden Sponsoren möchten wir uns herzlich für ihren Beitrag zu unserer Veranstaltung bedanken.'));
$sponsoren = $db->qry("SELECT * FROM %prefix%sponsor WHERE sponsor ORDER BY pos, sponsorid");

$db->qry("UPDATE %prefix%sponsor SET views = views + 1 WHERE sponsor");

$out = "<table>";
while ($sponsor = $db->fetch_array($sponsoren)){
	$col1 = "";
	// If entry is HTML-Code
	if (substr($sponsor["pic_path"], 0, 12) == 'html-code://') {
		$col1 = $func->AllowHTML(substr($sponsor["pic_path"], 12, strlen($sponsor["pic_path"]) - 12));

	// Else add Image-Tag
	} else if ($sponsor["pic_path"] != "" and $sponsor["pic_path"] != "http://") {
		$ImgSize = @GetImageSize($sponsor["pic_path"]);
		if (!$ImgSize[0]) $ImgSize[0] = 468;

		if ($ImgSize[0] > $cfg["sponsor_picwidth"]) $ImgSize[0] = $cfg["sponsor_picwidth"];
		$col1 = "<img src=\"". $sponsor["pic_path"] ."\" width=\"{$ImgSize[0]}\" border=\"0\" title=\"{$sponsor["name"]}\">";
		if ($sponsor["url"] != "" and $sponsor["url"] != "http://")
			$col1 = "<a href=\"index.php?mod=sponsor&amp;action=bannerclick&amp;design=base&amp;type=page&amp;sponsorid={$sponsor["sponsorid"]}\" target=\"_blank\">". $col1 ."</a>";
	}

	$col2 = '<b>'. $sponsor["name"] .'</b>';
	if ($sponsor["url"] != "" && $sponsor["url"] != "http://")
		$col2 = "<a href=\"index.php?mod=sponsor&amp;action=bannerclick&amp;design=base&amp;type=page&amp;sponsorid={$sponsor["sponsorid"]}\" target=\"_blank\">". $col2 ."</a>";
	$col2 .= HTML_NEWLINE. $func->text2html($sponsor["text"]);

  $smarty->assign('col1', $col1);
  $smarty->assign('col2', $col2);
	$out .= $smarty->fetch('modules/sponsor/templates/liste.htm');
}
$db->free_result($sponsoren);
$out .= "</table>";

$dsp->AddSingleRow($out);
$dsp->AddBackButton("index.php?mod=sponsor", "sponsor/show");
$dsp->AddContent();
?>
