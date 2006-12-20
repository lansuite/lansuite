<?php

switch($_GET['step']){
	default:
    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2('news');
    
    $ms2->query['from'] = "{$config["tables"]["partys"]} AS p";
    $ms2->query['default_order_by'] = 'p.startdate DESC';
    
    $ms2->config['EntriesPerPage'] = 20;
    
    $ms2->AddResultField('Name', 'p.name');
    $ms2->AddResultField('Gäste', 'p.max_guest');
    $ms2->AddResultField('Von', 'p.startdate');
    $ms2->AddResultField('Bis', 'p.enddate');
    
    $ms2->AddIconField('details', 'index.php?mod=signon&action=party&step=1&party_id=', $lang['ms2']['details']);
    if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=signon&action=party_edit&party_id=', $lang['ms2']['edit']);
    #if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=news&action=delete&step=2&newsid=', $lang['ms2']['delete']);
    $ms2->PrintSearch('index.php?mod=signon&action=party', 'p.party_id');

    $dsp->AddSingleRow($dsp->FetchButton('index.php?mod=signon&action=party_edit', 'add'));
    $dsp->AddContent();
	break;

	case 1:
		$row = $db->query_first("SELECT p.*, UNIX_TIMESTAMP(p.startdate) AS startdate, UNIX_TIMESTAMP(p.enddate) AS enddate, UNIX_TIMESTAMP(p.sstartdate) AS sstartdate, UNIX_TIMESTAMP(p.senddate) AS senddate FROM {$config['tables']['partys']} AS p WHERE party_id={$party->party_id}");

		$dsp->NewContent($lang['signon']['show_party_caption'],$lang['signon']['show_party_subcaption']);
		$dsp->AddDoubleRow($lang['signon']['partyname'],$row['name']);
		$dsp->AddDoubleRow($lang['signon']['max_guest'],$row['max_guest']);
		$dsp->AddDoubleRow($lang['signon']['plz'],$row['plz']);
		$dsp->AddDoubleRow($lang['signon']['ort'],$row['ort']);
		$dsp->AddDoubleRow($lang['signon']['stime'],$func->unixstamp2date($row['startdate'],"date"));
		$dsp->AddDoubleRow($lang['signon']['etime'],$func->unixstamp2date($row['enddate'],"date"));
		$dsp->AddDoubleRow($lang['signon']['sstime'],$func->unixstamp2date($row['sstartdate'],"date"));
		$dsp->AddDoubleRow($lang['signon']['setime'],$func->unixstamp2date($row['senddate'],"date"));
		$dsp->AddDoubleRow("", $dsp->FetchButton("index.php?mod=signon&action=party_edit&party_id={$_GET['party_id']}","edit"));

    $dsp->AddBackButton('index.php?mod=signon&action=party');
		$dsp->AddContent();
	break;
}
?>
