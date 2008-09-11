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

  if ($info['active'] == 1) {
	 $dsp->AddSingleRow($func->AllowHTML($info["text$val"]));
	} else $func->error(t('Diese Info-Seite ist nicht aktiviert. Ein Admin muss sie zuerst im Info-Modul aktivieren'), "");
	
	// Show edit/aktivate Buttons
	// FIX : add delete
	if ($auth["type"] > 1) {
		$dsp->AddSingleRow(" <font color=\"#ff0000\">".t('Diese Seite enthält selbst definierten Text. Sie können ihn ändern, indem Sie den Informationen-Link in der Navigations-Box auswählen.')."</font>");
		$buttons .= $dsp->FetchButton("index.php?mod=info2&action=change&step=2&id={$_GET["id"]}", "edit"). " ";
		if ($info['active'] == 1) {
    		$buttons .= $dsp->FetchButton("index.php?mod=info2&action=change&step=20&id={$_GET["id"]}", "deactivate"). " ";
		} else {
    		$buttons .= $dsp->FetchButton("index.php?mod=info2&action=change&step=20&id={$_GET["id"]}", "activate"). " ";	
		}
		$dsp->AddSingleRow($buttons);
    }

	$dsp->AddContent();
} else $func->error(t('Sie haben keine Seite ausgewählt'));
?>
