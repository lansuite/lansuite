<?php

if ($_POST["checkbox"]) {
	switch ($_POST["action_select"]) {

		// Delete entry
		case "del":
			foreach($_POST["checkbox"] AS $item) {
				$menu_intem = $db->query_first("SELECT caption FROM {$config['tables']['info']} WHERE infoID = $item");
				$db->query("DELETE FROM {$config['tables']['menu']} WHERE action = 'show_info2' AND caption = '{$menu_intem["caption"]}'");
				$db->query("DELETE FROM {$config['tables']['info']} WHERE infoID = $item");
			}

			$func->confirmation($lang["info"]["del_success"], "index.php?mod=info2&action=change");
		break;

		// Change active state
		case "active":
			foreach($_POST["checkbox"] AS $item) {
				$menu_intem = $db->query_first("SELECT active, caption, shorttext FROM {$config['tables']['info']} WHERE infoID = $item");
				$info_menu = $db->query_first("SELECT pos FROM {$config['tables']['menu']} WHERE module='info2'");
				if ($menu_intem["active"]) {
					// Set not active and delete menuitem
					$db->query("UPDATE {$config['tables']['info']} SET active = 0 WHERE infoID = $item");
					$db->query("DELETE FROM {$config['tables']['menu']} WHERE action = 'show_info2' AND caption = '{$menu_intem["caption"]}'");
				} else {
					// Set active and write menuitem
					$db->query("UPDATE {$config['tables']['info']} SET active = 1 WHERE infoID = $item");
					$db->query("INSERT INTO {$config['tables']['menu']}
						SET module = 'info2',
						caption = '{$menu_intem["caption"]}',
						hint = '{$menu_intem["shorttext"]}',
						link = '?mod=info2&action=show_info2&submod={$menu_intem["caption"]}',
						requirement = 0,
						level = 0,
						pos = {$info_menu["pos"]},
						action = 'show_info2',
						file = 'show'
						");
				}
			}

			$func->confirmation($lang["info"]["change_active_success"], "index.php?mod=info2&action=change");
		break;
	}


} else switch($_GET["step"]){
	default:
		$dsp->NewContent($lang["info"]["change_caption"], $lang["info"]["change_subcaption"]);
		$dsp->SetForm("index.php?mod=info2&action=change&step=2");
		$dsp->AddFormSubmitRow("add");
		$dsp->AddContent();

		$mastersearch = new MasterSearch($vars, "index.php?mod=info2&action=change", "index.php?mod=info2&action=change&step=2&id=", "");
		$mastersearch->LoadConfig("info2", "", $lang["info2"]["change_ms"]);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;

	case 2:
		if ($_POST["content"] == "" and $_POST["title"] == "" and $_GET["id"] != ""){
			$module = $db->query_first("SELECT info.text, info.caption, info.shorttext, menu.id FROM {$config['tables']['info']} AS info
						LEFT JOIN {$config['tables']['menu']} AS menu ON info.caption = menu.caption AND action = 'show_info2'
						WHERE info.infoID = '{$_GET["id"]}'");
			$_POST["content"] = $module["text"];
			$_POST["title"] = $module["caption"];
			$_POST["subtitle"] = $module["shorttext"];
		}

		$dsp->NewContent($lang["info"]["change_caption_2"], $lang["info"]["change_subcaption_2"]);
		$dsp->SetForm("index.php?mod=info2&action=change&step=3&infoid={$_GET["id"]}&menuid={$module["id"]}");

		$dsp->AddTextFieldRow("title", $lang["info"]["title"], $_POST["title"], $title_error);
		$dsp->AddTextFieldRow("subtitle", $lang["info"]["subtitle"], $_POST["subtitle"], $title_error);

		if ($cfg["info2_use_spaw"]) {
			include "ext_scripts/spaw/spaw_control.class.php";

			if ($cfg["info2_toolbar"] == 1) $tmp_spaw_type = "default";
			else $tmp_spaw_type = "mini";

			$sw = new SPAW_Wysiwyg('content', $_POST["content"], $language, $tmp_spaw_type, 'default', $cfg["info2_width"], $cfg["info2_height"]);
			$dsp->AddSingleRow($sw->show());
		} else $dsp->AddTextAreaRow("content", "", $_POST["content"], "", 80, 25, 0);

		$dsp->AddFormSubmitRow("add");
		$dsp->AddBackButton("index.php?mod=info2", "info2/form"); 
		$dsp->AddContent();
	break;

	case 3:
		if ($_POST["title"] == "" or $_POST["content"] == "") $func->information($lang["info"]["err_missing_fields"], "index.php?mod=info2&action=change&step=2&id={$_GET["id"]}");
		else {
			$info_menu = $db->query_first("SELECT pos FROM {$config['tables']['menu']} WHERE module='info2'");

			if ($_GET["infoid"] == "") {
				/*
				$db->query("INSERT INTO {$config['tables']['menu']}
					SET module = 'info2',
					caption = '{$_POST["title"]}',
					hint = '{$_POST["subtitle"]}',
					link = '?mod=info2&action=show_info2&submod={$_POST["title"]}',
					requirement = 0,
					level = 0,
					pos = {$info_menu["pos"]},
					action = 'show_info2',
					file = 'show'
					");
				*/
				$db->query("INSERT INTO {$config['tables']['info']}
					SET caption = '{$_POST["title"]}',
					shorttext = '{$_POST["subtitle"]}',
					text = '{$_POST["content"]}'");

				$func->confirmation($lang["info"]["add_success"], "index.php?mod=info2&action=change");

			} else {
				$menu_intem = $db->query_first("SELECT active, caption, shorttext FROM {$config['tables']['info']} WHERE infoID = {$_GET["infoid"]}");
				
				if($menu_intem['active'] == 1){
					$db->query("UPDATE {$config['tables']['menu']}
						SET module = 'info2',
						caption = '{$_POST["title"]}',
						hint = '{$_POST["subtitle"]}',
						link = '?mod=info2&action=show_info2&submod={$_POST["title"]}'
						WHERE id = '{$_GET["menuid"]}'");
				}

				$db->query("UPDATE {$config['tables']['info']}
					SET caption = '{$_POST["title"]}',
					shorttext = '{$_POST["subtitle"]}',
					text = '{$_POST["content"]}'
					WHERE infoID = '{$_GET["infoid"]}'");

				$func->confirmation($lang["info"]["change_success"], "index.php?mod=info2&action=change");
			}
		}
	break;
}
?>