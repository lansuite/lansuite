<?php

include_once("modules/noc/class_noc.php");
$noc = new noc();

 
switch ($_GET["step"]) {
    default:
    case 1:
        // Get all the Port data
        $row = $db->qry_first("SELECT * FROM %prefix%noc_ports WHERE portid = %int%", $_GET["portid"]);
        
        if ($row["portid"] == "") {
            $func->error(t('Dieser Port existiert nicht'));
        } else {
        // Is the Port Enabled? No? Is it connected?
            switch ($row["linkstatus"]) {
                case "1":
                case "up":
                case "up(1)":
                    if ($row["adminstatus"] == "down(2)" || $row["adminstatus"] == "down" || $row["adminstatus"] == "2") {
                        $linkstatus = "<font color=\"red\">" . t('Ausgeschaltet') . "</font>";
                    } else {
                        $linkstatus = "<font color=\"green\">" . t('Aktiv') . "</font>";
                    }
                    break;
            
                case "2":
                case "down":
                case "down(2)":
                    if ($row["adminstatus"] == "down(2)" || $row["adminstatus"] == "down" || $row["adminstatus"] == "2") {
                        $linkstatus = "<font color=\"red\">" . t('Ausgeschaltet') . "</font>";
                    } else {
                        $linkstatus = "<font color=\"red\">" . t('Inaktiv') . "</font>";
                    }
                    break;
            }
        
        // We assume that of course 1 PetaByte = 1024 Terabyte = 1024² Gigabyte = 1024³ Megabyte = 1024 * 1024 * 1024 * 1024 Kilobyte = 1024 * 1024 * 1024 * 1024 * 1024 Byte = 1024 * 1024 * 1024 * 1024 * 1024 * 8 Bit
        // Clear, right?
            $bytesIn  = round($row["bytesIn"]  / (1024 * 1024), 2) . " MBytes";
            $bytesOut = round($row["bytesOut"] / (1024 * 1024), 2) . " MBytes";
        
        
            $dsp->NewContent(t('Port &auml;ndern'), t('Um den Status des Ports zu &auml;ndern auf &Auml;ndern dr&uuml;cken.'));
            $dsp->SetForm("index.php?mod=noc&action=port_details&step=2&portid=" . $_GET['portid'], "noc");

        // Template Variables
            $dsp->AddDoubleRow(t('Portnummer'), $row["portnr"]);
            $dsp->AddDoubleRow(t('MAC-Adresse'), nl2br($row["mac"]));
            $dsp->AddDoubleRow(t('IP-Adresse'), $row["ip"]);
            $dsp->AddDoubleRow(t('Portstatus'), $linkstatus);
            $dsp->AddFormSubmitRow(t('Editieren'));
            $dsp->AddDoubleRow(t('Geschwindigkeit'), $row["speed"]. " MBit/s (entspricht ~ " . round($row["speed"] / 8, 2) . " MBytes/s)");
            $dsp->AddDoubleRow(t('Empfangene Bytes'), $bytesIn);
            $dsp->AddDoubleRow(t('Gesendete Bytes'), $bytesOut);
            $dsp->AddBackButton("index.php?mod=noc&action=details_device&deviceid=" . $row["deviceid"]);
            $dsp->AddContent();
        }//port exists
    
        break;
    
    case 2:
        $func->question(
            t('Bist du sicher, dass du den Status dieses Ports &auml;ndern willst?'),
            "index.php?mod=noc&action=port_details&portid={$_GET["portid"]}&step=3",
            "index.php?mod=noc&action=port_details&portid={$_GET["portid"]}"
        );
    
        break;
    
    
    // 3 stands for change the port "status" (deactivate it, regulate the speed, and so on)
    case 3:
        $port = $db->qry_first("SELECT portid, deviceid, portnr, adminstatus FROM %prefix%noc_ports WHERE portid = %int%", $_GET["portid"]);
        
        if ($port["portid"] == "") {
            $func->error(t('Dieser Port existiert nicht'));
        } else {
            $device = $db->qry_first("SELECT name, readcommunity, writecommunity, ip FROM %prefix%noc_devices WHERE id = %int%", $port['deviceid']);



            $status = $noc->getSNMPValue($device["ip"], $device["readcommunity"], ".1.3.6.1.2.1.2.2.1.7.{$port["portnr"]}");

            switch ($status) {
                case "1":
                case "up":
                case "up(1)":
                    $newstatus = "2";
                    $statusdescr = "<font color=\"red\">" . t('deaktiviert') . "</font>";
                    $statusdb = "down(2)";
                    break;

                case "2":
                case "down":
                case "down(2)":
                    $newstatus = "1";
                    $statusdescr = "<font color=\"green\">" . t('aktiviert') . "</font>";
                    $statusdb = "up(1)";
                    break;
            }

            if ($noc->setSNMPValue($device["ip"], $device["writecommunity"], ".1.3.6.1.2.1.2.2.1.7.{$port["portnr"]}", "i", $newstatus)) {
                $db->qry_first("UPDATE %prefix%noc_ports SET adminstatus=%string% WHERE portid=%int%", $statusdb, $_GET["portid"]);
            
                $func->confirmation(t('Der Portstatus wurde ge&auml;ndert'), "index.php?mod=noc&action=port_details&portid={$_GET["portid"]}");
            } else {
                $func->error(t('Der Port auf konnte nicht ge&auml;ndert werden.HTML_NEWLINE
											 Pr&uuml;fen sie die Einstellung der Write-Community'), "index.php?mod=noc&action=port_details&portid={$_GET["portid"]}");
            }
        }//port exists
        
        break;
}
