<?php
function YesNo($TargetLang)
{
    global $dsp;

    if ($TargetLang) {
        return $dsp->FetchIcon('', 'yes');
    } else {
        return $dsp->FetchIcon('', 'no');
    }
}

switch ($_GET['step']) {
    default:
        $dsp->NewContent(t('Übersetzen'), t('Es müssen nur Einträge eingetragen werden, die sich in der Zielsprache vom Orginal unterscheiden'));
    
        $dsp->AddFieldSetStart(t('Allgemeine Wartungsfunktionen'));
        $dsp->AddDoubleRow('', '<img src="design/images/icon_search.png" border="0" /> <a href="index.php?mod=install&action=translation&step=2">'. t('Übersetzungs-Eintrag suchen') .'</a>');
        $dsp->AddDoubleRow('', '<img src="design/images/icon_generate.png" border="0" /> <a href="index.php?mod=install&action=translation&step=10">'. t('Quellcode nach t() durchsuchen. Neu gefundene Texte in DB übernehmen') .'</a>');
        $dsp->AddDoubleRow('', '<img src="design/images/icon_in.png" border="0" /> <a href="index.php?mod=install&action=translation&step=60">'. t('mod_translation.xml auslesen und in DB schreiben (alle Module)') .'</a>');
        $dsp->AddDoubleRow('', '<img src="design/images/icon_forward.png" border="0" /> <a href="index.php?mod=install&action=translation&step=50">'. t('Einträge aus DB in mod_translation.xml schreiben (alle Module)') .'</a>');
        $dsp->AddDoubleRow('', '<img src="design/images/icon_change.png" border="0" /> <a href="index.php?mod=install&action=translation&step=70">'. t('Suche nach übereinstimmungen Aktuelle / Veraltete Texte (Mergen)') .'</a>');
        $dsp->AddFieldSetEnd();

        if ($_GET['target_language']) {
            $_SESSION['target_language'] = $_GET['target_language'];
        }

        $dsp->AddFieldSetStart(t('Module übersetzen'));
        include_once('modules/mastersearch2/class_mastersearch2.php');
        $ms2 = new mastersearch2('install');
        $ms2->query['from'] = "%prefix%translation";
        $ms2->config['EntriesPerPage'] = 100;
        $ms2->AddResultField(t('Modul'), 'file');
        $ms2->AddIconField('edit', 'index.php?mod=install&action=translation&step=20&file=', t('Edit'));
        $ms2->AddIconField('download', 'index.php?mod=install&action=translation&step=30&design=base&file=', t('Download'));
        $ms2->PrintSearch('index.php?mod=install&action=translation', 'file');
        $dsp->AddFieldSetEnd();
        break;

    case 2:
        $dsp->NewContent(t('Übersetzen'), t('Es müssen nur Einträge eingetragen werden, die sich in der Zielsprache vom Orginal unterscheiden'));

        include_once('modules/mastersearch2/class_mastersearch2.php');
        $ms2 = new mastersearch2('install');

        $ms2->query['from'] = "%prefix%translation";
        $ms2->config['EntriesPerPage'] = 50;

        $selections = array('' => t('Alle'));
        $res = $db->qry("SELECT file FROM %prefix%translation GROUP BY file");
        while ($row = $db->fetch_array($res)) {
            $selections[$row['file']] = $row['file'];
        }
        $db->free_result($res);
        $ms2->AddTextSearchDropDown(t('Fundstelle'), 'file', $selections);
        $ms2->AddTextSearchDropDown(t('Englisch'), 'en', array('' => t('Egal'), '>0' => t('Vorhanden')));
        $ms2->AddTextSearchDropDown(t('Veraltet'), 'obsolete', array('' => t('Alle'), '0' => t('Nur neue'), '1' => t('Nur veraltete')));

        $ms2->AddTextSearchField(t('Text'), array('org' => 'like'));

        $ms2->AddResultField(t('Text'), 'org');
        $ms2->AddResultField(t('Fundstelle'), 'file');
        $ms2->AddResultField(t('De'), 'de', 'YesNo');
        $ms2->AddResultField(t('En'), 'en', 'YesNo');
        $ms2->AddResultField(t('Es'), 'es', 'YesNo');
        $ms2->AddResultField(t('Nl'), 'nl', 'YesNo');
        $ms2->AddResultField(t('Fr'), 'fr', 'YesNo');
        $ms2->AddResultField(t('It'), 'it', 'YesNo');

        $ms2->AddIconField('edit', 'index.php?mod=install&action=translation&step=3&tid=', t('Edit'));

        $ms2->PrintSearch('index.php?mod=install&action=translation&step=2', 'tid');
        break;

    case 3:
        $dsp->NewContent(t('Übersetzen'), t('Es müssen nur Einträge eingetragen werden, die sich in der Zielsprache vom Orginal unterscheiden'));

        $mf = new masterform();
    
        // Name
        $mf->AddField(t('Orginal-Text'), 'org');
        $mf->AddField($dsp->FetchIcon('', 'de'), 'de', '', '', FIELD_OPTIONAL);
        $mf->AddField($dsp->FetchIcon('', 'en'), 'en', '', '', FIELD_OPTIONAL);
        $mf->AddField($dsp->FetchIcon('', 'es'), 'es', '', '', FIELD_OPTIONAL);
        $mf->AddField($dsp->FetchIcon('', 'nl'), 'nl', '', '', FIELD_OPTIONAL);
        $mf->AddField($dsp->FetchIcon('', 'fr'), 'fr', '', '', FIELD_OPTIONAL);
        $mf->AddField($dsp->FetchIcon('', 'it'), 'it', '', '', FIELD_OPTIONAL);
    
        $mf->SendForm('index.php?mod=install&action=translation&step=3', 'translation', 'tid', $_GET['tid']);
        $dsp->AddBackButton('index.php?mod=install&action=translation');
        break;


  // Search all files for strings in t()-functions and synchronize to DB
    case 10:
        $dsp->NewContent(t('Übersetzen'), t('Es müssen nur Einträge eingetragen werden, die sich in der Zielsprache vom Orginal unterscheiden'));

        if ($auth['type'] >= 3) {
            $dsp->AddFieldSetStart(t('FrameWork'));
            $dsp->AddSingleRow($translation->TUpdateFromFiles('inc/classes'));
            $dsp->AddFieldSetEnd();
            $dsp->AddFieldSetStart(t('Module'));
            $dsp->AddSingleRow($translation->TUpdateFromFiles('modules'));
            $dsp->AddFieldSetEnd();

            // Scan DB
            $FoundTransEntries = array();
            $translation->TUpdateFromDB('menu', 'caption');
            $translation->TUpdateFromDB('menu', 'hint');
            $translation->TUpdateFromDB('modules', 'description');
            $translation->TUpdateFromDB('config', 'cfg_desc');
            $translation->TUpdateFromDB('config_selections', 'cfg_display');
            $translation->TUpdateFromDB('plugin', 'caption');
            $translation->TUpdateFromDB('boxes', 'name');

            // DELETE empty rows
            $res = $db->qry("DELETE FROM %prefix%translation WHERE org = ''");

            // Mark entries as obsolete, which no do no longer exist
            $res = $db->qry("SELECT tid, file, org FROM %prefix%translation WHERE file = 'DB' AND obsolete = 0", $CurrentFile);
            while ($row = $db->fetch_array($res)) {
                if (!in_array($row['tid'], $FoundTransEntries)) {
                    $db->qry("UPDATE %prefix%translation SET obsolete = 1 WHERE tid = %int%", $row['tid']);
                }
            }
            $db->free_result($res);

            // For info output DB internal
            $output = '';
            $res = $db->qry("SELECT id, org, file FROM %prefix%translation WHERE file = 'DB'");
            while ($row = $db->fetch_array($res)) {
                $output .= $row['file'] .': '. $row['org'] .'<br />';
            }
            $db->free_result($res);
            $dsp->AddFieldSetStart(t('DB-Internal'));
            $dsp->AddSingleRow($output);
            $dsp->AddFieldSetEnd();

            $dsp->AddBackButton('index.php?mod=install&action=translation');
        }
        break;
  
  // Translate Module
    case 20:
        // If Write2File
        if ($_GET['subact'] == 'writetofile') {
            $translation->xml_write_db_to_file($_GET['file']);
        }

        $dsp->NewContent(t('Modul Übersetzen : ').$_GET['file'], '');
        $framework->add_js_path('http://www.google.com/jsapi');
        $framework->add_js_code('google.load("language", "1");
function translate(textid, from, to) {
  google.language.translate($("label[for="+ textid +"]").text(), from, to, function(result) {
    if (!result.error) {
      result.translation = result.translation.replace(/% /g, "%");
      $("textarea[name="+ textid +"]").text(result.translation);
      $("input[name="+ textid +"]").val(result.translation);
    }
  });
}

function translate_all_empty(from, to) {
  $("input").each(function (i) {
    if (this.value == "") translate(this.name, from, to);
  });
  $("textarea").each(function (i) {
    if (this.value == "") translate(this.name, from, to);
  });
}
');
    
        // Show switch between Lanuages
        $dsp->AddFieldSetStart(t('Sprache wechseln. Achtung, nicht gesicherte &Auml;nderungen gehen verloren.'));
        if ($_GET['target_language']) {
            $_SESSION['target_language'] = $_GET['target_language'];
        }
        if ($_SESSION['target_language'] == '') {
            $_SESSION['target_language'] = 'en';
        }

            $dsp->SetForm('index.php', '', 'GET');
            $dsp->AddSingleRow('<input type="hidden" name="mod" value="install" />
          <input type="hidden" name="action" value="translation" />
          <input type="hidden" name="step" value="20" />');

          $list = array();
          $res = $db->qry("SELECT cfg_value, cfg_display FROM %prefix%config_selections WHERE cfg_key = 'language'");
        while ($row = $db->fetch_array($res)) {
            ($_SESSION['target_language'] == $row['cfg_value'])? $selected = 'selected' : $selected = '';
            $list[] = "<option $selected value='{$row['cfg_value']}'>{$row['cfg_display']}</option>";
        }
            $db->free_result($res);
            $dsp->AddDropDownFieldRow('target_language', t('Ziel Sprache'), $list, '');

            $list = array('' => "<option value=''>Alle zeigen</option>");
            $res = $db->qry("SELECT file FROM %prefix%translation GROUP BY file ORDER BY file");
        while ($row = $db->fetch_array($res)) {
            ($_GET['file'] == $row['file'])? $selected = 'selected' : $selected = '';
            $list[] = "<option $selected value='{$row['file']}'>{$row['file']}</option>";
        }
            $db->free_result($res);
            $dsp->AddDropDownFieldRow('file', t('Modul'), $list, '');

            $dsp->AddFormSubmitRow(t('Ändern'));

            $tmp_link_write = "index.php?mod=install&action=translation&step=20&file=".$_GET['file']."&subact=writetofile";
            $dsp->AddDoubleRow(t('Schreibe Modulübersetzung in translation.xml'), $dsp->FetchSpanButton(t('Schreibe'), $tmp_link_write));
        $dsp->AddFieldSetEnd();

        // Start Tanslation
        $dsp->AddFieldSetStart(t('Texte editieren.'));
            $dsp->SetForm('index.php?mod=install&action=translation&step=21&file='. $_GET['file']);
            $dsp->AddDoubleRow('', '<a href="javascript:translate_all_empty(\'de\', \''. $_SESSION['target_language'] .'\')">'. t('Alle leeren Felder mit Google-Translate-Übersetzungen füllen') .'</a>');

        if ($_GET['file']) {
            $res = $db->qry("SELECT DISTINCT id, org, file, %plain% FROM %prefix%translation WHERE file = %string% AND obsolete = 0", $_SESSION['target_language'], $_GET['file']);
        } else {
            $res = $db->qry("SELECT DISTINCT id, org, file, %plain% FROM %prefix%translation WHERE obsolete = 0 GROUP BY id", $_SESSION['target_language']);
        }
        while ($row = $db->fetch_array($res)) {
            #$trans_link_google ="http://translate.google.com/translate_t?langpair=de|".$_SESSION['target_language']."&hl=de&ie=UTF8&text=".$row['org'];
            $trans_link_google = 'javascript:translate(\'id['. $row['id'] .']\', \'de\', \''. $_SESSION['target_language'] .'\');';
            $trans_link_google =" <a href=\"".$trans_link_google."\" target=\"_blank\"><img src=\"design/".$auth['design']."/images/arrows_transl.gif\" width=\"12\" height=\"13\" border=\"0\" /></a>";
            
            if (strlen($row['org'])<60) {
                $dsp->AddTextFieldRow("id[{$row['id']}]", $row['org'].$trans_link_google, $row[$_SESSION['target_language']], '', 65);
            } else {
                $dsp->AddTextAreaRow("id[{$row['id']}]", $row['org'].$trans_link_google, $row[$_SESSION['target_language']], '', 50, 5);
            }
        }
            $db->free_result($res);
            $dsp->AddFormSubmitRow(t('Editieren'));
        $dsp->AddFieldSetEnd();
        $dsp->AddBackButton('index.php?mod=install&action=translation');

        break;

  // Translate Module - DB Insert
    case 21:
        foreach ($_POST['id'] as $key => $value) {
            if ($_GET['file']) {
                $db->qry("UPDATE %prefix%translation SET %plain% = %string% WHERE file = %string% AND id = %string%", $_SESSION['target_language'], $value, $_GET['file'], $key);
            } else {
                $db->qry("UPDATE %prefix%translation SET %plain% = %string% WHERE id = %string%", $_SESSION['target_language'], $value, $key);
            }
        }

        $func->confirmation('Module-Übersetzung wurde erfolgreich upgedatet');
        break;

  // Export Module Translations
    case 30:
        include("modules/install/class_export.php");
        $export = new Export();

        $export->LSTableHead();
        $export->ExportMod($_GET['file'], 0, 0, 1);
        $export->LSTableFoot();
        break;


  // Translate Item
    case 40:
        $dsp->NewContent(t('Modul Übersetzen'), '');

        $mf = new masterform();
        $mf->AddField(t('Orginal-Text'), 'org', IS_NOT_CHANGEABLE);
        $mf->AddField(t('Deutsch'), 'de', '', '', FIELD_OPTIONAL);
        $mf->AddField(t('Englisch'), 'en', '', '', FIELD_OPTIONAL);
        $mf->AddField(t('Spanisch'), 'es', '', '', FIELD_OPTIONAL);
        $mf->AddField(t('Französisch'), 'fr', '', '', FIELD_OPTIONAL);
        $mf->AddField(t('Holländisch'), 'nl', '', '', FIELD_OPTIONAL);
        $mf->AddField(t('Italienisch'), 'it', '', '', FIELD_OPTIONAL);
        $mf->SendForm('index.php?mod=install&action=translation&step=40', 'translation', 'id', $_GET['id']);

        $dsp->AddBackButton('index.php?mod=install&action=translation');
        break;
      
  // Export Translation to Files
    case 50:
        if (!$_GET['confirm']=="yes") {
            $func->question(
                t('Achtung!!! Alle vorhandenen Übersetzungen in den XML-Dateien werden überschrieben'),
                'index.php?mod=install&action=translation&step=50&confirm=yes',
                'index.php?mod=install&action=translation'
            );
        } else {
            $modules = array();
            $res = $db->qry("SELECT name FROM %prefix%modules");
            while ($row = $db->fetch_array($res)) {
                $modules[] = $row['name'];
            }
            $db->free_result($res);
            // Add Systemtranslations
            $modules[] = "DB";
            $modules[] = "System";
            foreach ($modules as $modul) {
                $translation->xml_write_db_to_file($modul);
                $info .= t("Modulübersetzung wurde in <b>translation.xml (%1)</b> geschrieben<br \>", $modul);
            }
            $func->information($info, 'index.php?mod=install&action=translation');
        }
        break;

  // Import Translation to DB from mod_translation.xml
    case 60:
        if (!$_GET['confirm']=="yes") {
            $func->question(
                t('Achtung!!! Alle vorhandenen Übersetzungen werden von den XML-Dateien in die Datenbank geschrieben'),
                'index.php?mod=install&action=translation&step=60&confirm=yes',
                'index.php?mod=install&action=translation'
            );
        } else {
            $db->qry("TRUNCATE %prefix%translation");
            $db->qry("TRUNCATE %prefix%translation_long");

            include_once("modules/install/class_install.php");
            $install = new install;
            $install->InsertModules();

            $func->confirmation(t('Die Übersetzungen wurden in die Datenbank eingelesen'), 'index.php?mod=install&action=translation');
        }
        break;

  // Search for Old Text and New Entrys
    case 70:
        $output = '';

        $dsp->NewContent(t('Veraltete Eintraege'), t('Veraltete Eingtraege die aehnlichkeiten mit einem neuen Eintrag aufweisen'));

        // Show switch between Modules/Files
        $dsp->AddFieldSetStart(t('Modul/File Wechseln.'));
        if ($_POST['target_file']) {
            $_SESSION['target_file'] = $_POST['target_file'];
        }
        if ($_SESSION['target_file'] == '') {
            $_SESSION['target_file'] = 'System';
        }
          $dsp->SetForm('index.php?mod=install&action=translation&step=70');

          $modules = array();
          $res = $db->qry("SELECT name FROM %prefix%modules");
        while ($row = $db->fetch_array($res)) {
            $modules[] = $row['name'];
        }
          $db->free_result($res);
          // Add Systemtranslations
          $modules[] = "DB";
          $modules[] = "System";
          $list = array();
        foreach ($modules as $modul) {
            ($_SESSION['target_file'] == $modul)? $selected = 'selected' : $selected = '';
            $list[] = "<option $selected value='{$modul}'>{$modul}</option>";
        }

          $dsp->AddDropDownFieldRow('target_file', t('Ziel Modul/File'), $list, '');
          $db->free_result($res);
          $dsp->AddFormSubmitRow(t('Ändern'));
        $dsp->AddFieldSetEnd();

      
        $res1 = $db->qry("SELECT tid, id, org, en, file, obsolete FROM %prefix%translation WHERE obsolete = '1' AND file=%string%", $_SESSION['target_file']);
        while ($row1 = $db->fetch_array($res1)) {
            $res2 = $db->qry("SELECT tid, id, en, org, file, obsolete FROM %prefix%translation WHERE obsolete != '1' AND file=%string%", $_SESSION['target_file']);

            while ($row2 = $db->fetch_array($res2)) {
                similar_text($row1['org'], $row2['org'], $percent);
                $score = ceil($percent);
                if ($score >80) {
                    $dsp->AddFieldSetStart(t('Aktueller Text TID:'.$row2['tid'].""));
                    $dsp->AddDoubleRow('Aktuell', $row2['org']);
                    $dsp->AddDoubleRow('Veraltet', $row1['org']);
                    $dsp->AddDoubleRow('Score', $score);
                    $dsp->AddDoubleRow('Veraltete Übersetzung EN', $row2['en']);
                    $buttons = $dsp->FetchSpanButton(t('Zusammenführen'), "index.php?mod=install&action=translation&step=80"). " ";
                    $dsp->AddDoubleRow('', $buttons);
                    $dsp->AddFieldSetEnd();
                }
            }
        }
        $db->free_result($res2);
        $db->free_result($res1);
      
        break;
}
$dsp->AddContent();
