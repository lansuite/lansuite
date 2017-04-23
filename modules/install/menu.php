<?php

include_once('modules/install/class_install.php');
$install = new Install();

switch ($_GET["step"]) {
    // Move Up
    case 2:
        $db->qry("UPDATE %prefix%menu SET pos = 0 WHERE pos = %int%", ($_GET["pos"] - 1));
        $db->qry("UPDATE %prefix%menu SET pos = pos - 1 WHERE pos = %int%", $_GET["pos"]);
        $db->qry("UPDATE %prefix%menu SET pos = %int% WHERE pos = 0", $_GET["pos"]);
        break;

    // Move Down
    case 3:
        $db->qry("UPDATE %prefix%menu SET pos = 0 WHERE pos = %int%", ($_GET["pos"] + 1));
        $db->qry("UPDATE %prefix%menu SET pos = pos + 1 WHERE pos = %int%", $_GET["pos"]);
        $db->qry("UPDATE %prefix%menu SET pos = %int% WHERE pos = 0", $_GET["pos"]);
        break;

    // Add HRule Row
    case 4:
        $db->qry("UPDATE %prefix%menu SET pos = pos + 1 WHERE pos > %int%", $_GET["pos"]);
        $db->qry("INSERT INTO %prefix%menu SET caption = '--hr--', pos = %int%", ($_GET["pos"] + 1));
        break;

    // Delete
    case 5:
        $db->qry("DELETE FROM %prefix%menu WHERE pos = %int%", $_GET["pos"]);
        break;

    // Rewrite Menu Question
    case 6:
        $func->question(t('Bist du sicher, dass du alle Navigationseinträge zurücksetzen möchten?'), "index.php?mod=install&action=menu&step=7&onlyactive={$_GET["onlyactive"]}", "index.php?mod=install&action=menu&onlyactive={$_GET["onlyactive"]}");
        break;

    // Rewrite Menu Action
    case 7:
        $db->qry_first("DELETE FROM %prefix%menu");
        $install->InsertMenus(1);
        break;

    // Change Group Action
    case 9:
        $menu = $db->qry("UPDATE %prefix%menu SET group_nr = %int% WHERE pos = %string%", $_POST["group"], $_GET["pos"]);
        break;

    // Set Possition
    case 10:
        if ($_POST['pos']) {
            foreach ($_POST['pos'] as $key => $val) {
                $db->qry('UPDATE %prefix%menu SET pos = %int% WHERE id = %int%', $val, $key);
            }
        }
        if ($_POST['group']) {
            foreach ($_POST['group'] as $key => $val) {
                $db->qry('UPDATE %prefix%menu SET group_nr = %int% WHERE id = %int%', $val, $key);
            }
        }
        if ($_POST['box']) {
            foreach ($_POST['box'] as $key => $val) {
                $db->qry('UPDATE %prefix%menu SET boxid = %int% WHERE id = %int%', $val, $key);
            }
        }
        break;
}


switch ($_GET["step"]) {
    // Change Group Choice
    case 8:
        $dsp->NewContent(t('Gruppe ändern'), t('Hier kannst du diesen Navigationseintrag einer Gruppe zuweisen'));
        $dsp->SetForm("index.php?mod=install&action=menu&step=9&pos={$_GET["pos"]}&onlyactive={$_GET["onlyactive"]}");

        $menu = $db->qry_first("SELECT group_nr FROM %prefix%menu WHERE pos = %string%", $_GET["pos"]);
        $dsp->AddTextFieldRow("group", "Gruppe", (int)$menu["group_nr"], "");

        $dsp->AddFormSubmitRow(t('Weiter'));
        $dsp->AddBackButton("index.php?mod=install&action=menu&onlyactive={$_GET["onlyactive"]}", "install/modules");
        $dsp->AddContent();
        break;


    default:
        $dsp->NewContent(t('Navigationsmenü verwalten'), '');
        $dsp->AddDoubleRow("Hinweis MenüBox", "<font color=\"red\">".t("Verwende die MenüBox-Nr um neue Boxen zu bilden. Alle Einträge mit gleicher ID landen in der gleichen Box")."</font>");
        $dsp->AddDoubleRow("Hinweis Gruppen", "<font color=\"red\">".t("Verwende die Gruppen um in der URL mit dem Parameter &menu_group=xx nur bestimmte Menü-Eintrage auszugeben. Das ist nützlich bei einer eigenen Hauptnavigation im eigenen Design")."</font>");
        $dsp->SetForm("index.php?mod=install&action=menu&step=10&onlyactive={$_GET["onlyactive"]}");

        $dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=menu&step=6&onlyactive={$_GET["onlyactive"]}\">".t('Navigation zurücksetzen')."</a>");
        if ($_GET["onlyactive"]) {
            $dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=menu&onlyactive=0\">".t('Alle Einträge anzeigen')."</a>");
        } else {
            $dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=menu&onlyactive=1\">".t('Nur Einträge von aktivierten Modulen anzeigen')."</a>");
        }

        $validBoxIds = array();
        $res = $db->qry('SELECT boxid,name FROM %prefix%boxes WHERE source = \'menu\'');
        while ($row = $db->fetch_array($res)) {
            $validBoxIds[$row['boxid']] = $row['name'];
        }
        $db->free_result($res);

        $menus = $db->qry("SELECT module.active, menu.* FROM %prefix%menu AS menu
			LEFT JOIN %prefix%modules AS module ON (menu.module = module.name)
			WHERE (menu.level = 0) and (menu.caption != '') ORDER BY menu.boxid, menu.pos");
        $z = 0;
        while ($menu = $db->fetch_array($menus)) {
            $z++;
            $db->qry("UPDATE %prefix%menu SET pos = %int% WHERE id = %int%", $z, $menu["id"]);

            if ($menu["active"] or (!$_GET["onlyactive"])) {
                $link = "";
                if ($menu["caption"] == "--hr--") {
                    $menu["caption"] = "<i>Trennzeile</i>";
                    $link .= "[<a href=\"index.php?mod=install&action=menu&step=5&pos=$z&onlyactive={$_GET["onlyactive"]}\">".t('entfernen')."</a>] ";
                } else {
                    $link .= "[<a href=\"index.php?mod=install&action=modules&step=20&module={$menu["module"]}&onlyactive={$_GET["onlyactive"]}\">".t('editieren')."</a>] ";
                    if ($z < $db->num_rows($menus)) {
                        $link .= "[<a href=\"index.php?mod=install&action=menu&step=4&pos=$z&onlyactive={$_GET["onlyactive"]}\">".t('Trennzeile')."</a>] ";
                    }
                }
#				$link .= "[<a href=\"index.php?mod=install&action=menu&step=8&pos=$z&onlyactive={$_GET["onlyactive"]}\">".t('Gruppe')." ({$menu["group_nr"]})</a>] ";
                if ($z > 1) {
                    $link .= "[<a href=\"index.php?mod=install&action=menu&step=2&pos=$z&onlyactive={$_GET["onlyactive"]}\">^</a>] ";
                }
                if ($z < $db->num_rows($menus)) {
                    $link .= "[<a href=\"index.php?mod=install&action=menu&step=3&pos=$z&onlyactive={$_GET["onlyactive"]}\">v</a>]";
                }
                $link .= " ".t('Pos').": <input type=\"text\" name=\"pos[{$menu["id"]}]\" value=\"$z\" size=\"2\">";
                $link .= " Gruppe: <input type=\"text\" name=\"group[{$menu["id"]}]\" value=\"{$menu['group_nr']}\" size=\"2\">";
                $link .= " MenüBox-Nr: <select name=\"box[{$menu["id"]}]\">";
                foreach ($validBoxIds as $validBoxId => $validBoxName) {
                    ($menu['boxid'] == $validBoxId)? $sel = ' selected' : $sel = '';
                    $link .= "<option value=\"$validBoxId\"$sel>$validBoxName</option>";
                }
                $link .= "</select>";

                $dsp->AddDoubleRow("$z) ". $menu["caption"], $link);
            }
        }
        $db->free_result($menus);

        $dsp->AddFormSubmitRow(t('Weiter'));
        $dsp->AddBackButton("index.php?mod=install", "install/modules");
        $dsp->AddContent();
        break;
}
