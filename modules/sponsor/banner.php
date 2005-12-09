<?php
$banner = $db->query_first("SELECT sponsorid, pic_path, url FROM {$config['tables']['sponsor']} WHERE rotation AND pic_path != '' AND pic_path != 'http://' ORDER BY RAND()");

// If entry is HTML-Code
if (substr($banner['pic_path'], 0, 12) == 'html-code://') {
	$templ['index']['banner_code'] = substr($banner["pic_path"], 12, strlen($banner["pic_path"]) - 12);

// Else add Image-Tag
} else {
	$org_file_name = substr($banner["pic_path"], 0, strrpos($banner["pic_path"], "."));
	$org_ending = substr($banner["pic_path"], strrpos($banner["pic_path"], "."), 5);
	if (file_exists($org_file_name . "_banner" . $org_ending)) {
		$banner["pic_path"] = $org_file_name . "_banner" . $org_ending;
		$templ['index']['banner_code'] = '<img src="'. $banner["pic_path"] .'" border="1" width="468" height="60" class="img_border" alt="top"/>';

	// If no Banner-Thumb was found, take lansuite-banner
	} else $templ['index']['banner_code'] = '<img src="ext_inc/banner/one_network_banner.jpg" border="1" width="468" height="60" class="img_border" alt="top"/>';

	// Link banner, if in online mode
	if ($cfg["sys_internet"]) $templ['index']['banner_code'] = '<a href="base.php?mod=bannerclick&sponsorid='. $banner["sponsorid"] .'" target="_blank">'. $templ['index']['banner_code'] .'</a>';
}
?>