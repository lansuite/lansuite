<?php

include_once("modules/noc/class_noc.php");
$noc = new noc();

switch ($_GET["step"]) {
    // ------------------------------------------------------------------------------------
    // ERROR CHECKING
    case 3:
        if ($_POST["device_caption"] == "") {
            $noc_error['device_caption'] = t('Bitte gib einen Namen f&uuml;r das Device ein');

            $_GET["step"] = 2;
        }
        
        if ($_POST["device_ip"] == "") {
            $noc_error['device_ip'] = t('Bitte gib eine IP-Adresse f&uuml;r das Device ein');
            
            $_GET["step"] = 2;
        } else {
            if (!($func->checkIP($_POST["device_ip"]))) {
                $noc_error['device_ip'] = t('Bitte gib eine <em>g&uuml;ltige</em> IP-Adresse f&uuml;r das Device ein');
            
                $_GET["step"] = 2;
            }
        }
        
                
        if ($_POST["device_write"] == "") {
            $noc_error['device_write'] = t('Bitte gib eine Write-Community f&uuml;r das Device an.');
                
            $_GET["step"] = 2;
        }
        
        if ($_POST["device_read"] == "") {
            $noc_error['device_read'] = t('Bitte gib eine Read-Community f&uuml;r das Device an.');
                            
            $_GET["step"] = 2;
        }
        
        break;
} // END SWITCH I

// ----------------------------------------------------------------------------------------------------------

switch ($_GET["step"]) {
    // --------------------------------------------------------------------------------------------------
    // Display Form
    default:
    case 1:
        include_once('modules/noc/search.inc.php');
        break;
        
    case 2:
        $db->qry("SELECT * FROM %prefix%noc_devices WHERE id = %int%", $_GET["deviceid"]);
        
        if ($row = $db->fetch_array()) {
            $deviceid = $row["id"];
            $device_ip = $row["ip"];
            $device_caption = $row["name"];
            $device_read = $row["readcommunity"];
            $device_write = $row["writecommunity"];

            $dsp->NewContent(t('Device hinzuf&uuml;gen'), t('Um einen Device zum NOC hinzuzuf&uuml;gen, f&uuml;lle bitte
				         		  das folgende Formular vollst&auml;ndig aus.HTML_NEWLINEF&uuml;r das Feld Name
              					   stehen 30 Zeichen zur Verf&uuml;gung. '));
            $dsp->SetForm("index.php?mod=noc&action=change_device&step=3&deviceid=" . $_GET["deviceid"], "noc");
            $dsp->AddTextFieldRow("device_caption", t('Name'), $device_caption, $noc_error['device_caption']);
            $dsp->AddTextFieldRow("device_ip", t('IP-Adresse'), $device_ip, $noc_error['device_ip']);
            $dsp->AddTextFieldRow("device_read", t('Read-Community'), $device_read, $noc_error['device_read']);
            $dsp->AddTextFieldRow("device_write", t('Write-Community'), $device_write, $noc_error['device_write']);
        
            $dsp->AddFormSubmitRow(t('Ändern'));
            $dsp->AddBackButton("index.php?mod=noc", "noc");
            $dsp->AddContent();
    
            unset($noc_error);
        } else {
            $func->error(t('Das gew&auml;hlte Device existiert nicht'));
        }


        break;
    
    // --------------------------------------------------------------------------------------------------
    // Check and Update Device Data
    case 3:
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
				      				&nbsp; &nbsp;<a href="http://de.php.net">Der Deutschen PHP Seite</a> herunterHTML_NEWLINE, '));
            break;
        } // END IF
        
        // ------------------------------------------------------------------------------------------
    
        // U p d a t e it, not delete and reinsert it.
        $add_query = $db->qry("UPDATE %prefix%noc_devices SET
         name = %string%,
         ip = %string%,
         readcommunity = %string%,
         writecommunity = %string%
         WHERE id = %int%", $_POST['device_caption'], $_POST['device_ip'], $_POST['device_read'], $_POST['device_write'], $_GET["deviceid"]);
        
        if ($add_query == 1) {
            $func->confirmation(t('Das Device wurde erfolgreich ge&auml;ndert.'));
        } else {
            $func->error(t('Das Device konnte nicht ge&auml;ndert werden.'));
        } // END IF
                
    
        break;
} // END SWITCH II

// ----------------------------------------------------------------------------------------------------------
