<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.1
*	Filename: 			join.php
*	Module: 			Tournamentsystem
*	Main editor: 		jochen@one-network.org
*	Last change: 		26.04.2004
*	Description: 		Undo status changing
*	Remarks:
*
**************************************************************************/

$tournamentid    = $_GET["tournamentid"];

if ($tournamentid == "") {
    $func->error(t('Das ausgewählte Turnier existiert nicht'), "index.php?mod=tournament2");
} else {
    $tournament = $db->qry_first("SELECT status, teamplayer, name, mode, blind_draw FROM %prefix%tournament_tournaments WHERE tournamentid = %int%", $tournamentid);

    switch ($_GET["action"]) {
        case "undo_generate":
            switch ($_GET["step"]) {
                default:
                    $func->question(t('Bist du sicher, dass du das generieren rückgängig machen wilst? Alle Paarungen und alle bereits eingetragenen Ergebnisse dieses Turnieres werden dabei gelöscht! Bei bereits beendeten Turnieren geht dadurch außerdem die Rangliste verloren!'), "index.php?mod=tournament2&action=undo_generate&step=2&tournamentid=$tournamentid", "index.php?mod=tournament2&action=details&tournamentid=$tournamentid&headermenuitem=1");
                    break;

                case 2:
                    ## Blind-Draw Teas auflösen
                    if ($tournament["blind_draw"]) {
                        $bd_teams = $db->qry("SELECT * FROM %prefix%t2_teammembers WHERE tournamentid = %int%", $_GET["tournamentid"]);
                        while ($bd_team = $db->fetch_array($bd_teams)) {
                            $leader = $db->qry_first("SELECT username FROM %prefix%user WHERE userid = %int%", $bd_team["userid"]);
                            $db->qry("INSERT INTO %prefix%t2_teams 
        SET tournamentid = %int%,
        name = %string%,
        leaderid = %int%
        ", $_GET["tournamentid"], $leader["username"], $bd_team["userid"]);
                            $db->qry("DELETE FROM %prefix%t2_teammembers WHERE teamid = %int% AND userid = %int%", $bd_team["teamid"], $bd_team["userid"]);
                        }
                    }

                    $db->qry("DELETE FROM %prefix%t2_games WHERE tournamentid = %int%", $tournamentid);
                    $db->qry("UPDATE %prefix%tournament_tournaments SET status='open' WHERE tournamentid = %int%", $tournamentid);

                    $func->confirmation(t('Das Turnier \'%1\' wurde erfolgreich zurückgesetzt', $tournament["name"]), "index.php?mod=tournament2&action=details&tournamentid=$tournamentid");
                    $func->log_event(t('Das Generieren des Turnieres \'%1\' wurde rückgängig gemacht', $tournament["name"]), 1, t('Turnier Verwaltung'));
                    break;
            }
            break;

        case "undo_close":
            $db->qry("UPDATE %prefix%tournament_tournaments SET status='process' WHERE tournamentid = %int%", $tournamentid);

            $func->confirmation(t('Der Status wurde wieder auf \'wird gespielt\' gesetzt. Das Turnier wird wieder beendet, sobald du das nächste Ergebniss eingetragen hast.'), "index.php?mod=tournament2&action=details&tournamentid=$tournamentid");
            $func->log_event(t('Der Status wurde wieder auf \'wird gespielt\' gesetzt'), 1, t('Turnier Verwaltung'));
            break;
    }
}
