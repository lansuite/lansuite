<?php

$importXml = new \LanSuite\XML();
$installImport = new \LanSuite\Module\Install\Import($importXml);
$install = new \LanSuite\Module\Install\Install($installImport);

$stepParameter = $_GET["step"] ?? 0;
switch ($stepParameter) {
    // Update Modules
    case 2:
        $res = $db->qry("SELECT name, reqphp, reqmysql FROM %prefix%modules WHERE changeable");
        while ($row = $db->fetch_array($res)) {
            if (array_key_exists($row["name"], $_POST) && $_POST[$row["name"]]) {
                if ($row['reqphp'] and version_compare(PHP_VERSION, $row['reqphp']) < 0) {
                    $func->information(t('Das Modul %1 kann nicht aktiviert werden, da die PHP Version %2 benötigt wird', $row["name"], $row['reqphp']), NO_LINK);
                } else {
                    $database->query("UPDATE %prefix%modules SET active = 1 WHERE name = ?", [$row["name"]]);
                }
            } elseif (count($_POST)) {
                $database->query("UPDATE %prefix%modules SET active = 0 WHERE name = ?", [$row["name"]]);
            }
        }
        $db->free_result($res);

        $database->query("UPDATE %prefix%modules SET active = 1 WHERE name = 'settings'");
        $database->query("UPDATE %prefix%modules SET active = 1 WHERE name = 'banner'");
        $database->query("UPDATE %prefix%modules SET active = 1 WHERE name = 'about'");
        
        $install->CreateNewTables(0);
        $func->confirmation(t('Änderungen erfolgreich gespeichert.'), "index.php?mod=install&action=modules");
        break;

    // Question: Reset all Modules
    case 3:
        $func->question(t('Sollen wirklich <b>\'alle Module\'</b> zurückgesetzt werden?' . HTML_NEWLINE . ' Dies wirkt sich <u>nicht</u> auf die Datenbankeinträge der Module aus, jedoch gehen alle Einstellungen und Menüänderungen verloren, die zu den Modulen getätigt worden sind. Außerdem sind danach nur noch die Standardmodule aktiviert.'), "index.php?mod=install&action=modules&rewrite=all", "index.php?mod=install&action=modules");
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
        $database->query("DELETE FROM %prefix%menu WHERE caption = '' AND action = '' AND file = ''");

        $dsp->NewContent(t('Modul-Menüeinträge'), t('Hier kannst du die Navigationseinträge dieses Moduls ändern.'));
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

        $dsp->AddFormSubmitRow(t('Weiter'));
        break;

    // Change Menuentries
    case 21:
        foreach ($_POST["caption"] as $key => $val) {
            $boxId = $_POST["boxid"][$key] ?? 0;
            $db->qry(
                "UPDATE %prefix%menu SET caption = %string%, requirement = %string%, action = %string%, hint = %string%, link = %string%, file = %string%, pos = %string%, boxid = %int%, needed_config = %string% WHERE id = %int%",
                $_POST["caption"][$key],
                $_POST["requirement"][$key],
                $_POST["action"][$key],
                $_POST["hint"][$key],
                $_POST["link"][$key],
                $_POST["file"][$key],
                $_POST["pos"][$key],
                $boxId,
                $_POST["needed_config"][$key],
                $key
            );
        }

        $func->confirmation(t('Änderungen erfolgreich gespeichert.'), "index.php?mod=install&action=modules&step=20&module={$_GET["module"]}");
        break;

    // Delete Menuentry
    case 23:
        $row = $database->queryWithOnlyFirstRow("SELECT requirement FROM %prefix%menu WHERE id = ?", [$_GET["id"]]);
        if ($row['requirement'] > 0) {
            $func->information(t('Mit diesem Eintrag ist eine Zugriffsberechtigung verknüpft. Du solltest diesen Eintrag daher nicht löschen, da sonst jeder Zugriff auf die betreffende Datei hat.' . HTML_NEWLINE . 'Wenn du nur den Menülink entfernen möchten, lösche die Felder Titel und Linkziel.' . HTML_NEWLINE . 'Wenn du wirklich jedem Zugriff auf die Datei geben möchten, setze den Zugriff auf Jeder und lösche dann den Eintrag.'), "index.php?mod=install&action=modules&step=20&module={$_GET["module"]}");
        } else {
            $database->query("DELETE FROM %prefix%menu WHERE id = ?", [$_GET["id"]]);
            $func->confirmation(t('Der Menü-Eintrag wurde erfolgreich gelöscht'), "index.php?mod=install&action=modules&step=20&module={$_GET["module"]}");
        }
        break;


    // Show Modulelist
    default:
      // If Rewrite, delete corresponding items
        $rewrite_all = 0;
        $rewriteParameter = $_GET["rewrite"] ?? '';
        if ($rewriteParameter == "all") {
            $database->query("TRUNCATE TABLE %prefix%config");
            $database->query("TRUNCATE TABLE %prefix%modules");
            $database->query("TRUNCATE TABLE %prefix%menu");
            $rewrite_all = 1;
        } elseif ($rewriteParameter) {
            $database->query("DELETE FROM %prefix%modules WHERE name = ?", [$rewriteParameter]);
            $database->query("DELETE FROM %prefix%menu WHERE module = ?", [$rewriteParameter]);
            $database->query("DELETE FROM %prefix%boxes WHERE module = ?", [$rewriteParameter]);

            $rewriteParameter .= "_";
            if ($rewriteParameter == "downloads_") {
                $rewriteParameter= "Download";
            }
            if ($rewriteParameter == "usrmgr_") {
                $rewriteParameter = "Userdetails";
            }
            if ($rewriteParameter == "tournament2_") {
                $rewriteParameter = "t";
            }
            $find_config = $database->query("DELETE FROM %prefix%config WHERE cfg_group = ? OR cfg_key LIKE ?", [$rewriteParameter, $rewriteParameter . '%']);
        }

        // Auto-Load Modules from XML-Files
        $install->InsertModules(0);
        $install->InsertMenus($rewrite_all);

        // Output Module-List
        $dsp->NewContent(t('Modulverwaltung'), t('Hier kannst du Module de-/aktivieren, sowie deren Einstellungen verändern.'));

        $dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=modules&step=3\">".t('Alle Module zurücksetzen')."</a>");

        $dsp->AddHRuleRow();
        $dsp->SetForm("index.php?mod=install&action=modules&step=2");

        $res = $db->qry("SELECT * FROM %prefix%modules ORDER BY changeable DESC, caption");
        while ($row = $db->fetch_array($res)) {
            $dsp->AddContentLine($install->getModConfigLine($row));
        }
        $db->free_result($res);

        $dsp->AddFormSubmitRow(t('Weiter'));
        $dsp->AddBackButton("index.php?mod=install", "install/modules");
        break;
}
