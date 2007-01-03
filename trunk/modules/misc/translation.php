<?php
$LSCurFile = __FILE__;

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
        } else $CurrentFile = substr($CurrentFile, strrpos($CurrentFile, '/') + 1, strlen($CurrentFile));

        array_push($FoundTransEntries, $CurrentFile.'+'.$key); // Array later is compared to DB to synchronize

        $row = $db->query_first("SELECT 1 AS found FROM {$config['tables']['translation']} WHERE id = '{$key}' AND file = '$CurrentFile'");
        if ($row['found']) $output .= $CurrentFile .'@'. $CurrentPos .': '. $CurrentTrans .'<br />';
        else {
          // New -> Insert to DB
          $db->query("REPLACE INTO {$config['tables']['translation']} SET id = '$key', file = '{$CurrentFile}', org = '{$CurrentTrans}'");
          $output .= '<font color="#00ff00">'. $CurrentFile .'@'. $CurrentPos .': '. $CurrentTrans .'</font><br />';
        }
      }
    } elseif ($file != '.' and $file != '..' and $file != 'CVS' and is_dir($FilePath)) $output .= TUpdateFromFiles($FilePath);
  }
  closedir($ResDir);
  return $output;
}


switch ($_GET['step']) {
  default:
    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2('news');
    
    $ms2->query['from'] = "{$config['tables']['translation']}";
    $ms2->config['EntriesPerPage'] = 50;
    
    $selections = array('' => t('Alle'));
    $res = $db->query("SELECT file FROM {$config['tables']['translation']} GROUP BY file");
    while($row = $db->fetch_array($res)) $selections[$row['file']] = $row['file'];
    $db->free_result($res);
    $ms2->AddTextSearchDropDown(t('Fundstelle'), 'file', $selections, '');
    $ms2->AddTextSearchField(t('Text'), array('org' => 'like'));
    
    $ms2->AddResultField(t('Text'), 'org');
    $ms2->AddResultField(t('Fundstelle'), 'file');
    $ms2->AddResultField(t('En'), 'en', 'YesNo');

    $ms2->AddIconField('edit', 'index.php?mod=misc&action=translation&step=2&tid=', t('Edit'));
    
    $ms2->PrintSearch('index.php?mod=misc&action=translation', 'tid');
    $dsp->AddSingleRow($dsp->FetchIcon('index.php?mod=misc&action=translation&step=10', 'change'));
    $dsp->AddContent();
  break;
  
  case 2:
    include_once('inc/classes/class_masterform.php');
    $mf = new masterform();
    
    // Name
    $mf->AddField(t('Orginal-Text'), 'org');
    $mf->AddField(t('Englisch'), 'en');
    $mf->AddField(t('File'), 'file');
    
    $mf->SendForm('index.php?mod=misc&action=translation&step=2', 'translation', 'tid', $_GET['tid']);
    $dsp->AddBackButton('index.php?mod=misc&action=translation');
    $dsp->AddContent();
  break;


  // Search all files for strings in t()-functions and synchronize to DB
  case 10:
    $dsp->AddFieldSetStart(t('FrameWork'));
    $dsp->AddSingleRow(TUpdateFromFiles('inc/classes'));
    $dsp->AddFieldSetEnd();
    $dsp->AddFieldSetStart(t('Module'));
    $dsp->AddSingleRow(TUpdateFromFiles('modules'));
    $dsp->AddFieldSetEnd();

    // Delete entries, which no do no longer exist
    $output = '';
    $res = $db->query("SELECT id, org, file FROM {$config['tables']['translation']}");
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

    $dsp->AddBackButton('index.php?mod=misc&action=translation');
    $dsp->AddContent();
  break;
}
?>