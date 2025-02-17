<?php

$md = new \LanSuite\MasterDelete();

$md->References['t2_teams'] = '';
$md->References['t2_teammembers'] = '';
$md->References['t2_games'] = '';

match ($_GET['step']) {
    '2' => $md->Delete('tournament_tournaments', 'tournamentid', $_GET['tournamentid']),
    '10' => $md->MultiDelete('tournament_tournaments', 'tournamentid'),
    default => include_once('modules/tournament2/search.inc.php'),
};
