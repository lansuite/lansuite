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

$dsp->NewContent($lang["games"]["hm_caption"], $lang["games"]["hm_subcaption"]);

$menunames[] = $lang["games"]["ms_navi_start"];
$menunames[] = $lang["games"]["ms_navi_highscore"];
$dsp->AddHeaderMenu($menunames, "?mod=games&action=hangman", $_GET["headermenuitem"]);

if ($_GET["headermenuitem"] == 1) $_GET["step"] = 0;
if ($_GET["headermenuitem"] == 2) $_GET["step"] = 5;


if (!$_GET["sieg"]) {
	$_POST["buchstabe"] = strtoupper($_POST["buchstabe"]);

	// Bei Spiel-Start Variablen zurücksetzen
	if ($_GET["ratewort"] == "") {
		$_SESSION["do_highscore"] = 0;
		if ($_POST["word"]) $_SESSION["losungswort"] = $_POST["word"];
		else {
			$_SESSION["do_highscore"] = 1;
			$lines = 0;
			$handle = fopen("modules/games/woerter.txt", "r");
			while (!feof($handle)) {
				fgets($handle, 4096);
				$lines ++;
			}
			fclose ($handle);

			$linenr = rand (1, $lines -1);
			$handle = fopen ("modules/games/woerter.txt", "r");
			for ($z = 0; $z < $linenr; $z++) $_SESSION["losungswort"] = fgets($handle, 4096);
			fclose ($handle);
		}
		$_SESSION["losungswort"] = chop(trim($_SESSION["losungswort"]));
		$_SESSION["losungswort"] = strtoupper($_SESSION["losungswort"]);

		for ($z = 1; $z <= strlen($_SESSION["losungswort"]); $z++) $_GET["ratewort"] .= "-";
		$_SESSION["versuche"] = 0;
		$_SESSION["used_letters"] = "";
	}

	// Richtige Buchstaben ersetzen
	if ($_POST["buchstabe"] != ""){
		$_SESSION["used_letters"] .= $_POST["buchstabe"];

		$pos = 0;
		$found = 0;
		while (!(strpos($_SESSION["losungswort"], $_POST["buchstabe"], $pos) === false)) {
			$pos = strpos($_SESSION["losungswort"], $_POST["buchstabe"], $pos) + 1;
			$_GET["ratewort"] = substr_replace($_GET["ratewort"], $_POST["buchstabe"], $pos - 1, 1);			
			$found = 1;
		}

		if (!$found) $_SESSION["versuche"] ++;
	}

	// Sieg-Check
	if (($_GET["ratewort"] == $_SESSION["losungswort"]) && ($_SESSION["losungswort"] != "")) {
		$_GET["step"] = 2;
	}
}

switch ($_GET["step"]) {
	// Spiel
	case 1:
		$dsp->SetForm("?mod=games&action=hangman&step=1&ratewort={$_GET["ratewort"]}");

		$dsp->AddDoubleRow("Lösung", "<b>{$_GET["ratewort"]}</b>");
		$dsp->AddDoubleRow("Fehlversuche", $_SESSION["versuche"]);
		$dsp->AddDoubleRow("Versuchte Buchstaben", $_SESSION["used_letters"]);

		$dsp->AddTextFieldRow("buchstabe", $lang["games"]["hm_buchstabe"], "", "");
		$dsp->AddFormSubmitRow("next");
	break;

	// Sieg
	case 2:
		$dsp->AddDoubleRow("", "Herzlichen Glückwunsch! Sie haben das Wort '{$_SESSION["losungswort"]}' mit {$_SESSION["versuche"]} Fehlversuchen erraten");
		$dsp->AddHRuleRow();

		if ($_SESSION["do_highscore"]) {
			$dsp->SetForm("?mod=games&action=hangman&step=4&sieg=1");
			$dsp->AddSingleRow($lang["games"]["ms_go_highscore"]);
			$dsp->AddDoubleRow("Fehlversuche", $_SESSION["versuche"]);
			$dsp->AddTextFieldRow("nick", $lang["games"]["ms_nick"], $auth["username"], "");
			$dsp->AddFormSubmitRow("next");
		}
	break;

	// Highscoreeintrag hinzufügen
	case 4:
		$add_it = $db->query("INSERT INTO {$config["tables"]["game_hs"]} SET
								game = 'hm',
								nick = '". $func->text2db($_POST["nick"]) ."',
								score = {$_SESSION["versuche"]}
								");

		$func->confirmation($lang["games"]["ms_hs_success"], "?mod=games&action=hangman&headermenuitem=2");
	break;
	
	// Highscoreliste
	case 5:
		$dsp->AddSingleRow($lang["games"]["ms_hs_list"]);

		$hs_liste = $db->query("SELECT nick, score from {$config["tables"]["game_hs"]} WHERE game='hm' ORDER BY score;");
		while($entry = $db->fetch_array($hs_liste)){
			$dsp->AddDoubleRow($entry['nick'], $entry['score'] ." Fehlversuche");
		}
		$db->free_result($hs_liste);

		$dsp->AddBackButton("?mod=games", "games/hangman");
	break;

	// Startscreen
	default:
		$dsp->SetForm("?mod=games&action=hangman&step=1");
		$dsp->AddDoubleRow("", "Um ein zufälliges Wort zu erhalten, bitte kein Wort eingeben.<br>Nur bei zufälligen Wörtern gibt es einen Highscoreeintrag");
		$dsp->AddTextFieldRow("word", $lang["games"]["hm_word"], "", "");
		$dsp->AddFormSubmitRow("next");

		$dsp->AddBackButton("?mod=games", "games/hangman");
	break;
}

$dsp->AddContent();
?>