<?php
$templ['box']['rows'] = "";

$pics_row = $db->query_first("SELECT COUNT(p.picid) AS activepics FROM {$config["tables"]["picgallery"]} AS p");
		
if($pics_row["activepics"] > 0) 
{	
	$rowexisting = false;
	while($rowexisting == false)
	{
		$zufall = rand(1,$pics_row["activepics"]);		
		$pics_res = $db->query_first("SELECT p.picid, p.name, p.caption FROM {$config["tables"]["picgallery"]} AS p 
					    WHERE p.picid = $zufall");
	if($pics_res['name'] != "") $rowexisting = true;
	}
}

$pics_comrow = $db->query_first("SELECT COUNT(commentid) AS comments FROM {$config["tables"]["comments"]} WHERE relatedto_id = $pics_res[picid] ");
$pics_comments= $pics_comrow['comments']." Kommentare";

$pic_array = explode("/", $pics_res['name']);
$pic_name = $pic_array[sizeof($pic_array)-1];
$pic_thumbname = "lsthumb_".$pic_name;

$pic_neuname = str_replace($pic_name, $pic_thumbname, $pics_res['name']);

$pic_namelink ='<img src="ext_inc/picgallery/'.$pic_neuname.'">';

$box->DotRow($pics_res['caption']);
$box->EmptyRow();
$templ['randompic']['tag'] = $pics_res['caption'];
$templ['randompic']['thumblink'] = $pic_neuname;
$templ['randompic']['link'] = $pics_res['name'];
$box->AddTemplate("box_randompic");
$box->EngangedRow($pics_comments);
?>
