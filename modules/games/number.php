<?

/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-----------------------------------------------
*
*	(c) 2001-2003 by One-Network.Org
*
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 			Number
*	Module: 			Games
*	Main editor: 		jochen@one-network.org
*	Last change: 		25.05.2004 19:34
*	Description: 		Number Guessing
*	Remarks: 			
*
**************************************************************************/

$headermenuitem	= $vars["headermenuitem"];
$action = $_GET["action"];
$step = $_GET["step"];

$dsp->NewContent($lang["games"]["ng_caption"], $lang["games"]["ng_subcaption"]);

$menunames[] = $lang["games"]["ms_navi_start"];
$menunames[] = $lang["games"]["ms_navi_highscore"];
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
		$func->confirmation($lang["games"]["ng_success"], "?mod=games&action=number&headermenuitem=2");
	break;

	// Highscoreliste
	case 3:
		$dsp->AddSingleRow($lang["games"]["ms_hs_list"]);

		$hs_liste = $db->query("SELECT nick, score from {$config["tables"]["game_hs"]} WHERE game='num' ORDER BY score;");
		while($entry = $db->fetch_array($hs_liste)){
			$dsp->AddDoubleRow($entry['nick'], $entry['score'] . " ". $lang["games"]["mg_trys"]);
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
		if ($headermenuitem != 1) if ($_POST["eingabe"] > $_SESSION["zahl"]) $dsp->AddSingleRow(str_replace("%NUM%", $_POST['eingabe'], $lang["games"]["ng_lower"]));
		else if ($_POST["eingabe"] < $_SESSION["zahl"]) $dsp->AddSingleRow(str_replace("%NUM%", $_POST['eingabe'], $lang["games"]["ng_higher"]));
		else $gewonnen = 1;

		if (!$gewonnen) {
			$dsp->SetForm("?mod=games&action=number");
			$dsp->AddTextFieldRow("eingabe", $lang["games"]["ng_guess"], $_POST['eingabe'], "");
			$dsp->AddDoubleRow($lang["games"]["ng_trys"], $_SESSION["versuch"]);
			$dsp->AddFormSubmitRow("next");

			$dsp->AddBackButton("?mod=games", "games/number");
			$dsp->AddSingleRow("<font size=\"1\" color=\"#FF0000\">{$lang["games"]["ng_hint"]}</font>");

			$_SESSION["versuch"]++;
		} else {
			$dsp->AddSingleRow("<b>". $lang["games"]["ms_congrats"] ."</b>");
			$dsp->AddSingleRow($lang["games"]["ng_number"] .": ". $_SESSION['zahl']);
			$dsp->AddSingleRow(str_replace("%TRYS%", $_SESSION['versuch'], $lang["games"]["ng_anz_trys"]));

			$score = $_SESSION['versuch'];
			$dsp->SetForm("?mod=games&action=number&step=2&score=$score");
			$dsp->AddSingleRow($lang["games"]["ms_go_highscore"]);
			$dsp->AddDoubleRow($lang["games"]["ng_trys"], $score);
			$dsp->AddTextFieldRow("nick", $lang["games"]["ms_nick"], $_SESSION["auth"]["username"], "");
			$dsp->AddFormSubmitRow("next");

			$dsp->AddBackButton("?mod=games", "games/number");
		}
	break;
}

$dsp->AddContent();
?>