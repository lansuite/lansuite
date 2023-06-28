<?php

namespace LanSuite\Module\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class SMTPMail
{

    /**
     * SMTP Host address
     */
    private string $smtpHost;

    /**
     * SMTP Port
     */
    private int $smtpPort;

    /**
     * SMTP Username
     */
    private string $smtpUser;

    /**
     * SMTP Password
     */
    private string $smtpPassword;

    /**
     * Usage of TLS for the connection
     */
    private bool $useTLS;

    private string $mailPattern = "/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/";

    private \LanSuite\Func $func;

    public function __construct(string $host, int $port, bool $tls, string $user, string $password)
    {
        $this->smtpHost = $host;
        $this->smtpPort = empty($port) ? 25 : $port;
        $this->useTLS = $tls;
        $this->smtpUser = $user;
        $this->smtpPassword = $password;
        $this->func = new \LanSuite\Func();
    }

    /**
     * Replacement or substitute for PHP's mail command
     *
     * @param string $from
     * @param string $mail_to
     * @param string $subject
     * @param string $message
     * @param string $headers
     * @return bool
     */
    public function sendMail(string $from, string $mail_to, string $subject, string $message, string $headers = '')
    {
        if (!$this->validateFields($from, $subject, $message)) {
            $this->func->error(t("Not all required mail fields have been set"));
            return false;
        }


        // Fix any bare linefeeds in the message to make it RFC821 Compliant.
        $message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);


        if (!empty($headers)) {

            $headers = is_array($headers) ? implode("\n", $headers) : $headers;

            $headers = rtrim($headers);

            // Make sure there are no bare linefeeds in the headers
            $headers = preg_replace('#(?<!\r)\n#si', "\r\n", $headers);

            // Ok this is rather confusing all things considered,
            // but we have to grab bcc and cc headers and treat them differently
            // Something we really didn't take into consideration originally
            $header_array = explode("\r\n", $headers);
            @reset($header_array);

            $headers = '';
            foreach ($header_array as $header) {
                if (preg_match('#^cc:#si', $header)) {
                    $cc = preg_replace('#^cc:(.*)#si', '\1', $header);
                } elseif (preg_match('#^bcc:#si', $header)) {
                    $bcc = preg_replace('#^bcc:(.*)#si', '\1', $header);
                    $header = '';
                }
                $headers .= ($header != '') ? $header . "\r\n" : '';
            }

            $headers = rtrim($headers);
            $cc = explode(', ', $cc);
            $bcc = explode(', ', $bcc);
        }

        $mail = new PHPMailer(true);

        try {
            //Server settings
            #    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                    //Enable verbose debug output
            $mail->isSMTP();                                          //Send using SMTP
            $mail->Host = $this->smtpHost;                     //Set the SMTP server to send through

            if (!empty($this->smtpUser) && !empty($this->smtpPassword)) {
                $mail->SMTPAuth   = true;
                $mail->Username   = $this->smtpUser;                     //SMTP username
                $mail->Password   = $this->smtpPassword;                 //SMTP password
            }

            if ($this->useTLS == true) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;      //Enable implicit TLS encryption
            }

            $mail->Port = $this->smtpPort;

            $mail->setFrom($from);

            if (preg_match($this->mailPattern, $mail_to)) {
                $mail->addAddress($mail_to);
            }

            $this->addCC($mail, $cc);
            $this->addBCC($mail, $bcc);

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->AltBody = strip_tags($message);

            $mail->send();
        } catch (Exception $e) {
            $this->func->error(t("Message could not be sent. Mailer Error: %1", $mail->ErrorInfo));
            return false;
        }

        return true;
    }

    private function addCC(PHPMailer\PHPMailer\PHPMailer $mailer, array $cc): void
    {
        foreach ($cc as $ccAddress) {
            $ccAddress = trim($ccAddress);
            $ccAddress = filter_var($ccAddress, FILTER_VALIDATE_EMAIL);
            if ($ccAddress) {
                $mailer->addCC($ccAddress);
            }
        }
    }

    private function addBCC(PHPMailer\PHPMailer\PHPMailer $mailer, array $bcc): void
    {
        foreach ($bcc as $bccAddress) {
            $bccAddress = trim($bccAddress);
            $bccAddress = filter_var($bccAddress, FILTER_VALIDATE_EMAIL);
            if ($bccAddress) {
                $mailer->addBCC($bccAddress);
            }
        }
    }

    /**
     * validateFields checks if the incoming strings are not empty.
     */
    private function validateFields(string $from, string $subject, string $message): bool
    {
        if (empty($from) || empty($subject) || empty($message)) {
            return false;
        }
        return true;
    }
}
