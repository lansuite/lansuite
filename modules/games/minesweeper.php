<?
//
/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-----------------------------------------------
*
*	(c) 2001-2003 by One-Network.Org
*
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 			minesweeper
*	Module: 			Minesweeper
*	Main editor: 		jochen@one-network.org
*	Last change: 		24.05.2004 13:35
*	Description: 		The Classic Minesweeper Game, you all know
*	Remarks: 		
*
**************************************************************************/

$headermenuitem	= $_GET["headermenuitem"];
$action = $_GET["action"];

$dsp->NewContent(t('MineSweeper'), t('Versuchen Sie alle Felder aufzudecken, ohne dabei auf eine Miene zu klicken'));

$menunames[1] = t('Start');
$menunames[2] = t('Highscore');
$dsp->AddHeaderMenu($menunames, "?mod=games&action=minesweeper", $headermenuitem);

if ($headermenuitem == 1) $_GET["step"] = 1;
if ($headermenuitem == 2) $_GET["step"] = 5;


switch ($_GET["step"]) {
	case 2:
		if ($_POST["rows"] > 20) {
			$func->information(t('Es dürfen maximal 20 Reihen ausgewählt werden'), "?mod=games&action=minesweeper");

		} elseif ($_POST["columns"] > 40) {
			$func->information(t('Es dürfen maximal 40 Spalten ausgewählt werden'), "?mod=games&action=minesweeper");

		} elseif ($_POST["mines"] > $_POST["rows"] * $_POST["columns"]) {
			$func->information(t('Es dürfen nicht mehr Mienen, als Felder ausgewählt werden'), "?mod=games&action=minesweeper");

		} elseif ($_POST["mines"] < 5) {
			$func->information(t('Es sollten mindestens 5 Mienen versteckt sein, sonst ist das Spiel witzlos!'), "?mod=games&action=minesweeper");

		} else {
			$tmp_nick = rand(0, 100000);

			$db->query("REPLACE INTO {$config["tables"]["game_hs"]}
				SET game = 'mw_tmp', nick = '$tmp_nick', score = ". time() ."
				");

			$templ['games']['minesweeper']['rows'] = $_POST["rows"];
			$templ['games']['minesweeper']['columns'] = $_POST["columns"];
			$templ['games']['minesweeper']['mines'] = $_POST["mines"];

			$templ['games']['minesweeper']['link_won'] = "?mod=games&action=minesweeper&step=3&tmp_nick=$tmp_nick";

			$templ['games']['minesweeper']['generate_field'] = "";
			for ($i=0; $i< $_POST["rows"]; $i++) {
				$templ['games']['minesweeper']['generate_field'] .= "<tr>";
				for ($j=0; $j< $_POST["columns"]; $j++) {
					$templ['games']['minesweeper']['generate_field'] .= "<td><input type=\"button\" value=\" \" name=\"";
					if ($i<10) $templ['games']['minesweeper']['generate_field'] .= "0";
					$templ['games']['minesweeper']['generate_field'] .= $i;
					if ($j<10) $templ['games']['minesweeper']['generate_field'] .= "0";
					$templ['games']['minesweeper']['generate_field'] .= "$j\" style=\"width:19;height:19;border:solid 1px 000000\" onClick=\"Check(this)\"></td>";
				}
				$templ['games']['minesweeper']['generate_field'] .= "</tr>";
			}

			$dsp->AddSingleRow($dsp->FetchModTpl("games", "minesweeper"));
		}
	break;

	case 3:
		$dsp->AddSingleRow("<b>". t('Sie haben Gewonnen! Herzlichen Glückwunsch!') ."</b>");
		$dsp->AddHRuleRow();

		$db->query("UPDATE {$config["tables"]["game_hs"]}
			SET score = ". time() ." - score
			WHERE (nick = '{$_GET["tmp_nick"]}' AND game = 'mw_tmp')
			");

		$dsp->SetForm("?mod=games&action=minesweeper&step=4&tmp_nick={$_GET["tmp_nick"]}");
		$dsp->AddSingleRow(t('Hier können Sie sich in die Highscoreliste eintragen'));
		$dsp->AddDoubleRow(t('Zeit'), $score);
		$dsp->AddTextFieldRow("nick", t('Name'), $auth["username"], "");
		$dsp->AddFormSubmitRow("next");

		$dsp->AddBackButton("?mod=games", "games/minesweeper");
	break;

	case 4:
		$db->query("UPDATE {$config["tables"]["game_hs"]}
			SET game = 'mw', nick = '". $func->text2db($_POST["nick"]) ."'
			WHERE (nick = '{$_GET["tmp_nick"]}' AND game = 'mw_tmp')
			");

		if ($db->get_affected_rows() > 0) $func->confirmation(t('Highscore wurde eingetragen'), "?mod=games&action=minesweeper&headermenuitem=2");
		else $func->information("Der angegebene temporäre Nick wurde nicht gefunden. Das Ergebnis konnte daher leider nicht eingetragen werden.", "?mod=games&action=minesweeper&headermenuitem=2");
	break;

	case 5:
		$dsp->AddSingleRow(t('Highscoreliste'));

		$hs_liste = $db->qry('SELECT nick, score from %prefix%game_hs WHERE game=\'mw\' ORDER BY score;');
		while($entry = $db->fetch_array($hs_liste)){
			$dsp->AddDoubleRow($entry['nick'], $entry['score'] ." Sek.");
		}
		$db->free_result($hs_liste);

		$dsp->AddBackButton("?mod=games", "games/minesweeper");
	break;

	default:
		$dsp->SetForm("?mod=games&action=minesweeper&step=2");
		$dsp->AddTextFieldRow("rows", t('Reihen'), "12", "");
		$dsp->AddTextFieldRow("columns", t('Spalten'), "20", "");
		$dsp->AddTextFieldRow("mines", t('Minen'), "25", "");
		$dsp->AddFormSubmitRow("next");

		$dsp->AddBackButton("?mod=games", "games/minesweeper");
	break;
}

$dsp->AddContent();
?>