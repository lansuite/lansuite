<?php

/**
 * Replacement or substitute for PHP's mail command
 *
 * @param string $mail_to
 * @param string $subject
 * @param string $message
 * @param string $headers
 * @return bool
 */
function SMTPMail($mail_to, $subject, $message, $headers = '')
{
    global $board_config;

    // Fix any bare linefeeds in the message to make it RFC821 Compliant.
    $message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);

    if ($headers != '') {
        if (is_array($headers)) {
            if (sizeof($headers) > 1) {
                $headers = join("\n", $headers);
            } else {
                $headers = $headers[0];
            }
        }
        $headers = chop($headers);

        // Make sure there are no bare linefeeds in the headers
        $headers = preg_replace('#(?<!\r)\n#si', "\r\n", $headers);

        // Ok this is rather confusing all things considered,
        // but we have to grab bcc and cc headers and treat them differently
        // Something we really didn't take into consideration originally
        $header_array = explode("\r\n", $headers);
        @reset($header_array);

        $headers = '';
        while (list(, $header) = each($header_array)) {
            if (preg_match('#^cc:#si', $header)) {
                $cc = preg_replace('#^cc:(.*)#si', '\1', $header);
            } elseif (preg_match('#^bcc:#si', $header)) {
                $bcc = preg_replace('#^bcc:(.*)#si', '\1', $header);
                $header = '';
            }
            $headers .= ($header != '') ? $header . "\r\n" : '';
        }

        $headers = chop($headers);
        $cc = explode(', ', $cc);
        $bcc = explode(', ', $bcc);
    }

    if (trim($subject) == '') {
        echo "No email Subject specified";
    }

    if (trim($message) == '') {
        echo "Email message was blank";
    }

    // Ok we have error checked as much as we can to this point let's get on
    // it already.
    if ($socket = fsockopen($board_config['smtp_host'], 25, $errno, $errstr, 20)) {
        // Wait for reply
        ServerParse($socket, "220", __LINE__);

        // Do we want to use AUTH?, send RFC2554 EHLO, else send RFC821 HELO
        // This improved as provided by SirSir to accomodate
        if (!empty($board_config['smtp_username']) && !empty($board_config['smtp_password'])) {
            fputs($socket, "EHLO " . $board_config['smtp_host'] . "\r\n");
            ServerParse($socket, "250", __LINE__);

            fputs($socket, "AUTH LOGIN\r\n");
            ServerParse($socket, "334", __LINE__);

            fputs($socket, base64_encode($board_config['smtp_username']) . "\r\n");
            ServerParse($socket, "334", __LINE__);

            fputs($socket, base64_encode($board_config['smtp_password']) . "\r\n");
            ServerParse($socket, "235", __LINE__);
        } else {
            fputs($socket, "HELO " . $board_config['smtp_host'] . "\r\n");
            ServerParse($socket, "250", __LINE__);
        }

        // From this point onward most server response codes should be 250
        // Specify who the mail is from....
        fputs($socket, "MAIL FROM: <" . $board_config['board_email'] . ">\r\n");
        ServerParse($socket, "250", __LINE__);

        // Add an additional bit of error checking to the To field.
        $mail_to = (trim($mail_to) == '') ? 'Undisclosed-recipients:;' : trim($mail_to);
        if (preg_match('#[^ ]+\@[^ ]+#', $mail_to)) {
            fputs($socket, "RCPT TO: <$mail_to>\r\n");
            ServerParse($socket, "250", __LINE__);
        }

        // Ok now do the CC and BCC fields...
        @reset($bcc);
        while (list(, $bcc_address) = each($bcc)) {
            // Add an additional bit of error checking to bcc header...
            $bcc_address = trim($bcc_address);
            if (preg_match('#[^ ]+\@[^ ]+#', $bcc_address)) {
                fputs($socket, "RCPT TO: <$bcc_address>\r\n");
                ServerParse($socket, "250", __LINE__);
            }
        }

        @reset($cc);
        while (list(, $cc_address) = each($cc)) {
            // Add an additional bit of error checking to cc header
            $cc_address = trim($cc_address);
            if (preg_match('#[^ ]+\@[^ ]+#', $cc_address)) {
                fputs($socket, "RCPT TO: <$cc_address>\r\n");
                ServerParse($socket, "250", __LINE__);
            }
        }

        // Ok now we tell the server we are ready to start sending data
        fputs($socket, "DATA\r\n");

        // This is the last response code we look for until the end of the message.
        ServerParse($socket, "354", __LINE__);

        // Send the Subject Line...
        fputs($socket, "Subject: $subject\r\n");

        // Now any custom headers....
        fputs($socket, "$headers\r\n\r\n");

        // Ok now we are ready for the message...
        fputs($socket, "$message\r\n");

        // Ok the all the ingredients are mixed in let's cook this puppy...
        fputs($socket, ".\r\n");
        ServerParse($socket, "250", __LINE__);

        // Now tell the server we are done and close the socket...
        fputs($socket, "QUIT\r\n");

        fclose($socket);
    } else {
        echo "Could not connect to smtp host : $errno : $errstr";
        return false;
    }

    return true;
}
