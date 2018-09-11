<?php

switch ($_GET['step']) {
    case 2:
        if ($_POST['tticket_cat'] == 0 && $_GET['act'] == "change") {
            $error['tticket_cat'] = t('Du hast keine Kategorie zum ändern ausgewählt');
            $_GET['step'] = 1;
        }
        break;
    case 3:
        if (trim($_POST['name']) == "" || strlen($_POST['name']) > 30) {
            $error_cat['name'] = t('Name zu lang oder leer');
            $_POST['tticket_cat'] = $_GET['cat_id'];
            $_GET['step'] = 2;
        }
        break;
}

switch ($_GET['step']) {
    default:
        $dsp->NewContent(t('Kategorie'));
        
        $t_cat = $db->qry("SELECT * FROM %prefix%troubleticket_cat");
        if ($db->num_rows($t_cat) > 0) {
            $t_cat_array[] = "<option value=\"0\">".t('Bitte Auswählen')."</option>";
            
            while ($row = $db->fetch_array($t_cat)) {
                $t_cat_array[] .= "<option value=\"{$row['cat_id']}\">{$row['cat_text']}</option>";
            }

            $dsp->SetForm("index.php?mod=troubleticket&action=cat&act=change&step=2");
            $dsp->AddDropDownFieldRow("tticket_cat", t('Kategorie'), $t_cat_array, $error['tticket_cat']);
            $dsp->AddFormSubmitRow(t('Ändern'));
        } else {
            $dsp->AddSingleRow(t('Keine Kategorien vorhanden'));
        }
    
        $dsp->AddDoubleRow("", $dsp->FetchSpanButton(t('Hinzufügen'), "index.php?mod=troubleticket&action=cat&act=add&step=2"));
        $dsp->AddBackButton("index.php?mod=troubleticket");
        break;

    case 2:
        $dsp->NewContent(t('Kategorie'));
        $user_row = $db->qry('SELECT * FROM %prefix%user WHERE type > 1');
    
        if (isset($_POST["tticket_cat"]) && $_POST["tticket_cat"] > 0) {
            $user_row_option[] .= "<option value=\"0\">".t('Kein zuständiger Admin')."</option>";
        } else {
            $user_row_option[] .= "<option selected value=\"0\">".t('Kein zuständiger Admin')."</option>";
        }
        
        while ($user_data = $db->fetch_array($user_row)) {
            if ($user_data["userid"] == $_POST["tticket_cat"] && isset($_POST["tticket_cat"])) {
                $user_row_option[] .= "<option selected value=\"{$user_data["userid"]}\">{$user_data["username"]}</option>";
            } else {
                $user_row_option[] .= "<option value=\"{$user_data["userid"]}\">{$user_data["username"]}</option>";
            }
        }
        
        if ($_GET['act'] == "add") {
            $dsp->SetForm("index.php?mod=troubleticket&action=cat&act=add&step=3");
            $dsp->AddTextFieldRow("name", t('Kategorie'), "", $error_cat['name']);
            $dsp->AddDropDownFieldRow("orga", t('Zuständiger Admin'), $user_row_option, "");
            $dsp->AddFormSubmitRow(t('Hinzufügen'));
        } else {
            $cat_data = $db->qry_first("SELECT * FROM %prefix%troubleticket_cat WHERE cat_id = %string%", $_POST["tticket_cat"]);
            
            $dsp->SetForm("index.php?mod=troubleticket&action=cat&act=change&step=3&cat_id={$_POST['tticket_cat']}");
            $dsp->AddTextFieldRow("name", t('Kategorie'), $cat_data['cat_text'], $error_cat['name']);
            $dsp->AddDropDownFieldRow("orga", t('Zuständiger Admin'), $user_row_option, "");
            $dsp->AddFormSubmitRow(t('Ändern'));
        }
        break;
    
    case 3:
        if ($_GET['act'] == "add") {
            if ($db->qry("
              INSERT INTO %prefix%troubleticket_cat
              SET
                cat_text = %string%,
                orga = %string%", $_POST['name'], $_POST['orga'])) {
                $func->confirmation(t('Kategorie erfolgreich hinzugefügt/geändert'), "index.php?mod=troubleticket&action=cat");
            } else {
                $func->error(t('Kategorie konnte nicht hinzugefügt/geändert werden'), "index.php?mod=troubleticket&action=cat");
            }
        } else {
            if ($db->qry("
              UPDATE %prefix%troubleticket_cat
              SET
                cat_text = %string%,
                orga = %string%
              WHERE cat_id = %int%", $_POST['name'], $_POST['orga'], $_GET['cat_id'])) {
                $func->confirmation(t('Kategorie erfolgreich hinzugefügt/geändert'), "index.php?mod=troubleticket&action=cat");
            } else {
                $func->error(t('Kategorie konnte nicht hinzugefügt/geändert werden'), "index.php?mod=troubleticket&action=cat");
            }
        }
    
        break;
}
