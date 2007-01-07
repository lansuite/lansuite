<?php

switch($_GET["step"]){
	default:
    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2();

    $ms2->query['from'] = "{$config["tables"]["log"]} AS l
      LEFT JOIN {$config["tables"]["user"]} AS u ON u.userid = l.userid";
    $ms2->query['default_order_by'] = 'l.date DESC';

    $ms2->AddTextSearchField(t('Meldung'), array('l.description' => 'like'));
    $ms2->AddTextSearchField(t('Gruppe'), array('l.sort_tag' => 'like'));
    $ms2->AddTextSearchField(t('Auslöser'), array('l.userid' => 'exact', 'u.name' => 'like', 'u.firstname' => 'like'));

    $list = array('' => t('Alle'));
    $row = $db->query("SELECT sort_tag FROM {$config['tables']['log']} GROUP BY sort_tag");
    while($res = $db->fetch_array($row)) if($res['sort_tag']) $list[$res['sort_tag']] = $res['sort_tag'];
    $db->free_result($row);
    $ms2->AddTextSearchDropDown(t('Gruppe'), 'l.sort_tag', $list);

    $ms2->AddTextSearchDropDown(t('Prioritat'), 'l.type', array('' => 'Alle', '1' => 'Niedrig', '2' => 'Normal', '3' => 'Hoch'));

    $ms2->AddSelect('u.userid');
    $ms2->AddResultField(t('Meldung'), 'l.description', '', 140);
    $ms2->AddResultField(t('Gruppe'), 'l.sort_tag');
    $ms2->AddResultField(t('Datum'), 'l.date', 'MS2GetDate');
    $ms2->AddResultField(t('Auslöser'), 'u.username', 'UserNameAndIcon');

    $ms2->AddIconField('details', 'index.php?mod=misc&action=log&step=2&logid=', t('Details'));

    $ms2->PrintSearch('index.php?mod=misc&action=log', 'l.logid');
	break;

  case 2:
    $log = $db->query_first("SELECT * FROM {$config["tables"]["log"]} WHERE logid = {$_GET['logid']}");
    $dsp->AddSingleRow($log['sort_tag']);
    $dsp->AddSingleRow($log['description']);
    $dsp->AddSingleRow($func->unixstamp2date($log['date'], 'datetime'));
    if ($log['userid']) $dsp->AddSingleRow($dsp->FetchUserIcon($log['userid']));
    $dsp->AddBackButton("index.php?mod=misc&action=log", '');
  break;
}
?>