<?php

$stepParameter = $_GET["step"] ?? 0;
switch ($stepParameter) {
    default:
        include_once('modules/server/search.inc.php');
        break;
    
    case 2:
        $server = $database->queryWithOnlyFirstRow("SELECT caption FROM %prefix%server WHERE serverid = ?", [$_GET["serverid"]]);
        $servername = $server["caption"];

        if ($server) {
            $func->question(t('Wollen sie den Server <b>%1</b> wirklich l&ouml;schen?', $servername), "index.php?mod=server&action=delete&step=3&serverid={$_GET["serverid"]}", "index.php?mod=server&action=delete");
        } else {
            $func->error(t('Dieser Server existiert nicht'), "index.php?mod=server&action=delete");
        }
        break;

    case 3:
        $server = $database->queryWithOnlyFirstRow("
          SELECT
            caption,
            owner
          FROM %prefix%server
          WHERE
            serverid = ?", [$_GET["serverid"]]);

        if ($server) {
            if ($server["owner"] != $auth["userid"] and $auth['type'] <= \LS_AUTH_TYPE_USER) {
                $func->information(t('Nur der Besitzer und Administratoren d&uuml;rfen diese Aktion ausf&uuml;hren'), "index.php?mod=server&action=delete");
            } else {
                $delete = $database->query("DELETE FROM %prefix%server WHERE serverid = ?", [$_GET["serverid"]]);
            
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
