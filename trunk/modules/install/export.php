<?php

include("modules/install/class_export.php");
$export = New Export();

switch($_GET["step"]){
	default:
		$dsp->NewContent($lang["install"]["export_caption"], $lang["install"]["export_subcaption"]);
		$dsp->SetForm("install.php?mod=install&action=export&step=2", "", "", "");

		$type_array = array("xml" => $lang["install"]["export_xml_complete"],
			"xml_modules" => $lang["install"]["export_xml_module"],
			"xml_tables" => $lang["install"]["export_xml_tables"],
			"csv_complete" => $lang["install"]["export_cvs_complete"],
			"csv_sticker" => $lang["install"]["export_cvs_sticker"],
			"csv_card" => $lang["install"]["export_cvs_card"],
			"ext_inc_data" => $lang['install']['export_data_ext_inc']
			);
		$t_array = array();
		while (list ($key, $val) = each ($type_array)) {
			array_push ($t_array, "<option $selected value=\"$key\">$val</option>");
		}
		$dsp->AddDropDownFieldRow("type", $lang["tourney"]["t_add_ngl_game"], $t_array, "", 1);

		$dsp->AddFormSubmitRow("next");
		$dsp->AddBackButton("install.php?mod=install", "install/export"); 
		$dsp->AddContent();
	break;

	case 2:
		$db->connect();
		$dsp->NewContent($lang["install"]["export_caption"], $lang["install"]["export_subcaption"]);

		switch ($_POST["type"]){
			case "xml":	
				$dsp->SetForm("index.php?mod=install&action=export&design=base&type={$_POST["type"]}&step=3", "", "", "");

				$dsp->AddCheckBoxRow("e_struct", $lang["install"]["export_structure"], "", "", 1, 1);
				$dsp->AddCheckBoxRow("e_cont", $lang["install"]["export_content"], "", "", 1, 1);

				$dsp->AddFormSubmitRow("next");
			break;

			case "xml_modules":
				$dsp->SetForm("index.php?mod=install&action=export&design=base&type={$_POST["type"]}&step=3", "", "", "");

				$dsp->AddCheckBoxRow("e_struct", $lang["install"]["export_structure"], "", "", 1, 1);
				$dsp->AddCheckBoxRow("e_cont", $lang["install"]["export_content"], "", "", 1, 1);
				$dsp->AddHRuleRow();

				$res = $db->query("SELECT * FROM {$config["tables"]["modules"]} ORDER BY changeable DESC, caption");
				while ($row = $db->fetch_array($res)){

					if (is_dir("modules/{$row["name"]}/mod_settings")) {
						$found = 0;
						// Try db.xml
						$file = "modules/{$row["name"]}/mod_settings/db.xml";
						if (file_exists($file)) {
							$xml_file = fopen($file, "r");
							$xml_content = fread($xml_file, filesize($file));
							fclose($xml_file);

							$lansuite = $xml->get_tag_content("lansuite", $xml_content);
							$tables = $xml->get_tag_content_array("table", $lansuite);
							foreach ($tables as $table) {
								$table_head = $xml->get_tag_content("table_head", $table);
								$table_name = $xml->get_tag_content("name", $table_head);
								if ($table_name) $found = 1;
							}
						}

						if ($found) $dsp->AddCheckBoxRow("table[{$row["name"]}]", $row["caption"], "Dieses Modul exportieren", "", 1);
					}
				}
				$db->free_result($res);

				$dsp->AddFormSubmitRow("next");
			break;

			case "xml_tables":
				$dsp->SetForm("index.php?mod=install&action=export&design=base&type={$_POST["type"]}&step=3", "", "", "");

				$dsp->AddCheckBoxRow("e_struct", $lang["install"]["export_structure"], "", "", 1, 1);
				$dsp->AddCheckBoxRow("e_cont", $lang["install"]["export_content"], "", "", 1, 1);
				$dsp->AddHRuleRow();

				$res = $db->query("SELECT * FROM {$config["tables"]["modules"]} ORDER BY changeable DESC, caption");
				while ($row = $db->fetch_array($res)){

					if (is_dir("modules/{$row["name"]}/mod_settings")) {
						// Try db.xml
						$file = "modules/{$row["name"]}/mod_settings/db.xml";
						if (file_exists($file)) {
							$xml_file = fopen($file, "r");
							$xml_content = fread($xml_file, filesize($file));
							fclose($xml_file);

							$lansuite = $xml->get_tag_content("lansuite", $xml_content);
							$tables = $xml->get_tag_content_array("table", $lansuite);
							foreach ($tables as $table) {
								$table_head = $xml->get_tag_content("table_head", $table);
								$table_name = $xml->get_tag_content("name", $table_head);
								$dsp->AddCheckBoxRow("table[$table_name]", $table_name, $lang["install"]["export_table"], "", 1);
							}
						}
					}
				}
				$db->free_result($res);

				$dsp->AddFormSubmitRow("next");
			break;

			case "csv_complete":
				$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=export&design=base&type={$_POST["type"]}&step=3\">{$lang["install"]["export_cvs_complete_save"]}</a>", "", "", "");
			break;

			case "csv_sticker":
				$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=export&design=base&type={$_POST["type"]}&step=3\">{$lang["install"]["export_cvs_sticker_save"]}</a>", "", "", "");
			break;

			case "csv_card":
				$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=export&design=base&type={$_POST["type"]}&step=3\">{$lang["install"]["export_cvs_card_save"]}</a>", "", "", "");
			break;

      case 'ext_inc_data':
				$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=export&design=base&type={$_POST["type"]}&step=3\">{$lang['install']['export_ext_inc']}</a>", "", "", "");
      break;

			default:
				$func->information($lang["install"]["wizard_importupload_unsuportetfiletype"], "install.php?mod=install&action=import");
			break;
		}

		$dsp->AddBackButton("install.php?mod=install&action=export", "install/export"); 
		$dsp->AddContent();
	break;

	case 3:
		$db->connect();

		switch ($_GET["type"]){
			case "xml":	
				$export->ExportAllTables($_POST["e_struct"], $_POST["e_cont"]);
			break;

			case "xml_modules":
				$export->LSTableHead();
				foreach ($_POST["table"] as $key => $value) {
					if ($key) $export->ExportMod($key, $_POST["e_struct"], $_POST["e_cont"]);
				}
				$export->LSTableFoot();
			break;

			case "xml_tables":
				$export->LSTableHead();
				foreach ($_POST["table"] as $key => $value) {
					if ($key) $export->ExportTable($key, $_POST["e_struct"], $_POST["e_cont"]);
				}
				$export->LSTableFoot();
			break;


			case "csv_complete":
				$output = $export->ExportCSVComplete(";");
				$export->SendExport($output, "lansuite.csv");
			break;

			case "csv_sticker":
				$output = $export->ExportCSVSticker(";");
				$export->SendExport($output, "lansuite_sticker.csv");
			break;

			case "csv_card":
				$output = $export->ExportCSVCard(";");
				$export->SendExport($output, "lansuite_card.csv");
			break;

			case "ext_inc_data":
				$export->ExportExtInc('lansuite_data.tgz');
			break;

			default:
				$func->information($lang["install"]["wizard_importupload_unsuportetfiletype"], "install.php?mod=install&action=import");
			break;
		}
	break;
}
?>
