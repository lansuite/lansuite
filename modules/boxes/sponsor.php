<?php
$templ['box']['rows'] = "";
$box->DotRow(t('Wir danken').':');

if (!$cfg["sponsor_picwidth"]) $cfg["sponsor_picwidth"] = 120;

$sponsoren = $db->query("SELECT * FROM {$config['tables']['sponsor']}
		WHERE active
		ORDER BY pos, sponsorid");

$db->query("UPDATE {$config['tables']['sponsor']} SET views_box = views_box + 1 WHERE active");

while ($sponsor = $db->fetch_array($sponsoren)){
	$out = '';

	// If entry is HTML-Code
	if (substr($sponsor['pic_path_button'], 0, 12) == 'html-code://') {
		$out = substr($sponsor["pic_path_button"], 12, strlen($sponsor["pic_path_button"]) - 12);

	// Else add Image-Tag
	} else {
    #$file_name = '';
    #$old_file_name = 'ext_inc/banner/button_'. substr($sponsor['pic_path'], strrpos($sponsor["pic_path"], 'ext_inc/banner/') + 15, strlen($sponsor['pic_path']));
		#if (file_exists($sponsor['pic_path_button'])) $file_name = $sponsor['pic_path_button'];
		#elseif (file_exists($old_file_name)) $file_name = $old_file_name;
    #else
    $file_name = $sponsor['pic_path_button'];
		if ($file_name != '') {
			if (is_file($file_name)) $ImgSize = GetImageSize($file_name);
			if (!$ImgSize[0]) $ImgSize[0] = $cfg["sponsor_picwidth_small"];
			if ($ImgSize[0] > $cfg["sponsor_picwidth_small"]) $ImgSize[0] = $cfg["sponsor_picwidth_small"];
			$out = "<img src=\"$file_name\" width=\"{$ImgSize[0]}\" border=\"0\" title=\"{$sponsor["name"]}\">";
		} elseif ($sponsor["name"] != '') $out = "<b>{$sponsor["name"]}</b>";

		if ($out and $sponsor["url"] != '' and $sponsor["url"] != "http://") $out = "<a href=\"index.php?mod=sponsor&action=bannerclick&design=base&type=box&sponsorid={$sponsor["sponsorid"]}\" target=\"_blank\">$out</a>";
	}

	if ($out != '') $templ['box']['rows'] .= '<li>'. $out . "<br /><br /></li>";
}
$db->free_result($sponsoren);

?>