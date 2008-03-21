<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	 2.0
*	File Version:		 2.0
*	Filename: 		show.php
*	Module: 		FAQ
*	Main editor: 		micheal@one-network.org
*	Last change: 		29.03.2003 18:56
*	Description: 		Prints all FAQ-Data
*	Remarks:
*
**************************************************************************/


$get_cat = $db->query("SELECT catid, name FROM {$config["tables"]["faq_cat"]} ORDER BY name");

$count_cat = $db->num_rows($get_cat);

if($count_cat == 0) { $func->information(t('Keine Einträge vorhanden.'),"index.php?mod=home"); }

	else {
			
		$dsp->NewContent(t('FAQ'),t('Auf dieser Seite sehen Sie häufig gestellte Fragen und deren Antworten. Die Fragen sind in verschiedene Kategorien eingeteilt, die Sie mit dem /\'/+/\'/-Symbol aufklappen können.'));
		if ($_SESSION['menu_status']['faq'][$_GET['faqcatid']] == "closed") {
			$_SESSION['menu_status']['faq'][$_GET['faqcatid']] = "open";
		} else {
			$_SESSION['menu_status']['faq'][$_GET['faqcatid']] = "closed";
		}	
		
		while($row=$db->fetch_array($get_cat)) {
		
			$templ["faq"]["overview"]["row"]["cat"]["titel"]	= $row["name"];
			$templ["faq"]["overview"]["row"]["cat"]["link"]	= "index.php?mod=faq&action=show&faqcatid={$row['catid']}";
		
			$faq_content .= $dsp->FetchModTpl("faq","faq_overview_row_cat");
		
				if($_SESSION['menu_status']['faq'][$row['catid']] == '' or $_SESSION['menu_status']['faq'][$row['catid']] == "open") {
		
					$get_item = $db->query("SELECT caption,itemid FROM {$config["tables"]["faq_item"]}
													WHERE catid = '{$row['catid']}'
                          ORDER BY caption
                          ");
						while($row=$db->fetch_array($get_item)) {
		
							$templ["faq"]["overview"]["row"]["question"]["title"]	= $func->text2html($row["caption"]);
							$templ["faq"]["overview"]["row"]["question"]["id"]	= $row["itemid"];
							$faq_content .= $dsp->FetchModTpl("faq","faq_overview_row_question");
		
						}//while
				}//if
		}//while
		
		$dsp->AddSingleRow($faq_content, "class='menu'");
		$dsp->AddContent();
		
	} // close else
?>
