<?php

include_once("modules/noc/class_noc.php");
$noc = new noc();

switch ($_GET['step']) {
    case "2":
        if ($_POST['ip'] == '') {
            $error_noc['ip'] = t('Bitte gib eine IP-Adresse f&uuml;r das Device ein');
            $_GET['step'] = 1;
        }
}


switch ($_GET['step']) {
    default:
            $dsp->NewContent(t('User im Netzwerk finden'), t('Mit diesem Formular kannst du einen User im Netzwerk lokalisieren'));
            $dsp->SetForm("index.php?mod=noc&action=find&step=2");
            $dsp->AddTextFieldRow("ip", t('IP-Adresse'), $_POST['ip'], $error_noc['ip']);
            $dsp->AddFormSubmitRow(t('Weiter'));
            $dsp->AddBackButton("index.php?mod=noc");
        break;
    
    case "2":
            $dsp->NewContent(t('User im Netzwerk finden'), t('Mit diesem Formular kannst du einen User im Netzwerk lokalisieren'));
            $dsp->AddDoubleRow(t('IP-Adresse'), $_POST['ip']);
            $noc->IPtoMAC_arp($_POST['ip']);
            $dsp->AddSingleRow("<a href='index.php?mod=noc&action=find&step=3&ip={$_POST['ip']}'>Alle Ports Updaten</<a>");
            $dsp->AddBackButton("index.php?mod=noc&action=find&step=1");
        break;

    case "3":
            $func->question(t('Dieser Vorgang kann einige Zeit dauern. Willst du wirklich alle Ports updaten.'), "index.php?mod=noc&action=find&step=4&ip={$_GET['ip']}", "index.php?mod=noc&action=find&step=1");
        break;
    case "4":
            $dsp->NewContent(t('User im Netzwerk finden'), t('Mit diesem Formular kannst du einen User im Netzwerk lokalisieren'));
            
            // Alle Device Updaten
            $row = $db->qry_first("SELECT * FROM %prefix%noc_devices");
        while ($db->fetch_array($row)) {
            $noc->getMacAddress($row["ip"], $row["readcommunity"], $row["id"], $row["sysDescr"]);
        }
            $dsp->AddDoubleRow(t('IP-Adresse'), $_GET['ip']);
            $noc->IPtoMAC_arp($_GET['ip']);
            $dsp->AddBackButton("index.php?mod=noc&action=find&step=1");
        break;
}
