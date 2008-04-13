<?php
  $templ['home']['show']['item']['info']['caption'] = t('Statistiken') . " " . $_SESSION['party_info']['name'];
  $templ['home']['show']['item']['control']['row'] = '';
  
if ($party->count > 0) {

  // With or without admins?
  if ($cfg['guestlist_showorga'] == 0) $querytype = 'type = 1';
  else $querytype = 'type >= 1';

	if ($party->party_id != '') {

    // User paid / Checked In / Checked Out
		$user_paid = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["party_user"]} AS p
      LEFT JOIN {$config["tables"]["user"]} ON user_id=userid
      WHERE $querytype AND (p.paid > 0) AND p.party_id={$party->party_id}
      ");
		$user_checkin = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["party_user"]} AS p
      LEFT JOIN {$config["tables"]["user"]} ON user_id=userid
      WHERE p.checkin>1 AND p.checkout=0 AND $querytype AND p.party_id={$party->party_id}
      ");
		$user_checkout = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["party_user"]} AS p
      LEFT JOIN {$config["tables"]["user"]} ON user_id=userid
      WHERE p.checkout>1 AND $querytype AND p.party_id={$party->party_id}
      ");
    $templ['home']['show']['item']['control']['row'] .= t('Gäste bezahlt / eingecheckt / ausgecheckt') .': '. $user_paid['n'] .' / '. $user_checkin['n'] .' / '. $user_checkout['n'] . HTML_NEWLINE;


    // User overall / online
	$user_online = $db->query("SELECT SQL_CALC_FOUND_ROWS auth.userid
	FROM {$config['tables']['stats_auth']} AS auth
	WHERE (auth.lasthit > ". (time() - 60 * 10) .") AND auth.login = '1' AND auth.userid > 0
	GROUP BY auth.userid
	ORDER BY auth.lasthit
	");
	$online = $db->query_first('SELECT FOUND_ROWS() AS count');
	$visits = $db->query_first("SELECT SUM(visits) AS visits, SUM(hits) AS hits FROM {$config['tables']['stats_usage']}");

    $templ['home']['show']['item']['control']['row'] .= t('Besucher gesamt / Gerade eingeloggt') .": ". $visits['visits'] .' / '. $online['count'] . HTML_NEWLINE;
  }
}

// Additional Admin-Stats	
if ($auth["type"] >= 2) {
  // Troubletickets
  if (in_array('troubleticket', $ActiveModules)) {
    $row6 = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["troubleticket"]} WHERE target_userid = '0'");
    $row7 = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["troubleticket"]}");
    $templ['home']['show']['item']['control']['row'] .= t('Troubletickets') .": ".$row6["n"]." / ".$row7["n"] . HTML_NEWLINE;
  }
  
	// Rental
  if (in_array('rent', $ActiveModules)) {
    $row8 = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["rentuser"]} WHERE back_orgaid = '' AND out_orgaid != ''");
    $row9 = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["rentuser"]} WHERE back_orgaid > '0' AND out_orgaid > '0'");	
    $templ['home']['show']['item']['control']['row'] .= t('Verleih') .": ".$row8["n"]." / ".$row9["n"] . HTML_NEWLINE;
  }		
}			
?>
