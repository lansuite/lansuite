<?php
$banner = $db->query_first("SELECT sponsorid, pic_path_banner, url, name
  FROM {$config['tables']['sponsor']}
  WHERE rotation AND ((pic_path != '' AND pic_path != 'http://') OR pic_path_banner != '')
  ORDER BY RAND()");

$file_name = '';
$old_file_name = 'ext_inc/banner/banner_'. substr($banner['pic_path'], strrpos($banner["pic_path"], 'ext_inc/banner/') + 15, strlen($banner['pic_path']));

// If no specific rotation banner is given, use the banner from the sponsor page
if ($banner['pic_path_banner'] == '' and file_exists($old_file_name)) $file_name = $old_file_name;
else $file_name = $banner['pic_path_banner'];

// If no Banner-Thumb was found, use LanSuite default banner
if ($file_name == '') $file_name = 'ext_inc/banner/one_network_banner.jpg';

// If entry is HTML-Code
if (substr($file_name, 0, 12) == 'html-code://') $templ['index']['banner_code'] = substr($file_name, 12, strlen($file_name) - 12);
else {
	$templ['index']['banner_code'] = '<img src="'. $file_name .'" border="1" width="468" height="60" class="img_border" title="'. $banner['name'] .'" alt="Sponsor Banner"/>';

	// Link banner, if in online mode
	if ($cfg['sys_internet']) $templ['index']['banner_code'] = '<a href="index.php?mod=sponsor&action=bannerclick&design=base&sponsorid='. $banner["sponsorid"] .'" target="_blank">'. $templ['index']['banner_code'] .'</a>';
}
?>
