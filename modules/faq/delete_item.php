<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		delete_item.php
*	Module: 		FAQ
*	Main editor: 		Micheal@one-network.org
*	Last change: 		01.04.2003 13:58
*	Description: 		Removes FAQ Items
*	Remarks:
*
**************************************************************************/

switch ($_GET["step"]) {
    case 2:
        $get_caption = $db->qry_first("SELECT caption FROM %prefix%faq_item WHERE itemid = %int%", $_GET['itemid']);
        $caption = $get_caption["caption"];
    
        if ($caption != "") {
            $func->question(t('Bist du sicher, dass die Frage <b> %1 </b> löschen willst ?', $caption), "index.php?mod=faq&object=item&action=delete_item&itemid={$_GET['itemid']}&step=3", "index.php?mod=faq&object=cat&action=delete_cat");
        } else {
                $func->error(t('Diese Frage existiert nicht'));
        }

        break;
    
    case 3:
        $get_caption = $db->qry_first("SELECT caption FROM %prefix%faq_item WHERE itemid = %int%", $_GET['itemid']);
        $caption = $get_caption["caption"];
    
        if ($caption != "") {
            $del_item = $db->qry("DELETE FROM %prefix%faq_item WHERE itemid = %int%", $_GET['itemid']);
        
            if ($del_item == true) {
                $func->confirmation(t('Die Frage wurde erfolgreich gelöscht'), "index.php?mod=faq&object=cat&action=delete_cat");
            } else {
                $func->error("DB_ERROR");
            }
        } // close if caption
        
        else {
            $func->error(t('Diese Frage existiert nicht'));
        }
    
        break;
} // close switch step
