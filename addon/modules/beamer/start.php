<?php
if($_GET["stop_beamer"]!="") {

$db->query("UPDATE {$config["tables"]["beamer_beamer"]} SET current_view='-1' WHERE bID='{$_GET["stop_beamer"]}'");
}
$dsp->NewContent($lang["beamer"]["beamer"]." / ".$lang['beamer']['sub']['start'], $lang['beamer']['descr']['start']);


// Alle Beamer auflisten
if($_GET["mode"]=="") {
	

	$cnt = '<table width="100%" cellspacing="0" cellpadding="3">
          <tr>
            <td witdh="5%"></td>
			 <td width="35%" valign="top"><strong>Bezeichnung</strong></td>
			 <td width="35%" valign="top">Aktuelle Datei</td>
			 <td width="30%"></td>
		  </tr>	
        </table>';

	$res = $db->query("SELECT b.bID, b.bezeichnung, b.aufloesung_x, b.aufloesung_y, c.caption, b.current_view FROM {$config["tables"]["beamer_beamer"]} AS b LEFT JOIN {$config["tables"]["beamer_content"]} AS c ON c.cID=b.current_view ORDER BY b.bezeichnung");
	while ($beamer = $db->fetch_array($res)){
	$button = $dsp->FetchButton("index.php?mod=beamer&action=show&design=base&set_beamer=".$beamer["bID"], "open", 'Beamerfenster starten');
	$button2 = $dsp->FetchButton("?mod=beamer&action=start&stop_beamer=".$beamer["bID"], "close", 'Beamer schlieﬂen');


if($beamer["current_view"]=="0") {
$beamer["caption"] = "Leerlauf";
}

if($beamer["current_view"]=="-1") {
$beamer["caption"] = "Beamer bereit...";
} else {
$beamer["caption"] = "Beamer l‰uft bereits (".$beamer["caption"].")";
}

	$cnt .= '<tr>
      <td class="tbl_6" height="30"  colspan="2">
        <table width="100%" cellspacing="0" cellpadding="3" class="tbl_6">
          <tr><td witdh="5%"></td>
			 <td width="35%" valign="top"><strong>'.$beamer["bezeichnung"].'</strong></td>
			 <td width="35%" valign="top">'.$beamer["caption"].'</td
			 <td width="30%">'.$button.' '.$button2.'</td>
		  </tr></table></td></tr>';
	}

	$dsp->AddSingleRow($cnt);
	$beamer = $db->num_rows($res);
	$db->free_result($res);
}

	// This will finaly output all the $dsp-Rows
	$dsp->AddContent();
?>