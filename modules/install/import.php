<?php

include_once("modules/install/class_import.php");
$import = New Import();

switch($_GET["step"]){
	default:
		$dsp->NewContent($lang["install"]["import_caption"], $lang["install"]["import_subcaption"]);
		$dsp->SetForm("install.php?mod=install&action=import&step=2", "", "", "multipart/form-data");

		$dsp->AddSingleRow("<b>{$lang["install"]["import_file"]}</b>");
		$dsp->AddFileSelectRow("importdata", $lang["install"]["import_import"], "");

 		$dsp->AddFieldsetStart($lang["install"]["import_settings_new"]);
		$dsp->AddCheckBoxRow("rewrite", $lang["install"]["import_settings_overwrite"], "", "", 1, "");
 		$dsp->AddFieldsetEnd();

 		$dsp->AddFieldsetStart($lang["install"]["import_settings_lansurfer"]);
		$dsp->AddTextFieldRow("comment", $lang["install"]["import_comment"], "", "", "", 1);
		$dsp->AddCheckBoxRow("deldb", $lang["install"]["import_deldb"], "", "", 1, "");
		$dsp->AddCheckBoxRow("replace", $lang["install"]["import_replace"], "", "", 1, 1);
		$dsp->AddCheckBoxRow("signon", $lang["install"]["import_signon"], "", "", 1, 1);
		$dsp->AddCheckBoxRow("noseat", $lang["install"]["import_noseat"], "", "", 1, "");
 		$dsp->AddFieldsetEnd();

		$dsp->AddFormSubmitRow("next");
		$dsp->AddBackButton("install.php?mod=install", "install/import"); 
		$dsp->AddContent();
	break;

	case 2:
		$db->connect();

		if ($_GET["filename"] != "") $_FILES['importdata']['name'] = $_GET["filename"];
		switch ($import->GetUploadFileType($_FILES['importdata']['name'])){
			case "xml":
				$header = $import->GetImportHeader($_FILES['importdata']['tmp_name']);
				switch ($header["filetype"]) {
					case "LANsurfer_export":
					case "lansuite_import":
						$import->ImportLanSurfer($_POST["deldb"], $_POST["replace"], $_POST["noseat"], $_POST["signon"], $_POST["comment"]);

						$func->confirmation($lang["install"]["wizard_importupload_success"] . HTML_NEWLINE . HTML_NEWLINE
							. $lang["install"]["wizard_importupload_filetype"] . ": " . $header["filetype"] . HTML_NEWLINE
							. $lang["install"]["wizard_importupload_date"] . ": " . $header["date"] . HTML_NEWLINE
							. $lang["install"]["wizard_importupload_source"] . ": " . $header["source"] . HTML_NEWLINE
							. $lang["install"]["wizard_importupload_event"] . ": " . $header["event"] . HTML_NEWLINE
							. $lang["install"]["wizard_importupload_version"] . ": " . $header["version"] . HTML_NEWLINE
							, "install.php?mod=install&action=import");
					break;

					case "LanSuite":
						$import->ImportXML($_POST["rewrite"]);
						$func->confirmation($lang["install"]["import_success"], "install.php?mod=install&action=import");
					break;

					default:
						$func->Information(str_replace("%FILETYPE%", $header["filetype"], $lang["install"]["import_err_filetype"]), "install.php?mod=install&action=import");
					break;
				}
			break;

			case "csv":
				if ($_GET["filename"] == "") $_GET["filename"] = $func->FileUpload("importdata", "ext_inc/import/");
				$dsp->NewContent($lang["install"]["import_caption"], $lang["install"]["import_subcaption"]);

				$dsp->SetForm("install.php?mod=install&action=import&step=2&filename={$_GET["filename"]}", "", "", "multipart/form-data");
				if ($_POST["seperator"] == "") $_POST["seperator"] = ";";
				$dsp->AddTextFieldRow("seperator", "<b>Trennzeichen</b>", $_POST["seperator"], "");
				$dsp->AddFormSubmitRow("change"); 

				$dsp->AddHRuleRow();
				$dsp->SetForm("install.php?mod=install&action=import&step=3&filename={$_GET["filename"]}&seperator={$_POST["seperator"]}", "", "", "multipart/form-data");
				$dsp->AddDoubleRow("<b>Datenbank Feld</b>", "<b>CSV-Datei Eintrag</b>");

				// Read fields in CVS-file
				$csv_file = file($_GET["filename"]);
				$items = explode($_POST["seperator"], $csv_file[0]);

				// Read fields in user table
				$tables = array('user', 'party_user');
				foreach ($tables as $table){
  				$query = $db->query("DESCRIBE {$config["database"]["prefix"]}$table");
  				while ($row = $db->fetch_array($query)){
  					reset($items);
  					$fields = array();
  					array_push ($fields, "<option value=\"\">-Leer-</option>");
  					$z = 0;
  					foreach ($items as $item) {
  						if ($item == $row["Field"]) $selected = "selected"; else $selected = "";
  						array_push ($fields, "<option $selected value=\"$z\">$z - $item</option>");
  						$z++;
  					}
  					$dsp->AddDropDownFieldRow($table.'--'.$row["Field"], "<b>$table.{$row["Field"]}</b>", $fields, "");
  				}
  				$db->free_result($query);
        }
        
				$dsp->AddFormSubmitRow("next"); 
				$dsp->AddBackButton("install.php?mod=install&action=import", "install/import"); 
				$dsp->AddContent();
			break;
			
			case 'tgz':
			  $import->ImportExtInc($_FILES['importdata']['tmp_name']);
				$func->confirmation($lang["install"]["import_success"], "install.php?mod=install&action=import");
			break;

			default:
				$func->information($lang["install"]["wizard_importupload_unsuportetfiletype"], "install.php?mod=install&action=import");
			break;
		}
	break;

	case 3:
		$db->connect();

		switch ($import->GetUploadFileType($_GET["filename"])){
			case "csv":
				// Get index assignment
				$indexes = array();
				foreach ($_POST as $var => $val) if ($var != "imageField_x" and $var != "imageField_y") {
				  $var = split('--', $var);
				  $table = $var[0];
				  $field = $var[1];
					if ($val) $indexes[$table][$field] = $val;
				}

				// Read CSV file to DB
				$csv_file = file($_GET["filename"]);
				$z = 0;
				foreach ($csv_file as $csv_line) {
					if ($z > 0) {
						$items = explode($_GET["seperator"], $csv_line);

            // User table
						$table = $indexes['user'];
						$sql = '';
					  foreach ($table as $field => $itemnr) $sql .= "$field = '". $func->escape_sql($items[$itemnr]) ."', ";
						$sql = substr($sql, 0, strlen($sql) - 2);

						$db->query("REPLACE INTO {$config["database"]["prefix"]}user SET $sql");
						$userid = $db->insert_id();

            // Party-user table
            if ($userid) {
  						$table = $indexes['party_user'];
  						$sql = '';
  					  foreach ($table as $field => $itemnr) $sql .= "$field = '". $func->escape_sql($items[$itemnr]) ."', ";
  						$sql = substr($sql, 0, strlen($sql) - 2);
  
  						$db->query("REPLACE INTO {$config["database"]["prefix"]}party_user SET user_id = $userid, party_id = {$party->party_id}, $sql");
            }            
					}
					$z++;
				}

				$func->confirmation("CVS Import erfolgreich", "install.php?mod=install&action=import");
			break;

			default:
				$func->information($lang["install"]["wizard_importupload_unsuportetfiletype"], "install.php?mod=install&action=import");
			break;
		}
	break;
}
?> 
