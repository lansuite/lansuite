<?php

$HANDLE["POLLID"] = $_GET["pollid"];
$HANDLE["STEP"]   = $_GET["step"];

switch ($HANDLE["STEP"]) {
    default:
        include_once('modules/poll/search.inc.php');
        break;

    case 2:
            $POLL = $database->queryWithOnlyFirstRow("
              SELECT caption
              FROM %prefix%polls
              WHERE pollid = ?", [$HANDLE['POLLID']]);

        if (isset($POLL['caption'])) {
            $func->question(t('Wollen sie den Poll <b>%1</b> wirklich l&ouml;schen?', $POLL['caption']), "index.php?mod=poll&action=delete&step=3&pollid=" . $HANDLE["POLLID"], "index.php?mod=poll&action=delete");
        } else {
            $func->error(t('Dieser Poll existiert nicht'), "index.php?mod=poll&action=delete");
        }
        break;

    case 3:
        $POLL = $database->queryWithOnlyFirstRow("SELECT caption FROM %prefix%polls WHERE pollid = ?", [$HANDLE['POLLID']]);

        if (isset($POLL['caption'])) {
            $DELETE = $database->query("DELETE FROM %prefix%polls WHERE pollid = ?", [$HANDLE['POLLID']]);
            if ($DELETE) {
                $func->confirmation(t('Der Poll <b>%1</b> wurde gel&ouml;scht', $POLL['caption']), "index.php?mod=poll&action=delete");
            }
        } else {
            $func->error(t('Dieser Poll existiert nicht'), "index.php?mod=poll&action=delete");
        }
        break;
}
