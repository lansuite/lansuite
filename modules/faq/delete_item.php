<?php

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
        } else {
            $func->error(t('Diese Frage existiert nicht'));
        }
    
        break;
}
