<?php
$LSCurFile = __FILE__;

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
    
    $ms2->AddIconField('edit', 'index.php?mod=misc&action=translation&step=2&tid=', t('Edit'));
    
    $ms2->PrintSearch('index.php?mod=misc&action=translation', 'tid');
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
}
?>
