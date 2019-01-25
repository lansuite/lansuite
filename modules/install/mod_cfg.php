<?php

$importXml = new \LanSuite\XML();
$installImport = new \LanSuite\Module\Install\Import($importXml);
$install = new \LanSuite\Module\Install\Install($installImport);

// XML is a global requirement during installation
$xml = new \LanSuite\XML();

$CurrentMod = $db->qry_first('SELECT caption FROM %prefix%modules WHERE name=%string%', $_GET['module']);

$dsp->NewContent(t('Modul-Konfiguration') .' - '. $CurrentMod['caption'], t('Hier kannst du dieses Modul deinen Bedürfnissen anpassen'));

$menunames = array();
$res = $db->qry('SELECT name, caption FROM %prefix%modules WHERE active = 1 ORDER BY caption');
while ($row = $db->fetch_array($res)) {
    $menunames[$row['name']] = $row['caption'];
}
$db->free_result($res);

switch ($_GET['step']) {
    case 31:
        $db->qry("INSERT INTO %prefix%menu SET caption = 'Neuer Eintrag', requirement = '0', hint = '', link = 'index.php?mod=', needed_config = '', module=%string%, level = 1", $_GET["module"]);
        $_GET['step'] = 30;
        break;
}

if (!is_dir('modules/'. $_GET['module'] .'/mod_settings')) {
    $func->error(t('Modul "%1" wurde nicht gefunden', $_GET['module']));
} else {
    switch ($_GET['step']) {
        default:
            $dsp->StartTabs();

            $dsp->StartTab(t('Konfiguration'), 'config');
            $resGroup = $db->qry('SELECT cfg_group FROM %prefix%config WHERE cfg_module = %string% GROUP BY cfg_group ORDER BY cfg_group', $_GET['module']);
            if ($db->num_rows($resGroup) == 0) {
                $func->information(t('Zu diesem Modul sind keine Einstellungen vorhanden'), NO_LINK);
            } else {
                if ($_GET['step'] == 11) {
                    foreach ($_POST as $key => $val) {
                        // Date + Time Values
                        if (strpos($key, '_value_') > 0) {
                            if (strpos($key, '_value_minutes') > 0) {
                                $key = substr($key, 0, strpos($key, '_value_minutes'));
                                $cfg_value = mktime($_POST[$key.'_value_hours'], $_POST[$key.'_value_minutes'], $_POST[$key.'_value_seconds'], $_POST[$key.'_value_month'], $_POST[$key.'_value_day'], $_POST[$key.'_value_year']);
                                $db->qry('UPDATE %prefix%config SET cfg_value = %string% WHERE cfg_key = %string%', $cfg_value, $key);
                            }
                            // Other Values
                        } else {
                            $db->qry('UPDATE %prefix%config SET cfg_value = %string% WHERE cfg_key = %string%', $val, $key);
                        }
                    }
                    $func->confirmation(t('Erfolgreich geändert'), 'index.php?mod=install&action=mod_cfg&module='. $_GET["module"]. '&tab=0');
                } else {
                    $dsp->SetForm('index.php?mod=install&action=mod_cfg&step=11&module='. $_GET['module']. '&tab=0');
                    while ($rowGroup = $db->fetch_array($resGroup)) {
                        $dsp->AddFieldsetStart($rowGroup['cfg_group']);

                        // Get items in group
                        $res = $db->qry(
                            '
                          SELECT
                            cfg_key,
                            cfg_value,
                            cfg_desc,
                            cfg_type,
                            cfg_group
                          FROM %prefix%config
                          WHERE
                            cfg_module = %string%
                            AND cfg_group = %string%
                          ORDER BY cfg_pos, cfg_desc',
                            $_GET["module"],
                            $rowGroup['cfg_group']
                        );
                        while ($row = $db->fetch_array($res)) {
                            $row['cfg_desc'] = t($row['cfg_desc']);
                            if ($row['cfg_type'] == 'string') {
                                $row['cfg_value'] = t($row['cfg_value']);
                            }

                            // Get Selections
                            $get_cfg_selection = $db->qry('SELECT cfg_display, cfg_value FROM %prefix%config_selections WHERE cfg_key = %string% ORDER BY cfg_value', $row['cfg_type']);
                            if ($db->num_rows($get_cfg_selection) > 0) {
                                $t_array = array();
                                while ($selection = $db->fetch_array($get_cfg_selection)) {
                                    ($row['cfg_value'] == $selection['cfg_value']) ? $selected = 'selected' : $selected = '';
                                    array_push($t_array, "<option $selected value=\"{$selection["cfg_value"]}\">". t($selection['cfg_display']) .'</option>');
                                }
                                if ($selections) {
                                    asort($selections);
                                }
                                $dsp->AddDropDownFieldRow($row['cfg_key'], $row['cfg_desc'], $t_array, '', 1);

                            // Show Edit-Fields for Settings
                            } else {
                                switch ($row['cfg_type']) {
                                    case 'password':
                                          $dsp->AddPasswordRow($row['cfg_key'], $row['cfg_desc'], $row['cfg_value'], '', '', 1);
                                        break;

                                    case 'datetime':
                                        $dsp->AddDateTimeRow($row['cfg_key'], $row['cfg_desc'], $row['cfg_value'], '', '');
                                        break;

                                    case 'date':
                                        $dsp->AddDateTimeRow($row['cfg_key'], $row['cfg_desc'], $row['cfg_value'], '', '', '', '', '', 1);
                                        break;

                                    case 'time':
                                        $dsp->AddDateTimeRow($row['cfg_key'], $row['cfg_desc'], $row['cfg_value'], '', '', '', '', '', 2);
                                        break;

                                    case 'text':
                                        $dsp->AddTextAreaRow($row['cfg_key'], '<div style=white-space:normal;>'. $row['cfg_desc'] .'</div>', $row['cfg_value'], '');
                                        break;

                                    default:
                                        $row['cfg_value'] = str_replace('<', '&lt;', $row['cfg_value']);
                                        $row['cfg_value'] = str_replace('>', '&gt;', $row['cfg_value']);
                                        $row['cfg_value'] = str_replace('"', "'", $row['cfg_value']);
                                        $dsp->AddTextFieldRow($row['cfg_key'], $row['cfg_desc'], $row['cfg_value'], '');
                                        break;
                                }
                            }
                        }
                        $db->free_result($res);
                        $dsp->AddFieldsetEnd();
                    }
                    $db->free_result($resGroup);
                    $dsp->AddFormSubmitRow(t('Weiter'));
                }
            }
            $dsp->EndTab();

            $dsp->StartTab(t('Zugriff'), 'delete_group');
            $mf = new \LanSuite\MasterForm();

            $res = $db->qry('SELECT * FROM %prefix%menu WHERE module = %string% AND caption != \'\' ORDER BY level, requirement, pos', $_GET['module']);
            while ($row = $db->fetch_array($res)) {
                $mf->AddDBLineID($row['id']);

                $selections = array();
                $selections['0'] = t('Jeder');
                $selections['1'] = t('Nur Eingeloggte');
                $selections['2'] = t('Nur Admins');
                $selections['3'] = t('Nur Superadminen');
                $selections['4'] = t('Keine Admins');
                $selections['5'] = t('Nur Ausgeloggte');
                $mf->AddField(t('Zugriff'), 'requirement', \LanSuite\MasterForm::IS_SELECTION, $selections, \LanSuite\MasterForm::FIELD_OPTIONAL);

                $selections = array();
                if ($MenuCallbacks) {
                    foreach ($MenuCallbacks as $MenuCallback) {
                        $selections[$MenuCallback] = $MenuCallback;
                    }
                }
                asort($selections);
                $selections = array('' => t('Keine')) + $selections;
                $mf->AddField(t('Vorraussetzung'), 'needed_config', \LanSuite\MasterForm::IS_SELECTION, $selections, \LanSuite\MasterForm::FIELD_OPTIONAL);

                $mf->AddGroup($row['caption'] .' ('. $row['link'] .')');
            }
            $db->free_result($res);

            $mf->SendForm(
                'index.php?mod=install&action=mod_cfg&module='. $_GET['module'] .'&id='. $_GET['id'] .'&tab=1',
                'menu',
                'id',
                "module = '". $_GET['module'] ."' AND caption != ''"
            );
            $dsp->EndTab();

            $dsp->StartTab(t('Datenbank'), 'database');
            if (!file_exists('modules/'. $_GET['module'] .'/mod_settings/db.xml')) {
                $func->information(t('Dieses Modul benötigt keine Datenbank Tabellen'), NO_LINK);
            } elseif (!$func->isModActive($_GET['module'])) {
                $func->information(t('Dieses Modul ist nicht aktiv.'));
            } else {
                switch ($_GET['step']) {
                    // Rewrite specific Module-DB - Question
                    case 41:
                        $func->question(
                            t('Bist du sicher, dass du die Datenbank des Moduls "%1" zurücksetzen möchtest? Dies löscht unwiderruflich alle Daten, die in diesem Modul bereits geschrieben wurden!', array($_GET['module'])),
                            'index.php?mod=install&action=mod_cfg&step=42&module='. $_GET['module'] .'&tab=2',
                            'index.php?mod=install&action=mod_cfg&module='. $_GET['module'] .'&tab=2'
                        );
                        break;

                    // Rewrite specific Module-DB
                    case 42:
                        $install->WriteTableFromXMLFile($_GET['module'], 1);
                        $func->confirmation(t('Tabelle wurde erfolgreich neu geschrieben'), 'index.php?mod=install&action=mod_cfg&module='. $_GET['module'] .'&tab=2');
                        break;

                    // Export Module-DB
                    case 43:
                        $xmlExport = new \LanSuite\XML();
                        $export = new \LanSuite\Module\Install\Export($xmlExport);

                        if ($_GET['module']) {
                            $export->LSTableHead('lansuite_'. $_GET['module'] .'_'. date('ymd') .'.xml');
                            $export->ExportMod($_GET['module'], $_POST['e_struct'], $_POST['e_cont']);
                            $export->LSTableFoot();
                        }
                        break;

                    default:
                        $mod_tables = '';
                        $mod_tables_arr = array();
                        if (is_dir('modules/'. $_GET['module'] .'/mod_settings')) {
                            $file = 'modules/'. $_GET['module'] .'/mod_settings/db.xml';
                            if (file_exists($file)) {
                                $xml_file = fopen($file, 'r');
                                $xml_content = fread($xml_file, filesize($file));
                                fclose($xml_file);

                                $lansuite = $xml->get_tag_content('lansuite', $xml_content);
                                $tables = $xml->get_tag_content_array('table', $lansuite);
                                foreach ($tables as $table) {
                                    $table_head = $xml->get_tag_content('table_head', $table);
                                    $table_name = $xml->get_tag_content('name', $table_head);

                                    if ($table_name != 'translation') {
                                        $row = $db->qry_first("SHOW TABLE STATUS FROM %plain% LIKE '%prefix%%plain%'", $config['database']['database'], $table_name);
                                        $TableInfo = ' ['. $row['Rows'] .' Zeilen, '. $func->FormatFileSize($row['Data_length']) .' Daten, '. $func->FormatFileSize($row['Index_length']) .' Indizes]'; #Name, Engine, Version, Row_format, Rows, Avg_row_length, Data_length, Max_data_length, Index_length, Data_free, Auto_increment, Create_time, Update_time, Check_time, Collation, Checksum, Create_options, Comment
                                        $mod_tables .= '<b>'. $config['database']['prefix'] . $table_name .'</b>'. $TableInfo . HTML_NEWLINE;
                                        $mod_tables_arr[] = $table_name;

                                        $res = $db->qry('DESCRIBE %prefix%'. $table_name);
                                        while ($row = $db->fetch_array($res)) {
                                            $mod_tables .= '&nbsp;&nbsp;&nbsp;&nbsp;'. $row['Field'] .' ['. $row['Type'] .']'. HTML_NEWLINE; # Null, Key, Default, Extra
                                        }
                                        $db->free_result($res);

                                        $mod_tables .= HTML_NEWLINE;
                                    }
                                }
                            }
                        }
                        $mod_tables = substr($mod_tables, 0, strlen($mod_tables) - 5);
                        $dsp->AddDoubleRow(t('DB-Tabellen dieses Moduls'), $mod_tables);

                        $dsp->AddFieldsetStart(t('Abhängigkeiten'));
                        $where = '';
                        foreach ($mod_tables_arr as $table) {
                            $where .= ' OR pri_table = \''. $table .'\'';
                        }
                            $res = $db->qry('SELECT pri_table, pri_key, foreign_table, foreign_key, on_delete FROM %prefix%ref WHERE (0 = 1) %plain%', $where);
                        while ($row = $db->fetch_array($res)) {
                            switch ($row['on_delete']) {
                                case 'DELETE':
                                    $color = '#ff0000';
                                    break;
                                case 'ASK_DELETE':
                                    $color = '#ff0000';
                                    break;
                                case 'SET0':
                                    $color = '#ff0000';
                                    break;
                                case 'ASK_SET0':
                                    $color = '#ff0000';
                                    break;
                                case 'DENY':
                                    $color = '#008800';
                                    break;
                                default:
                                    $color = '#000000';
                                    break;
                            }
                            $dsp->AddDoubleRow('<font color="'. $color .'">'. $row['pri_table'] .'.'. $row['pri_key'] .'</font>', $row['foreign_table'] .'.'. $row['foreign_key']);
                        }
                        $dsp->AddSingleRow('<font color="#ff0000">'. t('Rot: Wenn rechts ein Eintrag gelöscht wird, wenden links die passenden mit gelöscht') .'</font>');
                        $dsp->AddSingleRow('<font color="#008800">'. t('Grün: Rechts kann kein Eintrag gelöscht werden, solange links nocht mindestens ein Wert auf diesen referenziert') .'</font>');
                        $dsp->AddFieldsetEnd();

                        $dsp->AddFieldsetStart(t('Tabellen, die Tabellen dieses Moduls vorraussetzen'));
                        $where = '';
                        foreach ($mod_tables_arr as $table) {
                            $where .= ' OR foreign_table = \''. $table .'\'';
                        }
                        $res = $db->qry('SELECT pri_table, pri_key, foreign_table, foreign_key, on_delete FROM %prefix%ref WHERE (0 = 1) %plain%', $where);
                        while ($row = $db->fetch_array($res)) {
                            switch ($row['on_delete']) {
                                case 'DELETE':
                                    $color = '#ff0000';
                                    break;
                                case 'ASK_DELETE':
                                    $color = '#ff0000';
                                    break;
                                case 'SET0':
                                    $color = '#ff0000';
                                    break;
                                case 'ASK_SET0':
                                    $color = '#ff0000';
                                    break;
                                case 'DENY':
                                    $color = '#008800';
                                    break;
                                default:
                                    $color = '#000000';
                                    break;
                            }
                            $dsp->AddDoubleRow('<font color="'. $color .'">'. $row['pri_table'] .'.'. $row['pri_key'] .'</font>', $row['foreign_table'] .'.'. $row['foreign_key']);
                        }
                        $dsp->AddSingleRow('<font color="#ff0000">'. t('Rot: Wenn rechts ein Eintrag gelöscht wird, wenden links die passenden mit gelöscht') .'</font>');
                        $dsp->AddSingleRow('<font color="#008800">'. t('Grün: Rechts kann kein Eintrag gelöscht werden, solange links nocht mindestens ein Wert auf diesen referenziert') .'</font>');
                        $dsp->AddFieldsetEnd();

                        $dsp->AddFieldsetStart(t('Modul-Datenbank exportieren'));
                        $dsp->SetForm('index.php?mod=install&action=mod_cfg&design=base&step=43&module='. $_GET['module'] .'&tab=2', '', '', '');
                        $dsp->AddCheckBoxRow('e_struct', t('Struktur exportieren'), '', '', 1, 1);
                        $dsp->AddCheckBoxRow('e_cont', t('Inhalt exportieren'), '', '', 1, 1);
                        $dsp->AddFormSubmitRow(t('DB exportieren'));
                        $dsp->AddFieldsetEnd();

                        $dsp->AddFieldsetStart(t('Weitere Aktionen'));
                        $dsp->AddDoubleRow('', $dsp->FetchSpanButton('Modul-Datenbank zurücksetzen', 'index.php?mod=install&action=mod_cfg&step=41&module='. $_GET['module'] .'&tab=2'));
                        $dsp->AddFieldsetEnd();
                        break;
                }
            }
            $dsp->EndTab();

            $dsp->StartTab(t('Menü'), 'tree');
            if ($_GET['step'] == 31) {
                $db->qry("INSERT INTO %prefix%menu SET caption = 'Neuer Eintrag', requirement = '0', hint = '', link = 'index.php?mod=', needed_config = '', module=%string%, level = 1", $_GET["module"]);
            }

            $mf = new \LanSuite\MasterForm();
            $mf->IncrementNumber();

            $res = $db->qry('SELECT * FROM %prefix%menu WHERE module = %string% AND caption != \'\' ORDER BY level, requirement, pos', $_GET['module']);
            while ($row = $db->fetch_array($res)) {
                $mf->AddDBLineID($row['id']);
                $mf->AddField(t('Titel'), 'caption');
                $mf->AddField(t('Link'), 'link');
                $mf->AddField(t('Popup-Hinweis'), 'hint', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
                $mf->AddGroup($row['caption']);
            }
            $db->free_result($res);

            $mf->SendForm('', 'menu', 'id', "module = '". $_GET['module'] ."' AND caption != ''");
            $dsp->AddDoubleRow('', $dsp->FetchSpanButton(t('Link hinzufügen'), 'index.php?mod=install&action=mod_cfg&step=31&module='. $_GET['module'] .'&tab=3'));
            $dsp->EndTab();

            $dsp->StartTab(t('Übersetzung'), 'translate');
            $dsp->AddFieldSetStart(t('Sprache wechseln. Achtung, nicht gesicherte &Auml;nderungen gehen verloren.'));
            if ($_POST['target_language']) {
                $_SESSION['target_language'] = $_POST['target_language'];
            }
            if ($_SESSION['target_language'] == '') {
                $_SESSION['target_language'] = 'en';
            }
            $dsp->SetForm('index.php?mod=install&action=mod_cfg&module='. $_GET['module'] .'&tab=4');
            $list = array();
            $res = $db->qry("SELECT cfg_value, cfg_display FROM %prefix%config_selections WHERE cfg_key = 'language'");
            while ($row = $db->fetch_array($res)) {
                ($_SESSION['target_language'] == $row['cfg_value'])? $selected = 'selected' : $selected = '';
                $list[] = "<option $selected value='{$row['cfg_value']}'>{$row['cfg_display']}</option>";
            }
            $dsp->AddDropDownFieldRow('target_language', t('Ziel Sprache'), $list, '');
            $db->free_result($res);
            $dsp->AddFormSubmitRow(t('Ändern'));
            $dsp->AddFieldSetEnd();

            // Start Tanslation
            $dsp->AddFieldSetStart(t('Texte editieren.'));
            if ($_POST['id']) {
                foreach ($_POST['id'] as $key => $value) {
                    $db->qry("UPDATE %prefix%translation SET %plain% = %string% WHERE file = %string% AND id = %string%", $_SESSION['target_language'], $value, $_GET['module'], $key);
                }

                $func->confirmation('Module-Übersetzung wurde erfolgreich upgedatet');
            } else {
                $dsp->SetForm('index.php?mod=install&action=mod_cfg&module='. $_GET['module'] .'&tab=4');
                $res = $db->qry("SELECT DISTINCT id, org, file, %plain% FROM %prefix%translation WHERE file = %string%", $_SESSION['target_language'], $_GET['module']);
                if ($db->num_rows($res) <= 0) {
                    $func->information('Zu diesem Modul existieren keine übersetzbaren Texte', NO_LINK);
                } else {
                    while ($row = $db->fetch_array($res)) {
                        $trans_link_google ="http://translate.google.com/translate_t?langpair=de|".$_SESSION['target_language']."&hl=de&ie=UTF8&text=".$row['org'];
                        $trans_link_google =" <a href=\"".$trans_link_google."\" target=\"_blank\"><img src=\"design/".$auth['design']."/images/arrows_transl.gif\" width=\"12\" height=\"13\" border=\"0\" /></a>";

                        if (strlen($row['org'])<60) {
                            $dsp->AddTextFieldRow("id[{$row['id']}]", $row['org'].$trans_link_google, $row[$_SESSION['target_language']], '', 65);
                        } else {
                            $dsp->AddTextAreaRow("id[{$row['id']}]", $row['org'].$trans_link_google, $row[$_SESSION['target_language']], '', 50, 5);
                        }
                    }
                    $db->free_result($res);
                    $dsp->AddFormSubmitRow(t('Editieren'));
                }
            }
            $dsp->AddFieldSetEnd();
            $dsp->EndTab();

            $dsp->StartTab('Modul-Info', 'help');
            $_GET['helpletid'] = 'help';
            include('modules/helplet/helplet.php');
            $dsp->EndTab();

            $dsp->EndTabs();
            break;
    }
}

$dsp->AddHeaderMenu2($menunames, 'index.php?mod=install&action=mod_cfg&module=', $_GET['headermenuitem']);
