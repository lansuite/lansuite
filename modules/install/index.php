<?php

$dsp->NewContent(t('Installation und Administration'), t('Auf diesen Seiten können Sie Lansuite installieren und verwalten'));

if (!func::admin_exists()) $dsp->AddSingleRow("<font color=\"red\">".t('<b>ACHTUNG</b>: Es existiert noch kein Admin-Account. Daher hat JEDER Benutzer Admin-Rechte. Legen Sie unbedingt im Benutzermanager einen Superadmin an.')."</font>");
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
		$dsp->AddSingleRow("<font color=\"red\">".t('Die folgenden Module haben noch keinen Admin und sind daher für jeden Admin änderbar:')."</font>" . HTML_NEWLINE . "$mod_list" . HTML_NEWLINE . "(".t('Aktuell sind noch nicht alle Module so programmiert, dass sie eigene Admins haben können.').")");
	}
}

$dsp->AddFieldSetStart(t('Lansuite konfigurieren'));
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=ls_conf\">".t('Grundeinstellungen (Datenbank-Zugangsdaten)')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=mod_cfg&step=10&module=install\">".t('Allgemeine Einstellungen')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=modules\">".t('Modulmanager')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=menu\">".t('Navigationsmenü verwalten')."</a>");
#$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=adminaccount\">".t('Administrator Account anlegen')."</a>");
$dsp->AddFieldSetEnd();

$dsp->AddFieldSetStart(t('Lansuite updaten / reparieren'));
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=envcheck\">".t('Systemvoraussetzungen testen')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=db\">".t('Datenbank updaten und verwalten')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=dbmenu\">".t('Menueinträge neu schreiben')."</a>");
$dsp->AddFieldSetEnd();

$dsp->AddFieldSetStart(t('Daten-Management'));
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=import\">".t('Daten-Import')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=export\">".t('Daten-Export')."</a>");
$dsp->AddFieldSetEnd();
$dsp->AddBackButton("index.php", "");


$dsp->AddContent();
?>