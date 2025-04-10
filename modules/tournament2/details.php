<?php

$mail = new \LanSuite\Module\Mail\Mail();
$seat2 = new \LanSuite\Module\Seating\Seat2();

$tfunc = new \LanSuite\Module\Tournament2\TournamentFunction($mail, $seat2);

$headermenuitem = $_GET['headermenuitem'] ?? '';

if ($headermenuitem == "") {
    $headermenuitem = 1;
}

$tournamentQuery = '
SELECT
    `t`.`tournamentid`,
    `t`.`name`,
    `t`.`game`,
    `t`.`version`,
    `t`.`maxteams`,
    `t`.`teamplayer`,
    `t`.`duration`,
    `t`.`round`,
    `t`.`comment`,
    `t`.`rules`,
    `t`.`rules_ext`,
    `t`.`icon`,
    `t`.`mode`,
    `t`.`status`,
    `t`.`over18`,
    `t`.`groupid`,
    `t`.`coins`,
    `t`.`max_games`,
    `t`.`game_duration`,
    `t`.`break_duration`,
    `t`.`defwin_on_time_exceed`,
    `t`.`blind_draw`,
    `t`.`mapcycle`,
    `t`.`party_id`,
    `t`.`starttime`,
    `t`.`endtime`,
    `t`.`tournamentadmin`,
    `t`.`techadmin`,
    `a`.`username` AS techadmin_name,
    `r`.`username` AS tournamentadmin_name,
    UNIX_TIMESTAMP(`t`.`starttime`) AS starttime
FROM
    `%prefix%tournament_tournaments` AS t
    LEFT JOIN `%prefix%user` AS r ON `t`.`tournamentadmin` = `r`.`userid`
    LEFT JOIN `%prefix%user` AS a ON `t`.`techadmin` = `a`.`userid`
WHERE
    `t`.`tournamentid` = ?';
$tournament = $database->queryWithOnlyFirstRow($tournamentQuery, [$_GET['tournamentid']]);
if (!$tournament["tournamentid"]) {
    $func->error(t('Das ausgewählte Turnier existiert nicht'), "index.php?mod=tournament2");
} else {
    $stepParameter = $_GET['step'] ?? 0;
    switch ($stepParameter) {
        // Shuffle maps
        case 20:
            if ($auth['type'] <= \LS_AUTH_TYPE_USER) {
                $func->information('ACCESS_DENIED');
            } else {
                $maps = explode("\n", $tournament["mapcycle"]);
                shuffle($maps);
                $tournament["mapcycle"] = implode("\n", $maps);
                $mapCycleShuffleQuery = '
                    UPDATE `%prefix%tournament_tournaments`
                    SET
                        `mapcycle` = ?
                    WHERE
                        `tournamentid` = ?';
                $database->query($mapCycleShuffleQuery, [$tournament['mapcycle'], $_GET['tournamentid']]);
            }
            break;
    }

    switch ($stepParameter) {
        // Activate Seeding
        case 10:
            $seeded = $database->queryWithOnlyFirstRow("
              SELECT
                COUNT(*) AS anz
              FROM `%prefix%t2_teams`
              WHERE
                `tournamentid` = ?
                AND `seeding_mark` = '1'
              GROUP BY `tournamentid`", [$_GET['tournamentid']]);

            $team = $database->queryWithOnlyFirstRow("
              SELECT
                COUNT(*) AS anz
              FROM `%prefix%t2_teams`
              WHERE
                `tournamentid` = ?
              GROUP BY `tournamentid`", [$_GET['tournamentid']]);

            if (($seeded['anz']+1) > ($team['anz'] / 2)) {
                $func->information(t('Es wurde bereits die Hälfte der fest angemeldeten Teams markiert! Demarkiere zuerst ein Team, bevor du ein weiteres markierst'), "index.php?mod=tournament2&action=details&tournamentid={$_GET['tournamentid']}&headermenuitem=2");
            } else {
                $database->query("UPDATE `%prefix%t2_teams` SET `seeding_mark` = '1' WHERE `teamid` = ?", [$_GET['teamid']]);
                $func->confirmation(t('Das Team wurde zum Setzen markiert.<br>Alle markierten Teams werden beim Generieren so gesetzt, dass sie möglichst spät im Turnierbaum aufeinander treffen werden.'), "index.php?mod=tournament2&action=details&tournamentid={$_GET['tournamentid']}&headermenuitem=2");
            }
            break;

        // Deaktivate Seeding
        case 11:
            $database->query("UPDATE `%prefix%t2_teams` SET `seeding_mark` = '0' WHERE `teamid` = ?", [$_GET['teamid']]);
            $func->confirmation(t('Das Team wurde demarkiert.'), "index.php?mod=tournament2&action=details&tournamentid={$_GET['tournamentid']}&headermenuitem=2");
            break;

        // Show details
        default:
            $dsp->NewContent(t('Turnier %1', $tournament['name']), t('Hier findest du Informationen zu diesem Turnier und kannst dich anmelden'));

            $dsp->StartTabs();
            $dsp->StartTab(t('Turnierinfos'), 'details');
            $dsp->AddDoubleRow(t('Turniername'), $tournament['name']);

            $icon = '';
            $iconPath = 'ext_inc/tournament_icons/' . $tournament['icon'];
            if ($tournament['icon'] && $tournament['icon'] != "none" && file_exists($iconPath)) {
                $icon = '<img src="' . $iconPath . '" alt="Icon"> ';
            }

            $versionInformation = '';
            if ($tournament['version']) {
                $versionInformation = ' (' . t('Version') . ': ' . $tournament['version'] . ')';
            }
            $dsp->AddDoubleRow(t('Spiel'), $icon . $tournament['game'] . $versionInformation);

            if ($tournament['mode'] == "single") {
                $modus = t('Single-Elimination');
            }
            if ($tournament['mode'] == "double") {
                $modus = t('Double-Elimination');
            }
            if ($tournament['mode'] == "liga") {
                $modus = t('Liga');
            }
            if ($tournament['mode'] == "groups") {
                $modus = t('Gruppenspiele + KO');
            }
            if ($tournament['mode'] == "all") {
                $modus = t('Alle in einem');
            }
            if ($tournament['blind_draw']) {
                $blind_draw = " (Blind Draw)";
            } else {
                $blind_draw = "";
            }
            $dsp->AddDoubleRow(t('Spiel-Modus'), $modus .", ". $tournament['teamplayer'] ." ".t('gegen')." ". $tournament['teamplayer'] . $blind_draw);

            if ($tournament['tournamentadmin']) {
                $dsp->AddDoubleRow(t('Turniermanagement'), $dsp->FetchUserIcon($tournament['tournamentadmin'], $tournament['tournamentadmin_name']));
            } else {
                $dsp->AddDoubleRow(t('Turniermanagement'), t('Noch nicht zugeordnet'));
            }
            if ($tournament['techadmin']) {
                $dsp->AddDoubleRow(t('Technik/Server'), $dsp->FetchUserIcon($tournament['techadmin'], $tournament['techadmin_name']));
            } else {
                $dsp->AddDoubleRow(t('Technik/Server'), t('Noch nicht zugeordnet'));
            }
            $sponsor_banners = '';
            $sponsorQuery = '
                SELECT
                    `pic_path`,
                    `name`,
                    `sponsorid`
                FROM `%prefix%sponsor`
                WHERE `tournamentid` = ?';
            $sponsor = $database->queryWithFullResult($sponsorQuery, [$_GET['tournamentid']]);
            foreach ($sponsor as $sponsor_row) {
                $sponsor_banner = '<img src="'. $sponsor_row['pic_path'] .'" border="1" class="img_border" title="'. $sponsor_row['name'] .'" alt="Sponsor Banner" style="max-width:468px; max-height:450px;"/>';
                if ($cfg['sys_internet']) {
                    $sponsor_banner = '<a href="index.php?mod=sponsor&action=bannerclick&design=base&type=banner&sponsorid='. $sponsor_row["sponsorid"] .'" target="_blank">'. $sponsor_banner .'</a><br>';
                }
                $sponsor_banners .= $sponsor_banner;
            }

            if ($sponsor_banners) {
                $dsp->AddDoubleRow('Sponsored by', $sponsor_banners);
            }

            $dsp->AddFieldsetStart(t('Anmeldeeinschränkungen'));
            if ($tournament['status'] == "invisible") {
                $status = t('Unsichtbar');
            }
            if ($tournament['status'] == "open") {
                $status = t('Anmeldung offen');
            }
            if ($tournament['status'] == "locked") {
                $status = t('Anmeldung geschlossen');
            }
            if ($tournament['status'] == "closed") {
                $status = "<div class=\"tbl_error\">".t('Turnier beendet')."</div>";
            }
            if ($tournament['status'] == "process") {
                $status = "<div class=\"tbl_error\">".t('Partien werden gespielt')."</div>";
            }
            $dsp->AddDoubleRow(t('Status'), $status);

            ($tournament['groupid'] == 0) ?
                $dsp->AddDoubleRow(t('Turniergruppe'), t('Dieses Turnier wurde keiner Gruppe zugeordnet. Jeder darf teilnehmen.'))
                : $dsp->AddDoubleRow(t('Turniergruppe'), t('Dieses Turnier wurde der Gruppe %1 zugeordnet. Es düfen sich nur Spieler anmelden, welche nicht bereits zu einem anderen Turnier der Gruppe %1 angemeldet sind.', $tournament['groupid']));

            if ($auth['userid'] != '') {
                if ($tournament['coins'] == 0) {
                    $dsp->AddDoubleRow(t('Coin-Kosten'), t('Für dieses Turnier werden keine Coins benötigt'));
                } else {

                    $team_coin = $database->queryWithOnlyFirstRow("
                      SELECT
                        SUM(`t`.`coins`) AS t_coins
                      FROM `%prefix%tournament_tournaments` AS t
                      INNER JOIN `%prefix%t2_teams` AS teams ON t.tournamentid = teams.tournamentid
                      WHERE
                        teams.leaderid = ?
                        AND t.party_id = ?
                      GROUP BY teams.leaderid", [$auth["userid"], $party->party_id]);
                    $sumTeamCoins = $team_coin['t_coins'] ?? 0;

                    $member_coin = $database->queryWithOnlyFirstRow("
                      SELECT
                        SUM(t.coins) AS t_coins
                      FROM %prefix%tournament_tournaments AS t
                      INNER JOIN %prefix%t2_teammembers AS members ON t.tournamentid = members.tournamentid
                      WHERE
                        members.userid = ?
                        AND t.party_id = ?
                      GROUP BY members.userid", [$auth["userid"], $party->party_id]);
                    $sumMemberCoins = $member_coin['t_coins'] ?? 0;

                    (($cfg['t_coins'] - $sumTeamCoins - $sumMemberCoins) < $tournament['coins']) ?
                          $coin_out = t('Das Anmelden kostet %COST% Coins, du besitzt jedoch nur %IS% Coin(s)!')
                          : $coin_out = t('Das Anmelden kostet %COST% Coins. Du besitzt noch: %IS% Coin(s)');

                    $dsp->AddDoubleRow(t('Coin-Kosten'), "<div class=\"tbl_error\">". str_replace("%IS%", ($cfg['t_coins'] - $sumTeamCoins - $sumMemberCoins), str_replace("%COST%", $tournament['coins'], $coin_out)) ."</div>");
                }
            }

            ($tournament['over18']) ?
                $dsp->AddDoubleRow(t('U18-Sperre'), t('Nur zugänglich für Spieler aus Über-18-Blöcken'))
                : $dsp->AddDoubleRow(t('U18-Sperre'), t('Keine Sperre'));
            $dsp->AddFieldsetEnd();

            ($tournament["defwin_on_time_exceed"] == "1")? $defwin_warning = "<div class=\"tbl_error\">".t('ACHTUNG: Bei Zeitüberschreitung wird das Ergebnis automatisch gelost!')."</div> ".t('Wir bitten euch daher die Spiele direkt zu beginnen und das Ergebnis umgehend zu melden!')."" : $defwin_warning = "";
            $dsp->AddFieldsetStart(t('Zeiten') . $defwin_warning);
            $dsp->AddDoubleRow(t('Turnier beginnt um'), $func->unixstamp2date($tournament["starttime"], "datetime"));

            $dsp->AddDoubleRow(t('Dauer einer Runde'), t('Maximal %1 Spiel(e) pro Runde (je %2) + %3 Pause -> %4', $tournament["max_games"], $tournament["game_duration"] ."min", $tournament["break_duration"] ."min", $tournament["max_games"] * $tournament["game_duration"] + $tournament["break_duration"] ."min"));
            $dsp->AddFieldsetEnd();

            $dsp->AddFieldsetStart(t('Regeln und Sonstiges'));
            if ($tournament['rules_ext']) {
                $dsp->AddDoubleRow(t('Regelwerk'), "<a href=\"./ext_inc/tournament_rules/{$tournament['rules_ext']}\" target=\"_blank\">".t('Regelwerk öffnen ')."({$tournament['rules_ext']})</a>");
            }

            $dsp->AddDoubleRow(t('Bemerkung'), $func->text2html($tournament["comment"]));

            $maps = explode("\n", $tournament["mapcycle"]);
            $map_str = '';
            foreach ($maps as $key => $val) {
                $map_str .= t('Runde')." $key: $val \n";
            }
            $mapcycle = t('Mapcycle'). HTML_NEWLINE . HTML_NEWLINE;
            if ($auth['type'] > \LS_AUTH_TYPE_USER) {
                $mapcycle .= '<a href="index.php?mod=tournament2&action=details&tournamentid='. $_GET['tournamentid'] .'&step=20">'. t('Maps neu mischen') .'</a>';
            }
            $dsp->AddDoubleRow($mapcycle, $func->text2html($map_str));
            $dsp->AddFieldsetEnd();
            $dsp->EndTab();


            $dsp->StartTab(t('Angemeldete Teams'), 'assign');
            $waiting_teams = "";
            $completed_teams = "";
            $teams = $database->queryWithFullResult("
              SELECT
                `name`,
                `teamid`,
                `seeding_mark`,
                `disqualified`
              FROM `%prefix%t2_teams`
              WHERE
                tournamentid = ?", [$_GET['tournamentid']]);

            $teamcount = [0, 0];
            foreach ($teams as $team) {
                $members = $database->queryWithOnlyFirstRow("
                  SELECT
                    COUNT(*) AS members
                  FROM `%prefix%t2_teammembers`
                  WHERE
                    `teamid` = ?
                  GROUP BY `teamid`", [$team['teamid']]);
                $team_out = $team["name"] . $tfunc->button_team_details($team['teamid'], $_GET['tournamentid']);
                if (($tournament['mode'] == "single") or ($tournament['mode'] == "double")) {
                    if ($team["seeding_mark"]) {
                        $team_out .= " ". t('Dieses Team wird beim Generieren gesetzt');
                    }
                    if (($auth['type'] > \LS_AUTH_TYPE_USER) && ($tournament['status'] == "open")) {
                        if ($team["seeding_mark"]) {
                            $team_out .= " <a href=\"index.php?mod=tournament2&action=details&step=11&tournamentid={$_GET['tournamentid']}&teamid={$team['teamid']}\">".t('demarkieren')."</a>";
                        } else {
                            $team_out .= " <a href=\"index.php?mod=tournament2&action=details&step=10&tournamentid={$_GET['tournamentid']}&teamid={$team['teamid']}\">".t('Team setzen')."</a>";
                        }
                    }
                }

                $team_out .= HTML_NEWLINE;
                if (is_array($members) && ($members["members"] + 1) < $tournament['teamplayer']) {
                    $teamcount[0]++;
                    $waiting_teams .= $team_out;
                } else {
                    $teamcount[1]++;
                    $completed_teams .= $team_out;
                }
            }

            $dsp->AddSingleRow(t('Es sind %1 von maximal %2 Teams zu diesem Turnier angemeldet.', ($teamcount[0] + $teamcount[1]), $tournament['maxteams']));

            if ($completed_teams == "") {
                $completed_teams = "<i>".t('Keine')."</i>";
            }
            $dsp->AddDoubleRow(t('Teamnamen'), $completed_teams);

            if (($tournament['teamplayer'] > 1) && ($waiting_teams != "")) {
                $dsp->AddSingleRow(t('Folgende %1 Teams sind noch unvollständig', ($teamcount[0] + 0)));
                $dsp->AddDoubleRow(t('Teamnamen'), $waiting_teams);
            }
            $dsp->EndTab();
            $dsp->EndTabs();

            $buttons="";
            switch ($tournament["status"]) {
                case "open":
                    $buttons .= $dsp->FetchSpanButton(t('Teilnehmen'), "index.php?mod=tournament2&action=join&tournamentid={$_GET['tournamentid']}&step=2"). " ";
                    if ($auth['type'] > \LS_AUTH_TYPE_USER) {
                        $buttons .= $dsp->FetchSpanButton(t('Generieren'), "index.php?mod=tournament2&action=generate_pairs&step=2&tournamentid={$_GET['tournamentid']}"). " ";
                    }
                    break;
                case "process":
                    $buttons .= $dsp->FetchSpanButton(t('Paarungen'), "index.php?mod=tournament2&action=games&step=2&tournamentid={$_GET['tournamentid']}"). " ";
                    $buttons .= $dsp->FetchSpanButton(t('Spielbaum'), "index.php?mod=tournament2&action=tree&step=2&tournamentid={$_GET['tournamentid']}"). " ";
                    if ($auth['type'] > \LS_AUTH_TYPE_USER) {
                        $buttons .= $dsp->FetchSpanButton(t('Generieren rückgängig'), "index.php?mod=tournament2&action=undo_generate&tournamentid={$_GET['tournamentid']}"). " ";
                    }
                    break;
                case "closed":
                    $buttons .= $dsp->FetchSpanButton(t('Paarungen'), "index.php?mod=tournament2&action=games&step=2&tournamentid={$_GET['tournamentid']}"). " ";
                    $buttons .= $dsp->FetchSpanButton(t('Spielbaum'), "index.php?mod=tournament2&action=tree&step=2&tournamentid={$_GET['tournamentid']}"). " ";
                    if ($auth['type'] > \LS_AUTH_TYPE_USER) {
                        $buttons .= $dsp->FetchSpanButton(t('Schließen rückgängig'), "index.php?mod=tournament2&action=undo_close&tournamentid={$_GET['tournamentid']}"). " ";
                    }
                    break;
            }
            $dsp->AddDoubleRow("", $buttons);
            $dsp->AddBackButton("index.php?mod=tournament2", "tournament2/details");
            break;
    }
}
