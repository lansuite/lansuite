<?php

namespace LanSuite\Module\Mail;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

class SMTPMail {

    private $smtp_host;
    private $smtp_port;
    private $smtp_user;
    private $smtp_password;
    private $use_tls;


    public function __construct($host,$port,$tls, $user, $password)
    {
        $this->smtp_host = $host;
        $this->smtp_port = empty($port) ? 25 : $port;
        $this->use_tls = is_bool($tls) ? $tls : false;
        $this->smtp_user = $user;
        $this->smtp_password = $password;
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
    public function Send(string $from,string $mail_to, string $subject,string $message,string $headers = '')
    {

        // Fix any bare linefeeds in the message to make it RFC821 Compliant.
        $message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);

        if ($headers != '') {
            if (is_array($headers)) {
                if (sizeof($headers) > 1) {
                    $headers = implode("\n", $headers);
                } else {
                    $headers = $headers[0];
                }
            }
            $headers = rtrim($headers);

            // Make sure there are no bare linefeeds in the headers
            $headers = preg_replace('#(?<!\r)\n#si', "\r\n", $headers);

            // Ok this is rather confusing all things considered,
            // but we have to grab bcc and cc headers and treat them differently
            // Something we really didn't take into consideration originally
            $header_array = explode("\r\n", $headers);
            @reset($header_array);

            $headers = '';
            foreach($header_array as $header){
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

        if (trim($subject) == '') {
            echo "No email Subject specified";
        }

        if (trim($message) == '') {
            echo "Email message was blank";
        }


        $mail = new PHPMailer(true);

        try {
            //Server settings
        #    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                    //Enable verbose debug output
            $mail->isSMTP();                                          //Send using SMTP
            $mail->Host = $this->smtp_host;                     //Set the SMTP server to send through

            if(!empty($this->smtp_user) && !empty($this->smtp_password))
            {
                $mail->SMTPAuth   = true;    
                $mail->Username   = $this->smtp_user;                     //SMTP username
                $mail->Password   = $this->smtp_password;                 //SMTP password
            }

            if($use_tls) $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;      //Enable implicit TLS encryption
            $mail->Port = $this->smtp_port;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            $mail->setFrom($from);

            if (preg_match('#[^ ]+\@[^ ]+#', $mail_to)) {
                $mail->addAddress($mail_to);  
            }


            @reset($cc);
            foreach($cc as $cc_address){
                // Add an additional bit of error checking to cc header
                $cc_address = trim($cc_address);
                if (preg_match('#[^ ]+\@[^ ]+#', $cc_address)) {
                    $mail->addCC($cc_address);
                }
            }

            @reset($bcc);
            foreach($bcc as $bcc_address){
                // Add an additional bit of error checking to bcc header...
                $bcc_address = trim($bcc_address);
                if (preg_match('#[^ ]+\@[^ ]+#', $bcc_address)) {
                    $mail->addBCC($bcc_address);
                }
            }


            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->AltBody = strip_tags($message);

            $mail->send();
            echo 'Message has been sent';

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            return false;
        }

        return true;
    }

    private function parse_resp($socket, $resp_code, $line = __LINE__)
    {
        #Read response
        $ok = true;

        while(($buffer = fgets($socket, 256)) !== false) {
            $code = substr($buffer, 3, 1);

            if($code == 500) {
                
                echo "Unrecognized command". HTML_NEWLINE;
                $ok = false;
                break;
            }

            if($code > 500) {
                
                echo "Erros have been occured". HTML_NEWLINE;
                $ok = false;
                break;
            } 

            if($code == ' ')
            {
                echo "Unrecognized response". HTML_NEWLINE;
                $ok = false;
                break;
            }
            
            if($code != $resp_code )
            {
                echo "Ran into problems sending Mail. Response: $server_response " . HTML_NEWLINE;
                $ok = false;
                break;
            }
        }
       
        return $ok;

    }

    private function smtp_write_b64($socket,$data){
        $this->smtp_write($socket,base64_encode($data));
    }

    private function smtp_write($socket,$data) {
        $nl = "\r\n";
        fwrite($socket, $data . $nl);
    }

    private function credentials_empty() {
        return (empty($this->smtp_user) || empty($this->smtp_password)); 
    }


    
}

