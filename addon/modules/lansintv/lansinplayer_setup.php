<?




// upload lansinplayer file and copy it onto the Beamer-PC
switch ($_FILES["userfile"]["error"]) {
				case 0: // OK
					// make sure charcaters are ok.
					$_FILES['userfile']['name'][$i] = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $_FILES['userfile']['name'][$i]);

					$lansintv_data = $db->query_first("SELECT max_upload_size, upload_directory FROM {$config["tables"]["lansintv_admin"]}");
					$uploaddir = $lansintv_data["upload_directory"];
					$filename = $_FILES['userfile']['name'];
					$uploadfile = $uploaddir . $filename;

						if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
							echo "exec";
						}
					
				break;		

				case 1:
					$func->error("Die hochgeladene Datei ist zu gross (PHP.ini-Limit).\n","?mod=lansintv&action=setup");
				break;

				case 2:
					$func->error("Die hochgeladene Datei ist zu gross.\n","?mod=lansintv&action=setup");
				break;

				case 3:
					$func->error("Die Datei ist koruppt, bitte versuchen sie es erneut.\n","?mod=lansintv&action=setup");
				break;
			}



if (get_cfg_var("safe_mode") == 1) {
	$dsp->NewContent("Lansin-TV (tm) - Setup", "");
	$func->error("Sie m&uuml;sen erst in der php.ini safe_mode=Off setzten bevor sie das Modul verweden k&ouml;nnen. Die kann ein Sicherheitsrisiko darstellen, und wird nur f&uuml; erfahrene Administratoren Empfohlen.","?mod=lansintv");
}

switch ($_GET["step"]){
	case 2:
		if ($_POST["lansinplayer_reset"]) {
			//empty tables
			$db->query("TRUNCATE TABLE {$config["tables"]["lansintv"]}");
                        $db->query("TRUNCATE TABLE {$config["tables"]["lansintv_history"]}");
			$db->query("TRUNCATE TABLE {$config["tables"]["lansintv_blacklist_files"]}");
			$db->query("TRUNCATE TABLE {$config["tables"]["lansintv_blacklist_user"]}");

		} else {
			
		$db->query("TRUNCATE TABLE {$config["tables"]["lansintv_admin"]}");
		/*
		$db->query("INSERT INTO {$config["tables"]["lansintv_admin"]} SET
			upload_directory='{$_POST["upload_path"]}',
			max_table_rows = '{$_POST["max_table_rows"]}',
			max_upload_size = '{$_POST["max_upload_size"]}',
			download_prefix = '{$_POST["download_prefix"]}',
			newsticker_text = '{$_POST["newsticker_text"]}',
			ue18_start_hour = '{$_POST["von_value_hours"]}',
			ue18_start_min = '{$_POST["von_value_minutes"]}',
			ue18_stop_hour = '{$_POST["bis_value_hours"]}',
			ue18_stop_min = '{$_POST["bis_value_minutes"]}',
			ssh_user = '{$_POST["ssh_user"]}',
			ssh_host = '{$_POST["ssh_host"]}',
			lansinplayer_client = '{$_POST["lansinplayer_client"]}'
			");
		*/
		// u18 removed
                $db->query("INSERT INTO {$config["tables"]["lansintv_admin"]} SET
                        upload_directory='{$_POST["upload_path"]}',
                        max_table_rows = '{$_POST["max_table_rows"]}',
                        max_upload_size = '{$_POST["max_upload_size"]}',
                        download_prefix = '{$_POST["download_prefix"]}',
                        newsticker_text = '{$_POST["newsticker_text"]}',
                        ssh_user = '{$_POST["ssh_user"]}',
                        ssh_host = '{$_POST["ssh_host"]}',
                        player_bin = '{$_POST["player_bin"]}',
                        player_flags = '{$_POST["player_flags"]}'
                        ");
		$func->confirmation("Daten erfolgreich aktuallisiert", "?mod=lansintv&action=setup");
		}
		
		
	break;

	default:

		$dsp->NewContent("Lansin-TV (tm) - Setup", "");
		$dsp->SetForm("?mod=lansintv&action=setup&step=2", "", "", "multipart/form-data");

		$data = $db->query_first("SELECT * FROM {$config["tables"]["lansintv_admin"]}");
		$_POST["upload_path"] = $data["upload_directory"];
		$_POST["max_table_rows"] = $data["max_table_rows"];
		$_POST["max_upload_size"] = $data["max_upload_size"];
		$_POST["download_prefix"] = $data["download_prefix"];
		$_POST["newsticker_text"] = $data["newsticker_text"];
		$_POST["ssh_user"] = $data["ssh_user"];
		$_POST["ssh_host"] = $data["ssh_host"];
		$_POST["player_bin"] = $data["player_bin"];
		$_POST["player_flags"] = $data["player_flags"];




				
		switch ($_GET["lansintv_client"]) {
			case install:
				// 1.) copy files to beamer pc
				if (!get_cfg_var("safe_mode") == 1) {
					$ssh_client_install_mkdir=shell_exec("ssh $_POST[ssh_user]@$_POST[ssh_host] mkdir lansintv_client");
					$ssh_client_install_scp=shell_exec("scp /server/apache/htdocs/ls2_1/ext_prog/lansintv/* $_POST[ssh_user]@$_POST[ssh_host]:lansintv_client/");
					$ssh_client_install_ls=shell_exec("ssh $_POST[ssh_user]@$_POST[ssh_host] ls lansintv_client/");
					$ssh_client_install_exports=shell_exec("ssh $_POST[ssh_user]@$_POST[ssh_host] echo 'export DISPLAY=:0.0' >> ~/.bashrc");
				}
				if ($ssh_client_install_ls) { 
					$func->confirmation("Daten erfolgreich kopiert. <p> $ssh_client_install_ls", "?mod=lansintv&action=setup");
				} else {
					$func->error("Das kopieren der LansinTV-Client Dateien schlug fehl. Der Befehl war: scp /server/apache/htdocs/ls2_1/ext_prog/lansintv/* $_POST[ssh_user]@$_POST[ssh_host]:lansintv_client/\n ","?mod=lansintv&action=setup");
				}
			break;
			
			
			case test:
				if (!get_cfg_var("safe_mode") == 1) {
					$ssh_client_install_ls=shell_exec("ssh $_POST[ssh_user]@$_POST[ssh_host] '~/lansintv_client/vlc -f --noaudio ~/lansintv_client/lansintv_bg.mov -I dummy vlc:quit'&");
					echo "Ran: ssh $_POST[ssh_user]@$_POST[ssh_host] '~/lansintv_client/vlc -f --noaudio ~/lansintv_client/lansintv_bg.mov -I dummy vlc:quit'&";
				}
			break;
		}




		//   function file_write($filename, &$content) {
		if (is_dir($data["upload_path"])) {
		$testfile=$data["upload_path"] . "/testfile";	
			if (!is_writable($filename)) {
				if (!$fp = @fopen($testfile, "w")) {
					$templ['lansinplayer']['case']['setup']['upload_pfad_check'] = "<font color=red>Check upload permissions (write)!</font>";
				}
			}
		} else $templ['lansinplayer']['case']['setup']['upload_pfad_check'] = "<font color=red>Upload directory does not exist!</font>";

		if ($_POST["upload_path"] == "") $_POST["upload_path"] = "/var/www/htdocs/ext_inc/lansintv_upload/";
		if (!is_writable($_POST["upload_path"])) {
				$dsp->AddTextFieldRow("upload_path", "Upload Pfad (vollen Pfad angeben!)", $_POST["upload_path"], "Apache muss hier Schreibrechte haben!", 60);
		} else {
				$dsp->AddTextFieldRow("upload_path", "Upload Pfad (vollen Pfad angeben!)", $_POST["upload_path"], "", 60);
		}
		
		$max_row_arr = array("30" => "30",
					"15" => "15",
					"30" => "30",
					"45" => "45",
					"60" => "60",
					"100" => "100",
					"200" => "200",
					"300" => "300",
					"400" => "400");
		$t_array = array();
		reset ($max_row_arr);
		while (list ($key, $val) = each ($max_row_arr)) {
			($_POST["max_table_rows"] == $key) ? $selected = "selected" : $selected = "";
			array_push ($t_array, "<option $selected value=\"$key\">$val</option>");
		}
		$dsp->AddDropDownFieldRow("max_table_rows", "Max. Tabellen Zeilen", $t_array, "");

		if ($_POST["max_upload_size"] == "") $_POST["max_upload_size"] = (int) ini_get('post_max_size');
		$dsp->AddTextFieldRow("max_upload_size", "Max. Upload Size in MB (hard limit)", $_POST["max_upload_size"], "Php.ini-Limits: <br>- post_max_size=" . ini_get('post_max_size'). "<br>- upload_max_filesize=". ini_get('upload_max_filesize'). "<br>- memory_limit=". ini_get('memory_limit'));
		$dsp->AddTextFieldRow("download_prefix", "Download Prefix", $_POST["download_prefix"], "");
		//$dsp->AddTextFieldRow("newsticker_text", "Newsticker Text", $_POST["newsticker_text"], "");

		// Add time then over 18 clips are allowed to be played.
		/*
		$zeit["hour"]=20;
		$zeit["min"]=45;
		$dsp->AddDateTimeRow("von", "ï¿½18 Clips von:", 0, 0, $zeit, 0, 0, 0, 2);
		$dsp->AddDateTimeRow("bis", "ï¿½18 Clips bis:", 0, "", "", 0, 0, 0, 2);
		*/
		
		if (!get_cfg_var("safe_mode") == 1) {
			$test_ssh_connection=shell_exec("if (ssh $_POST[ssh_user]@$_POST[ssh_host] ps ax); then echo works;fi");
		}
		
		$dsp->AddTextFieldRow("ssh_user", "SSH-Username", $_POST["ssh_user"], "");
		if ($test_ssh_connection) {
			$dsp->AddTextFieldRow("ssh_host", "SSH-Host", $_POST["ssh_host"], "Verbindung konnte hergestellt werden.");
			$dsp->AddDoubleRow("LansinTV-Client testen", "<a href=\"?mod=lansintv&action=setup&lansintv_client=test\">LansinTV-Client testen</a>&nbsp");
			
			// if connection can be established, add LansinPlayer upload row:
			$dsp->AddFileSelectRow("userfile", "LansinPlayer Installationsdatei", "", "", $lansintv_data["max_upload_size"], (bool) $z);
				
		} else {
			$dsp->AddTextFieldRow("ssh_host", "SSH-Host", $_POST["ssh_host"], "Connection could not be established. Please read the Documentation.");
		}
		
		if ($_POST["player_bin"] == "") $_POST["player_bin"] = "/usr/local/bin/vlc";
		$dsp->AddTextFieldRow("player_bin", "Voller Pfad zum einem Player (auf dem Beamer-PC)", $_POST["player_bin"], "", 60);
		
		if ($_POST["player_flags"] == "") $_POST["player_flags"] = "vlc:quit -I dummy -f";
		$dsp->AddTextFieldRow("player_flags", "Optionen f&uuml;r den Player (z.B. Full screen, noaudio, etc..)", $_POST["player_flags"], "", 60);
	
		$dsp->AddTextFieldRow("crontabt", "Bitte folgenden stündlichen Crontab eintragen", $_POST["crontab"], "echo \"UPDATE lansuite_user SET votes_left = '10'\" |mysql -u USERNAME -pPASSWORD -h HOST -D DATABASE");

		$dsp->AddFormSubmitRow("add");
		$dsp->AddBackButton("?mod=lansintv", "lansintv/setup"); 
		$dsp->AddContent();
	break;
}
?>
