<?php
$templ['box']['rows'] = "";

$pics_res = $db->qry_first('SELECT picid, name, caption FROM %prefix%picgallery WHERE name != \'\' ORDER BY RAND()');

if ($pics_res['picid']) {
  $pics_comrow = $db->qry_first('SELECT COUNT(commentid) AS comments FROM %prefix%comments WHERE relatedto_item = \'Picgallery\' AND relatedto_id = %int% ', $pics_res[picid]);
  $pics_comments= $pics_comrow['comments']." Kommentare";
  
  $pic_array = explode("/", $pics_res['name']);
  $pic_name = $pic_array[sizeof($pic_array)-1];
  $pic_thumbname = "lsthumb_".$pic_name;
  
  $pic_neuname = str_replace($pic_name, $pic_thumbname, $pics_res['name']);
  
  $pic_namelink ='<img src="ext_inc/picgallery/'.$pic_neuname.'" />';
  
  $box->DotRow($pics_res['caption']);
  $box->EmptyRow();
  $templ['randompic']['tag'] = $pics_res['caption'];
  $templ['randompic']['thumblink'] = $pic_neuname;
  $templ['randompic']['link'] = $pics_res['name'];
  $box->AddTemplate("box_randompic");
  $box->EngangedRow($pics_comments);
}
?>
