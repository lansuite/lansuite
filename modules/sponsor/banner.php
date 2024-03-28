<?php

$httpsServer = $request->server->get('HTTPS');
$where = "rotation AND ((pic_path != '' AND pic_path != 'http://') OR pic_path_banner != '')";
if ($httpsServer == 'on') {
    $where = "rotation AND ((pic_path != '' AND pic_path != 'http://') OR pic_path_banner != '') AND !ssl_hide_banner";
}

$banner = $db->qry_first("
  SELECT
    sponsorid,
    pic_path_banner,
    url,
    name
  FROM %prefix%sponsor
  WHERE %plain%
  ORDER BY RAND()", $where);
unset($where);

$file_name = '';
$old_file_name = '';
if ($banner) {
    $old_file_name = 'ext_inc/banner/banner_'. substr($banner['pic_path_banner'], strrpos($banner["pic_path_banner"], 'ext_inc/banner/') + 15, strlen($banner['pic_path_banner']));
}

if ($banner && $banner['sponsorid']) {
    $database->query("UPDATE %prefix%sponsor SET views_banner = views_banner + 1 WHERE sponsorid = ?", [$banner['sponsorid']]);
}

// If no specific rotation banner is given, use the banner from the sponsor page
$file_name = $banner['pic_path_banner'] ?? '';
if ($banner && $banner['pic_path_banner'] == '' && file_exists($old_file_name)) {
    $file_name = $old_file_name;
}

// If entry is HTML-Code
if (str_starts_with($file_name, 'html-code://')) {
    $smarty->assign('MainBanner', $func->AllowHTML(substr($file_name, 12, strlen($file_name) - 12)));
} else {
  // If no Banner-Thumb was found, use LanSuite default banner
    if ($file_name == '') {
        $file_name = 'ext_inc/banner/one_network_banner.jpg';
    }

    $imgTitleTag = ($banner) ? 'title="'. $banner['name'] .'"': '';
    $code = '<img src="'. $file_name .'" border="1" width="468" height="60" class="img_border" '. $imgTitleTag .' alt="Sponsor Banner"/>';

    // Link banner, if in online mode
    if ($cfg['sys_internet'] && $banner && $banner["sponsorid"]) {
        $code = '<a href="index.php?mod=sponsor&amp;action=bannerclick&amp;design=base&amp;type=banner&amp;sponsorid='. $banner["sponsorid"] .'" target="_blank">'. $code .'</a>';
    }
  
    $smarty->assign('MainBanner', $code);
    unset($code);
}

unset($file_name);
unset($old_file_name);
unset($banner);
