<?php

switch ($_GET["step"]) {
    case 3:
        $get_cat_names = $db->qry("SELECT name FROM %prefix%faq_cat");
        while ($row=$db->fetch_array($get_cat_names)) {
            $name = $row["name"];
            if ($name == $_POST["question_new_cat"] and $_POST["question_new_cat"] != "") {
                $faq_error['question_cat'] = t('Dieser Kategoriename existiert bereits');
                $_GET["step"] = 2;
            }
        }

        $i = strlen($_POST["question_text"]);
        if ($i > 5000) {
            $faq_error['question_text'] = t('Die Antwort darf nicht mehr als 5000 Zeichen enthalten');
            $_GET["step"] = 2;
        }

        if ($_POST["question_new_cat"] == "" and $_POST["question_cat"] == "new") {
            $faq_error['cat_name']    = t('Bitte gib einen Namen für die neue Kategorie ein');
            $_GET["step"] = 2;
        }

        if ($_POST["question_caption"] == "") {
            $faq_error['question_caption']    = t('Bitte gib eine Frage ein');
            $_GET["step"] = 2;
        }

        if ($_POST["question_text"] == "") {
            $faq_error['question_text']    = t('Bitte gib einen Text ein');
            $_GET["step"] = 2;
        }

        if ($_POST["question_cat"] == 0 and $_POST["question_new_cat"] == "") {
            $faq_error['question_cat']    = t('Bitte wähle eine Kategorie aus oder erstelle eine neue Kategorie');
            $_GET["step"] = 2;
        }

        if ($_POST["question_cat"] != 0 and $_POST["question_new_cat"] != "") {
            $faq_error['question_cat']    = t('Bitte wähle eine Kategorie aus <b> ODER </b> erstelle eine neue Kategorie');
            $_GET["step"] = 2;
        }

        break;
}

switch ($_GET["step"]) {
    default:
        include('show.php');
        break;

    case 2:
        unset($_SESSION['change_blocker_faqitem']);

        $get_data = $db->qry_first("SELECT caption, text, catid FROM %prefix%faq_item WHERE itemid = %int%", $_GET["itemid"]);
        $question_caption = $get_data["caption"];

        if ($question_caption != "") {
            if ($_POST["question_caption"] == "") {
                $_POST["question_caption"] = $get_data["caption"];
            }
            if ($_POST["question_text"] == "") {
                $_POST["question_text"] = $get_data["text"];
            }

            $_POST["question_cat"]=($_POST["question_cat"] == "") ? $get_data["catid"] : $_POST["question_cat"];

            $dsp->NewContent(t('Frage ändern'), t(' Um eine Frage hinzuzufügen, fülle bitte das folgende Formular vollständig aus. Für das Feld Überschirft stehen 30 Zeichen, für das Feld Text 5000 Zeichen zur Verfügung. Im Feld Kategorie kannst du die Kategorie definieren, in der die Frage angezeigt werden soll.'));
            $dsp->SetForm("index.php?mod=faq&object=item&came_from=$came_from&action=change_item&step=3&itemid=" .$_GET["itemid"]);

            $get_cats = $db->qry("SELECT name,catid FROM %prefix%faq_cat");

            $faq_cats[] = "<option value=\"0\"> ".t('Kategorie wählen')." </option>";

            while ($row=$db->fetch_array($get_cats)) {
                $selected=($row["catid"] == $_POST["question_cat"]) ? "selected" : "";

                $faq_cats[] .= "<option $selected value=" . $row["catid"] . "> " . $row["name"] . " </option>";
            }

            $dsp->AddTextFieldRow("question_caption", t('Frage / Überschrift'), $_POST['question_caption'], $faq_error['question_caption']);
            $dsp->AddTextAreaPlusRow("question_text", t('Text'), $_POST['question_text'], $faq_error['question_text'], 70, 20);
            $dsp->AddDropDownFieldRow("question_cat", t('Bestehende Kategorie'), $faq_cats, "");
            $dsp->AddTextFieldRow("question_new_cat", t('Neue Kategorie'), $_POST['question_new_cat'], $faq_error['question_cat']);
            $dsp->AddFormSubmitRow(t('Hinzufügen'));
        } else {
            $func->error(t('Diese Frage existiert nicht'));
        }
        break;

    case 3:
        $get_itemid = $db->qry_first("SELECT caption FROM %prefix%faq_item WHERE itemid = %int%", $_GET["itemid"]);
        $faqitem_caption_test = $get_itemid["caption"];

        if ($faqitem_caption_test != "") {
            $courent_date = date("U");

            if ($_POST["question_cat"] == 0 and $_POST["question_new_cat"] != "" and $_SESSION["change_blocker_faqitem"] != 1) {
                $update_it1 = $db->qry("
                  INSERT INTO %prefix%faq_cat
                  SET
                    name = %string%,
                    poster = %int%,
                    date = %string%", $_POST["question_new_cat"], $_SESSION["auth"]["userid"], $courent_date);

                $get_catid = $db->qry_first("SELECT catid FROM %prefix%faq_cat WHERE name = %string%", $_POST["question_new_cat"]);
                $catid = $get_catid["catid"];

                $update_it2 = $db->qry("
                  UPDATE %prefix%faq_item
                  SET
                    caption = %string%,
                    text = %string%,
                    poster = %int%,
                    date = %string%,
                    catid = %int%
                  WHERE itemid = '{$_GET["itemid"]}'", $_POST["question_caption"], $_POST["question_text"], $_SESSION["auth"]["userid"], $courent_date, $catid);

                if ($update_it1 == 1 and $update_it2 == 1) {
                    $func->confirmation(t('Frage und Kategorie wurden erfolgreich geändert'), "");

                    $_SESSION['change_blocker_faqitem'] = 1;
                } else {
                    $func->error("NO_REFRESH");
                }
            } else {
                if ($_SESSION['change_blocker_faqitem'] != 1) {
                    $add_it = $db->qry("
                      UPDATE %prefix%faq_item
                      SET
                        caption = %string%,
                        text = %string%,
                        poster = %int%,
                        date = %string%,
                        catid = %string%
                      WHERE itemid = '{$_GET["itemid"]}'", $_POST["question_caption"], $_POST["question_text"], $_SESSION["auth"]["userid"], $courent_date, $_POST["question_cat"]);

                    if ($add_it == 1) {
                        $func->confirmation(t('Die Frage wurde erfolgreich ge&ändert'), "");
                        $_SESSION['change_blocker_faqitem'] = 1;
                    }
                } else {
                    $func->error("NO_REFRESH");
                }
            }
        } else {
            $func->error(t('Diese Frage existiert nicht'));
        }
        break;
}
