<?php
$templ['box']['rows'] = "";
$box->DotRow($lang['boxes']['sponsor_thanks']);

if (!$cfg["sponsor_picwidth"]) $cfg["sponsor_picwidth"] = 120;

$sponsoren = $db->query("SELECT * FROM {$config['tables']['sponsor']}
		WHERE active
		ORDER BY pos, sponsorid");

while ($sponsor = $db->fetch_array($sponsoren)){
	$out = '';

	// If entry is HTML-Code
	if (substr($sponsor['pic_path'], 0, 12) == 'html-code://') {
		$out = substr($sponsor["pic_path"], 12, strlen($sponsor["pic_path"]) - 12);

	// Else add Image-Tag
	} else {
		$org_file_name = substr($sponsor["pic_path"], 0, strrpos($sponsor["pic_path"], "."));
		$org_ending = substr($sponsor["pic_path"], strrpos($sponsor["pic_path"], "."), 5);
		if (file_exists($org_file_name . "_button" . $org_ending)) {
			$sponsor["pic_path"] = $org_file_name . "_button" . $org_ending;

			$ImgSize = @GetImageSize($sponsor["pic_path"]);
			if (!$ImgSize[0]) $ImgSize[0] = 60;
			if ($ImgSize[0] > $cfg["sponsor_picwidth_small"]) $ImgSize[0] = $cfg["sponsor_picwidth_small"];

			$out = "<img src=\"{$sponsor["pic_path"]}\" width=\"{$ImgSize[0]}\" border=\"0\" title=\"{$sponsor["name"]}\">";
		} elseif ($sponsor["name"] != '') $out = "<b>{$sponsor["name"]}</b>";

		if ($out and $sponsor["url"] != '' and $sponsor["url"] != "http://") $out = "<a href=\"base.php?mod=bannerclick&sponsorid={$sponsor["sponsorid"]}\" target=\"_blank\">$out</a>";
	}

	if ($out != '') $templ['box']['rows'] .= $out . "<br />";
}
$db->free_result($sponsoren);

$boxes['sponsor'] .= $box->CreateBox("sponsor", $lang['boxes']['sponsor']);
?>