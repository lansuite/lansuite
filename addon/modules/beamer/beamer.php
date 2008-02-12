<?php
if (($auth["type"] >= 2) or (($auth["userid"] == $_GET["userid"]) && $cfg['user_self_details_change'])) {

// Änderung speichern
if($_GET["mode"]=="save_edit") {

if($_GET["beamer"]=="new") {
$db->query("INSERT INTO {$config["tables"]["beamer_beamer"]} (bezeichnung, aufloesung_x, aufloesung_y, nodata_pfad, show_msg, usertxt) VALUES ('{$_POST['b_bezeichnung']}', '{$_POST['b_aufloesung_x']}', '{$_POST['b_aufloesung_y']}', '{$_POST['b_nodata_pfad']}', '{$_POST['b_show_msg']}', '{$_POST['b_usertxt']}')");
$func->confirmation("Der neue Inhalt wurde angelegt", "index.php?mod=beamer&action=beamer&mode=edit&beamer=".$db->insert_id());

} else {
$db->query("UPDATE {$config["tables"]["beamer_beamer"]} SET bezeichnung='{$_POST['b_bezeichnung']}', aufloesung_x='{$_POST['b_aufloesung_x']}', aufloesung_y='{$_POST['b_aufloesung_y']}', nodata_pfad='{$_POST['b_nodata_pfad']}', show_msg='{$_POST['b_show_msg']}', usertxt='{$_POST['b_usertxt']}' WHERE bID='{$_GET['beamer']}'");
$func->confirmation("Die Änderungen wurden übernommen", "?mod=beamer&action=beamer&mode=edit&beamer=".$_GET['beamer']);
}
}



// Beamer bearbeiten
if($_GET["mode"]=="edit") {
$res = $db->query("SELECT * FROM {$config["tables"]["beamer_beamer"]} WHERE bID='{$_GET['beamer']}'");
$beamer = $db->fetch_array($res);

$dsp->NewContent($lang["beamer"]["beamer"]." / ".$lang['beamer']['sub']['admin'], $lang['beamer']['descr']['admin']);
$dsp->SetForm("index.php?mod=beamer&action=beamer&mode=save_edit&beamer=".$_GET['beamer']);
$dsp->AddTextFieldRow("b_bezeichnung", "Bezeichnung:", $beamer['bezeichnung'],NULL,NULL,0);
$dsp->AddTextFieldRow("b_aufloesung_x", "Auflösung X:", $beamer['aufloesung_x'],NULL,NULL,0);
$dsp->AddTextFieldRow("b_aufloesung_y", "Auflösung X:", $beamer['aufloesung_y'],NULL,NULL,0);
$dsp->AddTextFieldRow("b_nodata_pfad", "Pfad zur Standarddatei", $beamer['nodata_pfad'],NULL,NULL,1);
$dsp->AddCheckBoxRow("b_show_msg", "Nachrichtszeile", "Anzeigen", NULL, 1,$beamer['show_msg']);
$dsp->AddCheckBoxRow("b_usertxt", "User TXT", "Benutzer dürfen Nachrichten auf diesen Beamer schreiben", NULL, 1,$beamer['usertxt']);
$dsp->AddFormSubmitRow('save');
// This will finaly output all the $dsp-Rows
$dsp->AddContent();
}


// Alle Beamer auflisten
if($_GET["mode"]=="") {
$dsp->NewContent($lang["beamer"]["beamer"]." / ".$lang['beamer']['sub']['admin'], $lang['beamer']['descr']['admin']);

	$cnt = '<table width="100%" cellspacing="0" cellpadding="3">
          <tr>
            <td witdh="5%"></td>
			 <td width="25%" valign="top"><strong>Bezeichnung</strong></td>
			 <td width="25%" valign="top">Aktuelle Datei</td>
			 <td width="25%" valign="top">Auflösung</td>
			 <td width="25%"></td>
		  </tr>	
        </table>';

	$res = $db->query("SELECT b.bID, b.bezeichnung, b.aufloesung_x, b.aufloesung_y, c.caption, b.current_view FROM {$config["tables"]["beamer_beamer"]} AS b LEFT JOIN {$config["tables"]["beamer_content"]} AS c ON c.cID=b.current_view ORDER BY b.bezeichnung");
	while ($beamer = $db->fetch_array($res)){
	$button = $dsp->FetchButton("?mod=beamer&action=beamer&mode=edit&beamer=".$beamer["bID"], "edit", '');

if($beamer["current_view"]=="0") {
$beamer["caption"] = "Keine Anzeige aktiv";
}
if($beamer["current_view"]=="-1") {
$beamer["caption"] = "Keine Übertragung";
}

	$cnt .= '<tr>
      <td class="tbl_6" height="30"  colspan="2">
        <table width="100%" cellspacing="0" cellpadding="3" class="tbl_6">
          <tr><td witdh="5%"></td>
			 <td width="25%" valign="top"><strong>'.$beamer["bezeichnung"].'</strong></td>
			 <td width="25%" valign="top">'.$beamer["caption"].'</td>
			 <td width="25%" valign="top">'.$beamer["aufloesung_x"].' x '.$beamer["aufloesung_y"].'</td>
			 <td width="25%">'.$button.'</td>
		  </tr></table></td></tr>';
	}

	$dsp->AddSingleRow($cnt);
	$beamer = $db->num_rows($res);
	$db->free_result($res);
	// This will finaly output all the $dsp-Rows
	$dsp->AddContent();
}

} else $func->error("ACCESS_DENIED", "");
?>