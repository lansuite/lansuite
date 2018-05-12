<?php

$headermenuitem = $_GET["headermenuitem"];
$action = $_GET["action"];
$step = $_GET["step"];

$dsp->NewContent(t('ZahlenRaten'), t('Versuche mit möglichst wenig Versuchen die gesuchte Zahl zu erraten'));

$menunames[1] = t('Start');
$menunames[2] = t('Highscore');
$dsp->AddHeaderMenu($menunames, "index.php?mod=games&action=number", $headermenuitem);

if ($headermenuitem == 1) {
    $step = 1;
}
if ($headermenuitem == 2) {
    $step = 3;
}

switch ($step) {
    // Write Score to DB
    case 2:
        if ($_GET["score"] != $_SESSION["versuch"] or $_SESSION["gewonnen"] == 0) {
            $func->error("Faking verboten!", "index.php?mod=games&action=number");
        } elseif ($auth['login']) {
            $db->qry("INSERT INTO %prefix%game_hs SET game = 'num', nick = %string%, userid = %string%, score = %string%, comment = %string%", $auth["username"], $auth["userid"], $_GET["score"], $_POST["comment"]);
            $func->confirmation(t('Deine Highscore wurde eingetragen'), "index.php?mod=games&action=number&headermenuitem=2");
            $_SESSION["versuch"] = 0;
            $_SESSION["gewonnen"] = 0;
        } else {
            $db->qry("INSERT INTO %prefix%game_hs SET game = 'num', nick = %string%, score = %string%, comment = %string%", $_POST["nick"], $_GET["score"], $_POST["comment"]);
            $func->confirmation(t('Deine Highscore wurde eingetragen'), "index.php?mod=games&action=number&headermenuitem=2");
            $_SESSION["versuch"] = 0;
            $_SESSION["gewonnen"] = 0;
        }
        break;

    // Highscoreliste
    case 3:
        $dsp->AddSingleRow(t('Highscoreliste'));

        $ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2('games');

        //Anzeige der Aufgaben
        $ms2->query['from'] = "%prefix%game_hs AS g";
        $ms2->query['where'] ="game='num'";
        $ms2->query['default_order_by'] ="g.score";
        $ms2->config['EntriesPerPage'] = 50;

        $ms2->AddSelect('g.userid');
        $ms2->AddResultField(t('Name'), 'g.nick', 'UserNameAndIcon');
        $ms2->AddResultField(t('Versuche'), 'g.score');
        $ms2->AddResultField(t('Kommentar'), 'g.comment');
        $ms2->PrintSearch('index.php?mod=games&action=number&headermenuitem=2', 'g.id');
        
        $dsp->AddBackButton("index.php?mod=games", "games/number");
        break;

    // Game
    default:
        if ($headermenuitem == 1) {
            unset($_SESSION['zahl']);
            $_SESSION["versuch"] = 0;
            $_SESSION["gewonnen"] = 0;
        }

        if (!isset($_SESSION["zahl"])) {
            srand(date('U'));
            $_SESSION["zahl"] = rand(1, 1000);
            $_POST['eingabe'] = "0";
        }

        $_SESSION["gewonnen"] = 0;
        if ($headermenuitem != 1) {
            if ($_POST["eingabe"] > $_SESSION["zahl"]) {
                $dsp->AddSingleRow(t('Die Gesuchte Zahl ist <b>kleiner</b> als <b>%1</b>', $_POST['eingabe']));
            } elseif ($_POST["eingabe"] < $_SESSION["zahl"]) {
                $dsp->AddSingleRow(t('Die Gesuchte Zahl ist <b>größer</b> als <b>%1</b>', $_POST['eingabe']));
            } else {
                $_SESSION["gewonnen"] = 1;
            }
        }

        if (!$_SESSION["gewonnen"]) {
            $dsp->SetForm("index.php?mod=games&action=number");
            $dsp->AddTextFieldRow("eingabe", t('Zahl vorschlagen'), $_POST['eingabe'], "");
            $dsp->AddDoubleRow(t('Versuche'), $_SESSION["versuch"]);
            $dsp->AddFormSubmitRow(t('Weiter'));

            $dsp->AddBackButton("index.php?mod=games", "games/number");
            $dsp->AddSingleRow("<font size=\"1\" color=\"#FF0000\">".t('Tipp: Die Zahl ist größer -1 und kleiner 1001')."</font>");

            $_SESSION["versuch"]++;
        } else {
            $dsp->AddSingleRow("<b>". t('Du hast Gewonnen! Herzlichen Glückwunsch!') ."</b>");
            $dsp->AddSingleRow(t('Die gesuchte Zahl lautete') .": ". $_SESSION['zahl']);
            $dsp->AddSingleRow(t('Du hast %1 Versuche benötigt', $_SESSION['versuch']));

            $score = $_SESSION['versuch'];
            $dsp->SetForm("index.php?mod=games&action=number&step=2&score=$score");
            $dsp->AddSingleRow(t('Hier kannst du dich in die Highscoreliste eintragen'));
            $dsp->AddDoubleRow(t('Versuche'), $score);
            $dsp->AddTextFieldRow("nick", t('Name'), $auth["username"], "", "", "", $auth['login']);
            $dsp->AddTextFieldRow("comment", t('Kommentar'), "", "", "", \LanSuite\MasterForm::FIELD_OPTIONAL);
            
            $dsp->AddFormSubmitRow(t('Weiter'));

            $dsp->AddBackButton("index.php?mod=games", "games/number");
        }
        break;
}
