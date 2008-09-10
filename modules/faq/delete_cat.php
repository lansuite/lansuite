<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		delete_cat.php
*	Module: 		FAQ
*	Main editor: 		Micheal@one-network.org
*	Last change: 		29.03.2003 18:56
*	Description: 		Removes Faq Data
*	Remarks:
*
**************************************************************************/

switch($_GET["step"]) {
	
	default:


	$get_cat = $db->qry("SELECT catid, name FROM %prefix%faq_cat");

	$count_cat = $db->num_rows($get_cat);

	if($count_cat == 0) { $func->information(t('Keine Einträge vorhanden.'),"index.php?mod=home"); }

	else {

		$dsp->NewContent(t('FAQ löschen'),t('Auf dieser Seite sehen Sie häufig gestellte Fragen und deren Antworten. Die Fragen sind in verschiedene Kategorien eingeteilt, die Sie mit dem (+)-Symbol aufklappen können. Mit einem Klick auf die Löschen-Buttons können Sie die entsprechende Kategorie bzw. Frage löschen.'));
		if($_SESSION['menu_status']['faq'][$_GET['faqcatid']] == "open") {
			$_SESSION['menu_status']['faq'][$_GET['faqcatid']] = "closed";
		}else {
			$_SESSION['menu_status']['faq'][$_GET['faqcatid']] = "open";
		}

		while($row=$db->fetch_array($get_cat)) {

			$templ["faq"]["overview"]["row"]["cat"]["titel"]	= $row["name"];
			$templ["faq"]["overview"]["row"]["cat"]["link"]	= "index.php?mod=faq&action=show&faqcatid={$row['catid']}";
			$templ['faq']['overview']['row']['question']['change']['change']['link']	= $dsp->FetchButton("index.php?mod=faq&object=cat&action=delete_cat&catid={$row['catid']}&step=2","delete");

			$faq_content .= $dsp->FetchModTpl("faq","faq_overview_row_cat");

			if($_SESSION['menu_status']['faq'][$row['catid']] == "open") {

				$get_item = $db->qry("SELECT caption,itemid FROM %prefix%faq_item WHERE catid = %int%", $row['catid']);
				while($row=$db->fetch_array($get_item)) {

					$templ["faq"]["overview"]["row"]["question"]["title"]	= $func->text2html($row["caption"]);
					$templ["faq"]["overview"]["row"]["question"]["id"]	= $row["itemid"];
					$templ['faq']['overview']['row']['question']['change']['change']['link']	= $dsp->FetchButton("index.php?mod=faq&object=item&action=delete_item&itemid={$row['itemid']}&step=2","delete");
					$faq_content .= $dsp->FetchModTpl("faq","faq_overview_row_question");

				}//while
			}//if
		}//while

		$dsp->AddSingleRow($faq_content, "class='menu'");
		$dsp->AddContent();

	} // close else
	
	break;

	case 2: 
	
		$get_catname = $db->qry_first("SELECT name FROM %prefix%faq_cat WHERE catid = %int%", $_GET['catid']);
		
		if($get_catname["name"] != "") {
			
			$func->question(t('Sind Sie sicher, dass Sie die Kategorie  <b> %1 </b> und die darin enthaltenen Fragen wirklich löschen wollen?', $get_catname['name']),"index.php?mod=faq&object=cat&action=delete_cat&catid={$_GET['catid']}&step=3","index.php?mod=faq&object=cat&action=delete_cat");
		}
		
			else {
				$func->error(t('Diese Kategorie existiert nicht'),"");	
			}	
	
	break;
	
	case 3:
		$get_catname = $db->qry_first("SELECT name FROM %prefix%faq_cat WHERE catid = %int%", $_GET['catid']);
		
		if($get_catname["name"] != "") {
			
			$del_cat = $db->qry("DELETE FROM %prefix%faq_cat WHERE catid = %int%", $_GET['catid']);
			$del_item = $db->qry("DELETE FROM %prefix%faq_item WHERE catid = %int%", $_GET['catid']);
			
			if($del_cat == true && $del_item == true) {
				
				$func->confirmation(t('Die Kategorie wurde erfolgreich gelöscht'),"index.php?mod=faq&object=cat&action=delete_cat");
			}
			
				else {
				
					$func->error("DB_ERROR","");
				}
		
		} //if
		
			else {	
				
				$func->error(t('Diese Kategorie existiert nicht'),"");
			}	
	
	break;

} // close switch step
?>