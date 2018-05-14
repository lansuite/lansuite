<?php

if ($auth['type'] <= 1) {
    $ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2();

    $ms2->query['from'] = "%prefix%info AS i";
    $ms2->query['where'] = "i.active";

    $ms2->config['EntriesPerPage'] = 50;

    $ms2->AddResultField(t('Seitentitel'), 'i.caption');
    $ms2->AddResultField(t('Untertitel'), 'i.shorttext', '', 140);

    $ms2->AddIconField('details', 'index.php?mod=info2&action=show_info2&id=', t('Details'));
    $ms2->PrintSearch('index.php?mod=info2', 'i.infoID');
} else {
    if ($_POST['content'] == '') {
        $_POST['content'] = $_POST['FCKeditor1'];
    }

    switch ($_GET["step"]) {
        default:
            $dsp->NewContent(t('Informationsseite - Bearbeiten'), t('Hier kannst du den Inhalt der Info-Seiten editieren.'));
            $dsp->AddSingleRow($dsp->FetchSpanButton('Neuen Infotext hinzufügen', 'index.php?mod=info2&action=change&step=2'));

            $ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2();

            $ms2->query['from'] = "%prefix%info AS i";
            $ms2->query['where'] = "i.link = ''";

            $ms2->config['EntriesPerPage'] = 50;

            $ms2->AddResultField(t('Seitentitel'), 'i.caption');
            $ms2->AddResultField(t('Untertitel'), 'i.shorttext', '', 140);
            $ms2->AddResultField(t('Aktiv'), 'i.active', 'ShowActiveState');

            $ms2->AddIconField('details', 'index.php?mod=info2&action=show_info2&id=', t('Details'));
            if ($auth['type'] >= 2) {
                $ms2->AddIconField('edit', 'index.php?mod=info2&action=change&step=2&infoID=', t('Editieren'));
            }
            if ($auth['type'] >= 2) {
                  $ms2->AddMultiSelectAction('Deaktivieren', 'index.php?mod=info2&action=change&step=20', 1);
            }
            if ($auth['type'] >= 2) {
                  $ms2->AddMultiSelectAction('Aktivieren (jedoch nicht verlinken)', 'index.php?mod=info2&action=change&step=21', 1);
            }
            if ($auth['type'] >= 2) {
                  $ms2->AddMultiSelectAction('Aktivieren und verlinken', 'index.php?mod=info2&action=change&step=22', 1);
            }
            if ($auth['type'] >= 2) {
                  $ms2->AddMultiSelectAction('Aktivieren und verlinken nur für Admins', 'index.php?mod=info2&action=change&step=23', 1);
            }
            if ($auth['type'] >= 3) {
                  $ms2->AddMultiSelectAction('Löschen', 'index.php?mod=info2&action=change&step=10', 1);
            }

            $ms2->PrintSearch('index.php?mod=info2', 'i.infoID');

            $dsp->AddSingleRow($dsp->FetchSpanButton('Externen Link erstellen', 'index.php?mod=info2&action=change&step=30'));

            $ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2();
            $ms2->query['from'] = "%prefix%info AS i";
            $ms2->query['where'] = "i.link != ''";

            $ms2->config['EntriesPerPage'] = 50;

            $ms2->AddResultField(t('Seitentitel'), 'i.caption');
            $ms2->AddResultField(t('Untertitel'), 'i.shorttext', '', 140);
            $ms2->AddResultField(t('Link'), 'i.link', '', 140);
            $ms2->AddResultField(t('Aktiv'), 'i.active', 'ShowActiveState');

            if ($auth['type'] >= 2) {
                $ms2->AddIconField('edit', 'index.php?mod=info2&action=change&step=30&infoID=', t('Editieren'));
            }
            if ($auth['type'] >= 2) {
                $ms2->AddMultiSelectAction('Deaktivieren', 'index.php?mod=info2&action=change&step=20', 1);
            }
            if ($auth['type'] >= 2) {
                $ms2->AddMultiSelectAction('Aktivieren (jedoch nicht verlinken)', 'index.php?mod=info2&action=change&step=21', 1);
            }
            if ($auth['type'] >= 2) {
                $ms2->AddMultiSelectAction('Aktivieren und verlinken', 'index.php?mod=info2&action=change&step=22', 1);
            }
            if ($auth['type'] >= 2) {
                $ms2->AddMultiSelectAction('Aktivieren und verlinken nur für Admins', 'index.php?mod=info2&action=change&step=23', 1);
            }
            if ($auth['type'] >= 3) {
                $ms2->AddMultiSelectAction('Löschen', 'index.php?mod=info2&action=change&step=10', 1);
            }

            $ms2->PrintSearch('index.php?mod=info2', 'i.infoID');
            break;
    
        // Generate Editform
        case 2:
            if ($_GET['infoID'] != '') {
                $row = $db->qry_first("
                  SELECT
                    m.id
                  FROM
                    %prefix%info AS i
                    LEFT JOIN %prefix%menu AS m ON i.caption = m.caption AND m.action = 'show_info2'
                  WHERE
                    i.infoID = %int%", $_GET["infoID"]);
            }

            $dsp->NewContent(t('Informationsseite - Bearbeiten'), t('Hier kannst du den Inhalt der Seite editieren.'));

            $mf = new \LanSuite\MasterForm();

            foreach ($translation->valid_lang as $val) {
                $_POST[$language] = 1;

                if ($val == 'de') {
                    $valkey = '';
                    $optional = 0;
                } else {
                      $valkey = '_'. $val;
                      $optional = \LanSuite\MasterForm::FIELD_OPTIONAL;
                }
                $mf->AddField(t('Seitentitel'), 'caption'. $valkey, '', '', $optional);
                $mf->AddField(t('Untertitel'), 'shorttext'. $valkey, '', '', $optional);
                if ($cfg['info2_use_fckedit']) {
                    $mf->AddField(t('Text'), 'text'. $valkey, '', \LanSuite\MasterForm::HTML_WYSIWYG, $optional);
                } else {
                    $mf->AddField(t('Text'), 'text'. $valkey, '', '', $optional);
                }
                $mf->AddPage($translation->lang_names[$val]);
            }
            $mf->AdditionalDBUpdateFunction = 'UpdateInfo2';
            $mf->SendForm('index.php?mod=info2&action=change&step=2', 'info', 'infoID', $_GET['infoID']);

            $dsp->AddBackButton("index.php?mod=info2&action=change", "info2/form");
            break;

        // Delete entry
        case 10:
            foreach ($_POST["action"] as $item => $val) {
                $menu_intem = $db->qry_first("SELECT caption FROM %prefix%info WHERE infoID = %string%", $item);
                $db->qry("DELETE FROM %prefix%menu WHERE action = 'show_info2' AND caption = %string%", $menu_intem["caption"]);
                $db->qry("DELETE FROM %prefix%info WHERE infoID = %string%", $item);
            }

            $func->confirmation(t('Der Eintrag wurde gelöscht.'), "index.php?mod=info2&action=change");
            break;

        // Deactivate
        case 20:
            if ($_GET['infoID']) {
                $_POST["action"][$_GET['infoID']] = '1';
            }
            foreach ($_POST["action"] as $item => $val) {
                $db->qry("UPDATE %prefix%info SET active = 0 WHERE infoID = %string%", $item);
                $menu_intem = $db->qry_first("SELECT active, caption, shorttext FROM %prefix%info WHERE infoID = %string%", $item);
                $db->qry("DELETE FROM %prefix%menu WHERE action = 'show_info2' AND caption = %string%", $menu_intem["caption"]);
            }
            $func->confirmation(t('Eintrag deaktiviert'), "index.php?mod=info2&action=change");
            break;
    
        // Activate
        case 21:
            if ($_GET['infoID']) {
                $_POST["action"][$_GET['infoID']] = '1';
            }
            foreach ($_POST["action"] as $item => $val) {
                $db->qry("UPDATE %prefix%info SET active = 1 WHERE infoID = %string%", $item);
            }
            $func->confirmation(t('Eintrag aktiviert'), "index.php?mod=info2&action=change");
            break;
    
        // Activate and link
        case 22:
            if ($_GET['infoID']) {
                $_POST["action"][$_GET['infoID']] = '1';
            }
            foreach ($_POST["action"] as $item => $val) {
                $menu_intem = $db->qry_first("SELECT active, caption, shorttext, link FROM %prefix%info WHERE infoID = %string%", $item);
                $info_menu = $db->qry_first("SELECT pos FROM %prefix%menu WHERE module='info2'");

                $db->qry("DELETE FROM %prefix%menu WHERE action = 'show_info2' AND caption = %string%", $menu_intem["caption"]);

                ($cfg['info2_use_submenus'])? $level = 1 : $level = 0;

                if ($menu_intem['link']) {
                    $link = $menu_intem['link'] .'" target="_blank';
                } else {
                    $link = 'index.php?mod=info2&action=show_info2&id='. $item;
                }

                $db->qry("UPDATE %prefix%info SET active = 1 WHERE infoID = %string%", $item);
                $db->qry("
                  INSERT INTO %prefix%menu
                  SET
                    module = 'info2',
                    caption = %string%,
                    hint = %string%,
                    link = %string%,
                    requirement = 0,
                    level = %string%,
                    pos = %string%,
                    action = 'show_info2',
                    file = 'show'", $menu_intem["caption"], $menu_intem["shorttext"], $link, $level, $info_menu["pos"]);
            }
            $func->confirmation(t('Eintrag aktiviert'), "index.php?mod=info2&action=change");
            break;
    
        // Activate and link (admin only)
        case 23:
            if ($_GET['id']) {
                $_POST["action"][$_GET['id']] = '1';
            }
            foreach ($_POST["action"] as $item => $val) {
                $menu_intem = $db->qry_first("SELECT active, caption, shorttext, link FROM %prefix%info WHERE infoID = %string%", $item);
                $info_menu = $db->qry_first("SELECT pos FROM %prefix%menu WHERE module='info2'");

                $db->qry("DELETE FROM %prefix%menu WHERE action = 'show_info2' AND caption = %string%", $menu_intem["caption"]);

                ($cfg['info2_use_submenus'])? $level = 1 : $level = 0;

                if ($menu_intem['link']) {
                    $link = $menu_intem['link'] .'" target="_blank';
                } else {
                    $link = 'index.php?mod=info2&action=show_info2&id='. $item;
                }

                $db->qry("UPDATE %prefix%info SET active = 1 WHERE infoID = %string%", $item);
                $db->qry("
                  INSERT INTO %prefix%menu
                  SET
                    module = 'info2',
                    caption = %string%,
                    hint = %string%,
                    link = %string%,
                    requirement = 2,
                    level = %string%,
                    pos = %string%,
                    action = 'show_info2',
                    file = 'show'", $menu_intem["caption"], $menu_intem["shorttext"], $link, $level, $info_menu["pos"]);
            }
            $func->confirmation(t('Eintrag aktiviert'), "index.php?mod=info2&action=change");
            break;

        // Define external link
        case 30:
            $dsp->NewContent(t('Informationsseite - Bearbeiten'), t('Hier kannst du einen externen Link definieren.'));

            $mf = new \LanSuite\MasterForm();
            $mf->AddField(t('Link'), 'link');
            foreach ($translation->valid_lang as $val) {
                $_POST[$language] = 1;
                $mf->AddField(t($translation->lang_names[$val]).'|'.t('Einen Text für die Sprache "%1" definieren', t($translation->lang_names[$val])), $val, 'tinyint(1)', '', \LanSuite\MasterForm::FIELD_OPTIONAL, '', 2);
                if ($val == 'de') {
                    $val = '';
                } else {
                    $val = '_'. $val;
                }
                $mf->AddField(t('Seitentitel'), 'caption'. $val);
                $mf->AddField(t('Popup-Infotext'), 'shorttext'. $val);
            }
            $mf->AdditionalDBUpdateFunction = 'UpdateInfo2';
            $mf->SendForm('index.php?mod=info2&action=change&step=30', 'info', 'infoID', $_GET['infoID']);

            $dsp->AddBackButton("index.php?mod=info2&action=change", "info2/form");
            break;
    }
}
