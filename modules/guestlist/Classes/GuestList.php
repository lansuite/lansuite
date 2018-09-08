<?php

namespace LanSuite\Module\GuestList;

use Lansuite\Module\Mail\Mail;
use LanSuite\Module\Seating\Seat2;

class GuestList
{

    /**
     * @var Seat2
     */
    private $seating;

    /**
     * @var \LanSuite\Module\UsrMgr\UserManager
     */
    private $userManager;

    public function __construct(Seat2 $seating, \LanSuite\Module\UsrMgr\UserManager $userManager)
    {
        $this->seating = $seating;
        $this->userManager = $userManager;
    }

    /**
     * @param int $userid
     * @param int $partyid
     * @return array
     */
    public function SetPaid($userid, $partyid)
    {
        global $db, $cfg, $func;

        $mail = new Mail();

        if (!$userid) {
            $func->error(t('Keinen Benutzer ausgewählt'));
        }
        if (!$partyid) {
            $func->error(t('Keine Party ausgewählt'));
        }

        $Messages = array('success' => '', 'error' => '');
        $db->qry('UPDATE %prefix%party_user SET paid = 1, paiddate=NOW() WHERE user_id = %int% AND party_id = %int% LIMIT 1', $userid, $partyid);

        $row = $db->qry_first('SELECT username, email from %prefix%user WHERE userid = %int%', $userid);
        $row2 = $db->qry_first('SELECT name from %prefix%partys WHERE party_id = %int%', $partyid);
        $msgtext = $cfg['signon_paid_email_text'];
        $msgtext = str_replace('%USERNAME%', $row['username'], $msgtext);
        $msgtext = str_replace('%PARTYNAME%', $row2['name'], $msgtext);

        if ($cfg['signon_send_paid_email'] == 1 or $cfg['signon_send_paid_email'] == 3) {
            ($mail->create_sys_mail($userid, $cfg['signon_paid_email_subject'], $msgtext))?
                $Messages['success'] .= $row['username'] .' (System-Mail)'. HTML_NEWLINE :
                $Messages['error'] .= $row['username'] .' (System-Mail)'. HTML_NEWLINE;
        }

        if ($cfg['signon_send_paid_email'] == 2 or $cfg['signon_send_paid_email'] == 3) {
            ($mail->create_inet_mail($row['username'], $row['email'], $cfg['signon_paid_email_subject'], $msgtext))?
                $Messages['success'] .= $row['username'] .' (Internet-Mail)'. HTML_NEWLINE :
                $Messages['error'] .= $row['username'] .' (Internet-Mail)'. HTML_NEWLINE;
        }

        // Reserve Seat
        $this->seating->ReserveSeatIfPaidAndOnlyOneMarkedSeat($userid);

        $this->userManager->WriteXMLStatFile();

        $func->log_event(t('Benutzer "%1" wurde für die Party "%2" auf "bezahlt" gesetzt', $row['username'], $row2['name']), 1, '', 'Zahlstatus');
        return $Messages;
    }

    /**
     * @param int $userid
     * @param int $partyid
     * @return array
     */
    public function SetNotPaid($userid, $partyid)
    {
        global $db, $cfg, $func;

        $mail = new \LanSuite\Module\Mail\Mail();

        $Messages = array('success' => '', 'error' => '');
        $db->qry('UPDATE %prefix%party_user SET paid = 0, paiddate = "" WHERE user_id = %int% AND party_id = %int% LIMIT 1', $userid, $partyid);

        $row = $db->qry_first('SELECT username, email from %prefix%user WHERE userid = %int%', $userid);
        $row2 = $db->qry_first('SELECT name FROM %prefix%partys WHERE party_id = %int%', $partyid);
        $msgtext = $cfg['signon_not_paid_email_text'];
        $msgtext = str_replace('%USERNAME%', $row['username'], $msgtext);
        $msgtext = str_replace('%PARTYNAME%', $row2['name'], $msgtext);

        if ($cfg['signon_send_paid_email'] == 1 or $cfg['signon_send_paid_email'] == 3) {
            ($mail->create_sys_mail($userid, $cfg['signon_paid_email_subject'], $msgtext))?
                $Messages['success'] .= $row['username'] .' (System-Mail)'. HTML_NEWLINE
                : $Messages['error'] .= $row['username'] .' (System-Mail)'. HTML_NEWLINE;
        }

        if ($cfg['signon_send_paid_email'] == 2 or $cfg['signon_send_paid_email'] == 3) {
            ($mail->create_inet_mail($row['username'], $row['email'], $cfg['signon_paid_email_subject'], $msgtext))?
                $Messages['success'] .= $row['username'] .' (Internet-Mail)'. HTML_NEWLINE
                : $Messages['error'] .= $row['username'] .' (Internet-Mail)'. HTML_NEWLINE;
        }

        // Switch seat back to "marked"
        $this->seating->MarkSeatIfNotPaidAndSeatReserved($userid);

        $this->userManager->WriteXMLStatFile();

        $func->log_event(t('Benutzer "%1" wurde für die Party "%2" auf "nicht bezahlt" gesetzt', $row['username'], $row2['name']), 1, '', 'Zahlstatus');
        return $Messages;
    }

    /**
     * @param int $userid
     * @param int $partyid
     * @return int
     */
    public function CheckIn($userid, $partyid)
    {
        global $db, $func;

        // Check paid
        $row = $db->qry_first('SELECT paid FROM %prefix%party_user WHERE user_id = %int% AND party_id = %int% LIMIT 1', $userid, $partyid);
        if (!$row['paid']) {
            return 1;
        }

        $db->qry('UPDATE %prefix%party_user SET checkin = NOW() WHERE user_id = %int% AND party_id = %int% LIMIT 1', $userid, $partyid);

        // Log
        $row = $db->qry_first('SELECT username, email FROM %prefix%user WHERE userid = %int%', $userid);
        $row2 = $db->qry_first('SELECT name FROM %prefix%partys WHERE party_id = %int%', $partyid);
        $func->log_event(t('Benutzer "%1" wurde für die Party "%2" eingecheckt', $row['username'], $row2['name']), 1, '', 'Checkin');
    }

    /**
     * @param int $userid
     * @param int $partyid
     * @return int
     */
    public function CheckOut($userid, $partyid)
    {
        global $db, $func;

        // Check checkin
        $row = $db->qry_first('SELECT checkin FROM %prefix%party_user WHERE user_id = %int% AND party_id = %int% LIMIT 1', $userid, $partyid);
        if (!$row['checkin']) {
            return 1;
        }

        $db->qry('UPDATE %prefix%party_user SET checkout = NOW() WHERE user_id = %int% AND party_id = %int% LIMIT 1', $userid, $partyid);

        // Log
        $row = $db->qry_first('SELECT username, email FROM %prefix%user WHERE userid = %int%', $userid);
        $row2 = $db->qry_first('SELECT name FROM %prefix%partys WHERE party_id = %int%', $partyid);
        $func->log_event(t('Benutzer "%1" wurde für die Party "%2" ausgecheckt', $row['username'], $row2['name']), 1, '', 'Checkin');
    }

    /**
     * @param int $userid
     * @param int $partyid
     * @return void
     */
    public function UndoCheckInOut($userid, $partyid)
    {
        global $db, $func;

        $db->qry('UPDATE %prefix%party_user SET checkin = 0, checkout = 0 WHERE user_id = %int% AND party_id = %int% LIMIT 1', $userid, $partyid);

        // Log
        $row = $db->qry_first('SELECT username, email FROM %prefix%user WHERE userid = %int%', $userid);
        $row2 = $db->qry_first('SELECT name FROM %prefix%partys WHERE party_id = %int%', $partyid);
        $func->log_event(t('Einceck- und Auscheckstatus des Benutzers "%1" wurde für die Party "%2" zurückgesetzt', $row['username'], $row2['name']), 1, '', 'Checkin');
    }

    /**
     * @param int $userid
     * @param int $partyid
     * @return void
     */
    public function SetExported($userid, $partyid)
    {
        global $db;
        
        $db->qry('UPDATE %prefix%party_user SET exported = 1 WHERE user_id = %int% AND party_id = %int% LIMIT 1', $userid, $partyid);
    }

    /**
     * @param int $userid
     * @param int $partyid
     * @return string
     */
    public function Export($userid, $partyid)
    {
        global $db;
        
        $row = $db->qry_first('
          SELECT
            pu.user_id "user_id",
            u.username "username",
            u.firstname "firstname",
            u.name "secondname",
            c.name "clan"
          FROM
            %prefix%party_user pu
            INNER JOIN %prefix%user u ON u.userid = pu.user_id
            LEFT JOIN %prefix%clan c ON c.clanid = u.clanid
          WHERE
            pu.user_id = %int%
            AND pu.party_id = %int%
          LIMIT 1', $userid, $partyid);
            
        return $row['user_id'] . ';' . $row['username'] . ';' . $row['firstname'] . ';' . $row['secondname'] . ';' . $row['clan'];
    }
}
