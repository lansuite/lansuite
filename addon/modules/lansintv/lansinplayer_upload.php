<?

$lansintv_data = $db->query_first("SELECT max_upload_size, upload_directory FROM {$config["tables"]["lansintv_admin"]}");

// Error-Switch
switch ($_GET["step"]) {
	case 2:
		for ($i = 0; $i <= 2; $i++) {

			if ($_POST["categorie"][$i] == "-- Bitte ausw�hlen --") {
				$cat_error[$i] = "Bitte w�hlen sie eine Kategorie aus";
				$_GET["step"] = 1;
			}


			switch ($_FILES["userfile"]["error"][$i]) {
				default:
				case 0: // OK
				break;		

				case 1:
					$file_error = "Die hochgeladene Datei ist zu gross (PHP.ini-Limit).";
					$_GET["step"] = 1;
				break;

				case 2:
					$file_error = "Die hochgeladene Datei ist zu gross.";
					$_GET["step"] = 1;
				break;

				case 3:
					$file_error = "Die Datei ist koruppt, bitte versuchen sie es erneut.";
					$_GET["step"] = 1;
				break;
			}
			$categorie=$_POST["categorie"][$i];

			// make sure charcaters are ok.
			$_FILES['userfile']['name'][$i] = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $_FILES['userfile']['name'][$i]);

			$uploaddir = $lansintv_data["upload_directory"];
			$filename = $_FILES['userfile']['name'][$i];
			$uploadfile = $uploaddir . $filename;

			if (move_uploaded_file($_FILES['userfile']['tmp_name'][$i], $uploadfile)) {
				
				shell_exec("chmod -R o+r \"$uploaddir\"");
				$filesize[] = shell_exec("ls -alh \"$uploadfile\" | awk '{print $5}'");

				// check MD5
				$md5sum[] = exec ("md5sum \"$uploadfile\" | awk '{print $1}'");

				//is file banned? 
				$file_banned = $db->query_first("SELECT md5sum FROM lansuite_lansintv WHERE md5sum = '{$md5sum[$i]}'");
				if ($file_banned["md5sum"]) {
					$file_error = "Diese Datei ist leider gebannt!";
					
					$_GET["step"] = 1;
		        }

				// does file exist?
				$file_exist = $db->query_first("SELECT pfad from lansuite_lansintv WHERE md5sum = '{$md5sum[$i]}'");
				if ($file_exist) {
					$file_error = "Die Datei existiert bereits unter dem Namen: ". $file_exist["pfad"];
					$_GET["step"] = 1;
				}

			} // End: if (moved...)
		} // End: for
	break;
}
// Action-Switch
switch ($_GET["step"]){
	default:
		$dsp->NewContent("Lansin-TV (tm) - Upload", "");

	$user_data = $db->query_first("SELECT banned FROM {$config["tables"]["lansintv_user"]} WHERE userid='{$auth["userid"]}'");
	if ($user_data["banned"]) {
		$func->error("Dein Account wurde gebannt! Melde dich bei den Organisatoren um wieder Zugriff zu bekommen", "?mod=lansintv");
		//exit;
	} else {



		//check upload permissions
		if (is_dir($lansintv_data["upload_directory"])) {
			if (!is_writable($lansintv_data["upload_directory"])) $func->error("Fehler: Upload-Verzeichnis ( " . $lansintv_data["upload_directory"] . ") nicht beschreibbar. Bitte Berechtigungen �berpr�fen!", "?mod=lansintv");
		} else $func->error("Fehler: Upload-Verzeichnis existiert nicht. Bitte zuerst anlegen!", "?mod=lansintv");


		// F�r Admins Upload-Size auf PHP.ini-Wert setzen
		if($auth["type"] >= 2) $lansintv_data["max_upload_size"] = (int) ini_get('post_max_size');

		$dsp->AddDoubleRow("Unterst&uuml;tzte Formate", "AVI, MPEG, ASF, ASX, WMV, MP3, GIF, JPEG, PNG");
		$dsp->AddDoubleRow("Max. Upload-Gr&ouml;sse", $lansintv_data["max_upload_size"] . " MB");
		$dsp->AddDoubleRow("", "Die Dateinamen d&uuml;rfen keine Sonderzeichen, oder Umlaute enthalten!");
		$dsp->AddDoubleRow("TIP", "Wenn ihr eure Bilder in eine Zip-Datei packt, wird sie als Diashow abgespielt");
		$dsp->AddHRuleRow();

		// Upload-Size in Bytes umwandeln
		$lansintv_data["max_upload_size"] = $lansintv_data["max_upload_size"] * 1024 * 1024;

		$dsp->SetForm("?mod=lansintv&action=upload&step=2", "", "", "multipart/form-data");
		for ($z = 0; $z <= 2; $z++){
			$categorie_arr = array("-- Bitte ausw&auml;hlen --",
							"Extreme/Stunts/Fun",
							"Comic/Cartoon",
							"Spiele/Computer",
							"Music Video",
							"Erotik",
							"Bilder/Diashow",
							"Sonstiges");
			$t_array = array();
			reset ($categorie_arr);
			while (list ($key, $val) = each ($categorie_arr)) array_push ($t_array, "<option>$val</option>");
			$dsp->AddDropDownFieldRow("categorie", "Datei-Typ", $t_array, $cat_error[$z]);

			$dsp->AddFileSelectRow("userfile[]", "Datei", $file_error[$z], "", $lansintv_data["max_upload_size"], (bool) $z);
			$dsp->AddHRuleRow();
		}

		$dsp->AddFormSubmitRow("send");
		$dsp->AddBackButton("?mod=lansintv", "lansintv/upload");
		$dsp->AddContent();
		
	} // user banned end
	break;


	case 1:
		$func->error("$file_error", "?mod=lansintv");
		break;

	case 2:
		for ($i = 0; $i <= 2; $i++) {
			if (!$_FILES['userfile']['name'][$i] or !$auth["userid"] or !$auth["userid"] or !$filesize[$i] or !$md5sum[$i] or !$_POST["categorie"][$i]) {
				$file_error = "Einer der Upload Variablen ist leer, Abbruch. Bitte dem Systemadmin melden!";
			} else {
				$db->query_first("INSERT INTO {$config["tables"]["lansintv"]} SET
					pfad = '{$_FILES['userfile']['name'][$i]}',
					uploader = '{$auth["userid"]}',
					uid = '{$auth["userid"]}',
					votes = '0',
					size = '{$filesize[$i]}',
					md5sum = '{$md5sum[$i]}',
					categorie = '{$_POST["categorie"][$i]}'
					");
					
		$check_user=$db->query_first("SELECT votes FROM {$config['tables']['lansintv_user']} WHERE userid='{$auth["userid"]}'");
		if ($check_user) {
			$db->query_first("UPDATE {$config['tables']['lansintv_user']} SET uploads =uploads + 1 WHERE userid = '{$auth['userid']}'");
		}else{
			$db->query_first("INSERT into {$config['tables']['lansintv_user']} SET userid = '{$auth["userid"]}', uploads ='1'");
		}
		
				 	
			}
		}

		$func->confirmation("Die Dateien wurden erfolgreich hochgeladen", "?mod=lansintv&action=upload");
	break;
}
?>
