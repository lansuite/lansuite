<?php
$banner = $db->query_first("SELECT sponsorid, pic_path_banner, url
  FROM {$config['tables']['sponsor']}
  WHERE rotation AND pic_path != '' AND pic_path != 'http://' AND !(pic_path LIKE 'http://%')
  ORDER BY RAND()");

$file_name = '';
$old_file_name = 'ext_inc/banner/banner_'. substr($banner['pic_path'], strrpos($banner["pic_path"], 'ext_inc/banner/') + 15, strlen($banner['pic_path']));

// If no Banner-Thumb was found, take lansuite-banner
if ($banner['pic_path_banner'] == '') $templ['index']['banner_code'] = '<img src="ext_inc/banner/one_network_banner.jpg" border="1" width="468" height="60" class="img_border" alt="top"/>';

// If entry is HTML-Code
elseif (substr($banner['pic_path_banner'], 0, 12) == 'html-code://') {
	$templ['index']['banner_code'] = substr($banner["pic_path_banner"], 12, strlen($banner["pic_path_banner"]) - 12);

// Else add Image-Tag
} else {
  if (file_exists($banner['pic_path_banner'])) $file_name = $banner['pic_path_banner'];
  elseif (file_exists($old_file_name)) $file_name = $old_file_name;
  else  $file_name = $banner['pic_path_banner'];
  
  if ($file_name != '') {
  	$templ['index']['banner_code'] = '<img src="'. $file_name .'" border="1" width="468" height="60" class="img_border" alt="top"/>';

  	// Link banner, if in online mode
  	if ($cfg["sys_internet"]) $templ['index']['banner_code'] = '<a href="base.php?mod=bannerclick&sponsorid='. $banner["sponsorid"] .'" target="_blank">'. $templ['index']['banner_code'] .'</a>';
  }
}
?>