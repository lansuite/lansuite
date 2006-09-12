<?php

class signon {

var $birthday;
var $perso;

		
	function SplitStreet($input){
		$pieces = explode(" ", $input);
		$res["nr"] = array_pop($pieces);
		$res["street"] = implode(" ", $pieces);
		return $res;
	}


	function SplitCity($input){
		$pieces = explode(" ", $input);
		$res["plz"] = array_shift($pieces);
		$res["city"] = implode(" ", $pieces);
		return $res;
	}


	function CheckPerso($code){
		$perso_block = explode("<", $code);
		$perso_citycode = substr($perso_block[0], 0, 4);
		$perso_id = substr($perso_block[0], 4, 5);
		$perso_cs1 = substr($perso_block[0], 9, 1);
		$perso_country = substr($perso_block[0], 10, 1);
		$perso_birth = substr($perso_block[2], 0, 6);
		$perso_cs2 = substr($perso_block[2], 6, 1);
		$perso_expiration = substr($perso_block[3], 0, 6);
		$perso_cs3 = substr($perso_block[3], 6, 1);
		$perso_cs4 = substr($perso_block[10], 0, 1);

		// Length Check
		if ((strlen($code) != 36) || 
			(strlen($perso_block[0]) != 11) || (strlen($perso_block[2]) != 7) || (strlen($perso_block[3]) != 7) || (strlen($perso_block[10]) != 1)) return 2;
		
		// Chechsum Check
		else {
			$multiplier = array ("7", "3", "1");

			$cs1 = 0;
			for ($z = 0; $z <= 8; $z ++) {
				$cs1 += (substr($perso_block[0], $z, 1) * $multiplier[$z % 3]);
			}
			$cs1 = $cs1 % 10;

			$cs2 = 0;
			for ($z = 0; $z <= 5; $z ++) {
				$cs2 += (substr($perso_block[2], $z, 1) * $multiplier[$z % 3]);
			}
			$cs2 = $cs2 % 10;

			$cs3 = 0;
			for ($z = 0; $z <= 5; $z ++) {
				$cs3 += (substr($perso_block[3], $z, 1) * $multiplier[$z % 3]);
			}
			$cs3 = $cs3 % 10;

			$cs4 = 0;
			$perso_all = substr($perso_block[0], 0, 10) . $perso_block[2] . $perso_block[3]; 
			for ($z = 0; $z <= 24; $z ++) { 
				 $cs4 += (substr($perso_all, $z, 1) * $multiplier[$z % 3]); 
			}
			$cs4 = $cs4 % 10;

			if (($cs1 != $perso_cs1) || ($cs2 != $perso_cs2) || ($cs3 != $perso_cs3) || ($cs4 != $perso_cs4)){
				return 3;

			// Expiration Check
			} else {
				$perso_expir_timestamp = mktime(0, 0, 0, substr($perso_expiration, 2, 2), substr($perso_expiration, 4, 2), substr($perso_expiration, 0, 2));
				if (time() > $perso_expir_timestamp) return 4;
			}
		}
		return 1;
		// Return Values:
		// 1 = OK
		// 2 = Wrong length
		// 3 = Checksum error
		// 4 = Expired
	}


	function GetLansurfer($username, $password) {
		if (!function_exists("socket_create")) return false;
		else {
			$username = str_replace("@", "%40", $username);
			$username = str_replace(".", "%2E", $username);

			// Session-ID holen
	#		$address = gethostbyname ('www.lansurfer.com');
			$address = "212.112.228.36";
			$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			socket_set_nonblock($socket);
			@socket_set_timeout($socket, 1, 500);
			if ($socket < 0) echo "socket_create() fehlgeschlagen: Grund: " . socket_strerror($socket) . "\n";
			$result = @socket_connect($socket, $address, 80);
			if ($result < 0) echo "socket_connect() fehlgeschlagen.\nGrund: ($result) " . socket_strerror($result) . "\n";
			$in_post = "username=$username&password=$password&savecookie=&submit=Login";
			$content_lengt = strlen($in_post);
			#Accept: image/gif, image/jpeg, */*
			$in = "POST /user/edit.phtml HTTP/1.1
			Host: lansurfer.com
			User-Agent: Mozilla/4.0
			Content-type: application/x-www-form-urlencoded
			Content-length: $content_lengt
			Connection: close

			$in_post";

			@socket_write($socket, $in, strlen($in));
			$out = "";
			while ($line = @socket_read($socket, 2048)) $out .= $line;
			$LS_Session = substr($out, strpos($out, "LS_Session=") + 11, 50);
			$LS_Session = substr($LS_Session, 0, strpos($LS_Session, ";"));
			@socket_close($socket);

			// Login senden und Daten holen
			$opts['http']['method'] =	"POST";
			$opts['http']['header'] =	"Content-type: application/x-www-form-urlencoded\r\n";
										"Content-length: $content_lengt";
			$opts['http']['content'] =	$in_post;

			$lansurfer_site = "";
			$handle = fopen("http://www.lansurfer.com/user/edit.phtml?LS_Session=$LS_Session", 'r', false, stream_context_create($opts));
			while (!feof($handle)) {
				$lansurfer_site .= fgets($handle, 4096);
			}
			fclose($handle);

			// Log out
			$handle = fopen("http://lansurfer.com/user.phtml?action=logout?LS_Session=$LS_Session", 'r', false, stream_context_create($opts));

			// HTML-Daten auswerten
			$input_start = 2;
			$input_end = 2;
			$lansurfer_data = array();
			for ($z = 0; $z < 30 ; $z++) {
				$input_start = strpos($lansurfer_site, "<input", $input_end);
				if ($input_start == 0) break;
				$input_start += 6;
				$input_end = strpos($lansurfer_site, ">", $input_start) - 1;

				$line = substr($lansurfer_site, $input_start, $input_end - $input_start);
				$name = "";
				if (strpos($line, "name=") > 0) {
					$name = substr($line, strpos($line, "name=") + 6, strlen($line));
					if (strpos($name, " ") > 0) $name = substr($name, 0, strpos($name, " ") - 1);

					$value = "";
					if (strpos($line, "value=") > 0) {
						$value = substr($line, strpos($line, "value=") + 7, strlen($line));
						if (strpos($value, " ") > 0) $value = substr($value, 0, strpos($value, " ") - 1);
					}
					$lansurfer_data[$name] = $value;
				}
			}

			return $lansurfer_data;
		}
	}

} // class
?>
