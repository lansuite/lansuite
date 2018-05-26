<?php

switch ($_GET['step']) {
    default:
        $ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2('usrmgr');
    
        $ms2->query['from'] = "%prefix%user_fields AS f";
    
        $ms2->config['EntriesPerPage'] = 20;
    
        $ms2->AddResultField('Feldname', 'f.name');
        $ms2->AddResultField('Bezeichnung', 'f.caption');
        $ms2->AddResultField('Optional', 'f.optional');
    
        if ($auth['type'] >= 3) {
            $ms2->AddIconField('delete', 'index.php?mod=usrmgr&action=user_fields&step=20&fieldid=', t('Löschen'));
        }
        $ms2->PrintSearch('index.php?mod=usrmgr&action=user_fields', 'f.fieldid');
    
        $dsp->AddSingleRow($dsp->FetchSpanButton(t('Hinzufügen'), "index.php?mod=usrmgr&action=user_fields&step=10"));
        break;
  
    // Add new entry
    case 10:
        $mf = new \LanSuite\MasterForm();

        $mf->AddField('Feldname', 'name', '', '', '', 'check_no_space');
        $mf->AddField('Bezeichnung', 'caption');

        $selections = array();
        $selections['0'] = t('Ausblenden');
        $selections['1'] = t('Optional');
        $selections['2'] = t('Pflichtfeld');
        $mf->AddField(t('Optional'), 'optional', \LanSuite\MasterForm::IS_SELECTION, $selections);

        $mf->AdditionalDBUpdateFunction = 'UpdateUsrMgrUserFields';
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
