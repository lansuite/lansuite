<?php
include_once("modules/tournament2/class_tournament.php");
$tfunc = new tfunc;
include_once("modules/tournament2/class_t_league_export.php");
$t_league_export = new t_league_export;


$dsp->NewContent($lang["tourney"]["export_caption"], $lang["tourney"]["export_subcaption"]);

switch ($_GET["step"]) {
	case 2:
		// WWCL
		$dsp->AddSingleRow("WWCL");
		if (($_POST["pvd_id"] != "") && ($_POST["plp_id"] != "")){
			$dsp->AddSingleRow("<textarea cols=70 rows=25>". $t_league_export->wwcl_export($_POST["plp_id"], $_POST["pvd_id"]) ."</textarea>");
			$func->log_event($lang["tourney"]["export_wwcl_success"], 1, $lang["tourney"]["log_t_manage"]);
		} else $dsp->AddSingleRow($lang["tourney"]["export_no_wwcl"]);

		// NGL
		$dsp->AddSingleRow("NGL");
		if ($_POST["ngl_event_id"] != ""){
			$dsp->AddSingleRow("<textarea cols=70 rows=25>". $t_league_export->ngl_export($_POST["ngl_event_id"]) ."</textarea>");
			$func->log_event($lang["tourney"]["export_ngl_success"], 1, $lang["tourney"]["log_t_manage"]);
		} else  $dsp->AddSingleRow($lang["tourney"]["export_no_ngl"]);

		// LGZ
		$dsp->AddSingleRow("LGZ");
		if ($_POST["lgz_event_id"] != ""){
			$dsp->AddSingleRow("<textarea cols=70 rows=25>". $t_league_export->lgz_export($_POST["lgz_event_id"]) ."</textarea>");
			$func->log_event($lang["tourney"]["export_lgz_success"], 1, $lang["tourney"]["log_t_manage"]);
		} else  $dsp->AddSingleRow($lang["tourney"]["export_no_lgz"]);

		$dsp->AddBackButton("index.php?mod=tournament2&action=export", "tournament2/export"); 
	break;


	default:
		$dsp->SetForm("index.php?mod=tournament2&action=export&step=2");
		$dsp->AddSingleRow("WWCL");
		$dsp->AddTextFieldRow("pvd_id", "PVD-ID", "", "");
		$dsp->AddTextFieldRow("plp_id", "PlanetLan-Party-ID", "", "");
		$dsp->AddSingleRow("NGL");
		$dsp->AddTextFieldRow("ngl_event_id", "NGL-Event-ID", "", "");
		$dsp->AddSingleRow("LGZ");
		$dsp->AddTextFieldRow("lgz_event_id", "LGZ-Event-ID", "", "");
		$dsp->AddFormSubmitRow("next");	
		$dsp->AddBackButton("index.php?mod=tournament2", "tournament2/export"); 
	break;
}

$dsp->AddContent();
?>