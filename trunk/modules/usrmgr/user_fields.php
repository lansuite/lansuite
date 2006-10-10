<?php

function Update($id){
  global $db, $config;
  
  $db->query("ALTER TABLE {$config['tables']['user']} ADD {$_POST['name']} VARCHAR(255) NOT NULL;");
  
  return true;
}

switch ($_GET['step']) {
  default:
    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2('usrmgr');
    
    $ms2->query['from'] = "{$config['tables']['user_fields']} AS f";
    
    $ms2->config['EntriesPerPage'] = 20;
    
    $ms2->AddResultField('Feldname', 'f.name');
    $ms2->AddResultField('Bezeichnung', 'f.caption');
    $ms2->AddResultField('Optional', 'f.optional');
    
    $ms2->PrintSearch('index.php?mod=usrmgr&action=user_fields', 'f.fieldid');
    
    $dsp->AddSingleRow($dsp->FetchButton("index.php?mod=usrmgr&action=user_fields&step=10", 'add'));
    $dsp->AddContent();
  break;
  
  // Add new entry
  case 10:
    include_once('inc/classes/class_masterform.php');
    $mf = new masterform();

    $mf->AddField('Bezeichnung', 'caption');
    $mf->AddField('Feldname', 'name');
    $mf->AddField('Optional', 'optional');

    $mf->AdditionalDBUpdateFunction = 'Update';
    $mf->SendForm('index.php?mod=usrmgr&action=user_fields&step=10', 'user_fields', 'fieldid', $_GET['fieldid']);
  break;
}
?>
