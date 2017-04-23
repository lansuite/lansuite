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

switch ($_GET["step"]) {
    default:
        include('show.php');
        break;

    case 2:
        $get_catname = $db->qry_first("SELECT name FROM %prefix%faq_cat WHERE catid = %int%", $_GET['catid']);
        
        if ($get_catname["name"] != "") {
            $func->question(t('Bist du sicher, dass du die Kategorie  <b> %1 </b> und die darin enthaltenen Fragen wirklich löschen willst?', $get_catname['name']), "index.php?mod=faq&object=cat&action=delete_cat&catid={$_GET['catid']}&step=3", "index.php?mod=faq&object=cat&action=delete_cat");
        } else {
                $func->error(t('Diese Kategorie existiert nicht'));
        }
    
        break;
    
    case 3:
        $get_catname = $db->qry_first("SELECT name FROM %prefix%faq_cat WHERE catid = %int%", $_GET['catid']);
        
        if ($get_catname["name"] != "") {
            $del_cat = $db->qry("DELETE FROM %prefix%faq_cat WHERE catid = %int%", $_GET['catid']);
            $del_item = $db->qry("DELETE FROM %prefix%faq_item WHERE catid = %int%", $_GET['catid']);
            
            if ($del_cat == true && $del_item == true) {
                $func->confirmation(t('Die Kategorie wurde erfolgreich gelöscht'), "index.php?mod=faq&object=cat&action=delete_cat");
            } else {
                $func->error("DB_ERROR");
            }
        } //if
        
        else {
            $func->error(t('Diese Kategorie existiert nicht'));
        }
    
        break;
} // close switch step
