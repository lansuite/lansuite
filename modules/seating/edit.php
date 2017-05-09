<?php

include_once("modules/seating/class_seat.php");
$seat2 = new seat2();

if ($_GET['action'] == 'add' and $_GET['step'] < 2) {
    $_GET['step'] = 2;
}

// Error-Switch
$error = array();
switch ($_GET['step']) {
    case 3:
        // Error Columns
        if ($_POST['cols'] == "") {
            $error['cols'] = t('Bitte gib die horizontale Länge ein');
        } elseif ($_POST['cols'] <= 0) {
            $error['cols'] = t('Bitte gib eine Zahl, die größer als 0 ist ein');
        } elseif ($_POST['cols'] >= 60) {
            $error['cols'] = t('Bitte gib eine kleinere Zahl als 60 ein');
        } else {
            $row = $db->qry_first("SELECT count(*) AS number FROM %prefix%seat_seats
    WHERE blockid = %int% AND status = 2 AND col >= %int%", $_GET['blockid'], $_POST['cols']);
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
            $row = $db->qry_first("SELECT count(*) AS number FROM %prefix%seat_seats
    WHERE blockid = %int% AND status = 2 AND row >= %int%", $_GET['blockid'], $_POST['rows']);
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
        if ($_GET['change_sep_row'] > 0) {
            $seperator = $db->qry_first("SELECT value FROM %prefix%seat_sep
    WHERE blockid = %int% AND orientation = '1' AND value = %string%", $_GET['blockid'], $_GET['change_sep_row']);
            if ($seperator['value']) {
                $db->qry("DELETE FROM %prefix%seat_sep
    WHERE blockid = %int% AND orientation = '1' AND value = %string%", $_GET['blockid'], $_GET['change_sep_row']);
            } else {
                $db->qry("INSERT INTO %prefix%seat_sep SET blockid = %int%, orientation = '1', value = %string%", $_GET['blockid'], $_GET['change_sep_row']);
            }
        }
        if ($_GET['change_sep_col'] > 0) {
            $seperator = $db->qry_first("SELECT value FROM %prefix%seat_sep
    WHERE blockid = %int% AND orientation = '0' AND value = %string%", $_GET['blockid'], $_GET['change_sep_col']);
            if ($seperator['value']) {
                $db->qry("DELETE FROM %prefix%seat_sep
    WHERE blockid = %int% AND orientation = '0' AND value = %string%", $_GET['blockid'], $_GET['change_sep_col']);
            } else {
                $db->qry("INSERT INTO %prefix%seat_sep SET blockid = %int%, orientation = '0', value = %string%", $_GET['blockid'], $_GET['change_sep_col']);
            }
        }
        break;

    case 6:
         // $icon_nr = (int) substr($_POST['icon'], 0, 3);
        if ($_POST['cell']) {
            foreach ($_POST['cell'] as $cur_cell => $value) {
                $col = floor($cur_cell / 100);
                $row = $cur_cell % 100;
                $value = (int)$value;

                $seats_qry = $db->qry_first("SELECT seatid FROM %prefix%seat_seats
   WHERE blockid = %int% AND row = %string% AND col = %string%", $_GET['blockid'], $row, $col);

                if (!$seats_qry['seatid']) {
                    $db->qry("INSERT INTO %prefix%seat_seats SET
     blockid = %int%,
     row = %int%,
     col = %int%,
     status = %string%
     ", $_GET['blockid'], $row, $col, $value);
                } else {
                    $db->qry("UPDATE %prefix%seat_seats SET
     status = %string%
     WHERE seatid = %int%
     ", $value, $seats_qry['seatid']);
                }
            }
        }
        break;
}


// Form-Switch
switch ($_GET['step']) {
    default:
        include_once('modules/seating/search.inc.php');
        break;

    case 2:
        // Get data from DB
        if ($_GET['action'] == 'edit') {
            $block = $db->qry_first("SELECT * FROM %prefix%seat_block WHERE blockid = %int%", $_GET['blockid']);
            if ($_POST['name'] == "") {
                $_POST['name'] = $block['name'];
            }
            if ($_POST['cols'] == "") {
                $_POST['cols'] = $block['cols'] + 1;
            }
            if ($_POST['rows'] == "") {
                $_POST['rows'] = $block['rows'] + 1;
            }
            if ($_POST['orientation'] == "") {
                $_POST['orientation'] = $block['orientation'];
            }
            if ($_POST['u18'] == "") {
                $_POST['u18'] = $block['u18'];
            }
            if ($_POST['party_id'] == "") {
                $_POST['party_id'] = $block['party_id'];
            }
            if ($_POST['group_id'] == "") {
                $_POST['group_id'] = $block['group_id'];
            }
            if ($_POST['price_id'] == "") {
                $_POST['price_id'] = $block['price_id'];
            }
            if ($_POST['remark'] == "") {
                $_POST['remark'] = $block['remark'];
            }
            if ($_POST['text_tl'] == "") {
                $_POST['text_tl'] = $block['text_tl'];
            }
            if ($_POST['text_tc'] == "") {
                $_POST['text_tc'] = $block['text_tc'];
            }
            if ($_POST['text_tr'] == "") {
                $_POST['text_tr'] = $block['text_tr'];
            }
            if ($_POST['text_lt'] == "") {
                $_POST['text_lt'] = $block['text_lt'];
            }
            if ($_POST['text_lc'] == "") {
                $_POST['text_lc'] = $block['text_lc'];
            }
            if ($_POST['text_lb'] == "") {
                $_POST['text_lb'] = $block['text_lb'];
            }
            if ($_POST['text_rt'] == "") {
                $_POST['text_rt'] = $block['text_rt'];
            }
            if ($_POST['text_rc'] == "") {
                $_POST['text_rc'] = $block['text_rc'];
            }
            if ($_POST['text_rb'] == "") {
                $_POST['text_rb'] = $block['text_rb'];
            }
            if ($_POST['text_bl'] == "") {
                $_POST['text_bl'] = $block['text_bl'];
            }
            if ($_POST['text_bc'] == "") {
                $_POST['text_bc'] = $block['text_bc'];
            }
            if ($_POST['text_br'] == "") {
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
        $dsp->SetForm("index.php?mod=seating&action={$_GET['action']}&step=3&blockid={$_GET['blockid']}");

        $dsp->AddTextFieldRow('name', t('Sitzblockname'), $_POST['name'], $error['name']);
        $dsp->AddTextFieldRow('cols', t('Länge horizontal'), $_POST['cols'], $error['cols']);
        $dsp->AddTextFieldRow('rows', t('Länge vertikal'), $_POST['rows'], $error['rows']);

        // Orientation
        $selections = array();
        ($_POST['orientation'] == 0) ? $selected = 'selected' : $selected = '';
        array_push($selections, "<option $selected value=\"0\">".t('Vertikal').'</option>');
        ($_POST['orientation'] == 1) ? $selected = 'selected' : $selected = '';
        array_push($selections, "<option $selected value=\"1\">".t('Horizontal').'</option>');
        $dsp->AddDropDownFieldRow('orientation', t('Orientierung'), $selections, '');

        $dsp->AddCheckBoxRow('u18', t('U18 Block'), '', '', 0, $_POST['u18']);

                $t_array = array();
        array_push($t_array, '<option value="0">'. t('Für alle Benutzer offen') .'</option>');
        $res = $db->qry("SELECT group_id, group_name FROM %prefix%party_usergroups");
        while ($row = $db->fetch_array($res)) {
            ($_POST['group_id'] == $row['group_id'])? $selected = 'selected' : $selected = '';
            array_push($t_array, '<option '. $selected .' value="'. $row['group_id'] .'">'. $row['group_name'] .'</option>');
        }
        $db->free_result($res);
        $dsp->AddDropDownFieldRow("group_id", t('Nur für Benutzer dieser Gruppe'), $t_array, '');

        $t_array = array();
                array_push($t_array, '<option value="0">'. t('Für alle Benutzer offen') .'</option>');
                $res = $db->qry("SELECT price_id, price_text FROM %prefix%party_prices WHERE party_id = %int%", $party->party_id);
        while ($row = $db->fetch_array($res)) {
            ($_POST['price_id'] == $row['price_id'])? $selected = 'selected' : $selected = '';
            array_push($t_array, '<option '. $selected .' value="'. $row['price_id'] .'">'. $row['price_text'] .'</option>');
        }
        $db->free_result($res);
                $dsp->AddDropDownFieldRow("price_id", t('Nur für diesen Eintrittspreis'), $t_array, '');

        $dsp->AddTextAreaPlusRow('remark', t('Bemerkung'), $_POST['remark'], $error['remark'], '', 4, 1);
        $dsp->AddDoubleRow(t('Sitzblockbeschriftung'), $smarty->fetch('modules/seating/templates/plan_labels.htm'));

        // Partys
        $selections = array();
        if (!$_POST['party_id']) {
            $_POST['party_id'] = $party->party_id;
        }
        $res = $db->qry("SELECT party_id, name FROM %prefix%partys");
        while ($row = $db->fetch_array($res)) {
            ($_POST['party_id'] == $row['party_id']) ? $selected = 'selected' : $selected = '';
            array_push($selections, "<option $selected value=\"". $row['party_id'] ."\">". $row['name'] .'</option>');
        }
        $db->free_result($res);
        $dsp->AddDropDownFieldRow('party_id', t('Party'), $selections, '');

        $dsp->AddFormSubmitRow(t('Weiter'));
        $dsp->AddBackButton('index.php?mod=seating', 'seating/add');
        $dsp->AddContent();
        break;

    case 3:
        // Save block settings
        if ($_GET['action'] == 'add') {
            $db->qry("INSERT INTO %prefix%seat_block SET
    party_id = %int%,
    group_id = %int%,
    price_id = %int%,
    name = %string%,
    rows = %int%,
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
    ", $party->party_id, $_POST['group_id'], $_POST['price_id'], $_POST['name'], ($_POST['rows'] - 1), ($_POST['cols'] - 1), $_POST['orientation'], $_POST['u18'], $_POST['remark'], $_POST['text_tl'], $_POST['text_tc'], $_POST['text_tr'], $_POST['text_lt'], $_POST['text_lc'], $_POST['text_lb'], $_POST['text_rt'], $_POST['text_rc'], $_POST['text_rb'], $_POST['text_bl'], $_POST['text_bc'], $_POST['text_br']);
            $_GET['blockid'] = $db->insert_id();
        } else {
            $db->qry("UPDATE %prefix%seat_block SET
    party_id = %int%,
    group_id = %int%,
    price_id = %int%,
    name = %string%,
    rows = %int%,
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
    WHERE blockid = %int%
    ", $party->party_id, $_POST['group_id'], $_POST['price_id'], $_POST['name'], ($_POST['rows'] - 1), ($_POST['cols'] - 1), $_POST['orientation'], $_POST['u18'], $_POST['remark'], $_POST['text_tl'], $_POST['text_tc'], $_POST['text_tr'], $_POST['text_lt'], $_POST['text_lc'], $_POST['text_lb'], $_POST['text_rt'], $_POST['text_rc'], $_POST['text_rb'], $_POST['text_bl'], $_POST['text_bc'], $_POST['text_br'], $_GET['blockid']);
        }
    // No Break!
    case 4:
        // Continue with seperator definition
        $dsp->NewContent(t('Sitzblock Zwischengänge definieren'), t(' Abstände zwischen einzelnen Zeilen bzw. Reihen können mit den außen angezeigten Pfeilen eingefügt bzw. wieder gelöscht werden.'));

        $dsp->AddSingleRow($seat2->DrawPlan($_GET['blockid'], 1));

        $dsp->SetForm("index.php?mod=seating&action={$_GET['action']}&step=5&blockid={$_GET['blockid']}");
        $dsp->AddFormSubmitRow(t('Weiter'));
        $dsp->AddBackButton("index.php?mod=seating&action={$_GET['action']}&step=2&blockid={$_GET['blockid']}", 'seating/add');
        $dsp->AddContent();
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
        $dsp->AddContent();
        break;

    // Finished
    case 7:
        $func->confirmation(t('Der Sitzplan wurde erfolgreich bearbeitet'), 'index.php?mod=seating');
        break;
}
