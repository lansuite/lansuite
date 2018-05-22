<?php
/**
 * Show sponsor pictures
 */

$box->DotRow(t('Wir danken').':');
if (!$cfg["sponsor_picwidth"]) {
    $cfg["sponsor_picwidth"] = 120;
}

$sponsoren = $db->qry("
  SELECT *
  FROM %prefix%sponsor
  WHERE active
  ORDER BY pos, sponsorid");

$db->qry('UPDATE %prefix%sponsor SET views_box = views_box + 1 WHERE active');
while ($sponsor = $db->fetch_array($sponsoren)) {
    $out = '';

    // If entry is HTML-Code
    if (substr($sponsor['pic_path_button'], 0, 12) == 'html-code://') {
        $out = $func->AllowHTML(substr($sponsor["pic_path_button"], 12, strlen($sponsor["pic_path_button"]) - 12));

    // Else add Image-Tag
    } else {
        $file_name = $sponsor['pic_path_button'];
        if ($file_name != '') {
            if (is_file($file_name)) {
                $ImgSize = GetImageSize($file_name);
            }
            if (!$ImgSize[0]) {
                $ImgSize[0] = $cfg["sponsor_picwidth_small"];
            }
            if ($ImgSize[0] > $cfg["sponsor_picwidth_small"]) {
                $ImgSize[0] = $cfg["sponsor_picwidth_small"];
            }
            $out = "<img src=\"$file_name\" width=\"{$ImgSize[0]}\" style=\"max-width:{$cfg['sponsor_picwidth_small']}px;\" border=\"0\" alt=\"{$sponsor["name"]}\" title=\"{$sponsor["name"]}\" />";
        } elseif ($sponsor["name"] != '') {
            $out = "<b>{$sponsor["name"]}</b>";
        }

        if ($out and $sponsor["url"] != '' and $sponsor["url"] != "http://") {
            $out = "<a href=\"index.php?mod=sponsor&amp;action=bannerclick&amp;design=base&amp;type=box&amp;sponsorid={$sponsor["sponsorid"]}\" target=\"_blank\">$out</a>";
        }
    }
    if ($out != '') {
        $box->Row($out."<br /><br />");
    }
}
$db->free_result($sponsoren);
