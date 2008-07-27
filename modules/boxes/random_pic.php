<?php
/**
 * Show Random-Pic from Picturegalery
 *
 * @package lansuite_core
 * @author maztah, bytekilla, knox
 * @version $Id$
 * @todo Show picture without Comments
 */
$pics_res = $db->qry_first('SELECT picid, name, caption FROM %prefix%picgallery WHERE name != \'\' ORDER BY RAND()');

if ($pics_res['picid']) {
    // Make Data
    $pics_comrow = $db->qry_first('SELECT COUNT(commentid) AS comments FROM %prefix%comments WHERE relatedto_item = \'Picgallery\' AND relatedto_id = %int% ', $pics_res[picid]);
    $pic_array = explode("/", $pics_res['name']);
    $pic_name = $pic_array[sizeof($pic_array)-1];
    $pic_thumbname = "lsthumb_".$pic_name;
    $pic_neuname = str_replace($pic_name, $pic_thumbname, $pics_res['name']);
    $pic_namelink ='<img src="ext_inc/picgallery/'.$pic_neuname.'" />';
    // Fill Template and make output
    if ($pics_res['caption']) $box->DotRow($pics_res['caption']);
    $box->EmptyRow();
    $templ['randompic']['tag'] = $pics_res['caption'];
    $templ['randompic']['thumblink'] = $pic_neuname;
    $templ['randompic']['link'] = $pics_res['name'];
    $box->AddTemplate("box_randompic");
    if ($pics_comrow['comments'] == 1) $box->EngangedRow($pics_comrow['comments']." ".t("Kommentar"));
    else if ($pics_comrow['comments'] > 1) $box->EngangedRow($pics_comrow['comments']." ".t("Kommentare"));
}
?>
