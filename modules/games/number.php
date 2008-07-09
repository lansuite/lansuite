<?php

/*************************************************************************
* 
*   Lansuite - Webbased LAN-Party Management System
*   -----------------------------------------------
*
*   (c) 2001-2003 by One-Network.Org
*
*   Lansuite Version:   2.0
*   File Version:       2.0
*   Filename:           Number
*   Module:             Games
*   Main editor:        jochen@one-network.org
*   Last change:        25.05.2004 19:34
*   Description:        Number Guessing
*   Remarks:            
*
**************************************************************************/

$headermenuitem = $_GET["headermenuitem"];
$action = $_GET["action"];
$step = $_GET["step"];

$dsp->NewContent(t('ZahlenRaten'), t('Versuchen Sie mit möglichst wenig Versuchen die gesuchte Zahl zu erraten'));

$menunames[1] = t('Start');
$menunames[2] = t('Highscore');
$dsp->AddHeaderMenu($menunames, "?mod=games&action=number", $headermenuitem);

if ($headermenuitem == 1) $step = 1;
if ($headermenuitem == 2) $step = 3;

switch ($step){
    // Write Score to DB
    case 2:
        $db->query("INSERT INTO {$config["tables"]["game_hs"]} SET
                    game = 'num',
                    nick = '". $func->text2db($_POST["nick"]) ."',
                    score = '{$_GET["score"]}'
                    ");
        $func->confirmation(t('Ihre Highscore wurde eingetragen'), "?mod=games&action=number&headermenuitem=2");
    break;

    // Highscoreliste
    case 3:
        $dsp->AddSingleRow(t('Highscoreliste'));

        $hs_liste = $db->qry('SELECT nick, score from %prefix%game_hs WHERE game=\'num\' ORDER BY score;');
        while($entry = $db->fetch_array($hs_liste)){
            $dsp->AddDoubleRow($entry['nick'], $entry['score'] . " ". t('Versuche'));
        }
        $db->free_result($hs_liste);

        $dsp->AddBackButton("?mod=games", "games/number");
    break;

    // Game
    default:
        if ($headermenuitem == 1) {
            unset( $_SESSION['zahl'] );
            $_SESSION["versuch"] = 0;
        }

        if (!isset($_SESSION["zahl"])){
            srand(date(U));
            $_SESSION["zahl"] = rand(1, 1000);
            $_POST['eingabe'] = "0";
        }

        $gewonnen = 0;
        if ($headermenuitem != 1) if ($_POST["eingabe"] > $_SESSION["zahl"]) $dsp->AddSingleRow(t('Die Gesuchte Zahl ist <b>kleiner</b> als <b>%1</b>', $_POST['eingabe']));
        else if ($_POST["eingabe"] < $_SESSION["zahl"]) $dsp->AddSingleRow(t('Die Gesuchte Zahl ist <b>größer</b> als <b>%1</b>', $_POST['eingabe']));
        else $gewonnen = 1;

        if (!$gewonnen) {
            $dsp->SetForm("?mod=games&action=number");
            $dsp->AddTextFieldRow("eingabe", t('Zahl vorschlagen'), $_POST['eingabe'], "");
            $dsp->AddDoubleRow(t('Versuche'), $_SESSION["versuch"]);
            $dsp->AddFormSubmitRow("next");

            $dsp->AddBackButton("?mod=games", "games/number");
            $dsp->AddSingleRow("<font size=\"1\" color=\"#FF0000\">".t('Tipp: Die Zahl ist größer -1 und kleiner 1001')."</font>");

            $_SESSION["versuch"]++;
        } else {
            $dsp->AddSingleRow("<b>". t('Sie haben Gewonnen! Herzlichen Glückwunsch!') ."</b>");
            $dsp->AddSingleRow(t('Die gesuchte Zahl lautete') .": ". $_SESSION['zahl']);
            $dsp->AddSingleRow(t('Sie benötigten %1 Versuche', $_SESSION['versuch']));

            $score = $_SESSION['versuch'];
            $dsp->SetForm("?mod=games&action=number&step=2&score=$score");
            $dsp->AddSingleRow(t('Hier können Sie sich in die Highscoreliste eintragen'));
            $dsp->AddDoubleRow(t('Versuche'), $score);
            $dsp->AddTextFieldRow("nick", t('Name'), $_SESSION["auth"]["username"], "");
            $dsp->AddFormSubmitRow("next");

            $dsp->AddBackButton("?mod=games", "games/number");
        }
    break;
}

$dsp->AddContent();
?>