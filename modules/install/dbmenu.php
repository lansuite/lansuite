<?php

$install = new \LanSuite\Module\Install\Install();

$dsp->NewContent(t('Menu Einträge ersetzen'), "");

$stepParameter = $_GET['step'] ?? 0;
switch ($stepParameter) {
    default:
        $dsp->SetForm("index.php?mod=install&action=dbmenu&step=2");
        $dsp->AddCheckBoxRow("rewrite", t('Menu Einträge ersetzen'), "", "");
        $dsp->AddFormSubmitRow(t('Weiter'));
        $dsp->AddBackButton("index.php?mod=install");
        break;
    case 2:
        $rewriteParameter = $_POST["rewrite"] ?? false;
        $install->InsertMenus($rewriteParameter);
        $func->information(t('Menu erfolgreich neu geschrieben'), "index.php?mod=install");
        break;
}
