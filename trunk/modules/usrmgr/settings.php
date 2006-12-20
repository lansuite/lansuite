<?php
switch ($_GET["step"]) {
	case 2:
		if (strlen($_POST["signature"]) > 500) {
			$signature_error = $lang["usrmgr"]["settings_err_signature"];
			$_GET["step"] = 1;
		}

		// Avatar Upload
		if ($cfg['user_avatarupload'] and !$_FILES["newavatar"]["error"]) {
			$target = $func->FileUpload("newavatar", "ext_inc/avatare/", "avatar_". $auth["userid"]);
			if (!$target) {
				$newavatar_error = $lang['usrmgr']['settings_upload_error'];
				$_GET["step"] = 1;
			} else {
				if (!$gd->CreateThumb($target, $target, 80, 80)) {
					$picinfo = getimagesize($target);
					if ($picinfo[0] > 80) {
						$newavatar_error = $lang["usrmgr"]["settings_err_newavatar_width"];
						$_GET["step"] = 1;
					} elseif ($picinfo[1] > 80) {
						$newavatar_error = $lang["usrmgr"]["settings_err_newavatar_height"];
						$_GET["step"] = 1;
					} elseif ($picinfo[2] < 1 OR $picinfo[2] > 3) {
						$newavatar_error = $lang["usrmgr"]["settings_err_newavatar_type"];
						$_GET["step"] = 1;
					}
				}
			}
		}
	break;
}


switch ($_GET["step"]) {
	default:
		$res = $db->query_first("SELECT design, avatar_path, signature FROM {$config["tables"]["usersettings"]} WHERE userid = '{$auth["userid"]}'");
		if ($_POST["signature"] == "") $_POST["signature"] = $res["signature"];
		if ($_POST["design"] == "") $_POST["design"] = $res["design"];
		if ($_POST["avatar"] == "") $_POST["avatar"] = $res["avatar_path"];

		$dsp->NewContent($lang["usrmgr"]["settings_caption"], $lang["usrmgr"]["settings_subcaption"]);
		$dsp->SetForm("index.php?mod=usrmgr&action=settings&step=2", "", "", "multipart/form-data");

		if ($cfg["user_design_change"]) {
			#### Default Design
			// Open the design-dir
			$design_dir = opendir("design/");

			// Reading all subdirs from design and fill them into an array
			while (false != ($design_contents = readdir($design_dir))) {
				if($design_contents != "." AND $design_contents != "..") $found_designs[] = $design_contents;
			}

			if ($_POST["design"] == "") $_POST["design"] = $config['lansuite']['default_design'];
			// Check foreach subdir in design dir if it contains a vaild design.xml-file
			$t_array = array();
			foreach ($found_designs AS $found_design) {	        	
				$xml_file 	= @fopen("design/" . $found_design . "/design.xml","r");
				$xml_content    = @fread($xml_file, filesize("design/" . $found_design . "/design.xml"));
				if ($xml_content != "") {
					($_POST["design"] == $found_design) ? $selected = "selected" : $selected = "";
					array_push ($t_array, "<option $selected value=\"$found_design\">". $xml->get_tag_content("name", $xml_content) ."</option>");
				}
				@fclose($xml_file);
			}
			$dsp->AddDropDownFieldRow("design", $lang["usrmgr"]["settings_design"], $t_array, "", 1);
		}

		$dsp->AddPictureDropDownRow("avatar", $lang["usrmgr"]["settings_avatar"], "ext_inc/avatare", "", 1, $_POST["avatar"]);

		if ($cfg["user_avatarupload"]) {
			$dsp->AddFileSelectRow("newavatar", $lang["usrmgr"]["settings_newavatar"], $newavatar_error, "", "", 1);
		}

		$dsp->AddTextAreaRow("signature", $lang["usrmgr"]["settings_signature"], $_POST["signature"], $signature_error, "", "", 1);

		$dsp->AddFormSubmitRow("next");
		$dsp->AddBackButton("index.php", "usrmgr/usersettings"); 
		$dsp->AddContent();
	break;
	
	// Einstellungen speichern
	case 2:
		if ($target != "") $avatar = substr($target, strrpos($target, "/"), strlen($target));
		else $avatar = $_POST["avatar"];

		if ($_POST["design"] == "") $_POST["design"] = $config["lansuite"]["default_design"];

		$res = $db->query_first("SELECT userid FROM {$config["tables"]["usersettings"]} WHERE userid = '{$auth["userid"]}'");

		if ($res["userid"] == ""){
			$db->query("INSERT INTO {$config["tables"]["usersettings"]} SET
							design = '{$_POST["design"]}',
							avatar_path = '$avatar',
							signature = '{$_POST["signature"]}',
							userid = '{$auth["userid"]}'
							");
		} else {
			$db->query("UPDATE {$config["tables"]["usersettings"]} SET
							design = '{$_POST["design"]}',
							avatar_path = '$avatar',
							signature = '{$_POST["signature"]}'
							WHERE userid = '{$auth["userid"]}'
							");
		}

		$func->confirmation($lang["usrmgr"]["settings_success"], "index.php?mod=usrmgr&action=settings");

		$auth["design"] = $_POST["design"];
	break;
} // switch
?>
