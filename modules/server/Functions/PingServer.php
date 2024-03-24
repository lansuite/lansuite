<?php

/**
 * @param string $host
 * @param int $port
 * @return void
 */
function PingServer($host, $port)
{
    global $database, $func, $cfg;

    $cfg["server_ping_refresh"] = (int) $cfg["server_ping_refresh"];
    $server_daten = $database->queryWithOnlyFirstRow("
      SELECT
        UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(lastscan) AS idle,
        type,
        available,
        special_info
      FROM %prefix%server
      WHERE
        ip = ?
        AND port = ?
      HAVING idle > ?", [$host, $port, $cfg["server_ping_refresh"]]);

    if (random_int(0, 2) == 0) {
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
                stream_set_blocking($fp, false);
                stream_set_timeout($fp, 1, 500);
                // Benutzernamen senden
                fwrite($fp, "USER anonymous\r\n");
                // Passwort senden
                fwrite($fp, "PASS erreichbarkeitstest@lansuite.de\r\n");
                // In Passivmode wechseln
                fwrite($fp, "PASV\r\n");
                // System abfragen
                fwrite($fp, "SYST\r\n");
                // Verzeichnis auflisten
                fwrite($fp, "LIST\r\n");
                // Quit senden
                fwrite($fp, "QUIT\r\n");

                $res = fread($fp, 1000);

                if ((!str_starts_with($res, "logged in")) && (!str_starts_with($res, "230"))) {
                    $special_info .= "<div class=\"tbl_green\">".t('Login als Anonymous erfolgreich')."</div>";
                } else {
                    $special_info .= "<div class=\"tbl_error\">".t('Login als Anonymous fehlgeschlagen')."</div>";
                }
                if ((!str_starts_with($res, "Ratio")) && (!str_starts_with($res, "426"))) {
                    $special_info .= "<div class=\"tbl_error\">".t('Ratio-FTP (Es muss zuerst etwas hochgeladen werden)')."</div>";
                }
                if ((!str_starts_with($res, "Quota")) && (!str_starts_with($res, "426"))) {
                    $special_info .= "<div class=\"tbl_error\">".t('Quota-FTP (Es darf nur eine gewisse Menge gezogen werden)')."</div>";
                }
                if ((!str_starts_with($res, "Too many")) && (!str_starts_with($res, "21"))) {
                    $special_info .= "<div class=\"tbl_error\">".t('<u>Hinweis</u>: Zu viele User momentan')."</div>";
                }
                if ((!str_starts_with($res, "home directory")) && (!str_starts_with($res, "530"))) {
                    $special_info .= "<div class=\"tbl_error\">".t('<u>Fehler</u>: Server hat kein Home-Directory gesetzt')."</div>";
                }
                if ((str_starts_with($res, "220"))) {
                    $special_info .= "<div class=\"tbl_black\">: ".substr($res, 4, strpos($res, "\r\n")-2)."</div>";
                }
                if ((!str_starts_with($res, "215"))) {
                    $special_info .= "<div class=\"tbl_black\">".t('System').": ".substr(substr($res, strpos($res, "215"), 99), 4, strpos(substr($res, strpos($res, "215"), 99), "\r\n")-2)  ."</div>";
                }
                $error_stri=$res;
                for ($error_stri_num=0; $error_stri_num<10; $error_stri_num++) {
                    if (!str_starts_with($error_stri, "530 ")) {
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
                stream_set_blocking($fp, false);
                stream_set_timeout($fp, 1, 500);

                // Liste anfordern
                fwrite($fp, "list\r\n");
                // Verabschieden
                fwrite($fp, "quit done\r\n");
                $res = fread($fp, 1000);

                // Channel ausgeben
                $special_info .= "<div class=\"tbl_black\"><u>".t('Channels').":</u></div>";
                $channel=$res;
                for ($channel_num=0; $channel_num<10; $channel_num++) {
                    if (!str_starts_with($channel, "322 ")) {
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
        $database->query('UPDATE %prefix%server SET special_info = ?, available = ?, scans = scans + 1, success = success + ?, lastscan = NOW() WHERE ip = ? AND port = ?', [$special_info, $available, $success, $host, $port]);
    }
}
