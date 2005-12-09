<?php
/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		showfood.php
*	Module: 		catering
*	Main editor: 		fritzsche@emazynx.de
*	Last change: 		24.05.2003
*	Description: 		Show all available food
*	Remarks: 		none
*
**************************************************************************/
if ($_GET["action"]=="show" || $_GET["action"]=="") {

	$templ["catering"]["overview"]["case"]["info"]["page_title"] = "Catering";
	$templ["catering"]["overview"]["case"]["info"]["page_description"] = "Auf dieser Seite sehen Sie
					eine Liste der Lieferanten, bei denen Sie bestellen können. Die Speisen/Getränke sind bei jedem Lieferanten 
					in verschiedene Kategorien eingeteilt, die Sie mit dem +-Symbol aufklappen können.";

	$get_catid = $db->query("SELECT id FROM {$config["tables"]["catering_supplier"]}");

	while($row=$db->fetch_array($get_catid)) {
		$test_catid  = $row["id"];
	}//while

	if($test_catid == "") { 
		$func->no_items("Fragen/Kategorien","","rlist"); 
	}
	else {
		if($_SESSION['menu_status']['faq'][$_GET[faqcatid]] == "open") {
			$_SESSION['menu_status']['faq'][$_GET[faqcatid]] = "closed";
		}
				
			else {
			
				$_SESSION['menu_status']['faq'][$_GET[faqcatid]] = "open";
			}	
		
		$get_cat = $db->query("SELECT id, contact FROM {$config["tables"]["catering_supplier"]}");
		
			while($row=$db->fetch_array($get_cat)) {
		
				$templ["catering"]["overview"]["row"]["cat"]["titel"]	= $row["contact"];
				$templ["catering"]["overview"]["row"]["cat"]["link"]	= "index.php?mod=catering&action=show&faqcatid={$row['id']}";
		
				eval("\$templ['catering']['overview']['case']['rows'] .= \"". $func->gettemplate("catering_overview_row_suppl")."\";");
		
					if($_SESSION['menu_status']['faq'][$row['id']] == "open") {
		
						$get_item = $db->query("SELECT id, name, wizzard FROM {$config["tables"]["catering_foodgroups"]}
													WHERE supplID = '{$row['id']}'");
						$templ["catering"]["overview"]["row"]["question"]["supplid"]	= $row["id"];
						while($row=$db->fetch_array($get_item)) {
		
								$templ["catering"]["overview"]["row"]["question"]["title"]	= $func->text2html($row["name"]);
								$templ["catering"]["overview"]["row"]["question"]["id"]	= $row["id"];
								$templ["catering"]["overview"]["row"]["question"]["wizzard"]	= $row["wizzard"];
											eval("\$templ['catering']['overview']['case']['rows'] .= \"". $func->gettemplate("catering_overview_row_group")."\";");
		
							}//while
					}//if
			}//while
		
		eval("\$templ['index']['info']['content'] .= \"". $func->gettemplate("catering_overview_case")."\";");
		
		} // close else
}//if
?>
