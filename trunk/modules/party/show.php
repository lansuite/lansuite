<?php

function GetActiveState($id) {
	global $cfg;

  if ($cfg['signon_partyid'] == $id) return 'Aktive Party';
	else return '<a href="index.php?mod=party&action=show&step=10&party_id='. $id .'">Aktivieren</a>';
}

// Set Active PartyID
if ($_GET['step'] == 10 and is_numeric($_GET['party_id'])) {
  $db->query("UPDATE {$config['tables']['config']} SET cfg_value = '{$_GET['party_id']}' WHERE cfg_key = 'signon_partyid'");
  $cfg['signon_partyid'] = $_GET['party_id'];
}

$dsp->NewContent($lang['signon']['show_party_caption'],$lang['signon']['show_party_subcaption']);
switch($_GET['step']){
	default:
    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2('party');
    
    $ms2->query['from'] = "{$config["tables"]["partys"]} AS p";
    $ms2->query['default_order_by'] = 'p.startdate DESC';
    
    $ms2->config['EntriesPerPage'] = 20;
    
    $ms2->AddResultField('Name', 'p.name');
    $ms2->AddResultField('Gäste', 'p.max_guest');
    $ms2->AddResultField('Von', 'p.startdate');
    $ms2->AddResultField('Bis', 'p.enddate');
    $ms2->AddResultField('Aktiv', 'p.party_id', 'GetActiveState');
    
    $ms2->AddIconField('details', 'index.php?mod=party&action=show&step=1&party_id=', $lang['ms2']['details']);
    if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=party&action=edit&party_id=', $lang['ms2']['edit']);
    if ($auth['type'] >= 2) $ms2->AddIconField('paid', 'index.php?mod=party&action=price&step=2&party_id=');

    if ($auth['type'] >= 3) $ms2->AddMultiSelectAction($lang['ms2']['delete'], 'index.php?mod=party&action=delete', 1);

    $ms2->PrintSearch('index.php?mod=party', 'p.party_id');

    $dsp->AddSingleRow($dsp->FetchButton('index.php?mod=party&action=edit', 'add'));
	break;

	case 1:
		$row = $db->query_first("SELECT p.*, UNIX_TIMESTAMP(p.startdate) AS startdate, UNIX_TIMESTAMP(p.enddate) AS enddate, UNIX_TIMESTAMP(p.sstartdate) AS sstartdate, UNIX_TIMESTAMP(p.senddate) AS senddate FROM {$config['tables']['partys']} AS p WHERE party_id={$party->party_id}");

		$dsp->AddDoubleRow($lang['signon']['partyname'],$row['name']);
		$dsp->AddDoubleRow($lang['signon']['max_guest'],$row['max_guest']);
		$dsp->AddDoubleRow($lang['signon']['plz'],$row['plz']);
		$dsp->AddDoubleRow($lang['signon']['ort'],$row['ort']);
		$dsp->AddDoubleRow($lang['signon']['stime'],$func->unixstamp2date($row['startdate'],"date"));
		$dsp->AddDoubleRow($lang['signon']['etime'],$func->unixstamp2date($row['enddate'],"date"));
		$dsp->AddDoubleRow($lang['signon']['sstime'],$func->unixstamp2date($row['sstartdate'],"date"));
		$dsp->AddDoubleRow($lang['signon']['setime'],$func->unixstamp2date($row['senddate'],"date"));
		$dsp->AddDoubleRow("", $dsp->FetchButton("index.php?mod=party&action=edit&party_id={$_GET['party_id']}","edit"));

    $dsp->AddBackButton('index.php?mod=party');
	break;
}
$dsp->AddContent();
?>