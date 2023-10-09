<?php
$dsp->NewContent('Hall of Fame', '');
$dsp->AddSingleRow('Klicke auf ein Turnier um die vollständige Rangliste anzeigen zu lassen!<br /><br />');

$sqlQuery = '
	SELECT
		`party_id`,
		`name`
	FROM `%prefix%partys`
	ORDER BY `party_id` DESC';

$partyRows = $database->queryWithFullResult($sqlQuery);
foreach ($partyRows as $partyRow) {
	$partyID = $partyRow['party_id'];
	$partyName = $partyRow['name'];

	$sqlQuery = '
		SELECT
			`tournamentid`,
			`party_id`,
			`name`,
			`game`,
			`icon`,
			`status`,
			`teamplayer`
		FROM `%prefix%tournament_tournaments`
		WHERE
			`party_id` = ?
			AND `status` NOT LIKE \'invisible\'';
	$tournamentRows = $database->queryWithFullResult($sqlQuery, [$partyID]);

	// If there are tournaments, print party name headline
	if (count($tournamentRows) > 0) {
		$dsp->AddSingleRow("<br><strong><b>$partyName</b></strong>");
	}

	foreach ($tournamentRows as $tournamentRow) {
		$mail = new \LanSuite\Module\Mail\Mail();
		$seat2 = new \LanSuite\Module\Seating\Seat2();
		$tfunc = new \LanSuite\Module\Tournament2\TournamentFunction($mail, $seat2);
  		$rankingData = $tfunc->get_ranking($tournamentRow['tournamentid']);

		$tournamentLink = '<a href="index.php?mod=tournament2&action=details&tournamentid=' . $tournamentRow['tournamentid'] . '">' . $tournamentRow['name'] . '</a>';
		if($tournamentRow['status'] == 'open') {
			$dsp->AddDoubleRow($tournamentLink, '<b><span style="color:orange">anmeldung offen</span></b>');

		} else if($tournamentRow['status'] == 'process') {
			$dsp->AddDoubleRow($tournamentLink, '<b><span style="color:green">am laufen</span></b>');
		
		} else if(!array_key_exists(0, $rankingData->name) || $rankingData->name[0] == '') {
			$dsp->AddDoubleRow($tournamentLink, '<b><span style="color:red">ausgefallen</span></b>');
		
		} else {
			$verzeichnis = 'urkunden/' . $partyName . '/' . $tournamentRow['name'];
			$verz_enc = 'urkunden/' . rawurlencode($partyName) . '/' . rawurlencode($tournamentRow['name']);

			$ag = '';
			$prefix = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			
			if(is_dir($verzeichnis)) {
				$files_in_certificate_dir = scandir($verzeichnis);
				$any_certificate_found = false;
				
				for($p = 1; $p < 4; $p++) {
					$search_pattern = "/platz\s?$p/i";
					$search_pattern_2 = "/$p/";
					$matched_anything = false;
					
					foreach($files_in_certificate_dir as $filename) {
						if(preg_match($search_pattern, $filename)) {
							$ag = $ag . '<a href="' . $verz_enc . '/' . $filename . '"><span class="rng_platz">' . $p . '. Platz</span></a>&nbsp;&nbsp;&nbsp;&nbsp;';
							$matched_anything = true;
						
						} else if(preg_match($search_pattern_2, $filename)) {
							$ag = $ag . '<a href="'. $verz_enc . '/' . $filename . '"><span class="rng_platz">' . $p . '". Platz</span></a>&nbsp;&nbsp;&nbsp;&nbsp;';
							$matched_anything = true;
						}
					}
					
					if(!$matched_anything) {
						$ag = $ag . '&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					
					} else {
						$any_certificate_found = true;
					}
				}
				
				if($any_certificate_found) {
					$ag = $prefix . 'Urkunden:&nbsp;&nbsp;&nbsp;' . $ag;

				} else {
					$ag = '';
				}
			}
			
			if($ag == '') {
				$ag = $prefix . '<font style="color:#C4C4C4;">Keine Urkunden gefunden</font>';
			}

			$dsp->AddTripleRow(
				'<a href="index.php?mod=tournament2&action=rangliste&step=2&tournamentid=' . $tournamentRow['tournamentid'] . '">' . $tournamentRow['name'] . '</a>',
				'Gewinner: <b>' . $rankingData->name[0] . '</b>',
				'nope',
				'<div style="width:250px;text-align:left">' . $ag . '</div>'
			);
		}
	}
	
	$dsp->AddHRuleRow();
}

$dsp->AddSingleRow('&nbsp;');
