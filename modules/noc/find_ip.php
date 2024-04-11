<?php

include_once("modules/noc/class_noc.php");
$noc = new noc();

$ipAddressParameter = $_POST['ip'] ?? '';
$error_noc = [
    'ip' => '',
];
$stepParameter = $_GET['step'] ?? 0;
switch ($stepParameter) {
    case "2":
        if ($ipAddressParameter == '') {
            $error_noc['ip'] = t('Bitte gib eine IP-Adresse f&uuml;r das Device ein');
            $_GET['step'] = 1;
        }
}

$stepParameter = $_GET['step'] ?? 0;
switch ($stepParameter) {
    default:
            $dsp->NewContent(t('User im Netzwerk finden'), t('Mit diesem Formular kannst du einen User im Netzwerk lokalisieren'));
            $dsp->SetForm("index.php?mod=noc&action=find&step=2");
            $dsp->AddTextFieldRow("ip", t('IP-Adresse'), $ipAddressParameter, $error_noc['ip']);
            $dsp->AddFormSubmitRow(t('Weiter'));
            $dsp->AddBackButton("index.php?mod=noc");
        break;
    
    case "2":
            $dsp->NewContent(t('User im Netzwerk finden'), t('Mit diesem Formular kannst du einen User im Netzwerk lokalisieren'));
            $dsp->AddDoubleRow(t('IP-Adresse'), $ipAddressParameter);
            $noc->IPtoMAC_arp($ipAddressParameter);
            $dsp->AddSingleRow("<a href='index.php?mod=noc&action=find&step=3&ip={$ipAddressParameter}'>Alle Ports Updaten</<a>");
            $dsp->AddBackButton("index.php?mod=noc&action=find&step=1");
        break;

    case "3":
            $func->question(t('Dieser Vorgang kann einige Zeit dauern. Willst du wirklich alle Ports updaten.'), "index.php?mod=noc&action=find&step=4&ip={$_GET['ip']}", "index.php?mod=noc&action=find&step=1");
        break;
    case "4":
            $dsp->NewContent(t('User im Netzwerk finden'), t('Mit diesem Formular kannst du einen User im Netzwerk lokalisieren'));
            
            // Alle Device Updaten
            $row = $database->queryWithOnlyFirstRow("SELECT * FROM %prefix%noc_devices");
        while ($db->fetch_array($row)) {
            $noc->getMacAddress($row["ip"], $row["readcommunity"], $row["id"], $row["sysDescr"]);
        }
            $dsp->AddDoubleRow(t('IP-Adresse'), $_GET['ip']);
            $noc->IPtoMAC_arp($_GET['ip']);
            $dsp->AddBackButton("index.php?mod=noc&action=find&step=1");
        break;
}
