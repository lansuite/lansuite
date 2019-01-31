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
        $user_paid = $db->qry_first("
          SELECT COUNT(*) as n FROM %prefix%party_user AS p
          LEFT JOIN %prefix%user ON user_id=userid
          WHERE
            %plain%
            AND (p.paid > 0)
            AND p.party_id=%int%", $querytype, $party->party_id);

        $user_checkin = $db->qry_first("
          SELECT
            COUNT(*) as n
          FROM %prefix%party_user AS p
          LEFT JOIN %prefix%user ON user_id=userid
          WHERE
            p.checkin>1
            AND p.checkout=0
            AND %plain%
            AND p.party_id=%int%", $querytype, $party->party_id);

        $user_checkout = $db->qry_first("
          SELECT
            COUNT(*) as n
          FROM %prefix%party_user AS p
          LEFT JOIN %prefix%user ON user_id=userid
          WHERE
            p.checkout>1
            AND %plain%
            AND p.party_id=%int%", $querytype, $party->party_id);
        $content .= t('Gäste bezahlt / eingecheckt / ausgecheckt') .': '. $user_paid['n'] .' / '. $user_checkin['n'] .' / '. $user_checkout['n'] . HTML_NEWLINE;

        $visits = $db->qry_first("SELECT SUM(visits) AS visits, SUM(hits) AS hits FROM %prefix%stats_usage");
        $content .= t('Besucher gesamt / Gerade eingeloggt') .": ". $visits['visits'] .' / '. count($authentication->online_users) . HTML_NEWLINE;
    }
}
