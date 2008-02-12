<?php

$dsp->NewContent($lang['mmc']['headline'], 'Playliste beareiten');
$count = $db->query_first("SELECT count( * ) AS n FROM {$config['tables']['mmc_playlist']}");

// Datenbank aktualisieren

if ($_POST['enqueue']){
	$db->query_first("DELETE FROM {$config['tables']['mmc_playlist']}");	// Alle löschen (uneffizient aber erstmal ok...)
	foreach($vars as $key => $value){
		if(substr($key,0,2)=='id') {
			$items[substr($key,2)]['mediaid'] = $value;
		}
		if(substr($key,0,5)=='loops'){
			$items[substr($key,5)]['loops'] = $value;
		}
	}
	if (count($items)>0){
		foreach($items as $entry => $row){
			$db->query_first("INSERT INTO {$config['tables']['mmc_playlist']} VALUES (NULL , '".$row['mediaid']."', '".$entry."', '".$row['loops']."')");
		}
	}
}

//Löschen

if($_POST['delete'] && $_POST['delete']!='nothing'){
	$db->query_first("DELETE FROM {$config['tables']['mmc_playlist']} WHERE position = ".$_POST['delete']." LIMIT 1");
}

//Hinzufügen

if ($_POST['enqueue'] && $_POST['enqueue']!='no'){
	$count = $db->query_first("SELECT count( * ) AS n FROM {$config['tables']['mmc_playlist']}");
	$db->query_first("INSERT INTO {$config['tables']['mmc_playlist']} VALUES (NULL , '".$_POST['enqueue']."', '". $count['n']	 ."', '0')");
}

//Tabelle anzeigen...

$dsp->SetForm('index.php?mod=mmc&action=playlist&step=1', 'Playliste');
$count = $db->query_first("SELECT count( * ) AS n FROM {$config['tables']['mmc_playlist']}");
if ($count['n']>0){
	$templ['mmc']['t_rows']  = "";
	$templ['mmc']['th_nr']   = 'Nr.';
	$templ['mmc']['th_name'] = 'Name';
	$templ['mmc']['th_loop'] = 'Wiederh./Sekunden';
	$res = $db->query("SELECT {$config['tables']['mmc_playlist']}.mediaid as mediaid, name, position, loops 
					   FROM {$config['tables']['mmc_media']}, {$config['tables']['mmc_playlist']} 
					   WHERE {$config['tables']['mmc_media']}.mediaid={$config['tables']['mmc_playlist']}.mediaid
					   ORDER BY itemid ASC");
	while ($temp = $db->fetch_array($res)){
		$templ['mmc']['t_row_id']++;
		$templ['mmc']['t_row_loops']   = $temp['loops'];
		$templ['mmc']['t_row_name']    = $temp['name'];
		$templ['mmc']['t_row_mediaid'] = $temp['mediaid'];
		$templ['mmc']['t_rows'] .= $dsp->FetchModTpl("mmc", "mmc_playlist_rows");
	}
	$db->free_result($res);
	$dsp->AddModTpl("mmc", "mmc_playlist_table");
} else {
	$dsp->AddSingleRow('<br /><div align="center"><b>Die Playliste enth&auml;lt keine Eintr&auml;ge.</b><br >Wählen Sie eine Datei im Auswahlfeld aus und klicken Sie ´speichern´ um Sie zur Playliste hinzuzufügen.</div><br />');
	$addContent = TRUE;
}
$files = $db->query("SELECT mediaid, name
					 FROM {$config['tables']['mmc_media']}
					 ORDER BY name ASC");
$options = array();
$options[] = '<option value="no">bitte ausw&auml;hlen</option>';

while ($item = $db->fetch_array($files)){
	$options[] = '<option value="'.$item['mediaid'].'">'.$item['name'].'</option>';
}

$db->free_result($files);
$dsp->AddDropDownFieldRow('enqueue', 'Zur Playliste hinzuf&uuml;gen', $options,'',TRUE);
$dsp->AddFormSubmitRow('save');
if ($addContent) $dsp->AddContent();

?>