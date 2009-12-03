<?php

if ($language == 'de') $val = '';
else $val = '_'. $language;

if (($_GET["submod"] != "")||($_GET["id"]>=1)) {
	
	if ($_GET["submod"]) { 
		// FIX : Remove on next Version, SUBMOD is only for compartiblity
		$info = $db->qry_first("SELECT active, text%plain%, shorttext%plain%, caption%plain% FROM %prefix%info WHERE caption = %string%", $val, $val, $val, $_GET["submod"]);
	} else {
		$info = $db->qry_first("SELECT active, text%plain%, shorttext%plain%, caption%plain% FROM %prefix%info WHERE infoID = %int%", $val, $val, $val, $_GET["id"]);
	}

	$dsp->NewContent(t('Seite').": {$info["caption$val"]}", $info["shorttext$val"]);
	$framework->AddToPageTitle($info["caption$val"]);

  if ($info['active'] == 1) {
  	if($info["text$val"] == null)
  		$func->information(t("Es liegen Informationen zu der ausgewählten Seite vor, jedoch nicht in Ihrer aktuell gewählten Sprache: <b>%1</b>",$language));
	else
	 	$dsp->AddSingleRow($func->AllowHTML($info["text$val"]), '', 'textContent');
	} else $func->error(t('Diese Info-Seite ist nicht aktiviert. Ein Admin muss sie zuerst im Info-Modul aktivieren'), "");
	
	// Show edit/aktivate Buttons
	// FIX : add delete
	if ($auth["type"] > 1) {
		//$dsp->AddSingleRow(" <font color=\"#ff0000\">".t('Diese Seite enthält selbst definierten Text. Sie können ihn ändern, indem Sie den Informationen-Link in der Navigations-Box auswählen.')."</font>");
		$buttons .= $dsp->FetchButton("index.php?mod=info2&action=change&step=2&infoID={$_GET["id"]}", "edit"). " ";
		if ($info['active'] == 1) {
    		$buttons .= $dsp->FetchButton("index.php?mod=info2&action=change&step=20&infoID={$_GET["id"]}", "deactivate"). " ";
		} else {
    		$buttons .= $dsp->FetchButton("index.php?mod=info2&action=change&step=21&infoID={$_GET["id"]}", "activate"). " ";	
		}
		$dsp->AddSingleRow($buttons);
    }

	$dsp->AddContent();
} else $func->error(t('Sie haben keine Seite ausgewählt'));
?>
