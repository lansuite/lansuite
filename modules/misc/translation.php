<?php
function YesNo($TargetLang) {
    global $dsp;

  if ($TargetLang) return $dsp->FetchIcon('', 'yes');
  else return $dsp->FetchIcon('', 'no');
}

switch ($_GET['step']) {
  default:
    $dsp->NewContent(t('Übersetzen'), t('Es müssen nur Einträge eingetragen werden, die sich in der Zielsprache vom Orginal unterscheiden'));
    
    $dsp->AddFieldSetStart(t('Allgemeine Wartungsfunktionen'));
    $dsp->AddDoubleRow('','<a href="index.php?mod=misc&action=translation&step=2">'. t('Alle Datenbankeinträge auflisten') .'</a>');
    $dsp->AddDoubleRow('','<a href="index.php?mod=misc&action=translation&step=10">'. t('Einträge neu aus Quellcode auslesen und in die Datenbank schreiben') .'</a>');
    $dsp->AddDoubleRow('','<a href="index.php?mod=misc&action=translation&step=50">'. t('Einträge aus DB in mod_translation.xml schreiben (alle Module)') .'</a>');
    $dsp->AddDoubleRow('','<a href="index.php?mod=misc&action=translation&step=60">'. t('mod_translation.xml auslesen und in DB schreiben (alle Module)') .'</a>');
    $dsp->AddDoubleRow('','<a href="index.php?mod=misc&action=translation&step=70">'. t('Suche nach übereinstimmungen Aktuelle / Veraltete Texte (Mergen)') .'</a>');
    $dsp->AddFieldSetEnd();

    if ($_POST['target_language']) $_SESSION['target_language'] = $_POST['target_language'];

    $dsp->AddFieldSetStart(t('Module übersetzen'));
    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2('misc');
    $ms2->query['from'] = "{$config['tables']['translation']}";
    $ms2->config['EntriesPerPage'] = 20;
    $ms2->AddResultField(t('Modul'), 'file');
    $ms2->AddIconField('edit', 'index.php?mod=misc&action=translation&step=20&file=', t('Edit'));
    $ms2->AddIconField('download', 'index.php?mod=misc&action=translation&step=30&design=base&file=', t('Download'));
    $ms2->PrintSearch('index.php?mod=misc&action=translation', 'file');
    $dsp->AddFieldSetEnd();
  break;

  case 2:
    $dsp->NewContent(t('Übersetzen'), t('Es müssen nur Einträge eingetragen werden, die sich in der Zielsprache vom Orginal unterscheiden'));

    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2('misc');

    $ms2->query['from'] = "{$config['tables']['translation']}";
    $ms2->config['EntriesPerPage'] = 50;

    $selections = array('' => t('Alle'));
    $res = $db->query("SELECT file FROM {$config['tables']['translation']} GROUP BY file");
    while($row = $db->fetch_array($res)) $selections[$row['file']] = $row['file'];
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

    $ms2->AddIconField('edit', 'index.php?mod=misc&action=translation&step=3&tid=', t('Edit'));

    $ms2->PrintSearch('index.php?mod=misc&action=translation&step=2', 'tid');
  break;

  case 3:
    $dsp->NewContent(t('Übersetzen'), t('Es müssen nur Einträge eingetragen werden, die sich in der Zielsprache vom Orginal unterscheiden'));

    include_once('inc/classes/class_masterform.php');
    $mf = new masterform();
    
    // Name
    $mf->AddField(t('Orginal-Text'), 'org');
    $mf->AddField($dsp->FetchIcon('', 'de'), 'de', '', '', FIELD_OPTIONAL);
    $mf->AddField($dsp->FetchIcon('', 'en'), 'en', '', '', FIELD_OPTIONAL);
    $mf->AddField($dsp->FetchIcon('', 'es'), 'es', '', '', FIELD_OPTIONAL);
    $mf->AddField($dsp->FetchIcon('', 'nl'), 'nl', '', '', FIELD_OPTIONAL);
    $mf->AddField($dsp->FetchIcon('', 'fr'), 'fr', '', '', FIELD_OPTIONAL);
    $mf->AddField($dsp->FetchIcon('', 'it'), 'it', '', '', FIELD_OPTIONAL);
    
    $mf->SendForm('index.php?mod=misc&action=translation&step=3', 'translation', 'tid', $_GET['tid']);
    $dsp->AddBackButton('index.php?mod=misc&action=translation');
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

      // Delete entries, which no do no longer exist
      $output = '';
      $res = $db->query("SELECT id, org, file FROM {$config['tables']['translation']} WHERE file != 'DB'");
      while($row = $db->fetch_array($res)) {
        if (!in_array($row['file'].'+'.$row['id'], $FoundTransEntries)) {
          $db->query("UPDATE {$config['tables']['translation']} SET obsolete='1' WHERE id = '{$row['id']}'");
          $output .= '<font color="#ff0000">'. $row['file'] .': '. $row['org'] .'</font><br />';
        }
      }
      $db->free_result($res);
      $dsp->AddFieldSetStart(t('Veraltet (wurden als "veraltet" markiert)'));
      $dsp->AddSingleRow($output);
      $dsp->AddFieldSetEnd();

      // Scan DB
      $translation->TUpdateFromDB('menu', 'caption');
      $translation->TUpdateFromDB('menu', 'hint');
      $translation->TUpdateFromDB('modules', 'description');
      $translation->TUpdateFromDB('config', 'cfg_desc');
      $translation->TUpdateFromDB('config_selections', 'cfg_display');

      // For info output DB internal
      $output = '';
      $res = $db->query("SELECT id, org, file FROM {$config['tables']['translation']} WHERE file = 'DB'");
      while($row = $db->fetch_array($res)) $output .= $row['file'] .': '. $row['org'] .'<br />';
      $db->free_result($res);
      $dsp->AddFieldSetStart(t('DB-Internal'));
      $dsp->AddSingleRow($output);
      $dsp->AddFieldSetEnd();

      $dsp->AddBackButton('index.php?mod=misc&action=translation');
    }
  break;
  
  // Translate Module
  case 20:
    // If Write2File
    if ($_GET['subact'] == 'writetofile') $translation->xml_write_db_to_file($_GET['file']);
    if ($_GET['subact'] == 'writetodb') $translation->xml_write_file_to_db($_GET['file']);
    
    $dsp->NewContent(t('Modul Übersetzen : ').$_GET['file'], '');
    
    // Show switch between Lanuages
    $dsp->AddFieldSetStart(t('Sprache wechseln. Achtung, nicht gesicherte &Auml;nderungen gehen verloren.'));
        if ($_POST['target_language']) $_SESSION['target_language'] = $_POST['target_language'];
        if ($_SESSION['target_language'] == '') $_SESSION['target_language'] = 'en';
        $dsp->SetForm('index.php?mod=misc&action=translation&step=20&file='.$_GET['file']);
        $list = array();
        $res = $db->query("SELECT cfg_value, cfg_display FROM {$config["tables"]["config_selections"]} WHERE cfg_key = 'language'");
        while($row = $db->fetch_array($res)) {
          ($_SESSION['target_language'] == $row['cfg_value'])? $selected = 'selected' : $selected = '';
          $list[] = "<option $selected value='{$row['cfg_value']}'>{$row['cfg_display']}</option>";
        }
        $dsp->AddDropDownFieldRow('target_language', t('Ziel Sprache'), $list, '');
        $db->free_result($res);
        $dsp->AddFormSubmitRow('change');
        $tmp_link_write = "index.php?mod=misc&action=translation&step=20&file=".$_GET['file']."&subact=writetofile";
        $tmp_link_read = "index.php?mod=misc&action=translation&step=20&file=".$_GET['file']."&subact=writetodb";
        $dsp->AddDoubleRow(t('Schreibe Modulübersetzung in translation.xml') ,$dsp->FetchSpanButton(t('Schreibe'), $tmp_link_write));
        $dsp->AddDoubleRow(t('Lese Modulübersetzung von translation.xml') ,$dsp->FetchSpanButton(t('Lese'), $tmp_link_read));
    $dsp->AddFieldSetEnd();

    // Start Tanslation
    $dsp->AddFieldSetStart(t('Texte editieren.'));
        $dsp->SetForm('index.php?mod=misc&action=translation&step=21&file='. $_GET['file']);
        $res = $db->query("SELECT DISTINCT id, org, file, {$_SESSION['target_language']} FROM {$config['tables']['translation']} WHERE file = '{$_GET['file']}'");
        while($row = $db->fetch_array($res)) {
            $trans_link_google ="http://translate.google.com/translate_t?langpair=de|".$_SESSION['target_language']."&hl=de&ie=UTF8&text=".$row['org'];
            $trans_link_google =" <a href=\"".$trans_link_google."\" target=\"_blank\"><img src=\"design/".$auth['design']."/images/arrows_transl.gif\" width=\"12\" height=\"13\" border=\"0\" /></a>";
            
            if (strlen($row['org'])<60) {
                $dsp->AddTextFieldRow("id[{$row['id']}]",htmlentities($row['org']).$trans_link_google, $row[$_SESSION['target_language']], '', 65);
            } else { 
                $dsp->AddTextAreaRow ("id[{$row['id']}]",htmlentities($row['org']).$trans_link_google, $row[$_SESSION['target_language']], '', 50, 5);
            }
        
        }
        $db->free_result($res);
        $dsp->AddFormSubmitRow('edit');
    $dsp->AddFieldSetEnd();
    $dsp->AddBackButton('index.php?mod=misc&action=translation');

  break;

  // Translate Module - DB Insert
  case 21:
    foreach($_POST['id'] as $key => $value)
      $db->query("UPDATE {$config['tables']['translation']} SET {$_SESSION['target_language']} = '$value' WHERE file = '{$_GET['file']}' AND id = '$key'");

    $func->confirmation('Module-Übersetzung wurde erfolgreich upgedatet');
  break;

  // Export Module Translations
  case 30:
    include("modules/install/class_export.php");
    $export = New Export();

    $export->LSTableHead();
    $export->ExportMod($_GET['file'], 0, 0, 1);
    $export->LSTableFoot();
  break;


  // Translate Item
  case 40:
    $dsp->NewContent(t('Modul Übersetzen'), '');

    include_once('inc/classes/class_masterform.php');
    $mf = new masterform();
    $mf->AddField(t('Orginal-Text'), 'org', IS_NOT_CHANGEABLE);
    $mf->AddField(t('Deutsch'), 'de', '', '', FIELD_OPTIONAL);
    $mf->AddField(t('Englisch'), 'en', '', '', FIELD_OPTIONAL);
    $mf->AddField(t('Spanisch'), 'es', '', '', FIELD_OPTIONAL);
    $mf->AddField(t('Französisch'), 'fr', '', '', FIELD_OPTIONAL);
    $mf->AddField(t('Holländisch'), 'nl', '', '', FIELD_OPTIONAL);
    $mf->AddField(t('Italienisch'), 'it', '', '', FIELD_OPTIONAL);
    $mf->SendForm('index.php?mod=misc&action=translation&step=40', 'translation', 'id', $_GET['id']);

    $dsp->AddBackButton('index.php?mod=misc&action=translation');
  break;
      
  // Export Translation to Files
  case 50;
      if (!$_GET['confirm']=="yes") {
          $func->question(t('Achtung!!! Alle vorhandenen Übersetzungen in den XML-Dateien werden überschrieben'),
                            'index.php?mod=misc&action=translation&step=50&confirm=yes',
                            'index.php?mod=misc&action=translation');
      } else {
          $modules = array();
          $res = $db->query("SELECT name FROM {$config["tables"]["modules"]}");
          while($row = $db->fetch_array($res)) $modules[] = $row['name'];
          $db->free_result($res); 
          // Add Systemtranslations
          $modules[] = "DB";
          $modules[] = "System"; 
          foreach ($modules as $modul) {
              $translation->xml_write_db_to_file($modul);
              $info .= t("Modulübersetzung wurde in <b>translation.xml (%1)</b> geschrieben<br \>",$modul);
          }
          $func->information($info,'index.php?mod=misc&action=translation');
      }
  break;

  // Import Translation to DB from mod_translation.xml
  case 60;
      if (!$_GET['confirm']=="yes") {
          $func->question(t('Achtung!!! Alle vorhandenen Übersetzungen werden von den XML-Dateien in die Datenbank geschrieben'),
                            'index.php?mod=misc&action=translation&step=60&confirm=yes',
                            'index.php?mod=misc&action=translation');
      } else {
          $modules = array();
          $res = $db->query("SELECT name FROM {$config["tables"]["modules"]}");
          while($row = $db->fetch_array($res)) $modules[] = $row['name'];
          $db->free_result($res);  
          // Add Systemtranslations
          $modules[] = "DB";
          $modules[] = "System"; 
          foreach ($modules as $modul) {
              $meld = $translation->xml_write_file_to_db($modul);
              $info .= t("Modulübersetzung wurde von <b>%1</b> gelesen. (%2)<br \>",$translation->get_trans_filename($modul),$meld);
          }
          $func->information($info,'index.php?mod=misc&action=translation');
      }
  break;

  // Search for Old Text and New Entrys
  case 70;
      $output = '';

      $dsp->NewContent(t('Veraltete Eintraege'), t('Veraltete Eingtraege die aehnlichkeiten mit einem neuen Eintrag aufweisen'));

      // Show switch between Modules/Files
      $dsp->AddFieldSetStart(t('Modul/File Wechseln.'));
          if ($_POST['target_file']) $_SESSION['target_file'] = $_POST['target_file'];
          if ($_SESSION['target_file'] == '') $_SESSION['target_file'] = 'System';
          $dsp->SetForm('index.php?mod=misc&action=translation&step=70');

          $modules = array();
          $res = $db->query("SELECT name FROM {$config["tables"]["modules"]}");
          while($row = $db->fetch_array($res)) $modules[] = $row['name'];
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
          $dsp->AddFormSubmitRow('change');
      $dsp->AddFieldSetEnd();

      
      $res1 = $db->query("SELECT tid, id, org, en, file, obsolete FROM {$config['tables']['translation']} WHERE obsolete = '1' AND file='{$_SESSION['target_file']}'");
      while($row1 = $db->fetch_array($res1)) {
          $res2 = $db->query("SELECT tid, id, en, org, file, obsolete FROM {$config['tables']['translation']} WHERE obsolete != '1' AND file='{$_SESSION['target_file']}'");

          while($row2 = $db->fetch_array($res2)) {
              similar_text($row1['org'], $row2['org'], $percent );
              $score = ceil($percent);      
              if ($score >80) {
                  $dsp->AddFieldSetStart(t('Aktueller Text TID:'.$row2['tid'].""));
                  $dsp->AddDoubleRow('Aktuell',$row2['org']);
                  $dsp->AddDoubleRow('Veraltet',$row1['org']);
                  $dsp->AddDoubleRow('Score',$score);
                  $dsp->AddDoubleRow('Veraltete Übersetzung EN',$row2['en']);
                  $buttons = $dsp->FetchButton("index.php?mod=misc&action=translation&step=80", "Zusammenführen"). " "; // FIX Button
                  $dsp->AddDoubleRow('',$buttons);
                  $dsp->AddFieldSetEnd();
              }
          }
      }
      $db->free_result($res2);
      $db->free_result($res1);
      
  break;


}
$dsp->AddContent();
?>