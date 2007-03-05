<?php
if ($db->success) {
  $banner = $db->query_first("SELECT sponsorid, pic_path_banner, url, name
    FROM {$config['tables']['sponsor']}
    WHERE rotation AND ((pic_path != '' AND pic_path != 'http://') OR pic_path_banner != '')
    ORDER BY RAND()");

  $file_name = '';
  $old_file_name = 'ext_inc/banner/banner_'. substr($banner['pic_path'], strrpos($banner["pic_path"], 'ext_inc/banner/') + 15, strlen($banner['pic_path']));

  if ($banner['sponsorid']) $db->query("UPDATE {$config['tables']['sponsor']} SET views_banner = views_banner + 1 WHERE sponsorid = '{$banner['sponsorid']}'");

  // If no specific rotation banner is given, use the banner from the sponsor page
  if ($banner['pic_path_banner'] == '' and file_exists($old_file_name)) $file_name = $old_file_name;
  else $file_name = $banner['pic_path_banner'];

  // If no Banner-Thumb was found, use LanSuite default banner
  if ($file_name == '') $file_name = 'ext_inc/banner/one_network_banner.jpg';

  // If entry is HTML-Code
  if (substr($file_name, 0, 12) == 'html-code://') echo substr($file_name, 12, strlen($file_name) - 12);
  else {
  	$code = '<img src="'. $file_name .'" border="1" width="468" height="60" class="img_border" title="'. $banner['name'] .'" alt="Sponsor Banner"/>';

  	// Link banner, if in online mode
  	if ($cfg['sys_internet']) $code = '<a href="index.php?mod=sponsor&action=bannerclick&design=base&type=banner&sponsorid='. $banner["sponsorid"] .'" target="_blank">'. $code .'</a>';
  	
  	echo $code;
  }
}
?>