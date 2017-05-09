<?php
switch ($_GET["step"]) {
    default:
        include_once('modules/troubleticket/search.inc.php');
        break;


    case 2:
        include_once('modules/usrmgr/search_main.inc.php');
    
        $ms2->query['where'] .= "u.type > 1";
        if ($auth['type'] >= 2) {
            $ms2->AddIconField('assign', 'index.php?mod=troubleticket&action=assign&step=3&ttid='.$_GET['ttid'] .'&userid=', 'Assign');
        }
    
        $ms2->PrintSearch('index.php?mod=troubleticket&action=assign&step=2&ttid='. $_GET['ttid'], 'u.userid');
        break;

    case 3:
        // Variabeln zuweisen
        $tt_id = $_GET["ttid"];
        $t_userid = $_GET["userid"];
        $zeit = time();

        // aktuelles Ticket laden
        $get_ticket = $db->qry_first("SELECT target_userid, caption FROM %prefix%troubleticket WHERE ttid = %int%", $tt_id);
        $tt_caption = $get_ticket["caption"];
        $target_userid_old = $get_ticket["target_userid"];

        // Zuweisen, Status setzen, Comment setzen, Zeiten setzen, assign_by setzen, old_target_user setzen
        $assign_ticket = $db->qry("UPDATE %prefix%troubleticket SET target_userid = %int%,
    target_userid_old = %int%,
    status = '2',
    publiccomment = '',
    verified = %int%,
    assignby_userid = %int%
    WHERE ttid = %int%", $t_userid, $target_userid_old, time(), $auth["userid"], $tt_id);

        // Wenn Update erfolgreich folgende Funktionen ausführen
        if ($assign_ticket) {
            // Infobox Messages erstellen bzw. ggf. löschen
            $db->qry('DELETE FROM %prefix%infobox WHERE id_in_class = %int% AND class = \'troubleticket\'', $tt_id);
            $func->setainfo(t('dir wurde das Troubleticket "<b>%1</b>"zugewiesen. ', $tt_caption), $t_userid, 1, "troubleticket", $tt_id);
            // Bestätigung ausgeben
            $func->confirmation(t('Das ausgewählte Ticket wurde dem Orga zugewiesen.'), "index.php?mod=troubleticket&action=assign");
        } else {
            $func->error(t('Das Troubleticket konnte nicht zugewiesen werden! Problem mit der Datenbank !'), "index.php?mod=troubleticket&action=assign");
        }
        break;
}
