<?php
/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		1.0
*	Filename: 		show.php
*	Module: 		infocat
*	Original by: 		fritzsche@emazynx.de
*	Changes by: 		magic_erwin@gmx.de
*	Last change: 		02.07.2004 01:35
*	Description: 		Show all available infos
*	Remarks: 		none
*
**************************************************************************/

$gotgrp = $_GET["grp"];
$category = $_GET["category"];

if ($_GET["action"]=="sortorderup") {
	$db->query("UPDATE {$config["tables"]["infocat"]} SET sortorder=sortorder-1 WHERE infocatID='".$_GET["infoID"]."' AND category='".$category."'");
}

if ($_GET["action"]=="sortorderdown") {
	$db->query("UPDATE {$config["tables"]["infocat"]} SET sortorder=sortorder+1 WHERE infocatID='".$_GET["infoID"]."' AND category='".$category."'");
}


$fgres = $db->query("SELECT DISTINCT `infogroup` FROM {$config["tables"]["infocat"]} WHERE category='".$category."' ORDER BY `infogroup` ASC");
$templ['infocat']['show']['row']['info']['infogrps']="<a href=\"?mod=infocat&action=show&category=".$category."&grp=x\">Alle</a>";
$i=0;
while($row=$db->fetch_array($fgres)) {
	if ($i==0 && $gotgrp=="") { 
		$grpToShow = "WHERE `infogroup` = \"".$row["infogroup"]."\""; 
		$gotgrp = $row["infogroup"];
	}
	if ($row["infogroup"]==$gotgrp)
		$templ['infocat']['show']['row']['info']['infogrps'] .= " &nbsp;|&nbsp; <a href=\"?mod=infocat&action=show&category=".$category."&grp=".$row["infogroup"]."\"><b>".$row["infogroup"]."</b></a>";
	else
		$templ['infocat']['show']['row']['info']['infogrps'] .= " &nbsp;|&nbsp; <a href=\"?mod=infocat&action=show&category=".$category."&grp=".$row["infogroup"]."\">".$row["infogroup"]."</a>";
	$i++;
}

if ($gotgrp!="") {
	if ($gotgrp=="x")
		$grpToShow="WHERE 1  AND category='".$category."'";
	else
		$grpToShow="WHERE `infogroup` = \"".$gotgrp."\" AND category='".$category."'";
} else {
	$grpToShow="WHERE 1  AND category='".$category."'";
}



$get_foods = $db->query("SELECT * FROM {$config["tables"]["infocat"]} $grpToShow ORDER BY sortorder,title");

while($row=$db->fetch_array($get_foods)) {
	$templ['infocat']['show']['row']['info']['title']  	= $func->text2html($row["title"]);
	$infoID 						= $row["infocatID"];
	$templ['infocat']['show']['row']['info']['picture']	= $row["picture"];	
	$descr						  	= $row["descr"];
	$templ['infocat']['show']['row']['info']['descr'] 	= $func->text2html(chop($descr))."<br>";
	$templ['infocat']['show']['row']['info']['text1']	= $row["text1"];


	// SET ADMIN FUNCTION ONLY BUTTONS
	if($_SESSION["auth"]["type"] > 1) {
		$uparrow = 'design/'.$GLOBALS[auth][design].'/images/arrows_orderby_desc.gif';
		$downarrow = 'design/'.$GLOBALS[auth][design].'/images/arrows_orderby_asc.gif';
		$templ['infocat']['show']['row']['control']['buttons']['updown'] = "<table width=\"20\"><tr><td><a href=\"?mod=infocat&action=sortorderup&category=".$category."&infoID=".$infoID."&grp=$gotgrp\"><img border=\"0\" src=\"".$uparrow."\"></a></td><td>sortorder:</td></tr><tr><td><a href=\"?mod=infocat&action=sortorderdown&category=".$category."&infoID=".$infoID."&grp=$gotgrp\"><img border=\"0\"src=\"".$downarrow."\"></a></td><td>".$row["sortorder"]."</td></tr></table>";
		if ($row["active"]=="1") 
			$templ['infocat']['show']['row']['control']['buttons']['edit'] = "<td width=\"150\"><a href=\"?mod=infocat&action=lock&category=".$category."&infoID=" . $infoID ."&grp=$gotgrp\"  title=\"Artikel sperren!\"><img border=\"0\" src=\"design/" . $_SESSION["auth"]["design"] . "/images/arrows_info.gif\" vspace=\"1\"></a>&nbsp;";
		else 
			$templ['infocat']['show']['row']['control']['buttons']['edit'] = "<td width=\"150\"><a href=\"?mod=infocat&action=unlock&category=".$category."&infoID=" . $infoID ."&grp=$gotgrp\"  title=\"Artikel entsperren!\"><img border=\"0\" src=\"design/" . $_SESSION["auth"]["design"] . "/images/arrows_delete.gif\"  vspace=\"1\"></a>&nbsp;";
		$templ['infocat']['show']['row']['control']['buttons']['edit'] .= "<a href=\"?mod=infocat&action=change&category=".$category."&infoID=" . $infoID ."&grp=$gotgrp\"><img border=\"0\" src=\"design/" . $_SESSION["auth"]["design"] . "/images/buttons_edit.gif\"></a>&nbsp;";
		$templ['infocat']['show']['row']['control']['buttons']['delete'] = "<a href=\"?mod=infocat&action=delete&category=".$category."&infoID=" . $infoID ."&grp=$gotgrp\"><img border=\"0\" src=\"design/" . $_SESSION["auth"]["design"] . "/images/buttons_delete.gif\"></a></td>";
	} else {
		$templ['infocat']['show']['row']['control']['buttons']['edit'] = "<td width=\"0\"><!--admin-->";
		$templ['infocat']['show']['row']['control']['buttons']['delete'] = "</td>";
	}

	if ($row["picture"]=="") $row["picture"]="nopic.gif";
	$templ['infocat']['show']['row']['info']['picture'] = "<img src=\"/ext_inc/infocat/".$row["picture"]."\" border=\"0\">";

				
	eval("\$templ['infocat']['show']['case']['control']['rows'] .= \"". $func->gettemplate("infocat_show_row")."\";");

} // CLOSE WHILE

$templ['infocat']['show']['row']['info']['category'] =  $lang["infocat"]["category"][$category];

$templ['infocat']['show']['row']['info']['category_page_title'] =  $lang["infocat"]["page_title"][$category];

eval("\$templ['index']['info']['content'] .= \"". $func->gettemplate("infocat_show_case")."\";");


?>