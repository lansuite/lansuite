<?php

$dsp->NewContent(t('Installation und Administration'), t('Auf diesen Seiten kannst du Lansuite installieren und verwalten'));

// Scan for DB-Structure SQL-Errors
$row = $db->qry_first('SELECT 1 AS found FROM %prefix%log WHERE type = 3 AND description LIKE \'%Unknown column%\'');
if ($row['found']) {
    $func->information(t('Es wurden SQL-Fehler im Log gefunden, die auf eine nicht aktuelle Struktur der Lansuite-Datenbank hindeuten. Es wird empfohlen die Datenbank zu aktuallisieren.'). '<br><br><a href="index.php?mod=install&action=db">'. t('Datenbank jetzt aktuallisieren') .'</a>', NO_LINK);
}

$dsp->AddFieldSetStart(t('Lansuite konfigurieren'));
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=ls_conf\"><img src=\"design/images/icon_generate.png\" border=\"0\" /> ".t('Grundeinstellungen (Datenbank-Zugangsdaten)')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=modules\"><img src=\"design/images/icon_details.png\" border=\"0\" /> ".t('Modulmanager (Module de-/aktivieren)')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=mod_cfg&step=10&module=install\"><img src=\"design/images/icon_config.png\" border=\"0\" /> ".t('Allgemeine Einstellungen')."</a> [<a href=\"index.php?mod=install&action=search_cfg\"><img src=\"design/images/icon_search.png\" border=\"0\" />".t('durchsuchen')."</a>]");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=menu\"><img src=\"design/images/icon_tree.png\" border=\"0\" /> ".t('Navigationsmenü verwalten')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=translation\"><img src=\"design/images/icon_translate.png\" border=\"0\" /> ".t('Texte übersetzen')."</a>");
#$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=adminaccount\"><img src=\"design/images/icon_add_user.png\" border=\"0\" /> ".t('Administrator Account anlegen')."</a>");
$dsp->AddFieldSetEnd();

$dsp->AddFieldSetStart(t('System Zustand'));
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=envcheck\"><img src=\"design/images/icon_help.png\" border=\"0\" /> ".t('Systemvoraussetzungen testen')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=log\"><img src=\"design/images/icon_save.png\" border=\"0\" /> ".t('Log File ansehen')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=sessions\"><img src=\"design/images/icon_generate.png\" border=\"0\" /> ".t('Aktuelle Sessions auflisten')."</a>");
$dsp->AddFieldSetEnd();

$dsp->AddFieldSetStart(t('Lansuite updaten / reparieren'));
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=db\"><img src=\"design/images/icon_database.png\" border=\"0\" /> ".t('Datenbank updaten und verwalten (sollte nach jedem Lansuite-Update ausgeführt werden)')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=dbmenu\"><img src=\"design/images/icon_change.png\" border=\"0\" /> ".t('Menüeinträge neu schreiben')."</a>");
$dsp->AddFieldSetEnd();

$dsp->AddFieldSetStart(t('Daten-Management'));
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=import\"><img src=\"design/images/icon_in.png\" border=\"0\" /> ".t('Daten-Import')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=export\"><img src=\"design/images/icon_forward.png\" border=\"0\" /> ".t('Daten-Export')."</a>");
$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=mc_search\"><img src=\"design/images/icon_search.png\" border=\"0\" /> ".t('Kommentare aller Module durchsuchen')."</a>");
$dsp->AddFieldSetEnd();

if (!$func->admin_exists()) {
    $func->information(t('<b>ACHTUNG</b>: Es existiert noch kein Admin-Account. Daher hat JEDER Benutzer Admin-Rechte. Lege unbedingt im Benutzermanager einen Superadmin an.'));
} else {
    $module_list = $db->qry("SELECT module.caption FROM %prefix%modules AS module
            LEFT JOIN %prefix%menu AS menu ON menu.module = module.name
            LEFT JOIN %prefix%user_permissions AS perm ON (module.name = perm.module)
            WHERE menu.file != '' and ISNULL(perm.module)
            GROUP BY menu.module
            ");
    if ($db->num_rows() > 0) {
        while ($row = $db->fetch_array($module_list)) {
            $mod_list .= "{$row["caption"]}, ";
        }
        $mod_list = substr($mod_list, 0, strlen($mod_list) - 2);
        $func->information(t('Die folgenden Module haben noch keinen Admin und sind daher für jeden Admin änderbar:<br>%1', $mod_list), NO_LINK);
    }
}

$dsp->AddContent();
