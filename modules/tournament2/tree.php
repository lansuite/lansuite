
<?php

if (!$_GET['tournamentid']) {
    $func->error(t('Du hast kein Turnier ausgewählt!'));
} else {
    switch ($_GET['step']) {
        case 1:
                include_once('modules/tournament2/search.inc.php');
            break;
  
  
        case 2:
                $tournament = $db->qry_first('SELECT name, mode FROM %prefix%tournament_tournaments WHERE tournamentid = %int%', $_GET['tournamentid']);
  
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
  
              /*	$games = $db->qry("SELECT gameid FROM %prefix%t2_games WHERE (tournamentid = %int%) AND (round=0)", $_GET['tournamentid']);
                $team_anz = $db->num_rows($games);
                $db->free_result($games);*/
                include_once("modules/tournament2/class_tournament.php");
                $tfunc = new tfunc;
                $team_anz = $tfunc->GetTeamAnz($_GET['tournamentid'], $tournament['mode'], $_POST['group']);
  
                $dsp->NewContent(t('Turnierbaum zum Turnier %1 (%2)', $tournament['name'], $modus), t('Hier siehst du grafisch dargestellt, wer gegen wen spielt und kannst Ergebnisse melden'));
  
            if ($team_anz == 0) {
                  $func->information(t('Dieses Turnier wurde noch nicht generiert. Die Paarungen sind noch nicht bekannt.'), "index.php?mod=tournament2&action=tree&step=1");
                  break;
            } elseif ($tournament['mode'] == "all") {
                  $func->information(t('Ein Turnierbaum ist für diesen Spiel-Modus nicht vorgesehen. Schaue bitte unter Paarungen nach'), "index.php?mod=tournament2&action=games&step=2&tournamentid=". $_GET['tournamentid']);
                  break;
            } else {
                if ($tournament['mode'] == "liga") {
                    $height = $team_anz * 20 + 30;
                } else {
                    $height = (($team_anz/2) * 50) + 60;
                }
  
                if (($tournament["mode"] == "groups") && ($_POST['group'] == '')) {
                    $teams = $db->qry_first("SELECT MAX(group_nr) AS max_group_nr
      FROM %prefix%t2_games
      WHERE (tournamentid = %int%) AND (round = 0)
      ", $_GET['tournamentid']);
  
                    $t_array = array("<option value=\"0\">".t('Finalspiele')."</option>");
                    for ($i = 1; $i <= $teams["max_group_nr"]; $i++) {
                                array_push($t_array, "<option value=\"$i\">".t('Spiele der Gruppe')." $i</option>");
                    }
  
                    $dsp->SetForm("index.php?mod=tournament2&action=tree&step=2&tournamentid=". $_GET['tournamentid']);
                    $dsp->AddDropDownFieldRow("group", t('Gruppenauswahl'), $t_array, "");
                    $dsp->AddFormSubmitRow(t('Weiter'));

                } else {
                    // If specific games of a group was chosen
                    // pass this to the tree frame, otherwise keep it at the final games
                    $groupQueryParam = '';
                    $groupID = (int) $_POST['group'];
                    if ($groupID > 0) {
                        $groupQueryParam = '&group='. $groupID;
                    }

                    $iFrameURL = 'index.php?mod=tournament2&action=tree_frame&design=popup&tournamentid='. (int) $_GET['tournamentid'] . $groupQueryParam;
                    $dsp->AddSingleRow('<iframe src="' . $iFrameURL . '" width="100%" height="'. (int)$height .'" style="width:100%; min-width:600px;"><a href="index.php?mod=tournament2&action=tree_frame&design=base&tournamentid='. (int)$_GET['tournamentid'] .'&group='. (int)$_POST['group'] .'">Tree</a></iframe>');
                }

              /*
                      if ($tournament["mode"] == "groups"){
                  if(!file_exists("ext_inc/tournament_trees/tournament_" . $_GET['tournamentid'] . "_" . $_POST['group'] . ".png")){
              #                   $cronjob->load_job("cron_tmod");
              #                   $cronjob->loaded_class->add_job($_GET["tournamentid"], $_POST['group']);
                  }
              #               $dsp->AddDoubleRow("", "<a href=\"ext_inc/tournament_trees/tournament_" . $_GET['tournamentid'] . "_" . $_POST['group'] . ".png\">".t('Hier kannst du die Grafik herunterladen')."</a>");
                      }else{
                  if(!file_exists("ext_inc/tournament_trees/tournament_" . $_GET['tournamentid'] . ".png")){
              #                   $cronjob->load_job("cron_tmod");
              #                   $cronjob->loaded_class->add_job($_GET["tournamentid"],"");
                  }
              #               if (!$cfg['t_text_tree']) $dsp->AddDoubleRow("", "<a href=\"ext_inc/tournament_trees/tournament_". $_GET['tournamentid'] .".png\">".t('Hier kannst du die Grafik herunterladen')."</a>");
                      }
                  }
              */
  
        
                if ($func->internal_referer) {
                    $dsp->AddBackButton($func->internal_referer, "tournament2/games");
                } else {
                    $dsp->AddBackButton("index.php?mod=tournament2&action=tree&step=1", "tournament2/games");
                }
                  $dsp->AddContent();
            }
    } // Switch
}
