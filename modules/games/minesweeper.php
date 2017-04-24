<?php
//
/*************************************************************************
*
*   Lansuite - Webbased LAN-Party Management System
*   -----------------------------------------------
*
*   (c) 2001-2003 by One-Network.Org
*
*   Lansuite Version:   2.0
*   File Version:       2.0
*   Filename:           minesweeper
*   Module:             Minesweeper
*   Main editor:        jochen@one-network.org
*   Last change:        24.05.2004 13:35
*   Description:        The Classic Minesweeper Game, you all know
*   Remarks:
*
**************************************************************************/

$headermenuitem = $_GET["headermenuitem"];
$action = $_GET["action"];

$dsp->NewContent(t('MineSweeper'), t('Versuche alle Felder aufzudecken, ohne dabei auf eine Mine zu klicken'));

$menunames[1] = t('Start');
$menunames[2] = t('Highscore');
$dsp->AddHeaderMenu($menunames, "index.php?mod=games&action=minesweeper", $headermenuitem);

if ($headermenuitem == 1) {
    $_GET["step"] = 1;
}
if ($headermenuitem == 2) {
    $_GET["step"] = 5;
}


switch ($_GET["step"]) {
    case 2:
        if ($_POST["rows"] > 20) {
            $func->information(t('Es dürfen maximal 20 Reihen ausgewählt werden'), "index.php?mod=games&action=minesweeper");
        } elseif ($_POST["columns"] > 40) {
            $func->information(t('Es dürfen maximal 40 Spalten ausgewählt werden'), "index.php?mod=games&action=minesweeper");
        } elseif ($_POST["mines"] > $_POST["rows"] * $_POST["columns"]) {
            $func->information(t('Es dürfen nicht mehr Mienen, als Felder ausgewählt werden'), "index.php?mod=games&action=minesweeper");
        } elseif ($_POST["mines"] < 5) {
            $func->information(t('Es sollten mindestens 5 Mienen versteckt sein, sonst ist das Spiel witzlos!'), "index.php?mod=games&action=minesweeper");
        } else {
            $tmp_nick = rand(0, 100000);

            $db->qry("REPLACE INTO %prefix%game_hs
                SET game = 'mw_tmp', nick = %string%, score = %int%
                ", $tmp_nick, time());

            $generate_field = "";
            for ($i=0; $i< $_POST["rows"]; $i++) {
                $generate_field .= "<tr>";
                for ($j=0; $j< $_POST["columns"]; $j++) {
                    $generate_field .= "<td><input type=\"button\" value=\" \" name=\"";
                    if ($i<10) {
                        $generate_field .= "0";
                    }
                    $generate_field .= $i;
                    if ($j<10) {
                        $generate_field .= "0";
                    }
                    $generate_field .= "$j\" style=\"width:19;height:19;border:solid 1px 000000\" onClick=\"Check(this)\"></td>";
                }
                $generate_field .= "</tr>";
            }

            $smarty->assign('rows', $_POST["rows"]);
            $smarty->assign('columns', $_POST["columns"]);
            $smarty->assign('mines', $_POST["mines"]);
            $smarty->assign('link_won', "index.php?mod=games&action=minesweeper&step=3&tmp_nick=$tmp_nick");
            $smarty->assign('generate_field', $generate_field);
            $dsp->AddSingleRow($smarty->fetch('modules/games/templates/minesweeper.htm'));
        }
        break;

    case 3:
        $dsp->AddSingleRow("<b>". t('Du hast Gewonnen! Herzlichen Glückwunsch!') ."</b>");
        $dsp->AddHRuleRow();

        $db->qry("UPDATE %prefix%game_hs
            SET score = %int% - score
            WHERE (nick = %string% AND game = 'mw_tmp')
            ", time(), $_GET["tmp_nick"]);

        $dsp->SetForm("index.php?mod=games&action=minesweeper&step=4&tmp_nick={$_GET["tmp_nick"]}");
        $dsp->AddSingleRow(t('Hier kannst du dich in die Highscoreliste eintragen'));
        $dsp->AddDoubleRow(t('Zeit'), $score);
        $dsp->AddTextFieldRow("nick", t('Name'), $auth["username"], "");
        $dsp->AddFormSubmitRow(t('Weiter'));

        $dsp->AddBackButton("index.php?mod=games", "games/minesweeper");
        break;

    case 4:
        $db->qry("UPDATE %prefix%game_hs
            SET game = 'mw', nick = %string%
            WHERE (nick = %string% AND game = 'mw_tmp')
            ", $_POST["nick"], $_GET["tmp_nick"]);

        if ($db->get_affected_rows() > 0) {
            $func->confirmation(t('Highscore wurde eingetragen'), "index.php?mod=games&action=minesweeper&headermenuitem=2");
        } else {
            $func->information("Der angegebene temporäre Nick wurde nicht gefunden. Das Ergebnis konnte daher leider nicht eingetragen werden.", "index.php?mod=games&action=minesweeper&headermenuitem=2");
        }
        break;

    case 5:
        $dsp->AddSingleRow(t('Highscoreliste'));

        $hs_liste = $db->qry('SELECT nick, score from %prefix%game_hs WHERE game=\'mw\' ORDER BY score;');
        while ($entry = $db->fetch_array($hs_liste)) {
            $dsp->AddDoubleRow($entry['nick'], $entry['score'] ." Sek.");
        }
        $db->free_result($hs_liste);

        $dsp->AddBackButton("index.php?mod=games", "games/minesweeper");
        break;

    default:
        $dsp->SetForm("index.php?mod=games&action=minesweeper&step=2");
        $dsp->AddTextFieldRow("rows", t('Reihen'), "12", "");
        $dsp->AddTextFieldRow("columns", t('Spalten'), "20", "");
        $dsp->AddTextFieldRow("mines", t('Minen'), "25", "");
        $dsp->AddFormSubmitRow(t('Weiter'));

        $dsp->AddBackButton("index.php?mod=games", "games/minesweeper");
        break;
}

$dsp->AddContent();
