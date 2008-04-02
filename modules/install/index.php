<?php

$dsp->NewContent($lang["install"]["index_caption"], $lang["install"]["index_subcaption"]);

if (!func::admin_exists()) $dsp->AddSingleRow("<font color=\"red\">{$lang["install"]["index_no_admin_warnig"]}</font>");
else {
	$module_list = $db->query("SELECT module.caption FROM {$config["tables"]["modules"]} AS module
			LEFT JOIN {$config["tables"]["menu"]} AS menu ON menu.module = module.name
			LEFT JOIN {$config["tables"]["user_permissions"]} AS perm ON (module.name = perm.module)
			WHERE menu.file != '' and ISNULL(perm.module)
			GROUP BY menu.module
			");
	if ($db->num_rows() > 0){
		while($row = $db->fetch_array($module_list)) {
			$mod_list .= "{$row["caption"]}, ";
		}
		$mod_list = substr($mod_list, 0, strlen($mod_list) - 2);
		$dsp->AddSingleRow("<font color=\"red\">{$lang["install"]["index_no_mod_admin_warnig"]}</font>" . HTML_NEWLINE . "$mod_list" . HTML_NEWLINE . "({$lang["install"]["index_no_mod_admin_hint"]})");
	}
}

$dsp->AddFieldSetStart($lang["install"]["index_config_ls"]);
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=ls_conf\">{$lang["install"]["index_ls_conf"]}</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=mod_cfg&step=10&module=install\">{$lang["install"]["index_settings"]}</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=modules\">{$lang["install"]["index_module"]}</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=menu\">{$lang["install"]["index_navigation"]}</a>");
#$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=adminaccount\">{$lang["install"]["index_adminaccount"]}</a>");
$dsp->AddFieldSetEnd();

$dsp->AddFieldSetStart($lang["install"]["index_update_repair_ls"]);
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=envcheck\">{$lang["install"]["index_envcheck"]}</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=db\">{$lang["install"]["index_db"]}</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=dbmenu\">{$lang["install"]["index_dbmenu"]}</a>");
$dsp->AddFieldSetEnd();

$dsp->AddFieldSetStart($lang["install"]["index_data_management"]);
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=import\">{$lang["install"]["index_import"]}</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=export\">{$lang["install"]["index_export"]}</a>");
$dsp->AddFieldSetEnd();
$dsp->AddBackButton("index.php", "");


$dsp->AddContent();
?>