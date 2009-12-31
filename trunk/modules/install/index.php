<?php

$dsp->NewContent(t('Installation und Administration'), t('Auf diesen Seiten können Sie Lansuite installieren und verwalten'));

if (!func::admin_exists()) $func->information(t('<b>ACHTUNG</b>: Es existiert noch kein Admin-Account. Daher hat JEDER Benutzer Admin-Rechte. Legen Sie unbedingt im Benutzermanager einen Superadmin an.'));
else {
    $module_list = $db->qry("SELECT module.caption FROM %prefix%modules AS module
            LEFT JOIN %prefix%menu AS menu ON menu.module = module.name
            LEFT JOIN %prefix%user_permissions AS perm ON (module.name = perm.module)
            WHERE menu.file != '' and ISNULL(perm.module)
            GROUP BY menu.module
            ");
    if ($db->num_rows() > 0){
        while($row = $db->fetch_array($module_list)) {
            $mod_list .= "{$row["caption"]}, ";
        }
        $mod_list = substr($mod_list, 0, strlen($mod_list) - 2);
		$func->information(t('Die folgenden Module haben noch keinen Admin und sind daher für jeden Admin änderbar:[br]%1', $mod_list), NO_LINK);
    }
}

// Scan for DB-Structure SQL-Errors
$row = $db->qry_first('SELECT 1 AS found FROM %prefix%log WHERE type = 3 AND description LIKE \'%Unknown column%\'');
if ($row['found']) {
    $func->information(t('Es wurden SQL-Fehler im Log gefunden, die auf eine nicht aktuelle Struktur der Lansuite-Datenbank hindeuten. Es wird empfohlen die Datenbank zu aktuallisieren.'). '<br><br><a href="index.php?mod=install&action=db">'. t('Datenbank jetzt aktuallisieren') .'</a>', NO_LINK);
}

$dsp->AddFieldSetStart(t('Lansuite konfigurieren'));
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=ls_conf\">".t('Grundeinstellungen (Datenbank-Zugangsdaten)')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=mod_cfg&step=10&module=install\">".t('Allgemeine Einstellungen')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=modules\">".t('Modulmanager')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=menu\">".t('Navigationsmenü verwalten')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=search_cfg\">".t('Konfigurationseinstellungen suchen')."</a>");
#$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=adminaccount\">".t('Administrator Account anlegen')."</a>");
$dsp->AddFieldSetEnd();

$dsp->AddFieldSetStart(t('System Zustand'));
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=envcheck\">".t('Systemvoraussetzungen testen')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=log\">".t('Log File ansehen')."</a>");
$dsp->AddFieldSetEnd();

$dsp->AddFieldSetStart(t('Lansuite updaten / reparieren'));
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=db\">".t('Datenbank updaten und verwalten (sollte nach jedem Lansuite-Update ausgeführt werden)')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=dbmenu\">".t('Menueinträge neu schreiben')."</a>");
$dsp->AddFieldSetEnd();

$dsp->AddFieldSetStart(t('Daten-Management'));
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=import\">".t('Daten-Import')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=export\">".t('Daten-Export')."</a>");
$dsp->AddFieldSetEnd();
$dsp->AddBackButton("index.php", "");


$dsp->AddContent();
?>