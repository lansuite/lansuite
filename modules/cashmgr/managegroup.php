<?php

switch ($_GET['step']) {
    default:
        $dsp->NewContent(t('Gruppen verwalten'), t('Uebersicht'));
        $ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2('cashmgr');

        $ms2->query['from'] = "%prefix%cashmgr_group AS g
                               LEFT JOIN %prefix%cashmgr_accounting AS a ON g.id = a.groupid";
        $ms2->query['default_order_by'] = 'actiontime DESC';
        $ms2->config['EntriesPerPage'] = 20;
        
        $ms2->AddResultField(t('Name'), 'g.caption');
        $ms2->AddIconField('edit', 'index.php?mod=cashmgr&action=managegroup&step=1&id=', t('Editieren'));
        $ms2->AddIconField('delete', 'index.php?mod=cashmgr&action=managegroup&step=2&id=', t('Löschen'));
        $ms2->PrintSearch('index.php?mod=cashmgr&action=managegroup', 'g.id');
        
        $dsp->AddSingleRow($dsp->FetchSpanButton(t('Hinzufügen'), 'index.php?mod=cashmgr&action=managegroup&step=1'));
        break;
        
    case 1:
        $mf = new \LanSuite\MasterForm();
        $dsp->NewContent(t('Gruppe anlegen/ editieren '), t(''));
        $mf->AddField('Bezeichnung', 'caption');
        $mf->SendForm('index.php?mod=cashmgr&action=managegroup&step=1', 'cashmgr_group', 'id', $_GET['id']);
        break;
        
    case 2:
        $db->qry("DELETE FROM %prefix%cashmgr_group WHERE id = %int%", $_GET['id']);
        $func->confirmation('Erfolgreich gelöscht', 'index.php?mod=cashmgr&action=managegroup');
        break;
}
