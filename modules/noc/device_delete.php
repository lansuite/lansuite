<?php

include_once("modules/noc/class_noc.php");
$noc = new noc();

switch ($_GET["step"]) {
    default:
    case 1:
        include_once('modules/noc/search.inc.php');
        break;
    
    case 2:
        $func->question(t('Willst du dieses Device wirklich l&ouml;schen?' . HTML_NEWLINE . '
				 				   Dadurch gehen alle (auch f&uuml;r die Statistik relevante) Informationen verloren'), "index.php?mod=noc&action=delete_device&step=3&deviceid=" . $_GET["deviceid"], "index.php?mod=noc");
         
        break;
    
    case 3:
        // DELETE 'em all....
        $check_device = $db->qry_first("SELECT id FROM %prefix%noc_devices WHERE id=%int%", $_GET["deviceid"]);
        
        if ($check_device["id"] == "") {
            $func->error(t('Das gew&auml;hlte Device existiert nicht'));
        } else {
            $del_query1 = $db->qry("DELETE FROM %prefix%noc_devices WHERE id = %int%", $_GET["deviceid"]);
            $del_query2 = $db->qry("DELETE FROM %prefix%noc_ports WHERE deviceid = %int%", $_GET["deviceid"]);

            if ($del_query1 && $del_query2) {
                $func->confirmation(t('Das Device wurde erfolgreich gel&ouml;scht.'));
            } else {
                $func->error(t('Das Device konnte nicht gel&ouml;scht werden.'));
            }
        }
        
        break;
}
