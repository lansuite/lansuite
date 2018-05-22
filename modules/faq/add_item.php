<?php

switch ($_GET["step"]) {
    case 2:
        $get_cat_names = $db->qry("SELECT name FROM %prefix%faq_cat");

        while ($row=$db->fetch_array($get_cat_names)) {
            $name = $row["name"];

            if ($name == $_POST["question_new_cat"] and $_POST["question_new_cat"] != "") {
                $faq_error['question_cat'] = t('Dieser Kategoriename existiert bereits');
                $_GET["step"] = 1;
            }
        }

        $i = strlen($_POST["question_text"]);

        if ($i > 5000) {
            $faq_error['question_text'] = t('Die Antwort darf nicht mehr als 5000 Zeichen enthalten');
            $_GET["step"] = 1;
        }

        if ($_POST["question_new_cat"] == "" and $_POST["question_cat"] == "new") {
            $faq_error['cat_name']    = t('Bitte gib einen Namen für die neue Kategorie ein');
            $_GET["step"] = 1;
        }

        if ($_POST["question_caption"] == "") {
            $faq_error['question_caption']    = t('Bitte gib eine Frage ein');
            $_GET["step"] = 1;
        }

        if ($_POST["question_text"] == "") {
            $faq_error['question_text']    = t('Bitte gib einen Text ein');
            $_GET["step"] = 1;
        }

        if ($_POST["question_cat"] == 0 and $_POST["question_new_cat"] == "") {
            $faq_error['question_cat']    = t('Bitte wähle eine Kategorie aus oder erstelle eine neue Kategorie');
            $_GET["step"] = 1;
        }

        if ($_POST["question_cat"] != 0 and $_POST["question_new_cat"] != "") {
            $faq_error['question_cat']    = t('Bitte wähle eine Kategorie aus <b> ODER </b> erstelle eine neue Kategorie');
            $_GET["step"] = 1;
        }
        break;
}

switch ($_GET["step"]) {
    default:
        unset($_SESSION['add_blocker_faqitem']);

        $dsp->NewContent(t('Frage hinzufügen'), t(' Um eine Frage hinzuzufügen, fülle bitte das folgende Formular vollständig aus. Für das Feld Überschirft stehen 30 Zeichen, für das Feld Text 5000 Zeichen zur Verfügung. Im Feld Kategorie kannst du die Kategorie definieren, in der die Frage angezeigt werden soll.'));
        $dsp->SetForm("index.php?mod=faq&object=item&action=add_item&step=2");

        $get_cats = $db->qry("SELECT name,catid FROM %prefix%faq_cat");

        $faq_cats[] = "<option selected value=\"0\"> ".t('Kategorie wählen')." </option>";

        while ($row=$db->fetch_array($get_cats)) {
            $faq_cats[] .= "<option value=" . $row["catid"] . "> " . $row["name"] . " </option>";
        }

        $dsp->AddTextFieldRow("question_caption", t('Frage / Überschrift'), $_POST['question_caption'], $faq_error['question_caption']);
        $dsp->AddTextAreaPlusRow("question_text", t('Text'), $_POST['question_text'], $faq_error['question_text'], 70, 20);
        $dsp->AddDropDownFieldRow("question_cat", t('Bestehende Kategorie'), $faq_cats, "");
        $dsp->AddTextFieldRow("question_new_cat", t('Neue Kategorie'), $_POST['question_new_cat'], $faq_error['question_cat']);
        $dsp->AddFormSubmitRow("add");

        break;

    case 2:
        $courent_date = date("U");

        if ($_POST["question_cat"] == 0 and $_POST["question_new_cat"] != "" and $_SESSION['add_blocker_faqitem'] != 1) {
            $add_it = $db->qry("
              INSERT INTO %prefix%faq_cat
              SET
                name = %string%,
                poster = %int%,
                date = %string%", $_POST["question_new_cat"], $_SESSION["auth"]["userid"], $courent_date);
                $catid = $db->insert_id();
                $add_it = $db->qry("
                  INSERT INTO %prefix%faq_item
                  SET
                    caption = %string%,
                    text = %string%,
                    poster = %int%,
                    date = %string%,
                    catid = %int%", $_POST["question_caption"], $_POST["question_text"], $_SESSION["auth"]["userid"], $courent_date, $catid);

            if ($add_it == 1) {
                $func->confirmation(t('Die Frage und die Kategorie wurden erfolgreich eingetragen'), "");

                $_SESSION['add_blocker_faqitem'] = 1;
            } else {
                $func->error("NO_REFRESH");
            }
        } else {
            if ($_SESSION["add_blocker_faqitem"] != 1) {
                $add_it = $db->qry("
                  INSERT INTO %prefix%faq_item
                  SET
                    caption = %string%,
                    text = %string%,
                    poster = %int%,
                    date = %string%,
                    catid = %string%", $_POST["question_caption"], $_POST["question_text"], $_SESSION["auth"]["userid"], $courent_date, $_POST["question_cat"]);

                if ($add_it == 1) {
                    $func->confirmation(t('Die Frage wurde erfolgreich eingetragen'), "");
                    $add_blocker_faqitem = 1;
                    $_SESSION['add_blocker_faqitem'] = 1;
                }
            } else {
                $func->error("NO_REFRESH");
            }
        }

        break;
}
