<?php

include_once("modules/install/class_import.php");
$import = new Import();

switch ($_GET["step"]) {
    default:
        $dsp->NewContent(t('Daten importieren'), t('Hier kannst du Benutzerdaten, die du aus einem anderen System exportiert habst, in Lansuite importieren.'));
        $dsp->SetForm("index.php?mod=install&action=import&step=2", "", "", "multipart/form-data");

        $dsp->AddSingleRow("<b>".t('Zu importierende Datei')."</b>");
        $dsp->AddFileSelectRow("importdata", t('Import (.xml, .csv, .tgz)'), "");

        $dsp->AddFieldsetStart(t('Lansuite-XML-Export'));
        $dsp->AddCheckBoxRow("rewrite", t('Vorhandene Einträge ersetzen'), "", "", 1, "");
        $dsp->AddFieldsetEnd();

        $dsp->AddFieldsetStart(t('LanSurfer-XML-Export'));
        $dsp->AddTextFieldRow("comment", t('Kommentar für alle setzen'), "", "", "", 1);
        $dsp->AddCheckBoxRow("deldb", t('Alte Benutzerdaten löschen'), "", "", 1, "");
        $dsp->AddCheckBoxRow("replace", t('Vorhandene Einträge überschreiben'), "", "", 1, 1);
        $dsp->AddCheckBoxRow("signon", t('Benutzer zur aktuellen Party anmelden'), "", "", 1, 1);
        $dsp->AddCheckBoxRow("noseat", t('Sitzplan NICHT importieren'), "", "", 1, "");
        $dsp->AddFieldsetEnd();

        $dsp->AddFormSubmitRow(t('Weiter'));
        $dsp->AddBackButton("index.php?mod=install", "install/import");
        $dsp->AddContent();
        break;

    case 2:
        $db->connect();

        if ($_GET["filename"] != "") {
            $_FILES['importdata']['name'] = $_GET["filename"];
        }

        switch ($_FILES['importdata']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $func->error('Datei zu groß (upload_max_filesize in php.ini erhöhen, wenn möglich)');
                break;

            case UPLOAD_ERR_FORM_SIZE:
                $func->error('Datei zu groß (MAX_FILE_SIZE in HTML überschritten)');
                break;

            case UPLOAD_ERR_PARTIAL:
                $func->error('Die Datei wurde nur teilweise hochgeladen. Bitte versuche es erneut');
                break;

            case UPLOAD_ERR_NO_FILE:
                $func->error('Es wurde keine Datei hochgeladen');
                break;

            default:
                switch ($import->GetUploadFileType($_FILES['importdata']['name'])) {
                    case "xml":
                        $header = $import->GetImportHeader($_FILES['importdata']['tmp_name']);
                        switch ($header["filetype"]) {
                            case "LANsurfer_export":
                            case "lansuite_import":
                                $import->ImportLanSurfer($_POST["deldb"], $_POST["replace"], $_POST["noseat"], $_POST["signon"], $_POST["comment"]);

                                $func->confirmation(t('Datei-Import erfolgreich.') . HTML_NEWLINE . HTML_NEWLINE
                                . t('Dateityp') . ": " . $header["filetype"] . HTML_NEWLINE
                                . t('Exportiert am/um') . ": " . $header["date"] . HTML_NEWLINE
                                . t('Quelle') . ": " . $header["source"] . HTML_NEWLINE
                                . t('LanParty') . ": " . $header["event"] . HTML_NEWLINE
                                . t('Lansuite-Version') . ": " . $header["version"] . HTML_NEWLINE, "index.php?mod=install&action=import");
                                break;

                            case "LanSuite":
                                $import->ImportXML($_POST["rewrite"]);
                                $func->confirmation(t('Import erfolgreich.'), "index.php?mod=install&action=import");
                                break;

                            default:
                                $func->Information(t('Dies scheint keine Lansuite-kompatible-XML-Datei zu sein. Bitte Überprüfen sie den Eintrag &lt;filetype&gt; am Anfang der XML-Datei (FileType: \'%1\')', $header["filetype"]), "index.php?mod=install&action=import");
                                break;
                        }
                        break;

                    case "csv":
                        if ($_GET["filename"] == "") {
                            $_GET["filename"] = $func->FileUpload("importdata", "ext_inc/import/");
                        }
                        $dsp->NewContent(t('Daten importieren'), t('Hier kannst du Benutzerdaten, die du aus einem anderen System exportiert hast, in Lansuite importieren.'));

                        $dsp->SetForm("index.php?mod=install&action=import&step=2&filename={$_GET["filename"]}", "", "", "multipart/form-data");
                        if ($_POST["seperator"] == "") {
                            $_POST["seperator"] = ";";
                        }
                        $dsp->AddTextFieldRow("seperator", "<b>Trennzeichen</b>", $_POST["seperator"], "");
                        $dsp->AddFormSubmitRow(t('Ändern'));

                        $dsp->AddHRuleRow();
                        $dsp->SetForm("index.php?mod=install&action=import&step=3&filename={$_GET["filename"]}&seperator={$_POST["seperator"]}", "", "", "multipart/form-data");
                        $dsp->AddDoubleRow("<b>Datenbank Feld</b>", "<b>CSV-Datei Eintrag</b>");

                        // Read fields in CSV-file
                        $csv_file = file($_GET["filename"]);
                        $items = explode($_POST["seperator"], $csv_file[0]);

                        // Read fields in user table
                        $tables = array('user', 'party_user');
                        foreach ($tables as $table) {
                            $query = $db->qry("DESCRIBE %prefix%%plain%", $table);
                            while ($row = $db->fetch_array($query)) {
                                reset($items);
                                $fields = array();
                                array_push($fields, "<option value=\"\">-Leer-</option>");
                                $z = 0;
                                foreach ($items as $item) {
                                    if ($item == $row["Field"]) {
                                        $selected = "selected";
                                    } else {
                                        $selected = "";
                                    }
                                    array_push($fields, "<option $selected value=\"$z\">$z - $item</option>");
                                    $z++;
                                }
                                $dsp->AddDropDownFieldRow($table.'--'.$row["Field"], "<b>$table.{$row["Field"]}</b>", $fields, "");
                            }
                            $db->free_result($query);
                        }

                        $dsp->AddFormSubmitRow(t('Weiter'));
                        $dsp->AddBackButton("index.php?mod=install&action=import", "install/import");
                        $dsp->AddContent();
                        break;

                    case 'tgz':
                        $func->information(t('Der Export des Ext-Inc Ordners kann aktuell leider nicht über Lansuite importiert werden. Bitte lade und entpacke den Ordner manuell auf deinem Webspace.'), 'index.php?mod=install&action=import');
            //			  $import->ImportExtInc($_FILES['importdata']['tmp_name']);
            //				$func->confirmation(t('Import erfolgreich.'), "index.php?mod=install&action=import");
                        break;

                    default:
                        $func->information(t('Der von dir angegebene Dateityp wird nicht unterstützt. Bitte wähle eine Datei vom Typ *.xml, oder *.csv aus oder überspringe den Dateiimport.'), "index.php?mod=install&action=import");
                        break;
                }
                break;
        }
        break;

    case 3:
        $db->connect();

        switch ($import->GetUploadFileType($_GET["filename"])) {
            case "csv":
                // Get index assignment
                $indexes = array();
                foreach ($_POST as $var => $val) {
                    if ($var != "imageField_x" and $var != "imageField_y") {
                        $var = explode('--', $var);
                        $table = $var[0];
                        $field = $var[1];
                        if ($val) {
                            $indexes[$table][$field] = $val;
                        }
                    }
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
                        foreach ($table as $field => $itemnr) {
                            $sql .= "$field = '". $func->escape_sql($items[$itemnr]) ."', ";
                        }
                        $sql = substr($sql, 0, strlen($sql) - 2);

                        $db->qry("REPLACE INTO %prefix%user SET %plain%", $sql);
                        $userid = $db->insert_id();

            // Party-user table
                        if ($userid) {
                            $table = $indexes['party_user'];
                            $sql = '';
                            foreach ($table as $field => $itemnr) {
                                $sql .= "$field = '". $func->escape_sql($items[$itemnr]) ."', ";
                            }
                            $sql = substr($sql, 0, strlen($sql) - 2);
  
                            $db->qry("REPLACE INTO %prefix%party_user SET user_id = %int%, party_id = %int%, %plain%", $userid, $party->party_id, $sql);
                        }
                    }
                    $z++;
                }

                $func->confirmation(t("CSV Import erfolgreich"), "index.php?mod=install&action=import");
                break;

            default:
                $func->information(t('Der von dir angegebene Dateityp wird nicht unterstützt. Bitte wähle eine Datei vom Typ *.xml, oder *.csv aus oder überspringe den Dateiimport.'), "index.php?mod=install&action=import");
                break;
        }
        break;
}
