<?php

include("modules/install/class_export.php");
$export = new Export();

switch ($_GET["step"]) {
    default:
        $dsp->NewContent(t('Daten exportieren'), t('Hier kannst du Benutzerdaten exportieren. Diese kannst du später wieder in Lansuite importieren.'));
        $dsp->SetForm("index.php?mod=install&action=export&step=2", "", "", "");

        $type_array = array("xml" => t('XML: Komplette Datenbank Exportieren (Empfohlen)'),
            "xml_modules" => t('XML: Nur ausgewählte Module exportiern'),
            "xml_tables" => t('XML: Nur ausgewählte Tabellen exportieren (für Experten)'),
            "csv_complete" => t('CSV: Userdaten komplett (inkl. Sitzplatz und IP)'),
            "csv_sticker" => t('CSV: Userdaten \'Aufkleber\' (Name, Username, Clan, Sitzplatz und IP)'),
            "csv_card" => t('CSV: Sitzplatzkarten (Name, Username, Clan, Sitzplatz und IP)'),
            "ext_inc_data" => t('DATA: Daten-Ordner herunterladen (Avatare, Bildergallerie, Banner, ...)')
            );
        $t_array = array();
        while (list($key, $val) = each($type_array)) {
            array_push($t_array, "<option $selected value=\"$key\">$val</option>");
        }
        $dsp->AddDropDownFieldRow("type", t('Export Typ'), $t_array, "", 1);

        $dsp->AddFormSubmitRow(t('Weiter'));
        $dsp->AddBackButton("index.php?mod=install", "install/export");
        $dsp->AddContent();
        break;

    case 2:
        $db->connect();
        $dsp->NewContent(t('Daten exportieren'), t('Hier kannst du Benutzerdaten exportieren. Diese kannst du später wieder in Lansuite importieren.'));

        switch ($_POST["type"]) {
            case "xml":
                $dsp->SetForm("index.php?mod=install&action=export&design=base&type={$_POST["type"]}&step=3", "", "", "");

                $dsp->AddCheckBoxRow("e_struct", t('Struktur exportieren'), "", "", 1, 1);
                $dsp->AddCheckBoxRow("e_cont", t('Inhalt exportieren'), "", "", 1, 1);

                $dsp->AddFormSubmitRow(t('Weiter'));
                break;

            case "xml_modules":
                $dsp->SetForm("index.php?mod=install&action=export&design=base&type={$_POST["type"]}&step=3", "", "", "");

                $dsp->AddCheckBoxRow("e_struct", t('Struktur exportieren'), "", "", 1, 1);
                $dsp->AddCheckBoxRow("e_cont", t('Inhalt exportieren'), "", "", 1, 1);
                $dsp->AddCheckBoxRow("e_trans", 'Übersetzungsdaten exportieren', "", "", 1, 1);
                $dsp->AddHRuleRow();

                $res = $db->qry("SELECT * FROM %prefix%modules ORDER BY changeable DESC, caption");
                while ($row = $db->fetch_array($res)) {
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
                                if ($table_name) {
                                    $found = 1;
                                }
                            }
                        }

                        if ($found) {
                            $dsp->AddCheckBoxRow("table[{$row["name"]}]", $row["caption"], "Dieses Modul exportieren", "", 1);
                        }
                    }
                }
                $db->free_result($res);

                $dsp->AddFormSubmitRow(t('Weiter'));
                break;

            case "xml_tables":
                $dsp->SetForm("index.php?mod=install&action=export&design=base&type={$_POST["type"]}&step=3", "", "", "");

                $dsp->AddCheckBoxRow("e_struct", t('Struktur exportieren'), "", "", 1, 1);
                $dsp->AddCheckBoxRow("e_cont", t('Inhalt exportieren'), "", "", 1, 1);
                $dsp->AddCheckBoxRow("e_trans", 'Übersetzungsdaten exportieren', "", "", 1, 1);
                $dsp->AddHRuleRow();

                $res = $db->qry("SELECT * FROM %prefix%modules ORDER BY changeable DESC, caption");
                while ($row = $db->fetch_array($res)) {
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
                                $dsp->AddCheckBoxRow("table[$table_name]", $table_name, t('Diese Tabelle exportieren'), "", 1);
                            }
                        }
                    }
                }
                $db->free_result($res);

                $dsp->AddFormSubmitRow(t('Weiter'));
                break;

            case "csv_complete":
                $dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=export&design=base&type={$_POST["type"]}&step=3\">".t('Lansuite-CSV-Export speichern')."</a>", "", "", "");
                break;

            case "csv_sticker":
                $dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=export&design=base&type={$_POST["type"]}&step=3\">".t('Lansuite-Aufkleber-Export speichern')."</a>", "", "", "");
                break;

            case "csv_card":
                $dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=export&design=base&type={$_POST["type"]}&step=3\">".t('Lansuite-Sitzplatzkarten-Export speichern')."</a>", "", "", "");
                break;

            case 'ext_inc_data':
                $dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=export&design=base&type={$_POST["type"]}&step=3\">".t('Lansuite Daten-Ordner herunterladen')."</a>", "", "", "");
                break;

            default:
                $func->information(t('Der von dir angegebene Dateityp wird nicht unterstützt. Bitte wähle eine Datei vom Typ *.xml, oder *.csv aus oder überspringe den Dateiimport.'), "index.php?mod=install&action=import");
                break;
        }

        $dsp->AddBackButton("index.php?mod=install&action=export", "install/export");
        $dsp->AddContent();
        break;

    case 3:
        $db->connect();

        switch ($_GET["type"]) {
            case "xml":
                $export->ExportAllTables($_POST["e_struct"], $_POST["e_cont"]);
                break;

            case "xml_modules":
                $export->LSTableHead();
                foreach ($_POST["table"] as $key => $value) {
                    if ($key) {
                        $export->ExportMod($key, $_POST["e_struct"], $_POST["e_cont"], $_POST["e_trans"]);
                    }
                }
                $export->LSTableFoot();
                break;

            case "xml_tables":
                $export->LSTableHead();
                foreach ($_POST["table"] as $key => $value) {
                    if ($key) {
                        $export->ExportTable($key, $_POST["e_struct"], $_POST["e_cont"]);
                    }
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
                $func->information(t('Der von dir angegebene Dateityp wird nicht unterstützt. Bitte wählen dir eine Datei vom Typ *.xml, oder *.csv aus oder überspringe den Dateiimport.'), "index.php?mod=install&action=import");
                break;
        }
        break;
}
