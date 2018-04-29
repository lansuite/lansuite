<?php

$md = new \LanSuite\MasterDelete();

$md->References['t2_teams'] = '';
$md->References['t2_teammembers'] = '';
$md->References['t2_games'] = '';

switch ($_GET['step']) {
    default:
        include_once('modules/tournament2/search.inc.php');
        break;

    case 2:
        $md->Delete('tournament_tournaments', 'tournamentid', $_GET['tournamentid']);
        break;
  
    case 10:
        $md->MultiDelete('tournament_tournaments', 'tournamentid');
        break;
}
