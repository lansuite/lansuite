<?php

switch($_GET['step']){
	
	default:
	// Partys anzeigen
	if($cfg['signon_multiparty'] == 0){
		$row = $db->query_first("SELECT * FROM {$config['tables']['partys']} WHERE party_id={$party->party_id}");
		
		$dsp->NewContent($lang['signon']['show_party_caption'],$lang['signon']['show_party_subcaption']);
		$dsp->AddDoubleRow($lang['signon']['partyname'],$row['name']);
		$dsp->AddDoubleRow($lang['signon']['max_guest'],$row['max_guest']);
		$dsp->AddDoubleRow($lang['signon']['plz'],$row['plz']);
		$dsp->AddDoubleRow($lang['signon']['ort'],$row['ort']);
		$dsp->AddDoubleRow($lang['signon']['stime'],$func->unixstamp2date($row['startdate'],"datetime"));
		$dsp->AddDoubleRow($lang['signon']['etime'],$func->unixstamp2date($row['enddate'],"datetime"));
		$dsp->AddDoubleRow($lang['signon']['sstime'],$func->unixstamp2date($row['sstartdate'],"datetime"));
		$dsp->AddDoubleRow($lang['signon']['setime'],$func->unixstamp2date($row['senddate'],"datetime"));
		$dsp->AddDoubleRow("",$dsp->FetchButton("index.php?mod=signon&action=add_party&step=1&var=update","edit"));
		$dsp->AddContent();
				
	}else{
		$row = $db->query("SELECT * FROM {$config['tables']['partys']}");

		$dsp->NewContent($lang['signon']['show_list_caption'],$lang['signon']['show_list_subcaption']);
		
		while($result = $db->fetch_array($row)){	
			$dsp->AddDoubleRow("<a href='index.php?mod=signon&action=edit_party&step=1&party_id=" . $result['party_id'] . "&var=update'>" . $result['name']  . "</a>",$func->unixstamp2date($result['startdate'],"datetime") . " - " . $func->unixstamp2date($result['enddate'],"datetime"));
		}
		$dsp->AddDoubleRow("",$dsp->FetchButton("index.php?mod=signon&action=add_party&step=1&var=new","add"));
		
		
		$dsp->AddSingleRow($lang['signon']['change_party']);
		$party->get_party_dropdown_form(1,"index.php?mod=signon&action=add_party&step=3");
		$dsp->AddContent();
	}
	break;
	
	case 1:
		$row = $db->query_first("SELECT * FROM {$config['tables']['partys']} WHERE party_id={$party->party_id}");

		$dsp->NewContent($lang['signon']['show_party_caption'],$lang['signon']['show_party_subcaption']);
		$dsp->AddDoubleRow($lang['signon']['partyname'],$row['name']);
		$dsp->AddDoubleRow($lang['signon']['max_guest'],$row['max_guest']);
		$dsp->AddDoubleRow($lang['signon']['plz'],$row['plz']);
		$dsp->AddDoubleRow($lang['signon']['ort'],$row['ort']);
		$dsp->AddDoubleRow($lang['signon']['stime'],$func->unixstamp2date($row['startdate'],"datetime"));
		$dsp->AddDoubleRow($lang['signon']['etime'],$func->unixstamp2date($row['enddate'],"datetime"));
		$dsp->AddDoubleRow($lang['signon']['sstime'],$func->unixstamp2date($row['sstartdate'],"datetime"));
		$dsp->AddDoubleRow($lang['signon']['setime'],$func->unixstamp2date($row['senddate'],"datetime"));
		$dsp->AddDoubleRow("",$dsp->FetchButton("index.php?mod=signon&action=add_party&step=1&var=update","edit"));
		$dsp->AddContent();
	break;
	
}





?>
