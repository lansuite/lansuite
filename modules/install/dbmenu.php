<?php

include_once('modules/install/class_install.php');
$install = new Install();

$dsp->NewContent(t('Menu Einträge ersetzen'), "");

switch ($_GET['step']) {
    default:
                $dsp->SetForm("index.php?mod=install&action=dbmenu&step=2");
                $dsp->AddCheckBoxRow("rewrite", t('Menu Einträge ersetzen'), "", "");
                $dsp->AddFormSubmitRow(t('Weiter'));
                $dsp->AddBackButton("index.php?mod=install");
        break;
    case 2:
                $install->InsertMenus($_POST["rewrite"]);
                $func->information(t('Menu erfolgreich neu geschrieben'), "index.php?mod=install");
        break;
}

$dsp->AddContent();
