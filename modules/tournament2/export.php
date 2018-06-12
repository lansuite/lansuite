<?php

$mail = new \LanSuite\Module\Mail\Mail();
$seat2 = new \LanSuite\Module\Seating\Seat2();

$tfunc = new \LanSuite\Module\Tournament2\TournamentFunction($mail, $seat2);

$xml = new \LanSuite\XML();

$t_league_export = new \LanSuite\Module\Tournament2\TournamentLeagueExport($xml, $tfunc);

$dsp->NewContent(t('Exporte'), t('Hier stehen die Turnier-Exports der verschiedenen Ligen zum download bereit.'));

switch ($_GET["step"]) {
    case 2:
        // WWCL
        $dsp->AddSingleRow("WWCL");
        if (($_POST["pvd_id"] != "") && ($_POST["plp_id"] != "")) {
            $dsp->AddSingleRow("<textarea cols=70 rows=25>". $t_league_export->wwcl_export($_POST["plp_id"], $_POST["pvd_id"]) ."</textarea>");
            $func->log_event(t('WWCL-Export wurde erstellt'), 1, t('Turnier Verwaltung'));
        } else {
            $dsp->AddSingleRow(t('Nicht verfügbar. Bitte PVD-ID und PlanetLan-Party-ID angeben!'));
        }

        // NGL
        $dsp->AddSingleRow("NGL");
        if ($_POST["ngl_event_id"] != "") {
            $dsp->AddSingleRow("<textarea cols=70 rows=25>". $t_league_export->ngl_export($_POST["ngl_event_id"]) ."</textarea>");
            $func->log_event(t('NGL-Export wurde erstellt'), 1, t('Turnier Verwaltung'));
        } else {
            $dsp->AddSingleRow(t('Nicht verfügbar. Bitte NGL-Event-ID angeben!'));
        }

        // LGZ
        $dsp->AddSingleRow("LGZ");
        if ($_POST["lgz_event_id"] != "") {
            $dsp->AddSingleRow("<textarea cols=70 rows=25>". $t_league_export->lgz_export($_POST["lgz_event_id"]) ."</textarea>");
            $func->log_event(t('LGZ-Export wurde erstellt'), 1, t('Turnier Verwaltung'));
        } else {
            $dsp->AddSingleRow(t('Nicht verfügbar. Bitte LGZ-Event-ID angeben!'));
        }

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
        $dsp->AddFormSubmitRow(t('Weiter'));
        $dsp->AddBackButton("index.php?mod=tournament2", "tournament2/export");
        break;
}
