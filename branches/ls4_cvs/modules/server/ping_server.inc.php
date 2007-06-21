<?php

	function ping_server($host, $port) 
	{
		global $db, $config,$func;

		$cfg["server_ping_refresh"] = (int) $cfg["server_ping_refresh"];
		$server_daten = $db->query_first("SELECT UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(lastscan) AS idle, type, available, special_info
			FROM {$config["tables"]["server"]}
			WHERE (ip = '$host') AND (port = $port)
			HAVING (idle > {$cfg["server_ping_refresh"]})
			");

		if (rand(0, 2) == 0){
			
			// Erreichbarkeit testen
			$success = 0;
			if($func->ping($host)) $success = 1;
			$available = $success;

			$special_info = "";
			// Weitere Daten für FTPs herrausfinden
			if (($success) && ($server_daten["type"] == "ftp")) if ($fp = @fsockopen ($host, $port, $errno, $errstr, 1))
			{
				socket_set_blocking($fp, false);
				socket_set_timeout($fp, 1, 500);    					
				//Benutzernamen senden
				fputs ($fp, "USER anonymous\r\n");
				//Passwort senden
				fputs ($fp, "PASS erreichbarkeitstest@lansuite.de\r\n");
				//In Pasivmode wechseln
				fputs ($fp, "PASV\r\n");
				//System abfragen
				fputs ($fp, "SYST\r\n");
				//Verzeichnis auflisten
				fputs ($fp, "LIST\r\n");
				//Quit senden
				fputs ($fp, "QUIT\r\n");

				$res = fread($fp, 1000);

				//Antwort auswerten
				if ((strpos($res, "logged in") != 0) && (strpos($res, "230") != 0))
				{
					$special_info .= "<div class=\"tbl_green\">{$lang["server"]["ping_login_ano_success"]}</div>";
				} else {
					$special_info .= "<div class=\"tbl_error\">{$lang["server"]["ping_login_ano_failed"]}</div>";
				}
				if ((strpos($res, "Ratio") != 0) && (strpos($res, "426") != 0))
				{
					$special_info .= "<div class=\"tbl_error\">{$lang["server"]["ping_ratio"]}</div>";
				}
				if ((strpos($res, "Quota") != 0) && (strpos($res, "426") != 0))
				{
					$special_info .= "<div class=\"tbl_error\">{$lang["server"]["ping_quota"]}</div>";
				}
				if ((strpos($res, "Too many") != 0) && (strpos($res, "21") != 0))
				{
					$special_info .= "<div class=\"tbl_error\">{$lang["server"]["ping_to_many"]}</div>";
				}
				if ((strpos($res, "home directory") != 0) && (strpos($res, "530") != 0))
				{
					$special_info .= "<div class=\"tbl_error\">{$lang["server"]["ping_no_home"]}</div>";
				}
				if ((strpos($res, "220") === 0))
				{
					$special_info .= "<div class=\"tbl_black\">: ".substr($res, 4, strpos($res, "\r\n")-2)."</div>";
				}
				if ((strpos($res, "215") != 0))
				{
					$special_info .= "<div class=\"tbl_black\">{$lang["server"]["ping_system"]}: ".substr(substr($res, strpos($res, "215"), 99), 4, strpos(substr($res, strpos($res, "215"), 99), "\r\n")-2)  ."</div>";
				}
				$error_stri=$res;
				for ($error_stri_num=0; $error_stri_num<10; $error_stri_num++) if (strpos($error_stri, "530 ") != 0)
				{
					if ($error_stri_num == 0) { $special_info .= HTML_NEWLINE . "<div class=\"tbl_black\"><u>{$lang["server"]["ping_error_messages"]}:</u></div>"; }
					$error_stri=substr($error_stri, strpos($error_stri, "530 ")+4, 9999);
					$special_info .= "<div class=\"tbl_black\">". substr($error_stri, 0, strpos($error_stri, "\r\n")) ."</div>";
				}
				fclose($fp);

			// Wenn Socketverbindung fehlgeschlagen
			} else {
				$available=2;
				$success=0;
				// echo "$errstr ($errno)<br>\n";
			} // END: If Type=FTP


			// Weitere Daten für IRCs herrausfinden
			if (($success) && ($server_daten["type"] == "irc")) if ($fp = @fsockopen ($host, $port, $errno, $errstr, 1))
			{
				socket_set_blocking($fp, false);
				socket_set_timeout($fp, 1, 500);    					
				//Ident senden
				#fputs ($fp, "identify lansuite\r\n");
				//Nick setzen
				#fputs ($fp, "nick ls_script\r\n");
				//Liste anfordern
				fputs ($fp, "list\r\n");
				//Verabschieden
				fputs ($fp, "quit done\r\n");
				$channel_num=0;
 
				$res = fread($fp, 1000);

				// Channel ausgeben
				$special_info .= "<div class=\"tbl_black\"><u>{$lang["server"]["ping_channels"]}:</u></div>";
				$channel=$res;
				for ($channel_num=0;$channel_num<10;$channel_num++) if (strpos($channel, "322 ") != 0)
				{
					$channel=substr($channel, strpos($channel, "322 ")+5, 9999);
					$special_info .= "<div class=\"tbl_black\">". substr($channel, 0, strpos($channel, ":")-1) ."</div>";
				}
				fclose($fp);

			// Wenn Socketverbindung fehlgeschlagen
			} else {
				$available=2;
				$success=0;
				//echo "$errstr ($errno)<br>\n";
			} // END: If Type=IRC

			// Ergebins speichern
			if ($special_info =="") { $special_info=$server_daten["special_info"]; }
			$special_info = $special_info;
			$db->query("UPDATE {$config["tables"]["server"]} SET special_info='$special_info', available=$available, scans=scans+1, success=success+$success, lastscan=NOW() WHERE ((ip = '$host') AND (port=$port));");

		}
	}
?>
