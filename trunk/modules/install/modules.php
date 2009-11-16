<?php

include_once('modules/install/class_install.php');
$install = new Install();

function FindCfgKeyForMod($name) {
  global $db, $config;

    $find_config = $db->qry_first("SELECT cfg_key FROM %prefix%config WHERE (cfg_module = %string%)", $name);
    if ($find_config["cfg_key"] != '') return true; else return false;
} 

function WriteMenuEntries() {
  global $smarty, $res, $db, $config, $dsp, $MenuCallbacks;

  if ($db->num_rows($res) == 0) $dsp->AddDoubleRow("", "<i>- keine -</i>");
  else while ($row = $db->fetch_array($res)) {
  
    $smarty->assign('action', $row["action"]);
    $smarty->assign('file', $row["file"]);
    $smarty->assign('id', $row["id"]);
    $smarty->assign('caption', $row["caption"]);
    $smarty->assign('hint', $row["hint"]);
    $smarty->assign('link', $row["link"]);
    $smarty->assign('pos', $row["pos"]);
    $smarty->assign('module', $_GET['module']);
    
    $boxid = '';
    if ($row['level'] == 0) $boxid = 'Boxid: <input type="text" name="boxid['.$row['id'].']" value="'. $row['boxid'] .'" size="2" />';
    $smarty->assign('boxid', $boxid);

    $needed_config = "<option value=\"\">-".t('keine')."-</option>";
    $res2 = $db->qry("SELECT cfg_key FROM %prefix%config WHERE cfg_type = 'boolean' OR cfg_type = 'int' ORDER BY cfg_key");
    if ($MenuCallbacks) foreach ($MenuCallbacks as $MenuCallback) {
        ($MenuCallback == $row["needed_config"])? $selected = " selected" : $selected = "";
        $needed_config .= "<option value=\"{$MenuCallback}\"$selected>{$MenuCallback}</option>";
    }
    $db->free_result($res2);
    $smarty->assign('needed_config', $needed_config);

    $requirement = "";
    for ($i = 0; $i <= 5; $i++) {
      ($i == $row["requirement"])? $selected = " selected" : $selected = "";
      switch ($i) {
          default: $out = t('Jeder'); break;
          case 1: $out = t('Nur Eingeloggte'); break;
          case 2: $out = t('Nur Admins'); break;
          case 3: $out = t('Nur Superadminen'); break;
          case 4: $out = t('Keine Admins'); break;
          case 5: $out = t('Nur Ausgeloggte'); break;
      }
      $requirement .= "<option value=\"$i\"$selected>$out</option>";
    }
    $smarty->assign('requirement', $requirement);

    $dsp->AddSmartyTpl('menuitem', 'install');
    $dsp->AddHRuleRow();
  }
  $db->free_result($res);
}



switch($_GET["step"]) {
    // Update Modules
    case 2:
        $res = $db->qry("SELECT name FROM %prefix%modules WHERE changeable");
        while ($row = $db->fetch_array($res)){
            if ($_POST[$row["name"]]) $db->qry_first("UPDATE %prefix%modules SET active = 1 WHERE name = %string%", $row["name"]);
            elseif (count($_POST)) $db->qry_first("UPDATE %prefix%modules SET active = 0 WHERE name = %string%", $row["name"]);
        }
        $db->free_result($res);

        $db->qry_first("UPDATE %prefix%modules SET active = 1 WHERE name = 'settings'");
        $db->qry_first("UPDATE %prefix%modules SET active = 1 WHERE name = 'banner'");
        $db->qry_first("UPDATE %prefix%modules SET active = 1 WHERE name = 'about'");
        
        $install->CreateNewTables(0);
        $func->confirmation(t('Änderungen erfolgreich gespeichert.'), "index.php?mod=install&action=modules");
    break;

    // Question: Reset all Modules
    case 3:
        $func->question(t('Sollen wirklich <b>\'alle Module\'</b> zurückgesetzt werden?HTML_NEWLINEDies wirkt sich <u>nicht</u> auf die Datenbankeinträge der Module aus, jedoch gehen alle Einstellungen und Menüänderungen verloren, die zu den Modulen getätigt worden sind. Außerdem sind danach nur noch die Standardmodule aktiviert.'), "index.php?mod=install&action=modules&rewrite=all", "index.php?mod=install&action=modules");
    break;

    // Question: Reset this Module
    case 4:
        $func->question(t('Soll das Modul <b>\'%1\'</b> wirklich zurückgesetzt werden?<br />Dies wirkt sich <u>nicht</u> auf die Datenbankeinträge des Modules aus, jedoch gehen alle Einstellungen und Menüänderungen verloren, die zu diesem Modul getätigt worden sind.', $_GET["module"]), "index.php?mod=install&action=modules&rewrite={$_GET["module"]}", "index.php?mod=install&action=modules");
    break;

    // Add Menuentry
    case 22:
        $db->qry("INSERT INTO %prefix%menu SET caption = 'Neuer Eintrag', requirement = '0', hint = '', link = 'index.php?mod=', needed_config = '', module=%string%, level = 1", $_GET["module"]);

    // Menuentries
    case 20:
        $db->qry("DELETE FROM %prefix%menu WHERE caption='' AND action='' AND file=''");

        $dsp->NewContent(t('Modul-Menüeinträge'), t('Hier können Sie die Navigationseinträge dieses Moduls ändern.'));
        $dsp->SetForm("index.php?mod=install&action=modules&step=21&module={$_GET["module"]}");

        $dsp->AddFieldsetStart(t('Hauptmenüpunkt des Moduls / Modul-Startseite'));
        $res = $db->qry("SELECT * FROM %prefix%menu WHERE module=%string% AND level = 0 AND caption != '' ORDER BY requirement, pos", $_GET["module"]);
        WriteMenuEntries();
        $dsp->AddFieldsetEnd();

        $dsp->AddFieldsetStart(t('Untermenüpunkte'));
        $res = $db->qry("SELECT * FROM %prefix%menu WHERE module=%string% AND level > 0 AND caption != '' ORDER BY requirement, pos", $_GET["module"]);
        WriteMenuEntries();
        $dsp->AddFieldsetEnd();

        $dsp->AddFieldsetStart(t('Keine Menüpunkte - Interne Verweise'));
        $res = $db->qry("SELECT * FROM %prefix%menu WHERE module=%string% AND caption = '' ORDER BY requirement, pos", $_GET["module"]);
        WriteMenuEntries();
        $dsp->AddFieldsetEnd();

        $dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=modules&module={$_GET["module"]}&step=22\">".t('Neuen Menüeintrag hinzufügen')."</a>");

        $dsp->AddFormSubmitRow("next");
        $dsp->AddContent();
    break;

    // Change Menuentries
    case 21:
        foreach ($_POST["caption"] as $key => $val) {
            $db->qry("UPDATE %prefix%menu SET caption = %string%, requirement = %string%, action = %string%, hint = %string%, link = %string%, file = %string%, pos = %string%, boxid = %int%, needed_config = %string% WHERE id = %int%",
                        $_POST["caption"][$key], $_POST["requirement"][$key], $_POST["action"][$key], $_POST["hint"][$key], $_POST["link"][$key], $_POST["file"][$key], $_POST["pos"][$key], $_POST["boxid"][$key], $_POST["needed_config"][$key], $key);
        }

        $func->confirmation(t('Änderungen erfolgreich gespeichert.'), "index.php?mod=install&action=modules&step=20&module={$_GET["module"]}");
    break;

    // Delete Menuentry
    case 23:
      $row = $db->qry_first("SELECT requirement FROM %prefix%menu WHERE id=%int%", $_GET["id"]);
      if ($row['requirement'] > 0) $func->information(t('Mit diesem Eintrag ist eine Zugriffsberechtigung verknüpft. Sie sollten diesen Eintrag daher nicht löschen, da sonst jeder Zugriff auf die betreffende Datei hat.HTML_NEWLINEWenn Sie nur den Menülink entfernen möchten, löschen Sie die Felder Titel und Linkziel.HTML_NEWLINEWenn Sie wirklich jedem Zugriff auf die Datei geben möchten, setzen Sie den Zugriff auf Jeder und löschen Sie dann den Eintrag.'), "index.php?mod=install&action=modules&step=20&module={$_GET["module"]}");
      
      else {
        $db->qry("DELETE FROM %prefix%menu WHERE id=%int%", $_GET["id"]);
        $func->confirmation(t('Der Menü-Eintrag wurde erfolgreich gelöscht'), "index.php?mod=install&action=modules&step=20&module={$_GET["module"]}");
    }
    break;


    // Show Modulelist
    default:
      // If Rewrite, delete corresponding items
      $rewrite_all = 0;
      if ($_GET["rewrite"] == "all") {
        $db->qry("TRUNCATE TABLE %prefix%config");
        $db->qry("TRUNCATE TABLE %prefix%modules");
        $db->qry("TRUNCATE TABLE %prefix%menu");
        $rewrite_all = 1;
      } elseif ($_GET["rewrite"]) {
        $db->qry("DELETE FROM %prefix%modules WHERE name = %string%", $_GET["rewrite"]);
        $db->qry("DELETE FROM %prefix%menu WHERE module = %string%", $_GET["rewrite"]);

        $_GET["rewrite"] .= "_";
        if ($_GET["rewrite"] == "downloads_") $_GET["rewrite"] = "Download";
        if ($_GET["rewrite"] == "usrmgr_") $_GET["rewrite"] = "Userdetails";
        if ($_GET["rewrite"] == "tournament2_") $_GET["rewrite"] = "t";
        $find_config = $db->qry_first("DELETE FROM %prefix%config WHERE (cfg_group = %string%) OR (cfg_key LIKE %string%)", $_GET["rewrite"], $_GET["rewrite"].'%');
      }

      // Auto-Load Modules from XML-Files
      $install->InsertModules(0);
      $install->InsertMenus($rewrite_all);

      // Output Module-List
      $dsp->NewContent(t('Modulverwaltung'), t('Hier können Sie Module de-/aktivieren, sowie deren Einstellungen verändern.'));

      $dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=modules&step=3\">".t('Alle Module zurücksetzen')."</a>");

      $dsp->AddHRuleRow();
      $dsp->SetForm("index.php?mod=install&action=modules&step=2");

      $res = $db->qry("SELECT * FROM %prefix%modules ORDER BY changeable DESC, caption");
      while ($row = $db->fetch_array($res)){

        $smarty->assign('name', $row['name']);
        $smarty->assign('caption', $row['caption']);
        $smarty->assign('description', $row['description']);
        $smarty->assign('version', $row["version"]);

        if ($row["email"]) $author = "<a href=\"mailto:{$row["email"]}\">{$row["author"]}</a>";
        else $author = $row["author"];
        $smarty->assign('author', $author);

        $active = '';
        if ($row["active"]) $active = ' checked="checked"';
        $smarty->assign('active', $active);

        $changeable = '';
        if (!$row["changeable"]) $changeable = ' disabled';
        $smarty->assign('changeable', $changeable);
        
        ($row["state"] == "Stable")? $state = $row["state"] : $state = "<font color=\"red\">{$row["state"]}</font>";
        $smarty->assign('state', $state);

        (file_exists("modules/{$row["name"]}/icon.gif"))? $img = "modules/{$row["name"]}/icon.gif" : $img = "modules/sample/icon.gif";
        $smarty->assign('img', $img);

        if (FindCfgKeyForMod($row["name"])) $settings_link = " | <a href=\"index.php?mod=install&action=mod_cfg&step=10&module={$row["name"]}\">". t('Konfig.') ."</a>";
        else $settings_link = "";
        $smarty->assign('settings_link', $settings_link);

        $find_mod = $db->qry_first("SELECT module FROM %prefix%menu WHERE module=%string%", $row["name"]);
        if ($find_mod["module"]) $menu_link = " | <a href=\"index.php?mod=install&action=mod_cfg&step=30&module={$row["name"]}\">". t('Menü') ."</a>";
        else $menu_link = "";
        $smarty->assign('menu_link', $menu_link);

        if (file_exists("modules/{$row["name"]}/mod_settings/db.xml")) $db_link = " | <a href=\"index.php?mod=install&action=mod_cfg&step=40&module={$row["name"]}\">". t('DB') ."</a>";
        else $db_link = "";
        $smarty->assign('db_link', $db_link);

        if (file_exists("modules/{$row["name"]}/docu/{$language}_help.php")) $help_link = " | <a href=\"#\" onclick=\"javascript:var w=window.open('index.php?mod=helplet&action=helplet&design=base&module={$row["name"]}&helpletid=help','_blank','width=700,height=500,resizable=no,scrollbars=yes');\" class=\"Help\">?</a>";
        else $help_link = '';
        $smarty->assign('help_link', $help_link);
  
        $dsp->AddContentLine($smarty->fetch('modules/install/templates/module.htm'));
      }
      $db->free_result($res);

      $dsp->AddFormSubmitRow("next");
      $dsp->AddBackButton("index.php?mod=install", "install/modules");
      $dsp->AddContent();
    break;
} // Switch Action
?>