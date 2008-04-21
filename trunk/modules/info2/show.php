<?php

if (($_GET["submod"] != "")||($_GET["id"]>=1)) {
	
	if ($_GET["submod"]) { 
		// FIX : Remove on next Version, SUBMOD is only for compartiblity
		$info = $db->query_first("SELECT active, text, caption FROM {$config['tables']['info']} WHERE caption = '{$_GET["submod"]}'");
	} else {
		$info = $db->query_first("SELECT active, text, caption FROM {$config['tables']['info']} WHERE infoID = '{$_GET["id"]}'");
	}

	$dsp->NewContent(t('Seite').": {$info["caption"]}", $info["shorttext"]);

	$dsp->AddSingleRow($func->AllowHTML($info["text"]));
	
	// Show edit/aktivate Buttons
	// FIX : add delete
	if ($auth["type"] > 1) {
		$dsp->AddSingleRow(" <font color=\"#ff0000\">".t('Diese Seite enthält selbst definierten Text. Sie können ihn ändern, indem Sie den Informationen-Link in der Navigations-Box auswählen.')."</font>");
		$buttons .= $dsp->FetchButton("index.php?mod=info2&action=change&step=2&id={$_GET["id"]}", "edit"). " ";
		if ($info['active'] = 1) {
    		$buttons .= $dsp->FetchButton("index.php?mod=info2&action=change&step=20&id={$_GET["id"]}", "deactivate"). " ";
		} else {
    		$buttons .= $dsp->FetchButton("index.php?mod=info2&action=change&step=20&id={$_GET["id"]}", "activate"). " ";	
		}
		$dsp->AddSingleRow($buttons);
    }

	$dsp->AddContent();
} else $func->error(t('Sie haben keine Seite ausgewählt.'), "");
?>
