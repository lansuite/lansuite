<?php
  $templ['home']['show']['item']['info']['caption'] = t('Statistiken') . " " . $_SESSION['party_info']['name'];
  $templ['home']['show']['item']['control']['row'] = '';
  
if ($party->count > 0) {

  // With or without admins?
  if ($cfg['guestlist_showorga'] == 0) $querytype = 'type = 1';
  else $querytype = 'type >= 1';

	if ($party->party_id != '') {

    // User paid / Checked In / Checked Out
		$user_paid = $db->qry_first("SELECT count(*) as n FROM %prefix%party_user AS p
      LEFT JOIN %prefix%user ON user_id=userid
      WHERE %string% AND (p.paid > 0) AND p.party_id=%int%
      ", $querytype, $party->party_id);
		$user_checkin = $db->qry_first("SELECT count(*) as n FROM %prefix%party_user AS p
      LEFT JOIN %prefix%user ON user_id=userid
      WHERE p.checkin>1 AND p.checkout=0 AND %string% AND p.party_id=%int%
      ", $querytype, $party->party_id);
		$user_checkout = $db->qry_first("SELECT count(*) as n FROM %prefix%party_user AS p
      LEFT JOIN %prefix%user ON user_id=userid
      WHERE p.checkout>1 AND %string% AND p.party_id=%int%
      ", $querytype, $party->party_id);
    $templ['home']['show']['item']['control']['row'] .= t('Gäste bezahlt / eingecheckt / ausgecheckt') .': '. $user_paid['n'] .' / '. $user_checkin['n'] .' / '. $user_checkout['n'] . HTML_NEWLINE;


    // User overall / online
	$user_online = $db->qry("SELECT SQL_CALC_FOUND_ROWS auth.userid
	FROM %prefix%stats_auth AS auth
	WHERE (auth.lasthit > %int%) AND auth.login = '1' AND auth.userid > 0
	GROUP BY auth.userid
	ORDER BY auth.lasthit
	", (time() - 60 * 10));
	$online = $db->qry_first('SELECT FOUND_ROWS() AS count');
	$visits = $db->qry_first("SELECT SUM(visits) AS visits, SUM(hits) AS hits FROM %prefix%stats_usage");

    $templ['home']['show']['item']['control']['row'] .= t('Besucher gesamt / Gerade eingeloggt') .": ". $visits['visits'] .' / '. $online['count'] . HTML_NEWLINE;
  }
}

// Additional Admin-Stats	
if ($auth["type"] >= 2) {
  // Troubletickets
  if (in_array('troubleticket', $ActiveModules)) {
    $row6 = $db->qry_first("SELECT count(*) as n FROM %prefix%troubleticket WHERE target_userid = '0'");
    $row7 = $db->qry_first("SELECT count(*) as n FROM %prefix%troubleticket");
    $templ['home']['show']['item']['control']['row'] .= t('Troubletickets') .": ".$row6["n"]." / ".$row7["n"] . HTML_NEWLINE;
  }
  
	// Rental
  if (in_array('rent', $ActiveModules)) {
    $row8 = $db->qry_first("SELECT count(*) as n FROM %prefix%rentuser WHERE back_orgaid = '' AND out_orgaid != ''");
    $row9 = $db->qry_first("SELECT count(*) as n FROM %prefix%rentuser WHERE back_orgaid > '0' AND out_orgaid > '0'");	
    $templ['home']['show']['item']['control']['row'] .= t('Verleih') .": ".$row8["n"]." / ".$row9["n"] . HTML_NEWLINE;
  }		
}			
?>
