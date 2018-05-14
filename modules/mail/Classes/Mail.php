<?php

namespace LanSuite\Module\Mail;

class Mail
{
    /**
     * @var string
     */
    private $inet_headers;

    /**
     * @var string
     */
    public $error;

    /**
     * @param int $from_userid
     * @param int $to_userid
     * @param string $subject_text
     * @param string $msgbody_text
     * @return bool
     */
    public function create_mail($from_userid, $to_userid, $subject_text, $msgbody_text)
    {
        global $db, $cfg;

        if ($from_userid == "") {
            $this->error = t('Sys-Mail Fehler: Kein Absender angegeben');
            return false;
        }
        if ($to_userid == "") {
            $this->error = t('Sys-Mail Fehler: Kein Empfänger angegeben');
            return false;
        }

        $db->qry("
          INSERT INTO %prefix%mail_messages
          SET
            mail_status = 'active',
            des_status = 'new',
            fromUserID = %int%,
            toUserID = %int%,
            Subject= %string%,
            msgbody= %string%,
            tx_date= NOW()", $from_userid, $to_userid, $subject_text, $msgbody_text);
        $this->error = 'OK';
        
        // Send Info-Mail to receiver
        if ($cfg['sys_internet']) {
            $row = $db->qry_first('SELECT u.username, u.email, u.lsmail_alert FROM %prefix%user AS u WHERE u.userid = %int%', $to_userid);
            if ($row['lsmail_alert']) {
                $this->create_inet_mail($row['username'], $row['email'], t('Benachrichtigung: Neue LS-Mail'), t('Du hast eine neue Lansuite-Mail erhalten. Diese Benachrichtigung kkannst du im System unter "Meine Einstellungen" deaktivieren'));
            }
        }

        return true;
    }

    /**
     * @param int $to_userid
     * @param string $subject_text
     * @param string $msgbody_text
     * @return bool
     */
    public function create_sys_mail($to_userid, $subject_text, $msgbody_text)
    {
        if ($this->create_mail("0", $to_userid, $subject_text, $msgbody_text)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $to_user_name
     * @param string $to_user_email
     * @param string $subject_text
     * @param string $msgbody_text
     * @param string $from
     * @return bool
     */
    public function create_inet_mail($to_user_name, $to_user_email, $subject_text, $msgbody_text, $from = '')
    {
        global $cfg, $board_config;
        
        // The sending mail address must be the sys_part_mail, otherwise some mail-provider won't send the mail.
        // Set default Sender-Mail, if non is set
        if (!$from) {
            $from = $cfg['sys_party_mail'];
        }

        // No special characters in Username!
        $to_user_name = preg_replace('#[^a-zA-Z ]#', '', $to_user_name);

        // Do not send, when in intranet mode
        if (!$cfg['sys_internet']) {
            $this->error = t('Um Internet-Mails zu versenden, muss sich Lansuite im Internet-Modus befinden');
            return false;
        }

        // Set Charset
        if ($cfg['mail_utf8']) {
            $CharsetStr = ' charset=utf-8';
        } else {
            $CharsetStr = '';
            $subject_text = utf8_decode($subject_text);
            $msgbody_text = utf8_decode($msgbody_text);
        }

        $this->inet_headers = "MIME-Version: 1.0\n";
        $this->inet_headers .= "Content-type: text/plain;$CharsetStr\n";
        $this->inet_headers .= "From: $from\n";

        // Cut out double line breaks
        $msgbody_text = str_replace("\r", '', $msgbody_text);

        // SMTP-Mail
        if ($cfg["mail_use_smtp"]) {
            $board_config["smtp_host"] = $cfg["mail_smtp_host"];
            $board_config["smtp_username"] = $cfg["mail_smtp_user"];
            $board_config["smtp_password"] = $cfg["mail_smtp_pass"];
            $board_config["board_email"] = $from;

            include_once("modules/mail/smtp.php");
            if (SMTPMail($to_user_email, $subject_text, $msgbody_text, $this->inet_headers)) {
                return true;
            }

            return false;

        // PHP-Mail
        } else {
            if (@mail("$to_user_name <$to_user_email>", $subject_text, $msgbody_text, $this->inet_headers)) {
                return true;
            }

            return false;
        }
    }
}
