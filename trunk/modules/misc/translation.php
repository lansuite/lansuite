<?php
function YesNo($TargetLang) {
	global $dsp;

  if ($TargetLang) return $dsp->FetchIcon('', 'yes');
  else return $dsp->FetchIcon('', 'no');
}


function TUpdateFromFiles($BaseDir) {
  global $db, $config, $FoundTransEntries;

  $output = '';
  if (!is_array($FoundTransEntries)) $FoundTransEntries = array();

  $ResDir = opendir($BaseDir);
  while ($file = readdir($ResDir)) {
    $FilePath = $BaseDir .'/'. $file;

    if (substr($file, strlen($file) - 4, 4) == '.php') {

  		$ResFile = fopen($FilePath, "r");
  		$content = fread($ResFile, filesize($FilePath));
  		fclose($ResFile);

      $treffer = array();
      preg_match_all('/([^a-zA-Z0-9]+t\\(\\\')(.*?)(\\\'\\)|\\\'\\,)/', $content, $treffer, PREG_SET_ORDER + PREG_OFFSET_CAPTURE);
      foreach ($treffer as $wert) {

        $CurrentPos = $wert[2][1];
        $CurrentTrans = $wert[2][0];
        $key = md5($CurrentTrans);

        // Generate Mod-Name from FILE
        $CurrentFile = str_replace('\\','/', $FilePath);
        if (strpos($CurrentFile, 'modules/') !== false) {
          $start = strpos($CurrentFile, 'modules/') + 8;
          $CurrentFile = substr($CurrentFile, $start, strrpos($CurrentFile, '/') - $start);
        } else $CurrentFile = 'System';

        // Do only add expressions, which are not already in system lang-file
        $row = $db->query_first("SELECT 1 AS found FROM {$config['tables']['translation']} WHERE id = '{$key}' AND (file = 'System')");
        if (!$row['found'] or $CurrentFile == 'System'){
          array_push($FoundTransEntries, $CurrentFile.'+'.$key); // Array is compared to DB later for synchronization

          $row = $db->query_first("SELECT 1 AS found FROM {$config['tables']['translation']} WHERE id = '{$key}' AND (file = '$CurrentFile')");
          if ($row['found']) $output .= $CurrentFile .'@'. $CurrentPos .': '. $CurrentTrans .'<br />';
          else {
            // New -> Insert to DB
            $db->query("REPLACE INTO {$config['tables']['translation']} SET id = '$key', file = '{$CurrentFile}', org = '{$CurrentTrans}'");
            $output .= '<font color="#00ff00">'. $CurrentFile .'@'. $CurrentPos .': '. $CurrentTrans .'</font><br />';
          }
        }
      }
    } elseif ($file != '.' and $file != '..' and $file != '.svn' and is_dir($FilePath)) $output .= TUpdateFromFiles($FilePath);
  }
  closedir($ResDir);
  return $output;
}

function TUpdateFromDB($table, $field) {
  global $db;

  $res = $db->qry('SELECT '. $field .' FROM %prefix%'. $table);
  while ($row = $db->fetch_array($res)) if ($row[$field] != '') {
    $key = md5($row[$field]);
    $row2 = $db->qry_first('SELECT 1 AS found FROM %prefix%translation WHERE id = %string%', $key);
    if (!$row2['found']) $db->qry('REPLACE INTO %prefix%translation SET id = %string%, file = \'DB\', org = %string%', $key, $row[$field]);
  }
  $db->free_result($res);
}


switch ($_GET['step']) {
  default:
    $dsp->NewContent(t('Übersetzen'), t('Es müssen nur Einträge eingetragen werden, die sich in der Zielsprache vom Orginal unterscheiden'));

    $dsp->AddSingleRow('<a href="index.php?mod=misc&action=translation&step=2">'. t('Alle Einträge auflisten') .'</a>');
    $dsp->AddSingleRow('<a href="index.php?mod=misc&action=translation&step=10">'. t('Einträge neu auslesen und in die Datenbank schreiben') .'</a>');

    if ($_POST['target_language']) $_SESSION['target_language'] = $_POST['target_language'];

    $dsp->AddFieldSetStart(t('Modul übersetzen'));
    $dsp->SetForm('index.php?mod=misc&action=translation');
    $list = array();
    $res = $db->query("SELECT cfg_value, cfg_display FROM {$config["tables"]["config_selections"]} WHERE cfg_key = 'language'");
    while($row = $db->fetch_array($res)) {
      ($_SESSION['target_language'] == $row['cfg_value'])? $selected = 'selected' : $selected = '';
      $list[] = "<option $selected value='{$row['cfg_value']}'>{$row['cfg_display']}</option>";
    }
    $dsp->AddDropDownFieldRow('target_language', t('Ziel Sprache'), $list, '');
    $db->free_result($res);
    $dsp->AddFormSubmitRow('change');
    
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
      $dsp->AddSingleRow(TUpdateFromFiles('inc/classes'));
      $dsp->AddFieldSetEnd();
      $dsp->AddFieldSetStart(t('Module'));
      $dsp->AddSingleRow(TUpdateFromFiles('modules'));
      $dsp->AddFieldSetEnd();

      // Delete entries, which no do no longer exist
      $output = '';
      $res = $db->query("SELECT id, org, file FROM {$config['tables']['translation']} WHERE file != 'DB'");
      while($row = $db->fetch_array($res)) {
        if (!in_array($row['file'].'+'.$row['id'], $FoundTransEntries)) {
          $db->query("DELETE FROM {$config['tables']['translation']} WHERE id = '{$row['id']}'");
          $output .= '<font color="#ff0000">'. $row['file'] .': '. $row['org'] .'</font><br />';
        }
      }
      $db->free_result($res);
      $dsp->AddFieldSetStart(t('Veraltet (wurden nun gelöscht)'));
      $dsp->AddSingleRow($output);
      $dsp->AddFieldSetEnd();

      // Scan DB
      TUpdateFromDB('menu', 'caption');
      TUpdateFromDB('menu', 'hint');
      TUpdateFromDB('modules', 'description');
      TUpdateFromDB('config', 'cfg_desc');
      TUpdateFromDB('config_selections', 'cfg_display');

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
    $dsp->NewContent(t('Modul übersetzen : ').$_GET['file'], '');
    
    // Show switch between Lanuages
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

    // Start Tanslation
    
    $dsp->SetForm('index.php?mod=misc&action=translation&step=21&file='. $_GET['file']);
    $res = $db->query("SELECT DISTINCT id, org, file, {$_SESSION['target_language']} FROM {$config['tables']['translation']} WHERE file = '{$_GET['file']}'");
    while($row = $db->fetch_array($res)) {
        $trans_link_google ="http://translate.google.com/translate_t?langpair=de|".$_SESSION['target_language']."&hl=de&ie=UTF8&text=".$row['org'];
        $trans_link_google =" <a href=\"".$trans_link_google."\" target=\"_blank\"><img src=\"design/".$auth['design']."/images/arrows_transl.gif\" width=\"12\" height=\"13\" border=\"0\" /></a>";
        $dsp->AddTextFieldRow("id[{$row['id']}]",$row['org'].$trans_link_google, $row[$_SESSION['target_language']], '', 65);
    }
    $db->free_result($res);
    $dsp->AddFormSubmitRow('edit');

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
    $dsp->NewContent(t('Modul übersetzen'), '');

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
}
$dsp->AddContent();
?>
