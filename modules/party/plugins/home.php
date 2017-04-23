<?php
$smarty->assign('caption', t('Party') . " " . $_SESSION['party_info']['name']);
$content = '';
  
if ($party->count > 0) {
  // With or without admins?
    if ($cfg['guestlist_showorga'] == 0) {
        $querytype = 'type = 1';
    } else {
        $querytype = 'type >= 1';
    }

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
        $content .= t('Gäste bezahlt / eingecheckt / ausgecheckt') .': '. $user_paid['n'] .' / '. $user_checkin['n'] .' / '. $user_checkout['n'] . HTML_NEWLINE;


    // User overall / online
    /*
    $user_online = $db->qry("SELECT SQL_CALC_FOUND_ROWS auth.userid
    FROM %prefix%stats_auth AS auth
    WHERE (auth.lasthit > %int%) AND auth.login = '1' AND auth.userid > 0
    GROUP BY auth.userid
    ORDER BY auth.lasthit
    ", (time() - 60 * 10));
    $online = $db->qry_first('SELECT FOUND_ROWS() AS count');
    */
        $online = count($authentication->online_users);
        $visits = $db->qry_first("SELECT SUM(visits) AS visits, SUM(hits) AS hits FROM %prefix%stats_usage");
        $content .= t('Besucher gesamt / Gerade eingeloggt') .": ". $visits['visits'] .' / '. $online['count'] . HTML_NEWLINE;
    }
}
