<?php

$dsp->NewContent(t('Hangman'), t('Versuche durch Buchstaben tippen ein Wort zu erraten'));

$menunames[1] = t('Start');
$menunames[2] = t('Highscore');
$dsp->AddHeaderMenu($menunames, "index.php?mod=games&action=hangman", $_GET["headermenuitem"]);

if ($_GET["headermenuitem"] == 1) {
    $_GET["step"] = 0;
}
if ($_GET["headermenuitem"] == 2) {
    $_GET["step"] = 5;
}

if (!$_GET["sieg"]) {
    $_POST["buchstabe"] = strtoupper($_POST["buchstabe"]);

    // Bei Spiel-Start Variablen zurücksetzen
    if ($_GET["ratewort"] == "") {
        $_SESSION["do_highscore"] = 0;
        if ($_POST["word"]) {
            $_SESSION["losungswort"] = $_POST["word"];
        } else {
            $_SESSION["do_highscore"] = 1;
            $lines = 0;
            $handle = fopen("modules/games/woerter.txt", "r");
            while (!feof($handle)) {
                fgets($handle, 4096);
                $lines ++;
            }
            fclose($handle);

            $linenr = rand(1, $lines -1);
            $handle = fopen("modules/games/woerter.txt", "r");
            for ($z = 0; $z < $linenr; $z++) {
                $_SESSION["losungswort"] = fgets($handle, 4096);
            }
            fclose($handle);
        }
        $_SESSION["losungswort"] = chop(trim($_SESSION["losungswort"]));
        $_SESSION["losungswort"] = strtoupper($_SESSION["losungswort"]);

        for ($z = 1; $z <= strlen($_SESSION["losungswort"]); $z++) {
            $_GET["ratewort"] .= "-";
        }
        $_SESSION["versuche"] = 0;
        $_SESSION["used_letters"] = "";
    }

    // Richtige Buchstaben ersetzen
    $BuchstabeError = '';
    if ($_POST["buchstabe"] != "") {
        if (strlen($_POST["buchstabe"]) > 1) {
            $BuchstabeError = t('Bitte gebe nur einen Buchstaben ein');
        } else {
            $_SESSION["used_letters"] .= $_POST["buchstabe"];

            $pos = 0;
            $found = 0;
            while (!(strpos($_SESSION["losungswort"], $_POST["buchstabe"], $pos) === false)) {
                $pos = strpos($_SESSION["losungswort"], $_POST["buchstabe"], $pos) + 1;
                $_GET["ratewort"] = substr_replace($_GET["ratewort"], $_POST["buchstabe"], $pos - 1, 1);
                $found = 1;
            }

            if (!$found) {
                $_SESSION["versuche"] ++;
            }
        }
    }

    // Sieg-Check
    if (($_GET["ratewort"] == $_SESSION["losungswort"]) && ($_SESSION["losungswort"] != "")) {
        $_GET["step"] = 2;
    }
}

switch ($_GET["step"]) {
    // Spiel
    case 1:
        $dsp->SetForm("index.php?mod=games&action=hangman&step=1&ratewort={$_GET["ratewort"]}");

        $dsp->AddDoubleRow("Lösung", "<b>{$_GET["ratewort"]}</b>");
        $dsp->AddDoubleRow("Fehlversuche", $_SESSION["versuche"]);
        $dsp->AddDoubleRow("Versuchte Buchstaben", $_SESSION["used_letters"]);

        $dsp->AddTextFieldRow("buchstabe", t('Bitte einen Buchstaben raten'), "", $BuchstabeError);
        $dsp->AddFormSubmitRow(t('Weiter'));
        break;

    // Sieg
    case 2:
        $dsp->AddDoubleRow("", "Herzlichen Glückwunsch! Du hast das Wort '{$_SESSION["losungswort"]}' mit {$_SESSION["versuche"]} Fehlversuchen erraten");
        $dsp->AddHRuleRow();

        if ($_SESSION["do_highscore"]) {
            $_SESSION["ratewort"] = $_GET["ratewort"];
            $dsp->SetForm("index.php?mod=games&action=hangman&step=4&sieg=1");
            $dsp->AddSingleRow(t('Hier kannst du dich in die Highscoreliste eintragen'));
            $dsp->AddDoubleRow("Fehlversuche", $_SESSION["versuche"]);
            $dsp->AddTextFieldRow("nick", t('Name'), $auth["username"], "", "", "", $auth['login']);
            $dsp->AddTextFieldRow("comment", t('Kommentar'), "", "", "", \LanSuite\MasterForm::FIELD_OPTIONAL);
            $dsp->AddFormSubmitRow(t('Weiter'));
        }
        break;

    // Highscoreeintrag hinzufügen
    case 4:
        if (!$_SESSION["do_highscore"] or !($_SESSION["ratewort"] == $_SESSION["losungswort"]) && ($_SESSION["losungswort"] != "")) {
            $func->error("Faking verboten!", "index.php?mod=games&action=hangman");
        } elseif ($auth['login']) {
            $db->qry("INSERT INTO %prefix%game_hs SET game = 'hm', nick = %string%, userid = %string%, score = %string%, comment = %string%", $auth["username"], $auth["userid"], $_SESSION["versuche"], $_POST["comment"]);
            $func->confirmation(t('Highscore wurde eingetragen'), "index.php?mod=games&action=hangman&headermenuitem=2");
            unset($_SESSION["ratewort"]);
            unset($_SESSION["losungswort"]);
            unset($_SESSION["do_highscore"]);
        } else {
            $db->qry("INSERT INTO %prefix%game_hs SET game = 'hm', nick = %string%, score = %string%, comment = %string%", $_POST["nick"], $_SESSION["versuche"], $_POST["comment"]);
            $func->confirmation(t('Highscore wurde eingetragen'), "index.php?mod=games&action=hangman&headermenuitem=2");
            unset($_SESSION["ratewort"]);
            unset($_SESSION["losungswort"]);
            unset($_SESSION["do_highscore"]);
        }
        break;
    
    // Highscoreliste
    case 5:
        $dsp->AddSingleRow(t('Highscoreliste'));

        $ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2('games');

        //Anzeige der Aufgaben
        $ms2->query['from'] = "%prefix%game_hs AS g";
        $ms2->query['where'] ="game='hm'";
        $ms2->query['default_order_by'] ="g.score";
        $ms2->config['EntriesPerPage'] = 50;

        $ms2->AddSelect('g.userid');
        $ms2->AddResultField(t('Name'), 'g.nick', 'UserNameAndIcon');
        $ms2->AddResultField(t('Fehlversuche'), 'g.score');
        $ms2->AddResultField(t('Kommentar'), 'g.comment');
        $ms2->PrintSearch('index.php?mod=games&action=hangman&headermenuitem=2', 'g.id');


        $dsp->AddBackButton("index.php?mod=games", "games/hangman");
        break;

    // Startscreen
    default:
        $dsp->SetForm("index.php?mod=games&action=hangman&step=1");
        $dsp->AddDoubleRow("", "Um ein zufälliges Wort zu erhalten, bitte kein Wort eingeben.<br>Nur bei zufälligen Wörtern gibt es einen Highscoreeintrag");
        $dsp->AddTextFieldRow("word", t('Folgendes Wort erraten'), "", "");
        $dsp->AddFormSubmitRow(t('Weiter'));

        $dsp->AddBackButton("index.php?mod=games", "games/hangman");
        break;
}
