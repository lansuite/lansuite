<?php

include_once("modules/noc/class_noc.php");
$noc = new noc();

// STEPS: 1 = Display Form -- 2 = Register Device

// --------------------------------------------------------------------------------------------

switch ($_GET["step"]) {
    // ------------------------------------------------------------------------------------
    // ERROR CHECKING
    case 2:
        if ($_POST["device_caption"] == "") {
            $noc_error['device_caption'] = t('Bitte gib einen Namen f&uuml;r das Device ein');

            $_GET["step"] = 1;
        }
        
        if ($_POST["device_ip"] == "") {
            $noc_error['device_ip'] = t('Bitte gib eine IP-Adresse f&uuml;r das Device ein');
            
            $_GET["step"] = 1;
        } else {
            if (!($func->checkIP($_POST["device_ip"]))) {
                $noc_error['device_ip'] = t('Bitte gib eine <em>g&uuml;ltige</em> IP-Adresse f&uuml;r das Device ein');
            
                $_GET["step"] = 1;
            }
        }
        
                
        if ($_POST["device_write"] == "") {
            $noc_error['device_write'] = t('Bitte gib eine Write-Community f&uuml;r das Device an.');
                
            $_GET["step"] = 1;
        }
        
        if ($_POST["device_read"] == "") {
            $noc_error['device_read'] = t('Bitte gib eine Read-Community f&uuml;r das Device an.');
                            
            $_GET["step"] = 1;
        }
        
        break;
} // END SWITCH I

// -------------------------------------------------------------------------------------------

switch ($_GET["step"]) {
    // ------------------------------------------------------------------------------------
    // Display Form
    default:
    case 1:
        $dsp->NewContent(t('Device hinzuf&uuml;gen'), t('Um einen Device zum NOC hinzuzuf&uuml;gen, f&uuml;lle bitte
				         		  das folgende Formular vollst&auml;ndig aus.HTML_NEWLINEF&uuml;r das Feld Name
              					   stehen 30 Zeichen zur Verf&uuml;gung. '));
        $dsp->SetForm("index.php?mod=noc&action=add_device&step=2", "noc");
        $dsp->AddTextFieldRow("device_caption", t('Name'), $_POST['device_caption'], $noc_error['device_caption']);
        $dsp->AddTextFieldRow("device_ip", t('IP-Adresse'), $_POST['device_ip'], $noc_error['device_ip']);
        $dsp->AddTextFieldRow("device_read", t('Read-Community'), $_POST['device_read'], $noc_error['device_read']);
        $dsp->AddTextFieldRow("device_write", t('Write-Community'), $_POST['device_write'], $noc_error['device_write']);
        
        $dsp->AddFormSubmitRow(t('Hinzufügen'));
        $dsp->AddBackButton("index.php?mod=noc", "noc");
        $dsp->AddContent();
    
        unset($noc_error);
        break;
    
    // ------------------------------------------------------------------------------------
    // Store Everything, print confirmation
    case 2:
        if ($noc->checkSNMPDevice($_POST["device_ip"], $_POST["device_read"]) != 1) {
            $func->error(t('HTML_NEWLINEDas Device konnte nicht erreicht werden. M&ouml;gl. Ursachen:HTML_NEWLINEHTML_NEWLINE
				      				- Das Device hat keinen StromHTML_NEWLINE
				      				- Das Device hat noch keine IP-AdresseHTML_NEWLINE
				      				- Das Device unterst&uuml;tzt kein SNMPHTML_NEWLINE
				      				- Du hast eine falsche Read-Community angegebenHTML_NEWLINE
				      				- Du hast eine falsche IP-Adresse angegebenHTML_NEWLINE
				      				- Du hast vergessen, SNMP am device einzuschaltenHTML_NEWLINE
				      				- Dieses PHP unterst&uuml;tzt kein SNMP, kompilieren sie es mit SNMPHTML_NEWLINE
				      				&nbsp; &nbsp;oder laden sie sich ein vorkompiliertes PHP mit SNMP vonHTML_NEWLINE
				      				&nbsp; &nbsp;<a href="http://de.php.net">Der Deutschen PHP Seite</a> herunterHTML_NEWLINE, '), "index.php?mod=noc&action=add_device&step=1");
            break;
        }

        // Fetched Vars from SNMP from tha device
        $sysDescr    = $noc->getSNMPValue($_POST["device_ip"], $_POST["device_read"], ".1.3.6.1.2.1.1.1.0");
        $sysContact    = $noc->getSNMPValue($_POST["device_ip"], $_POST["device_read"], ".1.3.6.1.2.1.1.4.0");
        $sysUpTime    = $noc->getSNMPValue($_POST["device_ip"], $_POST["device_read"], ".1.3.6.1.2.1.1.3.0");
        $sysLocation    = $noc->getSNMPValue($_POST["device_ip"], $_POST["device_read"], ".1.3.6.1.2.1.1.6.0");
        $sysName    = $noc->getSNMPValue($_POST["device_ip"], $_POST["device_read"], ".1.3.6.1.2.1.1.5.0");
        $ports    = $noc->getSNMPwalk($_POST["device_ip"], $_POST["device_read"], ".1.3.6.1.2.1.2.2.1.1");
        $numport = count($ports);
        
        // Store the device into a SQL table
        $add_query = $db->qry("INSERT INTO %prefix%noc_devices SET
     name   = %string%,
     ip   = %string%,
     readcommunity = %string%,
     writecommunity = %string%,
     sysDescr  = %string%,
     sysContact  = %string%,
     sysUpTime  = %string%,
     sysLocation  = %string%,
     sysName  = %string%,
     ports  = %string%
     ", $_POST['device_caption'], $_POST['device_ip'], $_POST['device_read'], $_POST['device_write'], $sysDescr, $sysContact, $sysUpTime, $sysLocation, $sysName, $numport);


        $db->qry("SELECT id, ip, readcommunity FROM %prefix%noc_devices WHERE name=%string%", $_POST["device_caption"]);

        $row = $db->fetch_array();

        for ($ActualPort=0; $ActualPort < count($ports); $ActualPort++) {
            $Port[$ActualPort]["deviceid"] = $row["id"];

            $Port[$ActualPort]["PortNr"] =
                $noc->getSNMPValue($row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.1." . $ports[$ActualPort]);

            $Port[$ActualPort]["BytesIn"] =
                $noc->getSNMPValue($row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.10." . $ports[$ActualPort]);

            $Port[$ActualPort]["BytesOut"] =
                $noc->getSNMPValue($row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.16." . $ports[$ActualPort]);

            $Port[$ActualPort]["Speed"] =
                $noc->getSNMPValue($row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.5." . $ports[$ActualPort]) / (1000 * 1000);

            $Port[$ActualPort]["LinkStatus"] =
                $noc->getSNMPValue($row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.8." . $ports[$ActualPort]);

            $Port[$ActualPort]["AdminStatus"] =
                $noc->getSNMPValue($row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.7." . $ports[$ActualPort]);

            //$Port[$ActualPort]["MACAddress"] =
            //	$noc->getSNMPValue( $row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.6." . $ports[$ActualPort]);

            $Port[$ActualPort]["Type"] =
                $noc->getSNMPValue($row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.3." . $ports[$ActualPort]);

            $Port[$ActualPort]["indexname"] =
                $noc->getSNMPValue($row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.2." . $ports[$ActualPort]);

            //$Port[$ActualPort]["IPAddress"] =
            //	$noc->MACtoIP( $Port[$ActualPort]["MACAddress"], $row["ip"], $row["readcommunity"] );
            
            $Port[$ActualPort]["ifSpecific"] =
                $noc->getSNMPValue($row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.22." . $ports[$ActualPort]);
            
            // For W2k Local-Loopback Ports
            if ($Port[$ActualPort]["LinkStatus"] == "") {
                $Port[$ActualPort]["LinkStatus"]  = "up(1)";
            }
            if ($Port[$ActualPort]["AdminStatus"] == "") {
                $Port[$ActualPort]["AdminStatus"] = "up(1)";
            }
            
            // Type definieren
            switch ($Port[$ActualPort]["Type"]) {
                case "ethernetCsmacd(6)":
                case "6":
                case "ethernetCsmacd":
                    if ($Port[$ActualPort]["ifSpecific"] != "zeroDotZero") {
                        $Port[$ActualPort]["Type"] = 'rj45';
                    }
                    break;
                
                case "fibreChannel(56)":
                case "56":
                case "fibreChannel":
                    if ($Port[$ActualPort]["ifSpecific"] != "zeroDotZero") {
                        $Port[$ActualPort]["Type"] = 'rj45';
                    }
                    
                    break;
                
                default:
                    $Port[$ActualPort]["Type"] = 'system';
                    break;
            }
            // Save it all
                $add_query = $db->qry(
                    "INSERT INTO %prefix%noc_ports SET
       portnr = %string%,
       bytesIn = %string%,
       bytesOut = %string%,
       speed = %string%,
       mac = %string%,
       ip = %string%,
       adminstatus = %string%,
       linkstatus = %string%,
       deviceid = %int%,
       type = %string%,
       indexname = %string%",
                    $Port[$ActualPort]["PortNr"],
                    $Port[$ActualPort]["BytesIn"],
                    $Port[$ActualPort]["BytesOut"],
                    $Port[$ActualPort]["Speed"],
                    $Port[$ActualPort]["MACAddress"],
                    $Port[$ActualPort]["IPAddress"],
                    $Port[$ActualPort]["AdminStatus"],
                    $Port[$ActualPort]["LinkStatus"],
                    $Port[$ActualPort]["deviceid"],
                    $Port[$ActualPort]["Type"],
                    $Port[$ActualPort]["indexname"]
                );
        } // END FOR

        $noc->getMacAddress($row["ip"], $row["readcommunity"], $row["id"], $sysDescr);
            
        if ($add_query == 1) {
            $confirmationtext = t('Das Device wurde erfolgreich eingetragen.');
            
            if ($_POST['device_write'] == "private") {
                $confirmationtext .= t('HTML_NEWLINEHTML_NEWLINE<big>Warnung:</big> Eine Standardm&auml;ßig eingestellte Write-Community ( /\'/private/\'/ ) beinhaltet ein hohes Sicherheitsrisiko!');
            }
            
            if ($_POST['device_read'] == "public") {
                $confirmationtext .= t('HTML_NEWLINEHTML_NEWLINE<big>Warnung:</big> Eine Standardm&auml;ßig eingestellte Read-Community ( /\'/public/\'/ ) beinhaltet ein hohes Sicherheitsrisiko!');
            }
        
            $func->confirmation($confirmationtext, "");
        } else {
            $func->error(t('Device konnte nicht in die Datenbank eingetragen werden.'));
        }
    
        break;
    
    // ---------------------------------------------------------
}
