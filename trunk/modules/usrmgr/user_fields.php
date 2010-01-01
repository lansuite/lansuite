<?php

function Update($id){
  global $db, $config;
  
  $db->qry("ALTER TABLE %prefix%user ADD %plain% VARCHAR(255) NOT NULL;", $_POST['name']);
  
  return true;
}

switch ($_GET['step']) {
  default:
    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2('usrmgr');
    
    $ms2->query['from'] = "%plain%user_fields AS f";
    
    $ms2->config['EntriesPerPage'] = 20;
    
    $ms2->AddResultField('Feldname', 'f.name');
    $ms2->AddResultField('Bezeichnung', 'f.caption');
    $ms2->AddResultField('Optional', 'f.optional');
    
    if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=usrmgr&action=user_fields&step=20&fieldid=', t('Löschen'));
    $ms2->PrintSearch('index.php?mod=usrmgr&action=user_fields', 'f.fieldid');
    
    $dsp->AddSingleRow($dsp->FetchSpanButton(t('Hinzufügen'), "index.php?mod=usrmgr&action=user_fields&step=10"));
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
  
  // Delete entry
  case 20:
    $fild_row = $db->qry_first("SELECT name FROM %prefix%user_fields WHERE fieldid = %int%", $_GET['fieldid']);
    $db->qry("ALTER TABLE %prefix%user DROP %plain%", $fild_row['name']);

    $db->qry("DELETE FROM %prefix%user_fields WHERE fieldid = %int%", $_GET['fieldid']);
    
    $func->confirmation('Gelöscht', 'index.php?mod=usrmgr&action=user_fields');
  break;
}
?>