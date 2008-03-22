<?php

include("modules/tournament2/class_team.php");
$tteam = new team;

$tournamentid 	= $vars["tournamentid"];
$userid 	= $vars["userid"];
$teamid 	= $vars["teamid"];
$member_user 	= $vars["member_user"];


switch($_GET["step"]) {
	// Team l�schen
	case 10:
		if ($tteam->delete($_POST["teamid"])) $func->confirmation(t('Das Team wurde erfolgreich gel�scht'), "index.php?mod=tournament2&action=teammgr_admin");
	break;

	// Spieler einem Team hinzuf�gen - Suchen
	case 20:
    include_once('modules/usrmgr/search_main.inc.php');      

    $ms2->query['where'] .= "p.party_id={$party->party_id} AND (p.paid)";
    if ($auth['type'] >= 2) $ms2->AddIconField('assign', 'index.php?mod=tournament2&action=teammgr_admin&step=21&teamid='. $teamid .'&userid=', 'Assign');

    $ms2->PrintSearch('index.php?mod=tournament2&action=teammgr_admin&step=20&teamid='. $teamid, 'u.userid');
	break;

	// Spieler einem Team hinzuf�gen - Ausf�hren
	case 21:
		if ($tteam->join($_GET["teamid"], $_GET["userid"])) $func->confirmation(t('Der Spieler wurde dem Team hinzugef�gt'), "index.php?mod=tournament2&action=teammgr_admin");
	break;

	// Member aus Team l�schen
	case 30:
		list($team_id, $user_id) = split("-", $_POST["member_user"], 2);
		if ($tteam->kick($team_id, $user_id)) $func->confirmation(t('Der Spieler wurde erfolgreich aus dem Team entfernt'), "index.php?mod=tournament2&action=teammgr_admin");
	break;

	// Neues Team er�ffnen - Teamleiter ausw�hlen
	case 40:
		if ($tteam->SignonCheck($vars["tournamentid"])) {
      include_once('modules/usrmgr/search_main.inc.php');      

      $ms2->query['where'] .= "p.party_id={$party->party_id} AND (p.paid)";
      if ($auth['type'] >= 2) $ms2->AddIconField('assign', 'index.php?mod=tournament2&action=teammgr_admin&step=41&tournamentid='. $tournamentid .'&userid=', 'Assign');

      $ms2->PrintSearch('index.php?mod=tournament2&action=teammgr_admin&step=40&tournamentid='. $tournamentid, 'u.userid');
		}
	break;

	// Neues Team er�ffnen - Teamname eingeben
	case 41:
		$sec->unlock("t_admteam_create");

		$t = $db->query_first("SELECT teamplayer FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = '$tournamentid'");
		if ($t['teamplayer'] == 1) {
			$leader = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userid = '{$_GET['userid']}'");
			$_POST["team_name"] = $leader["username"];
		}

		if ($tteam->SignonCheckUser($_GET["tournamentid"], $_GET["userid"])) {
			$dsp->SetForm("index.php?mod=tournament2&action=teammgr_admin&step=42&tournamentid=$tournamentid&userid=$userid");
			$dsp->AddTextFieldRow("team_name", t('Teamname'), $_POST["team_name"], "");
			$dsp->AddPasswordRow("set_password", t('Team-Passwort festlegen'), $_POST["set_password"], $error["set_password"]);
			$dsp->AddPasswordRow("set_password2", t('Team-Passwort wiederholen'), $_POST["set_password2"], $error["set_password2"]);
			$dsp->AddTextAreaPlusRow("team_comment", t('Bemerkung'), $team_comment, "", "", "", 1);
			$dsp->AddFileSelectRow("team_banner", t('Team-Logo (max. 1MB)'), "", "", 1000000, 1);
			$dsp->AddFormSubmitRow("add");
			$dsp->AddBackButton("index.php?mod=tournament2&action=teammgr_admin", ""); 
		}
	break;

	// Neues Team er�ffnen - In DB schreiben
	case 42:
		if ($_POST["set_password"] and $_POST["set_password"] != $_POST["set_password2"]) $func->information("Die Passworteingaben stimmen nicht �berein", "index.php?mod=tournament2&action=teammgr_admin&step=41&tournamentid=$tournamentid&userid=$userid");
		elseif ($_POST['team_name'] == "") $func->information(t('Bitte geben Sie einen Teamnamen ein, oder w�hlen Sie ein vorhandenes Team aus'), "index.php?mod=tournament2&action=teammgr_admin&step=41&tournamentid=$tournamentid&userid=$userid");

		elseif (!$sec->locked("t_admteam_create")) {
			$t = $db->query_first("SELECT name FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = {$_GET["tournamentid"]}");

			if ($tteam->create($_GET["tournamentid"], $_GET["userid"], $_POST["team_name"], $_POST["set_password"], $_POST["team_comment"], "team_banner")) $func->confirmation(t('Der Spieler / Das Team wurden zum Turnier %1 erfolgreich angemeldet', $t["name"]), "index.php?mod=tournament2&action=teammgr_admin");

			$sec->lock("t_admteam_create");
		}
	break;


	default:
		$dsp->NewContent(t('Admin-Teammanager'), t('Hier k�nnen Sie Teams l�schen oder ihnen weitere Spieler zuweisen.'));

		// Neues Team anmelden
		$tourneys = $db->query("SELECT tournamentid, name FROM {$config["tables"]["tournament_tournaments"]} WHERE (status = 'open')  AND party_id='$party->party_id' ORDER BY name");
		if ($db->num_rows($tourneys) == 0) $dsp->AddDoubleRow(t('Neues Team (Spieler) anmeldenHTML_NEWLINE(Nur in Anmeldephase m�glich)'), t('Es sind keine Turniere vorhanden, welche sich noch in der Anmeldephase befinden'));
		else {
			$t_array = array("<option value=\"\">".t('Bitte Turnier ausw�hlen')."</option>");
			while($tourney = $db->fetch_array($tourneys)) {
				array_push ($t_array, "<option value=\"{$tourney['tournamentid']}\">{$tourney['name']}</option>");
			}
			$dsp->SetForm("index.php?mod=tournament2&action=teammgr_admin&step=40");
			$dsp->AddDropDownFieldRow("tournamentid", t('Neues Team (Spieler) anmeldenHTML_NEWLINE(Nur in Anmeldephase m�glich)'), $t_array, "");
			$dsp->AddFormSubmitRow("send");
		}
		$db->free_result($teams);

		// Team l�schen Auswahl
		$teams = $db->query("SELECT teams.teamid, teams.name, t.name AS tname, t.teamplayer
			FROM {$config["tables"]["t2_teams"]} AS teams
			LEFT JOIN {$config["tables"]["tournament_tournaments"]} AS t ON t.tournamentid = teams.tournamentid
			WHERE t.status = 'open' AND t.party_id = '$party->party_id'
			ORDER BY t.name, teams.name
			");
		if ($db->num_rows($teams) == 0) $dsp->AddDoubleRow(t('Komplettes Team l�schenHTML_NEWLINE(Nur in Anmeldephase m�glich)'), t('Es haben sich noch keine Spieler zu Turnieren angemeldet, welche noch nicht bereits laufen'));
		else {
			$t_array = array("<option value=\"\">".t('Bitte Team ausw�hlen')."</option>");
			while($team = $db->fetch_array($teams)) {
				array_push ($t_array, "<option value=\"{$team['teamid']}\">{$team['tname']} - {$team['name']}</option>");
			}
			$dsp->SetForm("index.php?mod=tournament2&action=teammgr_admin&step=10");
			$dsp->AddDropDownFieldRow("teamid", t('Komplettes Team l�schenHTML_NEWLINE(Nur in Anmeldephase m�glich)'), $t_array, "");
			$dsp->AddFormSubmitRow("delete");
		}
		$db->free_result($teams);

		// Spieler hinzuf�gen Auswahl
		$teams = $db->query("SELECT teams.teamid, teams.name, t.name AS tname, t.teamplayer
			FROM {$config["tables"]["t2_teams"]} AS teams
			LEFT JOIN {$config["tables"]["tournament_tournaments"]} AS t ON t.tournamentid = teams.tournamentid
			WHERE t.teamplayer > 1 AND t.status != 'closed' AND t.party_id = '$party->party_id'
			ORDER BY t.name, teams.name
			");
		if ($db->num_rows($teams) == 0) $dsp->AddDoubleRow(t('Spieler einem Team hinzuf�gen'), t('Es existieren keine Teams, die noch auf weitere Spieler warten'));
		else {
			$t_array = array("<option value=\"\">".t('Bitte Team ausw�hlen')."</option>");
			while($team = $db->fetch_array($teams)) {
				$member = $db->query_first("SELECT COUNT(*) AS members FROM {$config["tables"]["t2_teammembers"]} WHERE teamid = {$team['teamid']} GROUP BY teamid");

				$freie_platze = $team['teamplayer'] - ($member['members'] + 1);
				if ($freie_platze > 0) 
					array_push ($t_array, "<option value=\"{$team['teamid']}\">{$team['tname']} - {$team['name']} (". t('Noch %FREE% frei', $freie_platze) .")</option>");
			}
			$dsp->SetForm("index.php?mod=tournament2&action=teammgr_admin&step=20");
			$dsp->AddDropDownFieldRow("teamid", t('Spieler einem Team hinzuf�gen'), $t_array, "");
			$dsp->AddFormSubmitRow("send");
		}
		$db->free_result($teams);

		// Member aus Team l�schen - Auswahl
		$teams = $db->query("SELECT teams.teamid, teams.name, t.name AS tname, users.username AS mname, t.teamplayer, members.userid AS userid
			FROM {$config["tables"]["t2_teams"]} AS teams
			LEFT JOIN {$config["tables"]["t2_teammembers"]} AS members ON teams.teamid = members.teamid
			LEFT JOIN {$config["tables"]["user"]} AS users ON members.userid = users.userid
			LEFT JOIN {$config["tables"]["tournament_tournaments"]} AS t ON t.tournamentid = teams.tournamentid
			WHERE t.teamplayer > 1 AND t.party_id = '$party->party_id'
			ORDER BY t.name, teams.name
			");
		if ($db->num_rows($teams) == 0) $dsp->AddDoubleRow(t('Spieler aus einem Team l�schen'), t('Es haben sich noch keine Mitglieder zu Teams angemeldet'));
		else {
			$t_array = array("<option value=\"\">".t('Bitte Team ausw�hlen')."</option>");
			while($team = $db->fetch_array($teams)) {
				array_push ($t_array, "<option value=\"{$team['teamid']}-{$team['userid']}\">{$team['tname']} - {$team['name']} - {$team['mname']}</option>");
			}
			$dsp->SetForm("index.php?mod=tournament2&action=teammgr_admin&step=30");
			$dsp->AddDropDownFieldRow("member_user", t('Spieler aus einem Team l�schen'), $t_array, "");
			$dsp->AddFormSubmitRow("delete");
		}
		$db->free_result($teams);

		$dsp->AddBackButton("index.php?mod=tournament2", "tournament2/teammgr_admin"); 
	break;
}

$dsp->AddContent();
?>