<?
include_once("modules/usrmgr/class_usrmgr.php");

class guestlist {

  function SetPaid($userid, $partyid) {
    global $db, $config, $cfg, $func, $mail, $auth, $seat2, $usrmgr;

    if (!$userid) $func->error(t('Keinen Benutzer ausgewählt'));
    if (!$partyid) $func->error(t('Keine Party ausgewählt'));

    $Messages = array('success' => '', 'error' => '');
		$db->query('UPDATE '. $config['tables']['party_user'] .' SET paid = 1, paiddate=NOW() WHERE user_id = '. (int)$userid .' AND party_id='. (int)$partyid .' LIMIT 1');

		$row = $db->query_first('SELECT username, email from '. $config['tables']['user'] .' WHERE userid = '. (int)$userid);
		$row2 = $db->query_first('SELECT name from '. $config['tables']['partys'] .' WHERE party_id = '. (int)$partyid);
    $msgtext = $cfg['signon_paid_email_text'];
    $msgtext = str_replace('%USERNAME%', $row['username'], $msgtext);
    $msgtext = str_replace('%PARTYNAME%', $row2['name'], $msgtext);

		$signonmail = New Mail();
		if ($cfg['signon_send_paid_email'] == 1 or $cfg['signon_send_paid_email'] == 3)
			($signonmail->create_sys_mail($userid, $cfg['signon_paid_email_subject'], $msgtext))?
        $Messages['success'] .= $row['username'] .' (System-Mail)'. HTML_NEWLINE
        : $Messages['error'] .= $row['username'] .' (System-Mail)'. HTML_NEWLINE;

		if ($cfg['signon_send_paid_email'] == 2 or $cfg['signon_send_paid_email'] == 3)
			($signonmail->create_inet_mail($row['username'], $row['email'], $cfg['signon_paid_email_subject'], $msgtext, $auth['email']))?
        $Messages['success'] .= $row['username'] .' (Internet-Mail)'. HTML_NEWLINE
        : $Messages['error'] .= $row['username'] .' (Internet-Mail)'. HTML_NEWLINE;

    // Reserve Seat
    $seat2->ReserveSeatIfPaidAndOnlyOneMarkedSeat($userid);

    $usrmgr->WriteXMLStatFile();

    $func->log_event(t('Benutzer "%1" wurde für die Party "%2" auf "bezahlt" gesetzt', array($row['username'], $row2['name'])), 1, '', 'Zahlstatus');
    return $Messages;
  }

  function SetNotPaid($userid, $partyid) {
    global $db, $config, $cfg, $func, $mail, $auth, $seat2, $usrmgr;

    $Messages = array('success' => '', 'error' => '');
		$db->query('UPDATE '. $config['tables']['party_user'] .' SET paid = 0 WHERE user_id = '. (int)$userid .' AND party_id='. (int)$partyid .' LIMIT 1');

		$row = $db->query_first('SELECT username, email from '. $config['tables']['user'] .' WHERE userid = '. (int)$userid);
		$row2 = $db->query_first('SELECT name from '. $config['tables']['partys'] .' WHERE party_id = '. (int)$partyid);
    $msgtext = $cfg['signon_not_paid_email_text'];
    $msgtext = str_replace('%USERNAME%', $row['username'], $msgtext);
    $msgtext = str_replace('%PARTYNAME%', $row2['name'], $msgtext);

		$signonmail = New Mail();
		if ($cfg['signon_send_paid_email'] == 1 or $cfg['signon_send_paid_email'] == 3)
			($signonmail->create_sys_mail($userid, $cfg['signon_paid_email_subject'], $msgtext))?
        $Messages['success'] .= $row['username'] .' (System-Mail)'. HTML_NEWLINE
        : $Messages['error'] .= $row['username'] .' (System-Mail)'. HTML_NEWLINE;

		if ($cfg['signon_send_paid_email'] == 2 or $cfg['signon_send_paid_email'] == 3)
			($signonmail->create_inet_mail($row['username'], $row['email'], $cfg['signon_paid_email_subject'], $msgtext, $auth['email']))?
        $Messages['success'] .= $row['username'] .' (Internet-Mail)'. HTML_NEWLINE
        : $Messages['error'] .= $row['username'] .' (Internet-Mail)'. HTML_NEWLINE;

    // Switch seat back to "marked"
    $seat2->MarkSeatIfNotPaidAndSeatReserved($userid);

    $usrmgr->WriteXMLStatFile();

    $func->log_event(t('Benutzer "%1" wurde für die Party "%2" auf "nicht bezahlt" gesetzt', array($row['username'], $row2['name'])), 1, '', 'Zahlstatus');
    return $Messages;
  }

  function CheckIn($userid, $partyid) {
    global $db, $config, $func;

    // Check paid
		$row = $db->query_first('SELECT paid FROM '. $config['tables']['party_user'] .' WHERE user_id = '. (int)$userid .' AND party_id='. (int)$partyid .' LIMIT 1');
		if (!$row['paid']) return 1;

		$db->query('UPDATE '. $config['tables']['party_user'] .' SET checkin = NOW() WHERE user_id = '. (int)$userid .' AND party_id='. (int)$partyid .' LIMIT 1');

    // Log
		$row = $db->query_first('SELECT username, email from '. $config['tables']['user'] .' WHERE userid = '. (int)$userid);
		$row2 = $db->query_first('SELECT name from '. $config['tables']['partys'] .' WHERE party_id = '. (int)$partyid);
    $func->log_event(t('Benutzer "%1" wurde für die Party "%2" eingecheckt', array($row['username'], $row2['name'])), 1, '', 'Checkin');
  }

  function CheckOut($userid, $partyid) {
    global $db, $config, $func;

    // Check checkin
		$row = $db->query_first('SELECT checkin FROM '. $config['tables']['party_user'] .' WHERE user_id = '. (int)$userid .' AND party_id='. (int)$partyid .' LIMIT 1');
		if (!$row['checkin']) return 1;

		$db->query('UPDATE '. $config['tables']['party_user'] .' SET checkout = NOW() WHERE user_id = '. (int)$userid .' AND party_id='. (int)$partyid .' LIMIT 1');

    // Log
		$row = $db->query_first('SELECT username, email from '. $config['tables']['user'] .' WHERE userid = '. (int)$userid);
		$row2 = $db->query_first('SELECT name from '. $config['tables']['partys'] .' WHERE party_id = '. (int)$partyid);
    $func->log_event(t('Benutzer "%1" wurde für die Party "%2" ausgecheckt', array($row['username'], $row2['name'])), 1, '', 'Checkin');
  }

  function UndoCheckInOut($userid, $partyid) {
    global $db, $config, $func;

		$db->query('UPDATE '. $config['tables']['party_user'] .' SET checkin = 0, checkout = 0 WHERE user_id = '. (int)$userid .' AND party_id='. (int)$partyid .' LIMIT 1');

    // Log
		$row = $db->query_first('SELECT username, email from '. $config['tables']['user'] .' WHERE userid = '. (int)$userid);
		$row2 = $db->query_first('SELECT name from '. $config['tables']['partys'] .' WHERE party_id = '. (int)$partyid);
    $func->log_event(t('Einceck- und Auscheckstatus des Benutzers "%1" wurde für die Party "%2" zurückgesetzt', array($row['username'], $row2['name'])), 1, '', 'Checkin');
  }
}
?>
