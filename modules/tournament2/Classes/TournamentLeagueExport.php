<?php

namespace LanSuite\Module\Tournament2;

class TournamentLeagueExport
{

    private ?\LanSuite\XML $xml = null;

    private ?\LanSuite\Module\Tournament2\TournamentFunction $tournamentFunc = null;

    public function __construct(\LanSuite\XML $xml, \LanSuite\Module\Tournament2\TournamentFunction $tournamentFunc)
    {
        $this->xml = $xml;
        $this->tournamentFunc = $tournamentFunc;
    }

    /**
     * @param int $eventid
     * @return string
     */
    public function ngl_export($eventid)
    {
        $mode = null;
        $db_teamid = [];
        $score1 = null;
        global $db, $party;

        $output = '<?xml version="1.0" encoding="ISO-8859-15"?'.'>'."\r\n";

        // Allgemeine Party-Daten
        $laninfo = $this->xml->write_tag("eventid", $eventid, 2);
        $laninfo .= $this->xml->write_tag("name", $_SESSION['party_info']['name'], 2);
        $laninfo .= $this->xml->write_tag("country", "de", 2);
        $laninfo .= $this->xml->write_tag("date", date("Y-m-d", $_SESSION['party_info']['partybegin']), 2);
        $laninfo .= $this->xml->write_tag("contact", "knox@orgapage.de (Programmierer dieses LS-Moduls, nicht Veranstalter)", 2);
        $export = $this->xml->write_master_tag("laninfo", $laninfo, 1);

        $tournaments = $db->qry("
          SELECT
            mode,
            ngl_gamename,
            tournamentid
          FROM %prefix%tournament_tournaments
          WHERE
            party_id=%int%
            AND (
              (mode = 'single')
              OR (mode = 'double')
            )
            AND (ngl_gamename != '')
            AND (status = 'closed')", $party->party_id);
        while ($tournament = $db->fetch_array($tournaments)) {
            if ($tournament['mode'] == "double") {
                $mode = "DE";
            }
            if ($tournament['mode'] == "single") {
                $mode = "SE";
            }

            $ranking_data = $this->tournamentFunc->get_ranking($tournament['tournamentid']);

            $gameinfo = $this->xml->write_tag("type", $tournament['ngl_gamename'], 3);
            $gameinfo .= $this->xml->write_tag("mode", $mode, 3);
            $game = $this->xml->write_master_tag("gameinfo", $gameinfo, 2);

            $teams = "";
            $db_teams = $db->qry("
              SELECT
                teams.name AS tname,
                teams.teamid,
                users.username,
                users.email,
                users.firstname,
                users.name,
                users.nglid,
                users.nglclanid
              FROM
                %prefix%t2_teams AS teams,
                %prefix%user AS users
              WHERE
                (teams.leaderid = users.userid)
                AND (teams.tournamentid = %int%)", $tournament['tournamentid']);
            while ($db_team = $db->fetch_array($db_teams)) {
                $ngl_id = $db_team['nglid'];
                if ($ngl_id == "") {
                    $ngl_id = 0;
                }
                $ngl_clanid = $db_team['nglclanid'];
                if ($ngl_clanid == "") {
                    $ngl_clanid = 0;
                }
                $teamname = $db_team['tname'];
                $db_members = $db->qry("
                  SELECT
                    users.username,
                    users.email,
                    users.firstname,
                    users.name,
                    users.nglid
                  FROM
                    %prefix%t2_teammembers AS members,
                    %prefix%user AS users
                  WHERE
                    (members.userid = users.userid)
                    AND (members.teamid = %int%)", $db_team['teamid']);
                if ($db->num_rows($db_members) == 0) {
                    $ngl_clanid = $ngl_id;
                    $teamname = $db_team['username'];
                }

                $team = $this->xml->write_tag("place", array_search($db_team['teamid'], $ranking_data->tid) + 1, 4);
                $team .= $this->xml->write_tag("nglid", $ngl_clanid, 4);
                $team .= $this->xml->write_tag("name", $teamname, 4);
                $team .= $this->xml->write_tag("tmpid", $db_team['teamid'], 4);

                $player = $this->xml->write_tag("nglid", $ngl_id, 6);
                $player .= $this->xml->write_tag("nickname", $db_team['username'], 6);
                $player .= $this->xml->write_tag("email", $db_team['email'], 6);
                $player .= $this->xml->write_tag("firstname", $db_team['firstname'], 6);
                $player .= $this->xml->write_tag("lastname", $db_team['name'], 6);
                $player .= $this->xml->write_tag("leader", "yes", 6);
                $members = $this->xml->write_master_tag("player", $player, 5);

                while ($db_member = $db->fetch_array($db_members)) {
                    $ngl_id = $db_member['nglid'];
                    if ($ngl_id == "") {
                        $ngl_id = 0;
                    }

                    $player = $this->xml->write_tag("nglid", $ngl_id, 6);
                    $player .= $this->xml->write_tag("nickname", $db_member['username'], 6);
                    $player .= $this->xml->write_tag("email", $db_member['email'], 6);
                    $player .= $this->xml->write_tag("firstname", $db_member['firstname'], 6);
                    $player .= $this->xml->write_tag("lastname", $db_member['name'], 6);
                    $player .= $this->xml->write_tag("leader", "no", 6);

                    $members .= $this->xml->write_master_tag("player", $player, 5);
                }
                $db->free_result($db_members);

                $team .= $this->xml->write_master_tag("members", $members, 4);
                $teams .= $this->xml->write_master_tag("team", $team, 3);
            }
            $db->free_result($db_teams);
            $game .= $this->xml->write_master_tag("teams", $teams, 2);

            $matches = "";
            $db_rounds = $db->qry("
              SELECT round
              FROM %prefix%t2_games
              WHERE tournamentid = %int%
              GROUP BY round
              ORDER BY round", $tournament['tournamentid']);
            while ($db_round = $db->fetch_array($db_rounds)) {
                $tmpid1 = "";
                $round = "";
                $db_matchs = $db->qry("
                  SELECT
                    leaderid,
                    score
                  FROM %prefix%t2_games
                  WHERE
                    (tournamentid = %int%)
                    AND (round = %string%)
                  ORDER BY position", $tournament['tournamentid'], $db_round['round']);
                while ($db_match = $db->fetch_array($db_matchs)) {
                    if ($db_match['leaderid'] == 0) {
                        $db_teamid['teamid'] = 0;
                    } else {
                        $db_teamid = $db->qry_first("SELECT teamid FROM %prefix%t2_teams AS teams WHERE (tournamentid = %int%) AND (teams.leaderid = %int%)", $tournament['tournamentid'], $db_match['leaderid']);
                    }

                    if ($tmpid1 == "") {
                        $tmpid1 = "{$db_teamid['teamid']}";
                        $score1 = $db_match['score'];
                    } else {
                        if ($db_match['score'] > $score1) {
                            $winner = $db_teamid['teamid'];
                        } else {
                            $winner = $tmpid1;
                        }
                        $match = $this->xml->write_tag("tmpid1", $tmpid1, 5);
                        $match .= $this->xml->write_tag("tmpid2", $db_teamid['teamid'], 5);
                        $match .= $this->xml->write_tag("score1", (int)$score1, 5);
                        $match .= $this->xml->write_tag("score2", (int)$db_match['score'], 5);
                        $match .= $this->xml->write_tag("winner", $winner, 5);
                        $round .= $this->xml->write_master_tag("match", $match, 4);
                        $tmpid1 = "";
                    }
                }
                $db->free_result($db_matchs);


                if ($db_round['round'] >= 0) {
                    $round_formated = "WB=\"". ($db_round['round'] + 1) ."\"";
                } else {
                    $round_formated = "LB=\"". (abs($db_round['round']) * 2) ."\"";
                }
                if ($round) {
                    $matches .= $this->xml->write_master_tag("round $round_formated", $round, 3);
                }
            }
            $db->free_result($db_rounds);

            $game .= $this->xml->write_master_tag("matches", $matches, 2);
            $export .= $this->xml->write_master_tag("game", $game, 1);
        }
        $db->free_result($tournaments);

        $output .= $this->xml->write_master_tag("export version=\"1.4\"", $export, 0);
        return $output;
    }

    /**
     * @param int $eventid
     * @return string
     */
    public function lgz_export($eventid)
    {
        $mode = null;
        $db_teamid = [];
        $score1 = null;
        global $db, $party;

        $output = '<?xml version="1.0"?'.'>'."\r\n";

        // Allgemeine Party-Daten
        $laninfo = $this->xml->write_tag("eventid", $eventid, 2);
        $laninfo .= $this->xml->write_tag("contact", "knox@orgapage.de (Programmierer dieses LS-Moduls, nicht Veranstalter)", 2);
        $export = $this->xml->write_master_tag("laninfo", $laninfo, 1);

        $tournaments = $db->qry("
          SELECT
            mode,
            lgz_gamename,
            tournamentid
          FROM %prefix%tournament_tournaments
          WHERE
            party_id=%int%
            AND (
              (mode = 'single')
              OR (mode = 'double')
            )
            AND (lgz_gamename != '')
            AND (status = 'closed')", $party->party_id);
        while ($tournament = $db->fetch_array($tournaments)) {
            if ($tournament['mode'] == "double") {
                $mode = "DE";
            }
            if ($tournament['mode'] == "single") {
                $mode = "SE";
            }

            $ranking_data = $this->tournamentFunc->get_ranking($tournament['tournamentid']);

            $gameinfo = $this->xml->write_tag("type", $tournament['lgz_gamename'], 3);
            $gameinfo .= $this->xml->write_tag("mode", $mode, 3);
            $game = $this->xml->write_master_tag("gameinfo", $gameinfo, 2);

            $teams = "";
            $db_teams = $db->qry("
              SELECT
                teams.name AS tname,
                teams.teamid,
                users.username,
                users.email,
                users.firstname,
                users.name,
                users.lgzid,
                users.lgzclanid
              FROM
                %prefix%t2_teams AS teams,
                %prefix%user AS users
              WHERE
                (teams.leaderid = users.userid)
                AND (teams.tournamentid = %int%)", $tournament['tournamentid']);
            while ($db_team = $db->fetch_array($db_teams)) {
                $ngl_id = $db_team['lgzid'];
                if ($ngl_id == "") {
                    $ngl_id = 0;
                }
                $ngl_clanid = $db_team['lgzclanid'];
                if ($ngl_clanid == "") {
                    $ngl_clanid = 0;
                }
                $teamname = $db_team['tname'];
                $db_members = $db->qry("
                  SELECT
                    users.username,
                    users.email,
                    users.firstname,
                    users.name,
                    users.lgzid
                  FROM
                    %prefix%t2_teammembers AS members,
                    %prefix%user AS users
                  WHERE
                    (members.userid = users.userid)
                    AND (members.teamid = %int%)", $db_team['teamid']);
                if ($db->num_rows($db_members) == 0) {
                    $ngl_clanid = $ngl_id;
                    $teamname = $db_team['username'];
                }

                // John Doe
                if ($ngl_id == 14475) {
                    $db_team['firstname'] = "John";
                    $db_team['name'] = "Doe";
                }
                if ($ngl_clanid == 38) {
                    $teamname = "John Doe";
                }

                $team = $this->xml->write_tag("place", array_search($db_team['teamid'], $ranking_data->tid) + 1, 4);
                $team .= $this->xml->write_tag("lgzid", $ngl_clanid, 4);
                $team .= $this->xml->write_tag("name", $teamname, 4);
                $team .= $this->xml->write_tag("tmpid", $db_team['teamid'], 4);

                $player = $this->xml->write_tag("lgzid", $ngl_id, 6);
                $player .= $this->xml->write_tag("nickname", $db_team['username'], 6);
                $player .= $this->xml->write_tag("email", $db_team['email'], 6);
                $player .= $this->xml->write_tag("firstname", $db_team['firstname'], 6);
                $player .= $this->xml->write_tag("lastname", $db_team['name'], 6);
                $player .= $this->xml->write_tag("leader", "yes", 6);
                $members = $this->xml->write_master_tag("player", $player, 5);

                while ($db_member = $db->fetch_array($db_members)) {
                    $ngl_id = $db_member['nglid'];
                    if ($ngl_id == "") {
                        $ngl_id = 0;
                    }

                    // Member - John Doe
                    if ($ngl_id == 14475) {
                        $db_member['firstname'] = "John";
                        $db_member['name'] = "Doe";
                    }

                    $player = $this->xml->write_tag("lgzid", $ngl_id, 6);
                    $player .= $this->xml->write_tag("nickname", $db_member['username'], 6);
                    $player .= $this->xml->write_tag("email", $db_member['email'], 6);
                    $player .= $this->xml->write_tag("firstname", $db_member['firstname'], 6);
                    $player .= $this->xml->write_tag("lastname", $db_member['name'], 6);
                    $player .= $this->xml->write_tag("leader", "no", 6);

                    $members .= $this->xml->write_master_tag("player", $player, 5);
                }
                $db->free_result($db_members);

                $team .= $this->xml->write_master_tag("members", $members, 4);
                $teams .= $this->xml->write_master_tag("team", $team, 3);
            }
            $db->free_result($db_teams);
            $game .= $this->xml->write_master_tag("teams", $teams, 2);


            $matches = "";
            $db_rounds = $db->qry("
              SELECT round
              FROM %prefix%t2_games
              WHERE tournamentid = %int%
              GROUP BY round
              ORDER BY round", $tournament['tournamentid']);
            while ($db_round = $db->fetch_array($db_rounds)) {
                $tmpid1 = "";
                $round = "";
                $db_matchs = $db->qry("
                  SELECT
                    leaderid,
                    score
                  FROM %prefix%t2_games
                  WHERE
                    (tournamentid = %int%)
                    AND (round = %string%)
                  ORDER BY position", $tournament['tournamentid'], $db_round['round']);
                while ($db_match = $db->fetch_array($db_matchs)) {
                    if ($db_match['leaderid'] == 0) {
                        $db_teamid['teamid'] = 0;
                    } else {
                        $db_teamid = $db->qry_first("SELECT teamid FROM %prefix%t2_teams AS teams WHERE (tournamentid = %int%) AND (teams.leaderid = %int%)", $tournament['tournamentid'], $db_match['leaderid']);
                    }

                    if ($tmpid1 == "") {
                        $tmpid1 = "{$db_teamid['teamid']}";
                        $score1 = $db_match['score'];
                    } else {
                        if ($db_match['score'] > $score1) {
                            $winner = $db_teamid['teamid'];
                        } else {
                            $winner = $tmpid1;
                        }
                        $match = $this->xml->write_tag("tmpid1", $tmpid1, 5);
                        $match .= $this->xml->write_tag("tmpid2", $db_teamid['teamid'], 5);
                        $match .= $this->xml->write_tag("score1", $score1, 5);
                        $match .= $this->xml->write_tag("score2", $db_match['score'], 5);
                        $match .= $this->xml->write_tag("winner", $winner, 5);
                        $round .= $this->xml->write_master_tag("match", $match, 4);
                        $tmpid1 = "";
                    }
                }
                $db->free_result($db_matchs);

                if ($db_round['round'] >= 0) {
                    $round_formated = "WB=\"". ($db_round['round'] + 1) ."\"";
                } else {
                    $round_formated = "LB=\"". (abs($db_round['round']) * 2) ."\"";
                }
                if ($round) {
                    $matches .= $this->xml->write_master_tag("round $round_formated", $round, 3);
                }
            }
            $db->free_result($db_rounds);

            $game .= $this->xml->write_master_tag("matches", $matches, 2);
            $export .= $this->xml->write_master_tag("game", $game, 1);
        }
        $db->free_result($tournaments);

        $output .= $this->xml->write_master_tag("export version=\"1.0\"", $export, 0);
        return $output;
    }
}
