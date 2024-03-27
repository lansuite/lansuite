<?php

use LanSuite\Module\Seating\Seat2;

$stepParameter = $_GET['step'] ?? 0;

$seat2 = new Seat2();

if ($_GET['action'] == 'add' && $stepParameter < 2) {
    $_GET['step'] = 2;
}

// Error-Switch
$error = [];
$stepParameter = $_GET['step'] ?? 0;
switch ($stepParameter) {
    case 3:
        // Error Columns
        if ($_POST['cols'] == "") {
            $error['cols'] = t('Bitte gib die horizontale Länge ein');
        } elseif ($_POST['cols'] <= 0) {
            $error['cols'] = t('Bitte gib eine Zahl, die größer als 0 ist ein');
        } elseif ($_POST['cols'] >= 60) {
            $error['cols'] = t('Bitte gib eine kleinere Zahl als 60 ein');
        } else {
            $row = $database->queryWithOnlyFirstRow("
              SELECT
                COUNT(*) AS number
              FROM %prefix%seat_seats
              WHERE
                blockid = ?
                AND status = 2
                AND col >= ?", [$_GET['blockid'], $_POST['cols']]);
            if ($row["number"] != 0) {
                $error['cols'] = t('Bitte gib eine größere Zahl ein, da sonst Sitzplätze gelöscht werden. Um Trotzdem einen kleineren Sitzblock zu erzeugen, entferne bitte die betroffenen Benutzer.');
            }
        }

        // Error Rows
        if ($_POST['rows'] == "") {
            $error['rows'] = t('Bitte gib die vertikale Länge ein');
        } elseif ($_POST['rows'] <= 0) {
            $error['rows'] = t('Bitte gib eine Zahl, die größer als 0 ist ein');
        } elseif ($_POST['rows'] >= 100) {
            $error['rows'] = t('Bitte gib eine kleinere Zahl als 100 ein');
        } else {
            $row = $database->queryWithOnlyFirstRow("
              SELECT
                COUNT(*) AS number
              FROM %prefix%seat_seats
              WHERE
                blockid = ?
                AND status = 2
                AND `row` >= ?", [$_GET['blockid'], $_POST['rows']]);
            if ($row["number"] != 0) {
                $error['rows'] = t('Bitte gib eine größere Zahl ein, da sonst Sitzplätze gelöscht werden. Um Trotzdem einen kleineren Sitzblock zu erzeugen, entferne bitte die betroffenen Benutzer.');
            }
        }

        // Remark
        if (strlen($_POST['remark']) > 1500) {
            $error['remark'] = t('Bitte gib weniger als 1500 Zeichen ein');
        }

        foreach ($error as $key => $val) {
            if ($val) {
                $_GET['step']--;
                break;
            }
        }
        break;

    // Update Seperators
    case 4:
        $changeSepRowParameter = $_GET['change_sep_row'] ?? 0;
        if ($changeSepRowParameter > 0) {
            $seperator = $database->queryWithOnlyFirstRow("
              SELECT
                value
              FROM %prefix%seat_sep
              WHERE
                blockid = ?
                AND orientation = '1'
                AND value = ?", [$_GET['blockid'], $_GET['change_sep_row']]);

            if ($seperator && $seperator['value']) {
                $database->query("
                  DELETE FROM %prefix%seat_sep
                  WHERE
                    blockid = ?
                    AND orientation = '1'
                    AND value = ?", [$_GET['blockid'], $_GET['change_sep_row']]);
            } else {
                $database->query("
                  INSERT INTO %prefix%seat_sep
                  SET
                    blockid = ?,
                    orientation = '1',
                    value = ?", [$_GET['blockid'], $_GET['change_sep_row']]);
            }
        }

        $changeSepColParameter = $_GET['change_sep_col'] ?? 0;
        if ($changeSepColParameter > 0) {
            $seperator = $database->queryWithOnlyFirstRow("
              SELECT value
              FROM %prefix%seat_sep
              WHERE
                blockid = ?
                AND orientation = '0'
                AND value = ?", [$_GET['blockid'], $_GET['change_sep_col']]);

            if ($seperator && $seperator['value']) {
                $database->query("
                  DELETE FROM %prefix%seat_sep
                  WHERE
                    blockid = ?
                    AND orientation = '0'
                    AND value = ?", [$_GET['blockid'], $_GET['change_sep_col']]);
            } else {
                $database->query("
                  INSERT INTO %prefix%seat_sep
                  SET
                    blockid = ?,
                    orientation = '0',
                    value = ?", [$_GET['blockid'], $_GET['change_sep_col']]);
            }
        }
        break;

    case 6:
        if ($_POST['cell']) {
            foreach ($_POST['cell'] as $cur_cell => $value) {
                $col = floor($cur_cell / 100);
                $row = $cur_cell % 100;
                $value = (int)$value;

                $seats_qry = $database->queryWithOnlyFirstRow("
                  SELECT
                    seatid
                  FROM %prefix%seat_seats
                  WHERE
                    blockid = ?
                    AND row = ?
                    AND col = ?", [$_GET['blockid'], $row, $col]);

                if (!$seats_qry) {
                    $database->query("
                      INSERT INTO %prefix%seat_seats
                      SET
                        blockid = ?,
                        row = ?,
                        col = ?,
                        status = ?", [$_GET['blockid'], $row, $col, $value]);
                } else {
                    $database->query("
                      UPDATE %prefix%seat_seats
                      SET
                        status = ?
                      WHERE seatid = ?", [$value, $seats_qry['seatid']]);
                }
            }
        }
        break;
}

// Form-Switch
$stepParameter = $_GET['step'] ?? 0;
switch ($stepParameter) {
    default:
        include_once('modules/seating/search.inc.php');
        break;

    case 2:
        $smarty->assign('text_tl', '');
        $smarty->assign('text_tc', '');
        $smarty->assign('text_tr', '');
        $smarty->assign('text_lt', '');
        $smarty->assign('text_lc', '');
        $smarty->assign('text_lb', '');
        $smarty->assign('text_rt', '');
        $smarty->assign('text_rc', '');
        $smarty->assign('text_rb', '');
        $smarty->assign('text_bl', '');
        $smarty->assign('text_bc', '');
        $smarty->assign('text_br', '');

        // Get data from DB
        if ($_GET['action'] == 'edit') {
            $block = $database->queryWithOnlyFirstRow("SELECT * FROM %prefix%seat_block WHERE blockid = ?", [$_GET['blockid']]);
            if (!array_key_exists('name', $_POST)) {
                $_POST['name'] = $block['name'];
            }
            if (!array_key_exists('cols', $_POST)) {
                $_POST['cols'] = $block['cols'] + 1;
            }
            if (!array_key_exists('rows', $_POST)) {
                $_POST['rows'] = $block['rows'] + 1;
            }
            if (!array_key_exists('orientation', $_POST)) {
                $_POST['orientation'] = $block['orientation'];
            }
            if (!array_key_exists('u18', $_POST)) {
                $_POST['u18'] = $block['u18'];
            }
            if (!array_key_exists('party_id', $_POST)) {
                $_POST['party_id'] = $block['party_id'];
            }
            if (!array_key_exists('group_id', $_POST)) {
                $_POST['group_id'] = $block['group_id'];
            }
            if (!array_key_exists('price_id', $_POST)) {
                $_POST['price_id'] = $block['price_id'];
            }
            if (!array_key_exists('remark', $_POST)) {
                $_POST['remark'] = $block['remark'];
            }
            if (!array_key_exists('text_tl', $_POST)) {
                $_POST['text_tl'] = $block['text_tl'];
            }
            if (!array_key_exists('text_tc', $_POST)) {
                $_POST['text_tc'] = $block['text_tc'];
            }
            if (!array_key_exists('text_tr', $_POST)) {
                $_POST['text_tr'] = $block['text_tr'];
            }
            if (!array_key_exists('text_lt', $_POST)) {
                $_POST['text_lt'] = $block['text_lt'];
            }
            if (!array_key_exists('text_lc', $_POST)) {
                $_POST['text_lc'] = $block['text_lc'];
            }
            if (!array_key_exists('text_lb', $_POST)) {
                $_POST['text_lb'] = $block['text_lb'];
            }
            if (!array_key_exists('text_rt', $_POST)) {
                $_POST['text_rt'] = $block['text_rt'];
            }
            if (!array_key_exists('text_rc', $_POST)) {
                $_POST['text_rc'] = $block['text_rc'];
            }
            if (!array_key_exists('text_rb', $_POST)) {
                $_POST['text_rb'] = $block['text_rb'];
            }
            if (!array_key_exists('text_bl', $_POST)) {
                $_POST['text_bl'] = $block['text_bl'];
            }
            if (!array_key_exists('text_bc', $_POST)) {
                $_POST['text_bc'] = $block['text_bc'];
            }
            if (!array_key_exists('text_br', $_POST)) {
                $_POST['text_br'] = $block['text_br'];
            }

            $smarty->assign('text_tl', $_POST['text_tl']);
            $smarty->assign('text_tc', $_POST['text_tc']);
            $smarty->assign('text_tr', $_POST['text_tr']);
            $smarty->assign('text_lt', $_POST['text_lt']);
            $smarty->assign('text_lc', $_POST['text_lc']);
            $smarty->assign('text_lb', $_POST['text_lb']);
            $smarty->assign('text_rt', $_POST['text_rt']);
            $smarty->assign('text_rc', $_POST['text_rc']);
            $smarty->assign('text_rb', $_POST['text_rb']);
            $smarty->assign('text_bl', $_POST['text_bl']);
            $smarty->assign('text_bc', $_POST['text_bc']);
            $smarty->assign('text_br', $_POST['text_br']);
        }

        $dsp->NewContent(t('Sitzblock erstellen'), t(' Mit Hilfe des folgenden Formulars kannst du einen neuen Sitzblock erstellen. In einem folgenden zweiten Schritt kannst du dann Plätze des Sitzblockes aktivieren bzw. deaktivieren um den Sitzblock deinen Bedürfnissen anzupassen.'));
        $blockIDParameter = $_GET['blockid'] ?? 0;
        $dsp->SetForm("index.php?mod=seating&action={$_GET['action']}&step=3&blockid={$blockIDParameter}");

        $namePostParameter = $_POST['name'] ?? '';
        $colsPostParameter = $_POST['cols'] ?? '';
        $rowsPostParameter = $_POST['rows'] ?? '';

        $nameError = $error['name'] ?? '';
        $colsError = $error['cols'] ?? '';
        $rowsError = $error['rows'] ?? '';

        $dsp->AddTextFieldRow('name', t('Sitzblockname'), $namePostParameter, $nameError);
        $dsp->AddTextFieldRow('cols', t('Länge horizontal'), $colsPostParameter, $colsError);
        $dsp->AddTextFieldRow('rows', t('Länge vertikal'), $rowsPostParameter, $rowsError);

        // Orientation
        $selections = array();
        (array_key_exists('orientation', $_POST) && $_POST['orientation'] == 0) ? $selected = 'selected' : $selected = '';
        $selections[] = "<option $selected value=\"0\">" . t('Vertikal') . '</option>';
        (array_key_exists('orientation', $_POST) && $_POST['orientation'] == 1) ? $selected = 'selected' : $selected = '';
        $selections[] = "<option $selected value=\"1\">" . t('Horizontal') . '</option>';
        $dsp->AddDropDownFieldRow('orientation', t('Orientierung'), $selections, '');

        $u18Parameter = $_POST['u18'] ?? 0;
        $dsp->AddCheckBoxRow('u18', t('U18 Block'), '', '', 0, $u18Parameter);

        $t_array   = array();
        $t_array[] = '<option value="0">' . t('Für alle Benutzer offen') . '</option>';
        $res       = $db->qry("SELECT group_id, group_name FROM %prefix%party_usergroups");
        while ($row = $db->fetch_array($res)) {
            $groupIDParameter = $_POST['group_id'] ?? 0;
            ($groupIDParameter == $row['group_id'])? $selected = 'selected' : $selected = '';
            $t_array[] = '<option ' . $selected . ' value="' . $row['group_id'] . '">' . $row['group_name'] . '</option>';
        }
        $db->free_result($res);
        $dsp->AddDropDownFieldRow("group_id", t('Nur für Benutzer dieser Gruppe'), $t_array, '');

        $t_array   = array();
        $t_array[] = '<option value="0">' . t('Für alle Benutzer offen') . '</option>';
        $res       = $db->qry("SELECT price_id, price_text FROM %prefix%party_prices WHERE party_id = %int%", $party->party_id);
        while ($row = $db->fetch_array($res)) {
            $priceIDParameter = $_POST['price_id'] ?? 0;
            ($priceIDParameter == $row['price_id'])? $selected = 'selected' : $selected = '';
            $t_array[] = '<option ' . $selected . ' value="' . $row['price_id'] . '">' . $row['price_text'] . '</option>';
        }
        $db->free_result($res);
        $dsp->AddDropDownFieldRow("price_id", t('Nur für diesen Eintrittspreis'), $t_array, '');

        $remarkParameter = $_POST['remark'] ?? '';
        $remarkError = $error['remark'] ?? '';
        $dsp->AddTextAreaPlusRow('remark', t('Bemerkung'), $remarkParameter, $remarkError, '', 4, 1);
        $dsp->AddDoubleRow(t('Sitzblockbeschriftung'), $smarty->fetch('modules/seating/templates/plan_labels.htm'));

        // Partys
        $partyIDParameter = $_POST['party_id'] ?? null;
        if (!$partyIDParameter) {
            $_POST['party_id'] = $party->party_id;
        }
        $selections = array();
        $res = $db->qry("SELECT party_id, name FROM %prefix%partys");
        while ($row = $db->fetch_array($res)) {
            ($_POST['party_id'] == $row['party_id']) ? $selected = 'selected' : $selected = '';
            $selections[] = "<option $selected value=\"" . $row['party_id'] . "\">" . $row['name'] . '</option>';
        }
        $db->free_result($res);
        $dsp->AddDropDownFieldRow('party_id', t('Party'), $selections, '');

        $dsp->AddFormSubmitRow(t('Weiter'));
        $dsp->AddBackButton('index.php?mod=seating', 'seating/add');
        break;

    case 3:
        // Save block settings

        // u18 is not checked, then the value is '0'
        if (empty($_POST['u18'])) {
            $_POST['u18'] = '0';
        }
        if ($_GET['action'] == 'add') {
            $db->qry("
              INSERT INTO %prefix%seat_block
              SET
                party_id = %int%,
                group_id = %int%,
                price_id = %int%,
                `name` = %string%,
                `rows` = %int%,
                cols = %int%,
                orientation = %string%,
                u18 = %string%,
                remark = %string%,
                text_tl = %string%,
                text_tc = %string%,
                text_tr = %string%,
                text_lt = %string%,
                text_lc = %string%,
                text_lb = %string%,
                text_rt = %string%,
                text_rc = %string%,
                text_rb = %string%,
                text_bl = %string%,
                text_bc = %string%,
                text_br = %string%", $party->party_id, $_POST['group_id'], $_POST['price_id'], $_POST['name'], ($_POST['rows'] - 1), ($_POST['cols'] - 1), $_POST['orientation'], $_POST['u18'], $_POST['remark'], $_POST['text_tl'], $_POST['text_tc'], $_POST['text_tr'], $_POST['text_lt'], $_POST['text_lc'], $_POST['text_lb'], $_POST['text_rt'], $_POST['text_rc'], $_POST['text_rb'], $_POST['text_bl'], $_POST['text_bc'], $_POST['text_br']);
            $_GET['blockid'] = $db->insert_id();
        } else {
            $db->qry("
              UPDATE %prefix%seat_block
              SET
                party_id = %int%,
                group_id = %int%,
                price_id = %int%,
                `name` = %string%,
                `rows` = %int%,
                cols = %int%,
                orientation = %string%,
                u18 = %string%,
                remark = %string%,
                text_tl = %string%,
                text_tc = %string%,
                text_tr = %string%,
                text_lt = %string%,
                text_lc = %string%,
                text_lb = %string%,
                text_rt = %string%,
                text_rc = %string%,
                text_rb = %string%,
                text_bl = %string%,
                text_bc = %string%,
                text_br = %string%
              WHERE blockid = %int%", $party->party_id, $_POST['group_id'], $_POST['price_id'], $_POST['name'], ($_POST['rows'] - 1), ($_POST['cols'] - 1), $_POST['orientation'], $_POST['u18'], $_POST['remark'], $_POST['text_tl'], $_POST['text_tc'], $_POST['text_tr'], $_POST['text_lt'], $_POST['text_lc'], $_POST['text_lb'], $_POST['text_rt'], $_POST['text_rc'], $_POST['text_rb'], $_POST['text_bl'], $_POST['text_bc'], $_POST['text_br'], $_GET['blockid']);
        }
    // No Break!
    case 4:
        // Continue with seperator definition
        $dsp->NewContent(t('Sitzblock Zwischengänge definieren'), t(' Abstände zwischen einzelnen Zeilen bzw. Reihen können mit den außen angezeigten Pfeilen eingefügt bzw. wieder gelöscht werden.'));

        $dsp->AddSingleRow($seat2->DrawPlan($_GET['blockid'], 1));

        $dsp->SetForm("index.php?mod=seating&action={$_GET['action']}&step=5&blockid={$_GET['blockid']}");
        $dsp->AddFormSubmitRow(t('Weiter'));
        $dsp->AddBackButton("index.php?mod=seating&action={$_GET['action']}&step=2&blockid={$_GET['blockid']}", 'seating/add');
        break;

    // Seat-Selection
    case 5:
    case 6:
        $dsp->NewContent(t('Sitzblock Sitze definieren'), t('Nun kannst du Plätze des Sitzblockes aktivieren bzw. deaktivieren um den Sitzblock deinen Bedürfnissen anzupassen.<br /><br />Ganze Reihen bzw. Spalten von Plätzen können aktiviert bzw. deaktiviert werden, indem du auf die Spalten- bzw. Reihen-Beschriftung  klicken.'));
        $dsp->SetForm("index.php?mod=seating&action={$_GET['action']}&step=6&blockid={$_GET['blockid']}", "block");
        $dsp->AddSingleRow($seat2->DrawPlan($_GET['blockid'], 2));
        $dsp->AddFormSubmitRow(t('Speichern'));
        $dsp->AddDoubleRow('', $dsp->FetchSpanButton(t('Weiter'), "index.php?mod=seating&action={$_GET['action']}&step=7&blockid={$_GET['blockid']}"));
        $dsp->AddBackButton("index.php?mod=seating&action={$_GET['action']}&step=4&blockid={$_GET['blockid']}", 'seating/add');
        break;

    // Finished
    case 7:
        $func->confirmation(t('Der Sitzplan wurde erfolgreich bearbeitet'), 'index.php?mod=seating');
        break;
}
