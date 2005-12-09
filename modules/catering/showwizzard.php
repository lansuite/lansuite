<?php

	//**************************************
	// Config wird nicht gelesen deshalb:
	//**************************************
	$config['catering']['currency']="EUR";

	$fgres = $db->query("SELECT * FROM {$config["tables"]["catering_foodgroups"]} WHERE supplID = ".$_GET["suppl"]." ORDER BY name");
	$templ['catering']['show']['row']['info']['foodgrps']="<a href=\"index.php?mod=catering&action=showgrp&wizzard=0&suppl=".$_GET["suppl"]."&grp=x\">Alle</a>";
	$i=0;
	while($row=$db->fetch_array($fgres)) {
		if ($i==0 && $gotgrp=="") { 
			$grpToShow = "grpID=\"".$row["ID"]."\" AND"; 
			$gotgrp = $row["ID"];
			$gotwiz = $row["wizzard"];
			} else $grpToShow = "";
		if ($row["ID"]==$gotgrp)
			$templ['catering']['show']['row']['info']['foodgrps'] .= " &nbsp;|&nbsp; <a href=\"index.php?mod=catering&action=showgrp&wizzard=".$row["wizzard"]."&suppl=".$_GET["suppl"]."&grp=".$row["ID"]."\"><b>".$row["name"]."</b></a>";
		else
			$templ['catering']['show']['row']['info']['foodgrps'] .= " &nbsp;|&nbsp; <a href=\"index.php?mod=catering&action=showgrp&wizzard=".$row["wizzard"]."&suppl=".$_GET["suppl"]."&grp=".$row["ID"]."\">".$row["name"]."</a>";
		$i++;
	}
	
	$get_wiz_food = $db->query("SELECT ID FROM {$config["tables"]["catering_foods"]} WHERE supplID=".$_GET["suppl"]." AND grpID=".$_GET["grp"]);
	$row=$db->fetch_array($get_wiz_food);
	$gotfood = $row["ID"];
	$get_wiz_adds = $db->query("SELECT * FROM {$config["tables"]["catering_wizzard"]} WHERE foodID=$gotfood AND master='y'");
	$master=$db->fetch_array($get_wiz_adds);
	
	


	$templ['catering']['show']['row']['info']['title'] = $master["title"];
	
	$typ="radio";
	$block="master";
	$wert=$master["ID"];
	$price_s=$master['price_s'];
	$price_m=$master['price_m'];
	$price_l=$master['price_l'];
	$price_p=$master['price_p'];
	if($master["size_s"]=="1") $templ['catering']['show']['row']['info']['text'] .= "<input onclick=\"setSize(this);\" name=\"base\" type=\"".$typ."\" block=\"".$block."\" class=\"".$class."\" art_size=\"s\" price_s=\"".$price_s."\" price_m=\"\" price_l=\"\" price_p=\"\" value=\"".$wert."/s\"> klein<br />\n";
	if($master["size_m"]=="1") $templ['catering']['show']['row']['info']['text'] .= "<input onclick=\"setSize(this);\" name=\"base\" type=\"".$typ."\" block=\"".$block."\" class=\"".$class."\" art_size=\"m\" price_s=\"\" price_m=\"".$price_m."\" price_l=\"\" price_p=\"\" value=\"".$wert."/m\"> mittel<br />\n";
	if($master["size_l"]=="1") $templ['catering']['show']['row']['info']['text'] .= "<input onclick=\"setSize(this);\" name=\"base\" type=\"".$typ."\" block=\"".$block."\" class=\"".$class."\" art_size=\"l\" price_s=\"\" price_m=\"\" price_l=\"".$price_l."\" price_p=\"\" value=\"".$wert."/l\"> groﬂ<br />\n";
	if($master["size_p"]=="1") $templ['catering']['show']['row']['info']['text'] .= "<input onclick=\"setSize(this);\" name=\"base\" type=\"".$typ."\" block=\"".$block."\" class=\"".$class."\" art_size=\"p\" price_s=\"\" price_m=\"\" price_l=\"\" price_p=\"".$price_p."\" value=\"".$wert."/p\"> Pfannenpizza<br />\n";

	$templ['catering']['show']['row']['info']['price'] = "0.00";
	$templ['catering']['show']['row']['info']['pricetag'] = "price_master";
	eval("\$templ['catering']['show']['case']['control']['rows'] .= \"". $func->gettemplate("catering_show_wiz_master_row")."\";");

	$td_class="tbl_1";

	$templ['catering']['show']['row']['info']['title']="";
	$templ['catering']['show']['row']['info']['text_links']="";
	$templ['catering']['show']['row']['info']['text_rechts']="";
//	$templ['catering']['show']['row']['info']['price']="";

	$templ['cateringwizzard']['show']['javascript']="var test=0;\n";

	$pre_block="x";
	$tab_count="0";
	$right_tab="";
	$left_tab="";
	
	$addarrow = 'design/'.$GLOBALS[auth][design].'/images/arrows_basket.gif';
	$foodid=$gotfood;
	
	if ($_SESSION["catering_user_order"]["$foodid"]["0"]=="") $_SESSION["catering_user_order"]["$foodid"]["0"]=0;
	if ($_SESSION["catering_user_order"]["$foodid"]["1"]=="") $_SESSION["catering_user_order"]["$foodid"]["1"]=0;
	if ($_SESSION["catering_user_order"]["$foodid"]["2"]=="") $_SESSION["catering_user_order"]["$foodid"]["2"]=0;
	if ($_SESSION["catering_user_order"]["$foodid"]["3"]=="") $_SESSION["catering_user_order"]["$foodid"]["3"]=0;
	
	$templ['catering']['show']['js']['var']['addarrow']=$addarrow;
	$templ['catering']['show']['js']['var']['foodid']=$gotfood;
	$templ['catering']['show']['js']['var']['wizzard']=$_GET['wizzard'];
	$templ['catering']['show']['js']['var']['suppl']=$gotsuppl;
	$templ['catering']['show']['js']['var']['grp']=$gotgrp;
	$templ['catering']['show']['js']['var']['title']="(offen:".$_SESSION["catering_user_order"]["{$foodid}"]["0"]." &nbsp;&nbsp; noch nicht bezahlt:".$_SESSION["catering_user_order"]["{$foodid}"]["3"]." &nbsp;&nbsp; bestellt:".$_SESSION["catering_user_order"]["{$foodid}"]["1"]." &nbsp;&nbsp; abgeschlossen:".$_SESSION["catering_user_order"]["{$foodid}"]["2"]."!)";
	eval("\$templ['index']['info']['content'] .= \"". $func->gettemplate("catering_show_wiz_js")."\";");

	$get_wiz_adds = $db->query("SELECT * FROM {$config["tables"]["catering_wizzard"]} WHERE foodID=$gotfood AND master='n' ORDER BY block ASC");

	while($add=$db->fetch_array($get_wiz_adds)) {
		if($add["size_s"]=="1"){$price_s=$add["price_s"];} else {$price_s="x";}
		if($add["size_m"]=="1"){$price_m=$add["price_m"];} else {$price_m="x";}
		if($add["size_l"]=="1"){$price_l=$add["price_l"];} else {$price_l="x";}
		if($add["size_p"]=="1"){$price_p=$add["price_p"];} else {$price_s="x";}
		$wert=$add["ID"];
		if($pre_block=="x"){
			$pre_block=$add["block"];
			if($add["sel_type"]=='c') {
				$typ="checkbox";
			}
			else if ($add["sel_type"]=='o') {
				$typ="radio";
			}
		}
		if($add["block"]==$pre_block){
			if($tab_count=="0"){
				if($typ=="checkbox"){$input_name="adds_c_" . $pre_block;}else{$input_name="adds_r_" . $pre_block;}
				$left_tab.="<input onclick=\"calcPreis(this);\" akt_sel=\"\" type=\"".$typ."\" name=\"".$input_name."\" block=\"".$pre_block."\" class=\"".$class."\" art_size=\"\" price_s=\"".$price_s."\" price_m=\"".$price_m."\" price_l=\"".$price_l."\" price_p=\"".$price_p."\" id=\"".$wert."\" value=\"".$wert."\"> ".$add["title"]."<br />\n";
				$tab_count="1";
			}
			else if($tab_count=="1"){
				if($typ=="checkbox"){$input_name="adds_c_" . $pre_block;}else{$input_name="adds_r_" . $pre_block;}
				$right_tab.="<input onClick=\"javascript: calcPreis(this)\" akt_sel=\"\" type=\"".$typ."\" name=\"".$input_name."\" block=\"".$pre_block."\" class=\"".$class."\" art_size=\"\" price_s=\"".$price_s."\" price_m=\"".$price_m."\" price_l=\"".$price_l."\" price_p=\"".$price_p."\" id=\"".$wert."\" value=\"".$wert."\"> ".$add["title"]."<br />\n";
				$tab_count="0";
			}
		}
		else {
			if($tab_count=="1"){$right_tab.="<br />&nbsp;";}
			$templ['catering']['show']['row']['info']['text_links']=$left_tab;
			$templ['catering']['show']['row']['info']['text_rechts']=$right_tab;
			$templ['catering']['show']['row']['info']['pricetag']="price_".$pre_block;
			$templ['catering']['show']['row']['info']['price']="0.00";
			eval("\$templ['catering']['show']['case']['control']['rows'] .= \"". $func->gettemplate("catering_show_wiz_row")."\";");
			$templ['catering']['show']['row']['info']['text_links']="";
			$templ['catering']['show']['row']['info']['text_rechts']="";
			$tab_count="0";
			$right_tab="";
			$left_tab="";
		$pre_block=$add["block"];
			if($add["sel_type"]=='c') {
				$typ="checkbox";
			}
			else if ($add["sel_type"]=='o') {
				$typ="radio";
			}
			if($tab_count=="0"){
				if($typ=="checkbox"){$input_name="adds_c_" . $pre_block;}else{$input_name="adds_r_" . $pre_block;}
				$left_tab.="<input onClick=\"javascript: calcPreis(this)\" akt_sel=\"\" type=\"".$typ."\" name=\"".$input_name."\" block=\"".$pre_block."\" class=\"".$class."\" art_size=\"\" price_s=\"".$price_s."\" price_m=\"".$price_m."\" price_l=\"".$price_l."\" price_p=\"".$price_p."\" id=\"".$wert."\" value=\"".$wert."\"> ".$add["title"]."<br />\n";
				$tab_count="1";
			}
			else if ($tab_count=="1"){
				if($typ=="checkbox"){$input_name="adds_c_" . $pre_block;}else{$input_name="adds_r_" . $pre_block;}
				$right_tab.="<input onClick=\"javascript: calcPreis(this)\" akt_sel=\"\" type=\"".$typ."\" name=\"".$input_name."\" block=\"".$pre_block."\" class=\"".$class."\" art_size=\"\" price_s=\"".$price_s."\" price_m=\"".$price_m."\" price_l=\"".$price_l."\" price_p=\"".$price_p."\" id=\"".$wert."\" value=\"".$wert."\"> ".$add["title"]."<br />\n";
				$tab_count="0";
			}
		}

	}//while
		
	$templ['catering']['show']['row']['info']['text_links']=$left_tab;
	$templ['catering']['show']['row']['info']['text_rechts']=$right_tab;
	$templ['catering']['show']['row']['info']['pricetag']="price_".$pre_block;
	$templ['catering']['show']['row']['info']['price']="0.00";
	eval("\$templ['catering']['show']['case']['control']['rows'] .= \"". $func->gettemplate("catering_show_wiz_row")."\";");

	$templ['catering']['show']['row']['info']['title']="";
	$templ['catering']['show']['row']['info']['text']="";
	$templ['catering']['show']['row']['info']['text_links']="";
	$templ['catering']['show']['row']['info']['text_rechts']="";
	$templ['catering']['show']['row']['info']['price']="";

	$addarrow = 'design/'.$GLOBALS[auth][design].'/images/arrows_basket.gif';
//	$templ['catering']['show']['row']['info']['addtoshop'] = "<a href=\"index.php?mod=catering&action=addtocart&foodid=".$gotfood."&suppl=$gotsuppl&wizzard=".$_GET["wizzard"]."&grp=$gotgrp\" name=\"cartlink\" title=\"(offen:".$_SESSION["catering_user_order"]["{$foodid}"]["0"]." &nbsp;&nbsp; noch nicht bezahlt:".$_SESSION["catering_user_order"]["{$foodid}"]["3"]." &nbsp;&nbsp; bestellt:".$_SESSION["catering_user_order"]["{$foodid}"]["1"]." &nbsp;&nbsp; abgeschlossen:".$_SESSION["catering_user_order"]["{$foodid}"]["2"]."!)\"><img border=\"0\" src=\"".$addarrow."\" vspace=\"2\" align=\"top\"></a>";
	$templ['catering']['show']['row']['info']['addtoshop'] = "<span id=\"cartlink\"></span>";

							
	eval("\$templ['catering']['show']['case']['control']['rows'] .= \"". $func->gettemplate("catering_show_wiz_price")."\";");
	eval("\$templ['index']['info']['content'] .= \"". $func->gettemplate("catering_show_wiz_case")."\";");
?>
