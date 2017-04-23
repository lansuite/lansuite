<?php
switch ($_GET["step"]) {
    default:
        include_once('modules/server/search.inc.php');
        break;
    
    case 2:
        $server = $db->qry_first("SELECT caption FROM %prefix%server WHERE serverid = %int%", $_GET["serverid"]);
        
        $servername = $server["caption"];

        if ($server) {
            $func->question(t('Wollen sie den Server <b>%1</b> wirklich l&ouml;schen?', $servername), "index.php?mod=server&action=delete&step=3&serverid={$_GET["serverid"]}", "index.php?mod=server&action=delete");
        } else {
            $func->error(t('Dieser Server existiert nicht'), "index.php?mod=server&action=delete");
        }
        break;
    
    
    case 3:
        $server = $db->qry_first("SELECT caption, owner FROM %prefix%server
  WHERE serverid = %int%", $_GET["serverid"]);

        if ($server) {
            if ($server["owner"] != $auth["userid"] and $auth["type"] <= 1) {
                $func->information(t('Nur der Besitzer und Administratoren d&uuml;rfen diese Aktion ausf&uuml;hren'), "index.php?mod=server&action=delete");
            } else {
                $delete = $db->qry("DELETE FROM %prefix%server WHERE serverid = %int%", $_GET["serverid"]);
            
                $servername = $server["caption"];
                if ($delete) {
                    $func->confirmation(t('Der Server <b>%1</b> wurde gel&ouml;scht', $servername), "index.php?mod=server&action=delete");
                }
            }
        } else {
            $func->error(t('Dieser Server existiert nicht'), "index.php?mod=server&action=delete");
        }
        break;
}
