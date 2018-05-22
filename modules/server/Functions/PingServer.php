<?php

/**
 * @param string $host
 * @param int $port
 * @return void
 */
function PingServer($host, $port)
{
    global $db, $func, $cfg;

    $cfg["server_ping_refresh"] = (int) $cfg["server_ping_refresh"];
    $server_daten = $db->qry_first("
      SELECT
        UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(lastscan) AS idle,
        type,
        available,
        special_info
      FROM %prefix%server
      WHERE
        (ip = %string%)
        AND (port = %string%)
      HAVING (idle > %int%)", $host, $port, $cfg["server_ping_refresh"]);

    if (rand(0, 2) == 0) {
        // Erreichbarkeit testen
        $success = 0;
        if ($func->ping($host)) {
            $success = 1;
        }
        $available = $success;

        $special_info = "";

        // Weitere Daten für FTPs herrausfinden
        if (($success) && ($server_daten["type"] == "ftp")) {
            if ($fp = @fsockopen($host, $port, $errno, $errstr, 1)) {
                socket_set_blocking($fp, false);
                socket_set_timeout($fp, 1, 500);
                // Benutzernamen senden
                fputs($fp, "USER anonymous\r\n");
                // Passwort senden
                fputs($fp, "PASS erreichbarkeitstest@lansuite.de\r\n");
                // In Passivmode wechseln
                fputs($fp, "PASV\r\n");
                // System abfragen
                fputs($fp, "SYST\r\n");
                // Verzeichnis auflisten
                fputs($fp, "LIST\r\n");
                // Quit senden
                fputs($fp, "QUIT\r\n");

                $res = fread($fp, 1000);

                if ((strpos($res, "logged in") != 0) && (strpos($res, "230") != 0)) {
                    $special_info .= "<div class=\"tbl_green\">".t('Login als Anonymous erfolgreich')."</div>";
                } else {
                    $special_info .= "<div class=\"tbl_error\">".t('Login als Anonymous fehlgeschlagen')."</div>";
                }
                if ((strpos($res, "Ratio") != 0) && (strpos($res, "426") != 0)) {
                    $special_info .= "<div class=\"tbl_error\">".t('Ratio-FTP (Es muss zuerst etwas hochgeladen werden)')."</div>";
                }
                if ((strpos($res, "Quota") != 0) && (strpos($res, "426") != 0)) {
                    $special_info .= "<div class=\"tbl_error\">".t('Quota-FTP (Es darf nur eine gewisse Menge gezogen werden)')."</div>";
                }
                if ((strpos($res, "Too many") != 0) && (strpos($res, "21") != 0)) {
                    $special_info .= "<div class=\"tbl_error\">".t('<u>Hinweis</u>: Zu viele User momentan')."</div>";
                }
                if ((strpos($res, "home directory") != 0) && (strpos($res, "530") != 0)) {
                    $special_info .= "<div class=\"tbl_error\">".t('<u>Fehler</u>: Server hat kein Home-Directory gesetzt')."</div>";
                }
                if ((strpos($res, "220") === 0)) {
                    $special_info .= "<div class=\"tbl_black\">: ".substr($res, 4, strpos($res, "\r\n")-2)."</div>";
                }
                if ((strpos($res, "215") != 0)) {
                    $special_info .= "<div class=\"tbl_black\">".t('System').": ".substr(substr($res, strpos($res, "215"), 99), 4, strpos(substr($res, strpos($res, "215"), 99), "\r\n")-2)  ."</div>";
                }
                $error_stri=$res;
                for ($error_stri_num=0; $error_stri_num<10; $error_stri_num++) {
                    if (strpos($error_stri, "530 ") != 0) {
                        if ($error_stri_num == 0) {
                            $special_info .= HTML_NEWLINE . "<div class=\"tbl_black\"><u>".t('Fehlermeldungen').":</u></div>";
                        }
                        $error_stri=substr($error_stri, strpos($error_stri, "530 ")+4, 9999);
                        $special_info .= "<div class=\"tbl_black\">". substr($error_stri, 0, strpos($error_stri, "\r\n")) ."</div>";
                    }
                }
                fclose($fp);

            // Wenn Socketverbindung fehlgeschlagen
            } else {
                $available=2;
                $success=0;
            }
        }

        // Weitere Daten für IRCs herrausfinden
        if (($success) && ($server_daten["type"] == "irc")) {
            if ($fp = @fsockopen($host, $port, $errno, $errstr, 1)) {
                socket_set_blocking($fp, false);
                socket_set_timeout($fp, 1, 500);

                // Liste anfordern
                fputs($fp, "list\r\n");
                // Verabschieden
                fputs($fp, "quit done\r\n");
                $res = fread($fp, 1000);

                // Channel ausgeben
                $special_info .= "<div class=\"tbl_black\"><u>".t('Channels').":</u></div>";
                $channel=$res;
                for ($channel_num=0; $channel_num<10; $channel_num++) {
                    if (strpos($channel, "322 ") != 0) {
                        $channel=substr($channel, strpos($channel, "322 ")+5, 9999);
                        $special_info .= "<div class=\"tbl_black\">". substr($channel, 0, strpos($channel, ":")-1) ."</div>";
                    }
                }
                fclose($fp);

            // Wenn Socketverbindung fehlgeschlagen
            } else {
                $available = 2;
                $success = 0;
            }
        }

        // Ergebins speichern
        if ($special_info =="") {
            $special_info=$server_daten["special_info"];
        }
        $db->qry('UPDATE %prefix%server SET special_info=%string%, available=%string%, scans=scans+1, success=success+%int%, lastscan=NOW() WHERE ((ip = %string%) AND (port=%int%));', $special_info, $available, $success, $host, $port);
    }
}
