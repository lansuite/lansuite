<?php

switch($_GET["step"]) {
	default:
		if ($_GET["action"] == "change") {
			if ($_GET["sponsorid"] == "") {
				$mastersearch = new MasterSearch($vars, "index.php?mod=sponsor&action=change", "index.php?mod=sponsor&action=change&sponsorid=", "");
				$mastersearch->LoadConfig("sponsor", "", $lang["sponsor"]["add_ms"]);
				$mastersearch->PrintForm();
				$mastersearch->Search();
				$mastersearch->PrintResult();
				$templ['index']['info']['content'] .= $mastersearch->GetReturn();
			} else {
				$sponsor = $db->query_first("SELECT * FROM {$config['tables']['sponsor']} WHERE sponsorid = {$_GET["sponsorid"]}");
				$name = $sponsor["name"];
				$url = $sponsor["url"];
				if (substr($sponsor["pic_path"], 0, 12) == 'html-code://') $pic_code = $sponsor["pic_path"];
				else $pic_url = $sponsor["pic_path"];
				$text = $sponsor["text"];
				$pos = $sponsor["pos"];
				$active = $sponsor["active"];
				$_POST["rotation"] = $sponsor["rotation"];
				$_POST["sponsor"] = $sponsor["sponsor"];
			}
		}
	break;
	case 2:
		$name = $_POST["name"];
		$url = $_POST["url"];
		$text = $_POST["text"];
		$pos = $_POST["pos"];
		$active = $_POST["active"];

		$pic_is_code = 0;
		if ($_POST['pic_code']) {
			$pic_url = $_POST['pic_code'];
			if (substr($sponsor['pic_path'], 0, 12) != 'html-code://') $pic_url = 'html-code://'. $pic_url;
			$pic_is_code = 1;
		} else $pic_url = $_POST["pic_url"];

		// Check for errors
		if ($name == "") {
			$name_error = $lang["sponsor"]["err_name"];
			$_GET["step"] = 1;
		}
		if (strlen($text) > 5000) {
			$text_error = $lang["sponsor"]["err_text"];
			$_GET["step"] = 1;
		}

		if (!$pic_is_code) {
			// Upload new pic, if submitted
			if ($_FILES["pic_upload"]["name"]) $pic = $func->FileUpload("pic_upload", "ext_inc/banner/");
			if ($pic) $pic_url = $pic;
			$org_file_name = substr($_FILES["pic_upload"]['name'], 0, strrpos($_FILES["pic_upload"]['name'], "."));
			$org_ending = substr($_FILES["pic_upload"]['name'], strrpos($_FILES["pic_upload"]['name'], "."), 5);
			if ($_FILES["pic_upload_banner"]["name"]) $pic_banner = $func->FileUpload("pic_upload_banner", "ext_inc/banner/", $org_file_name . "_banner" . $org_ending);
			if ($_FILES["pic_upload_button"]["name"]) $pic_button = $func->FileUpload("pic_upload_button", "ext_inc/banner/", $org_file_name . "_button" . $org_ending);

			// Create according thumbs, if not uploaded already
			if (file_exists("ext_inc/banner/". $_FILES["pic_upload"]["name"])){
				if (!file_exists("ext_inc/banner/". $org_file_name . "_banner" . $org_ending)) {
					$gd->CreateThumb("ext_inc/banner/". $_FILES["pic_upload"]["name"], "ext_inc/banner/". $org_file_name . "_banner" . $org_ending, 468, 60);
				}
				if (!file_exists("ext_inc/banner/". $org_file_name . "_button" . $org_ending)) {
					$gd->CreateThumb("ext_inc/banner/". $_FILES["pic_upload"]["name"], "ext_inc/banner/". $org_file_name . "_button" . $org_ending, 120, 60);
				}
			}
		}
	break;
}

switch($_GET["step"]) {
	default:
		if ($_GET["action"] == "add" || $_GET["sponsorid"] != "") {
			$_SESSION["add_blocker_sponsor"] = FALSE;

			if ($url == "") $url = "http://";
			if ($pic_url == "") $pic_url = "http://";
			if ($pos == "") $pos = "0";
			if ($active == "") $active = SELECTED;
			if ($_POST["rotation"] == "") $_POST["rotation"] = SELECTED;
			if ($_POST["sponsor"] == "") $_POST["sponsor"] = SELECTED;
			if (substr($pic_code, 0, 12) == 'html-code://') $pic_code = substr($sponsor["pic_path"], 12, strlen($sponsor["pic_path"]) - 12);

			$dsp->NewContent($lang["sponsor"]["add_caption"], $lang["sponsor"]["add_sub_caption"]);
			$dsp->SetForm("index.php?mod=sponsor&action={$_GET["action"]}&step=2&sponsorid={$_GET["sponsorid"]}", "", "", "multipart/form-data");

			$dsp->AddTextFieldRow("name", $lang["sponsor"]["add_name"], $name, $name_error);
			$dsp->AddTextFieldRow("url", $lang["sponsor"]["add_url"], $url, "", "", OPTIONAL);
			$dsp->AddHRuleRow();
			$dsp->AddFileSelectRow("pic_upload", $lang["sponsor"]["add_pic_upload"], $pic_error, "", "", OPTIONAL);
			$dsp->AddTextFieldRow("pic_url", $lang["sponsor"]["add_pic"], $pic_url, "", "", OPTIONAL);
			$dsp->AddTextAreaRow("pic_code", $lang["sponsor"]["add_pic_code"], $pic_code, "", "", 4, OPTIONAL);
			$dsp->AddSingleRow($lang["sponsor"]["add_other_sizes"]);
			$dsp->AddFileSelectRow("pic_upload_banner", $lang["sponsor"]["add_pic_upload"] ." (120 x 60)", $pic_error, "", "", OPTIONAL);
			$dsp->AddFileSelectRow("pic_upload_button", $lang["sponsor"]["add_pic_upload"] ." (468 x 60)", $pic_error, "", "", OPTIONAL);
			$dsp->AddHRuleRow();
			$dsp->AddTextFieldRow("pos", $lang["sponsor"]["add_pos"], $pos, "", "", OPTIONAL);
			$dsp->AddCheckBoxRow("sponsor", $lang["sponsor"]["add_sponsor"], $lang["sponsor"]["add_sponsor2"], "", OPTIONAL, $_POST["sponsor"]);
			$dsp->AddCheckBoxRow("rotation", $lang["sponsor"]["add_banner"], $lang["sponsor"]["add_banner2"], "", OPTIONAL, $_POST["rotation"]);
			$dsp->AddCheckBoxRow("active", $lang["sponsor"]["add_active"], $lang["sponsor"]["add_active2"], "", OPTIONAL, $active);
			$dsp->AddTextAreaPlusRow("text", $lang["sponsor"]["add_text"], $text, $text_error);

			$dsp->AddFormSubmitRow("add");
			$dsp->AddBackButton("index.php?mod=sponsor", "sponsor/add");
			$dsp->AddContent();
		}
	break;

	case 2:
		if ($_SESSION["add_blocker_sponsor"] == TRUE) {
			$func->error("NO_REFRESH", "index.php?mod=sponsor&action={$_GET["action"]}");
			break;
		}
		$_SESSION["add_blocker_sponsor"] = TRUE;

		if ($active == "") $active = "0";
		if ($_GET["action"] == "change") {
			$db->query("UPDATE {$config['tables']['sponsor']} SET
								name = '". $func->text2db($name) ."',
								url = '". $func->text2db($url) ."',
								pic_path = '". $func->text2db($pic_url) ."',
								text = '". $func->text2db($text) ."',
								pos = $pos,
								rotation = '{$_POST["rotation"]}',
								sponsor = '{$_POST["sponsor"]}',
								active = $active
								WHERE sponsorid = {$_GET["sponsorid"]}");
			$func->confirmation($lang["sponsor"]["change_success"], "index.php?mod=sponsor&action={$_GET["action"]}");
		}
		if ($_GET["action"] == "add") {
			$db->query("INSERT INTO {$config['tables']['sponsor']} SET
								name = '". $func->text2db($name) ."',
								url = '". $func->text2db($url) ."',
								pic_path = '". $func->text2db($pic_url) ."',
								text = '". $func->text2db($text) ."',
								pos = $pos,
								rotation = '{$_POST["rotation"]}',
								sponsor = '{$_POST["sponsor"]}',
								active = $active
								");
			$func->confirmation($lang["sponsor"]["add_success"], "index.php?mod=sponsor&action={$_GET["action"]}");
		}
	break;
}
?>