<?php
include_once("modules/usrmgr/class_usrmgr.php");

include_once("modules/seating/class_seat.php");
$seat2 = new seat2();

class guestlist {

  function SetPaid($userid, $partyid) {
    global $db, $config, $cfg, $func, $auth, $seat2, $usrmgr;

    include_once("modules/mail/class_mail.php");
    $mail = new mail();

    if (!$userid) $func->error(t('Keinen Benutzer ausgewählt'));
    if (!$partyid) $func->error(t('Keine Party ausgewählt'));

    $Messages = array('success' => '', 'error' => '');
    $db->qry('UPDATE %prefix%party_user SET paid = 1, paiddate=NOW() WHERE user_id = %int% AND party_id = %int% LIMIT 1', $userid, $partyid);

    $row = $db->qry_first('SELECT username, email from %prefix%user WHERE userid = %int%', $userid);
    $row2 = $db->qry_first('SELECT name from %prefix%partys WHERE party_id = %int%', $partyid);
    $msgtext = $cfg['signon_paid_email_text'];
    $msgtext = str_replace('%USERNAME%', $row['username'], $msgtext);
    $msgtext = str_replace('%PARTYNAME%', $row2['name'], $msgtext);

    if ($cfg['signon_send_paid_email'] == 1 or $cfg['signon_send_paid_email'] == 3)
        ($mail->create_sys_mail($userid, $cfg['signon_paid_email_subject'], $msgtext))? 
				$Messages['success'] .= $row['username'] .' (System-Mail)'. HTML_NEWLINE : 
				$Messages['error'] .= $row['username'] .' (System-Mail)'. HTML_NEWLINE;

    if ($cfg['signon_send_paid_email'] == 2 or $cfg['signon_send_paid_email'] == 3)
        ($mail->create_inet_mail($row['username'], $row['email'], $cfg['signon_paid_email_subject'], $msgtext))? 
				$Messages['success'] .= $row['username'] .' (Internet-Mail)'. HTML_NEWLINE : 
				$Messages['error'] .= $row['username'] .' (Internet-Mail)'. HTML_NEWLINE;

    // Reserve Seat
    $seat2->ReserveSeatIfPaidAndOnlyOneMarkedSeat($userid);

    $usrmgr->WriteXMLStatFile();

    $func->log_event(t('Benutzer "%1" wurde für die Party "%2" auf "bezahlt" gesetzt', $row['username'], $row2['name']), 1, '', 'Zahlstatus');
    return $Messages;
  }

  function SetNotPaid($userid, $partyid) {
    global $db, $config, $cfg, $func, $auth, $seat2, $usrmgr;

    include_once("modules/mail/class_mail.php");
    $mail = new mail();

    $Messages = array('success' => '', 'error' => '');
    $db->qry('UPDATE %prefix%party_user SET paid = 0, paiddate = "" WHERE user_id = %int% AND party_id = %int% LIMIT 1', $userid, $partyid);

    $row = $db->qry_first('SELECT username, email from %prefix%user WHERE userid = %int%', $userid);
    $row2 = $db->qry_first('SELECT name FROM %prefix%partys WHERE party_id = %int%', $partyid);
    $msgtext = $cfg['signon_not_paid_email_text'];
    $msgtext = str_replace('%USERNAME%', $row['username'], $msgtext);
    $msgtext = str_replace('%PARTYNAME%', $row2['name'], $msgtext);

		if ($cfg['signon_send_paid_email'] == 1 or $cfg['signon_send_paid_email'] == 3)
				($mail->create_sys_mail($userid, $cfg['signon_paid_email_subject'], $msgtext))?
				$Messages['success'] .= $row['username'] .' (System-Mail)'. HTML_NEWLINE
				: $Messages['error'] .= $row['username'] .' (System-Mail)'. HTML_NEWLINE;

		if ($cfg['signon_send_paid_email'] == 2 or $cfg['signon_send_paid_email'] == 3)
				($mail->create_inet_mail($row['username'], $row['email'], $cfg['signon_paid_email_subject'], $msgtext))?
				$Messages['success'] .= $row['username'] .' (Internet-Mail)'. HTML_NEWLINE
				: $Messages['error'] .= $row['username'] .' (Internet-Mail)'. HTML_NEWLINE;

    // Switch seat back to "marked"
    $seat2->MarkSeatIfNotPaidAndSeatReserved($userid);

    $usrmgr->WriteXMLStatFile();

    $func->log_event(t('Benutzer "%1" wurde für die Party "%2" auf "nicht bezahlt" gesetzt', $row['username'], $row2['name']), 1, '', 'Zahlstatus');
    return $Messages;
  }

  function CheckIn($userid, $partyid) {
    global $db, $config, $func;

    // Check paid
    $row = $db->qry_first('SELECT paid FROM %prefix%party_user WHERE user_id = %int% AND party_id = %int% LIMIT 1', $userid, $partyid);
    if (!$row['paid']) return 1;

    $db->qry('UPDATE %prefix%party_user SET checkin = NOW() WHERE user_id = %int% AND party_id = %int% LIMIT 1', $userid, $partyid);

    // Log
    $row = $db->qry_first('SELECT username, email FROM %prefix%user WHERE userid = %int%', $userid);
    $row2 = $db->qry_first('SELECT name FROM %prefix%partys WHERE party_id = %int%', $partyid);
    $func->log_event(t('Benutzer "%1" wurde für die Party "%2" eingecheckt', $row['username'], $row2['name']), 1, '', 'Checkin');
  }

  function CheckOut($userid, $partyid) {
    global $db, $config, $func;

    // Check checkin
    $row = $db->qry_first('SELECT checkin FROM %prefix%party_user WHERE user_id = %int% AND party_id = %int% LIMIT 1', $userid, $partyid);
    if (!$row['checkin']) return 1;

    $db->qry('UPDATE %prefix%party_user SET checkout = NOW() WHERE user_id = %int% AND party_id = %int% LIMIT 1', $userid, $partyid);

    // Log
    $row = $db->qry_first('SELECT username, email FROM %prefix%user WHERE userid = %int%', $userid);
    $row2 = $db->qry_first('SELECT name FROM %prefix%partys WHERE party_id = %int%', $partyid);
    $func->log_event(t('Benutzer "%1" wurde für die Party "%2" ausgecheckt', $row['username'], $row2['name']), 1, '', 'Checkin');
  }

  function UndoCheckInOut($userid, $partyid) {
    global $db, $config, $func;

    $db->qry('UPDATE %prefix%party_user SET checkin = 0, checkout = 0 WHERE user_id = %int% AND party_id = %int% LIMIT 1', $userid, $partyid);

    // Log
    $row = $db->qry_first('SELECT username, email FROM %prefix%user WHERE userid = %int%', $userid);
    $row2 = $db->qry_first('SELECT name FROM %prefix%partys WHERE party_id = %int%', $partyid);
    $func->log_event(t('Einceck- und Auscheckstatus des Benutzers "%1" wurde für die Party "%2" zurückgesetzt', $row['username'], $row2['name']), 1, '', 'Checkin');
  }
}
?>