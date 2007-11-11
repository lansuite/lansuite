<?php

if (($_GET["submod"] != "")||($_GET["id"]>=1)) {
	
	if ($_GET["submod"]) { 
		$info = $db->query_first("SELECT text, caption FROM {$config['tables']['info']} WHERE infoID = '{$_GET["submod"]}'");
	} else {
		$info = $db->query_first("SELECT text, caption FROM {$config['tables']['info']} WHERE infoID = '{$_GET["id"]}'");
	}

	($auth["type"] > 1)? $admin_info = " <font color=\"#ff0000\">({$lang["info"]["admin_info"]})</font>" : $admin_info = "";
	$dsp->NewContent("{$lang["info"]["page"]}: {$info["caption"]}$admin_info", $info["shorttext"]);

	$dsp->AddSingleRow($info["text"]);

	$dsp->AddContent();
} else $func->error($lang["info"]["err_nopage"], "");
?>
