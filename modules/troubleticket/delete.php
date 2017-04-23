<?php // by denny@esa-box.de

switch ($_GET["step"]) {
    default:
        include_once('modules/troubleticket/search.inc.php');
        break;

    case 2:
        $tt_id = $_GET["ttid"];
        $func->question(t('Willst du das ausgewählte Troubleticket wirklich löschen?'), "index.php?mod=troubleticket&action=delete&step=3&ttid=$tt_id", "index.php?mod=troubleticket&action=delete");
        break;

    case 3:
        $tt_id = $_GET["ttid"];
        $del_ticket = $db->qry('DELETE FROM %prefix%troubleticket WHERE ttid = %int%', $tt_id);
        $db->qry('DELETE FROM %prefix%infobox WHERE id_in_class = %int% AND class = \'troubleticket\'', $tt_id);
        if ($del_ticket) {
            $func->confirmation(t('Das ausgewählte Ticket wurde gelöscht.'), "index.php?mod=troubleticket&action=delete");
        } else {
            $func->error(t('Das Troubleticket konnte nicht gelöscht werden! Problem mit der Datenbank!'), "index.php?mod=troubleticket&action=delete");
        }
        break;
}
