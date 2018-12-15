<?php

include_once("modules/noc/class_noc.php");
$noc = new noc();


$filepath = "design/images/";

// Get the device details
$db->qry("SELECT * from %prefix%noc_devices WHERE id = %int%", $_GET["deviceid"]);

if (!$row = $db->fetch_array()) {
    $func->error(t('Das gew&auml;hlte Device existiert nicht'));
} else {
    switch ($_GET['step']) {
        default:
            $device_ip = $row["ip"];
            $readcommunity  = $row["readcommunity"];
        
        // DISPLAYED TEXT
            $smarty->assign('caption', t('Name'));
            $smarty->assign('ip', t('IP-Adresse'));
            $smarty->assign('descr', t('Beschreibung'));
            $smarty->assign('contact', t('Kontaktadresse'));
            $smarty->assign('uptime', t('Laufzeit'));
            $smarty->assign('location', t('Standort'));
            $smarty->assign('context', t('Lengende'));
            $smarty->assign('active', t('Aktiv'));
            $smarty->assign('inactive', t('Inaktiv'));
            $smarty->assign('off', t('Ausgeschaltet'));

        // DISPLAYED VALUES
            $smarty->assign('deviceid', $_GET["deviceid"]);
            $smarty->assign('ip', $row["ip"]);
            $smarty->assign('caption', $row["name"]);
            $smarty->assign('contact', $row["sysContact"]);
            $smarty->assign('descr', $row["sysDescr"]);
            $smarty->assign('uptime', $row["sysUpTime"]);
            $smarty->assign('location', $row["sysLocation"]);


        // Choose the right Picture
            if (stristr($row["sysDescr"], "linux")) {
                $ProdPic = "ext_inc/noc/linux.gif";
            }
            if (stristr($row["sysDescr"], "XP")) {
                $ProdPic = "ext_inc/noc/winxp.gif";
            }
            if (stristr($row["sysDescr"], "Windows 2000")) {
                $ProdPic = "ext_inc/noc/win2000.gif";
            }
            if (stristr($row["sysDescr"], "suse")) {
                $ProdPic = "ext_inc/noc/suse.gif";
            }
            if (stristr($row["sysDescr"], "sun microsystems")) {
                $ProdPic = "ext_inc/noc/sun.gif";
            }
            if (stristr($row["sysDescr"], "slack")) {
                $ProdPic = "ext_inc/noc/slackware.gif";
            }
            if (stristr($row["sysDescr"], "redhat")) {
                $ProdPic = "ext_inc/noc/redhat.gif";
            }
            if (stristr($row["sysDescr"], "openbsd")) {
                $ProdPic = "ext_inc/noc/openbsd.gif";
            }
            if (stristr($row["sysDescr"], "nortel")) {
                $ProdPic = "ext_inc/noc/nortel.gif";
            }
            if (stristr($row["sysDescr"], "netgear")) {
                $ProdPic = "ext_inc/noc/netgear.gif";
            }
            if (stristr($row["sysDescr"], "mandrake")) {
                $ProdPic = "ext_inc/noc/mandrake.gif";
            }
            if (stristr($row["sysDescr"], "mac")) {
                $ProdPic = "ext_inc/noc/macos.gif";
            }
            if (stristr($row["sysDescr"], "longshine")) {
                $ProdPic = "ext_inc/noc/longshine.gif";
            }
            if (stristr($row["sysDescr"], "lancom")) {
                $ProdPic = "ext_inc/noc/lancom.gif";
            }
            if (stristr($row["sysDescr"], "kingston")) {
                $ProdPic = "ext_inc/noc/kingston.gif";
            }
            if (stristr($row["sysDescr"], "hp")) {
                $ProdPic = "ext_inc/noc/hp.gif";
            }
            if (stristr($row["sysDescr"], "gentoo")) {
                $ProdPic = "ext_inc/noc/gentoo.gif";
            }
            if (stristr($row["sysDescr"], "freebsd")) {
                $ProdPic = "ext_inc/noc/freebsd.gif";
            }
            if (stristr($row["sysDescr"], "elsa")) {
                $ProdPic = "ext_inc/noc/elsa.gif";
            }
            if (stristr($row["sysDescr"], "d-link")) {
                $ProdPic = "ext_inc/noc/dlink.gif";
            }
            if (stristr($row["sysDescr"], "debian")) {
                $ProdPic = "ext_inc/noc/debian.gif";
            }
            if (stristr($row["sysDescr"], "cisco")) {
                $ProdPic = "ext_inc/noc/cisco.gif";
            }
            if (stristr($row["sysDescr"], "batm")) {
                $ProdPic = "ext_inc/noc/batm.gif";
            }
            if (stristr($row["sysDescr"], "allnet")) {
                $ProdPic = "ext_inc/noc/allnet.gif";
            }
            if (stristr($row["sysDescr"], "3com")) {
                $ProdPic = "ext_inc/noc/3com.gif";
            }

            if (!$ProdPic) {
                $ProdPic = "ext_inc/noc/switch.gif";
            }


        // Set the manufacturer Picture
            $smarty->assign('image', $ProdPic);

        //Mac-Addressen auslesen
            $noc->getMacAddress($row["ip"], $row["readcommunity"], $row["id"], $row["sysDescr"]);
        
        // Get the Ports and display 'em
            $db->qry("SELECT name FROM %prefix%noc_devices WHERE id = %int%", $_GET["deviceid"]);

            $row = $db->fetch_array();
        
        // Ports are all saved into 1 template variable
            $ports = "<tr align=\"center\">";

            $port_query = $db->qry("SELECT portnr, portid, linkstatus, adminstatus, speed, type, indexname FROM %prefix%noc_ports WHERE deviceid = %int% AND type != 'system' ORDER BY portnr ASC", $_GET["deviceid"]);

            $Portcount = 1;

            while ($row = $db->fetch_array($port_query)) {
                $Port["LinkStatus"] = $noc->getSNMPValue($device_ip, $readcommunity, ".1.3.6.1.2.1.2.2.1.8." . $row["portnr"]);
                $Port["AdminStatus"] =    $noc->getSNMPValue($device_ip, $readcommunity, ".1.3.6.1.2.1.2.2.1.7." . $row["portnr"]);

                if ($Port["LinkStatus"] != $row["linkstatus"]) {
                    $db->qry_first("UPDATE %prefix%noc_ports SET linkstatus=%string% WHERE portid=%int%", $Port["LinkStatus"], $row["portid"]);
                    $row["linkstatus"] = $Port["LinkStatus"];
                }

                if ($Port["AdminStatus"] != $row["adminstatus"]) {
                    $db->qry_first("UPDATE %prefix%noc_ports SET adminstatus=%string% WHERE portid=%int%", $Port["AdminStatus"], $row["portid"]);
                    $row["adminstatus"] = $Port["AdminStatus"];
                }

                // LWL Ports are as double as wide as rj45 ports
                if ($row["type"] == "lwl") {
                    $colspan = "2";
                }
                if ($row["type"] == "rj45") {
                    $colspan = "1";
                }

            
                switch ($row["linkstatus"]) {
                    case "up(1)":
                    case "up":
                    case "1":
                        if ($row["adminstatus"] == "down(2)" || $row["adminstatus"] == "down" || $row["adminstatus"] == "2") {
                            if (file_exists($filepath . "noc_port_" . $row["type"] . "_failed.png")) {
                                $port_pic = $filepath . "noc_port_" . $row["type"] . "_failed.png";
                            } else {
                                $port_pic = "index.php?mod=noc&action=port_picture&design=base&type=" . $row["type"] . "&status=failed&speed=" . $row["speed"] . "&unit=MBit&portnr=" . $row["portnr"] . "\" title=\"" . $row['indexname'];
                            }
                                $ports .= "<td colspan=\"" . $colspan . "\"><a class=\"menu\" href=\"index.php?mod=noc&action=port_details&portid=" . $row["portid"] . "\"><img alt='' border=0 src=\"$port_pic\"/></a></td>";
                        } else {
                            if (file_exists($filepath . "noc_port_" . $row["type"] . "_on.png")) {
                                $port_pic = $filepath . "noc_port_" . $row["type"] . "_on.png";
                            } else {
                                $port_pic = "index.php?mod=noc&action=port_picture&design=base&type=" . $row["type"] . "&status=on&speed=" . $row["speed"] . "&unit=MBit&portnr=" . $row["portnr"] . "\" title=\"" . $row['indexname'];
                            }
                                $ports .= "<td colspan=\"" . $colspan . "\"><a class=\"menu\" href=\"index.php?mod=noc&action=port_details&portid=" . $row["portid"] . "\"><img alt='' border=0 src=\"$port_pic\"/></a></td>";
                        }
                    
        
                        break;

                    case "down(2)":
                    case "down":
                    case "2":
                        if ($row["adminstatus"] == "down(2)" || $row["adminstatus"] == "down" || $row["adminstatus"] == "2") {
                            if (file_exists($filepath . "noc_port_" . $row["type"] . "_failed.png")) {
                                $port_pic = $filepath . "noc_port_" . $row["type"] . "_failed.png";
                            } else {
                                $port_pic = "index.php?mod=noc&action=port_picture&design=base&type=" . $row["type"] . "&status=failed&speed=" . $row["speed"] . "&unit=MBit&portnr=" . $row["portnr"] . "\" title=\"" . $row['indexname'];
                            }
                                $ports .= "<td colspan=\"" . $colspan . "\"><a class=\"menu\" href=\"index.php?mod=noc&action=port_details&portid=" . $row["portid"] . "\"><img alt='' border=0 src=\"$port_pic\"/></a></td>";
                        } else {
                            if (file_exists($filepath . "noc_port_" . $row["type"] . "_off.png")) {
                                $port_pic = $filepath . "noc_port_" . $row["type"] . "_off.png";
                            } else {
                                $port_pic = "index.php?mod=noc&action=port_picture&design=base&type=" . $row["type"] . "&status=failed&speed=" . $row["speed"] . "&unit=MBit&portnr=" . $row["portnr"] . "\" title=\"" . $row['indexname'];
                            }
                                $ports .= "<td colspan=\"" . $colspan . "\"><a class=\"menu\" href=\"index.php?mod=noc&action=port_details&portid=" . $row["portid"] . "\"><img alt='' border=0 src=\"$port_pic\"/></a></td>";
                        }

                        break;

                    default:
                    //$ports .= "<td colspan=\"" . $colspan . "\"><img border=0 src=\"index.php?mod=noc&action=port_picture&design=base&type=" . $row["type"] . "&status=off&speed=" . $row["speed"] . "&unit=MBit&portnr=" . $row["portnr"] . "\"></td>";
                        break;
                } // END SWITCH($row["linkstatus"])

                if ($Portcount%16 == 0) {
                    $ports .= "</tr><tr align=\"center\">";
                }
                $Portcount++;
            } // END WHILE

            $ports .= "</tr>";
            $smarty->assign('ports', $ports);
            $changebutton = $dsp->FetchSpanButton(t('Zurück'), "index.php?mod=noc&action=show_device");
            $changebutton .= $dsp->FetchSpanButton(t('Editieren'), "index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"] ."&step=2");
            $smarty->assign('changebutton', $changebutton);
    
        // DISPLAY TEMPLATE
            $dsp->AddSmartyTpl('device_details', 'noc');
            break;
        
        
        case 2:
            $dsp->NewContent(t('Portstatus &auml;ndern'), t('Gib bitte alle Ports an die du &auml;ndern willst'));
            $dsp->SetForm("index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"] ."&step=3");

            $db->qry("SELECT portnr, portid, linkstatus, adminstatus, speed, type, indexname FROM %prefix%noc_ports WHERE deviceid = %int% AND type != 'system' ORDER BY portnr ASC", $_GET["deviceid"]);

            $Portcount = 1;
            $tmp_noc = "</td></tr><tr align=\"center\">";
            while ($row = $db->fetch_array()) {
                // LWL Ports are as double as wide as rj45 ports
                if ($row["type"] == "lwl") {
                    $colspan = "2";
                }
                if ($row["type"] == "rj45") {
                    $colspan = "1";
                }

                switch ($row["linkstatus"]) {
                    case "up(1)":
                    case "up":
                    case "1":
                        if ($row["adminstatus"] == "down(2)" || $row["adminstatus"] == "down") {
                            if (file_exists($filepath . "noc_port_" . $row["type"] . "_failed.png")) {
                                $port_pic = $filepath . "noc_port_" . $row["type"] . "_failed.png";
                            } else {
                                $port_pic = "index.php?mod=noc&action=port_picture&design=base&type=" . $row["type"] . "&status=failed&speed=" . $row["speed"] . "&unit=MBit&portnr=" . $row["portnr"] . "\" title=\"" . $row['indexname'];
                            }
                                $tmp_noc .= "<td colspan=\"" . $colspan . "\" ><input name='noc[]' type='checkbox' value='". $row["portnr"] ."'/><img alt='' border=0 src=\"$port_pic\"/></td>\n";
                        } else {
                            if (file_exists($filepath . "noc_port_" . $row["type"] . "_on.png")) {
                                $port_pic = $filepath . "noc_port_" . $row["type"] . "_on.png";
                            } else {
                                $port_pic = "index.php?mod=noc&action=port_picture&design=base&type=" . $row["type"] . "&status=on&speed=" . $row["speed"] . "&unit=MBit&portnr=" . $row["portnr"] . "\" title=\"" . $row['indexname'];
                            }
                                $tmp_noc .= "<td colspan=\"" . $colspan . "\"><input name='noc[]' type='checkbox' value='". $row["portnr"] ."'/><img alt='' border=0 src=\"$port_pic\"/></td>\n";
                        }
                
                        break;

                    case "down(2)":
                    case "down":
                    case "2":
                        if ($row["adminstatus"] == "down(2)" || $row["adminstatus"] == "down") {
                            if (file_exists($filepath . "noc_port_" . $row["type"] . "_failed.png")) {
                                $port_pic = $filepath . "noc_port_" . $row["type"] . "_failed.png";
                            } else {
                                $port_pic = "index.php?mod=noc&action=port_picture&design=base&type=" . $row["type"] . "&status=failed&speed=" . $row["speed"] . "&unit=MBit&portnr=" . $row["portnr"] . "\" title=\"" . $row['indexname'];
                            }
                                $tmp_noc .= "<td colspan=\"" . $colspan . "\" ><input name='noc[]' type='checkbox' value='". $row["portnr"] ."'/><img alt='' border=0 src=\"$port_pic\"/></td>\n";
                        } else {
                            if (file_exists($filepath . "noc_port_" . $row["type"] . "_off.png")) {
                                $port_pic = $filepath . "noc_port_" . $row["type"] . "_off.png";
                            } else {
                                $port_pic = "index.php?mod=noc&action=port_picture&design=base&type=" . $row["type"] . "&status=off&speed=" . $row["speed"] . "&unit=MBit&portnr=" . $row["portnr"] . "\" title=\"" . $row['indexname'];
                            }

                                $tmp_noc .= "<td colspan=\"" . $colspan . "\"><input name='noc[]' type='checkbox' value='". $row["portnr"] ."'/><img alt='' border=0 src=\"$port_pic\"/></td>\n";
                        }

                        break;
                } // END SWITCH($row["linkstatus"])

                if ($Portcount%16 == 0) {
                    $tmp_noc .= "</tr><tr align=\"center\">";
                }
                $Portcount++;
            } // END WHILE
        
            $dsp->AddSingleRow($tmp_noc . "<td>");
            $dsp->AddFormSubmitRow(t('Editieren'));
            break;
        
        case 3:
            if (is_array($_POST['noc'])) {
                foreach ($_POST['noc'] as $noc_data) {
                    $ports .= $noc_data . HTML_NEWLINE;
                }
                $func->question(t('Willst du folgende Ports &auml;ndern?') . HTML_NEWLINE . $ports, "index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"] ."&step=4", "index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"]. "&step=2");
            } elseif (isset($_POST['noc'])) {
                $ports = $_POST['noc'];
                $func->question(t('Willst du folgende Ports &auml;ndern?') . HTML_NEWLINE . $ports, "index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"] ."&step=4", "index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"]. "&step=2");
            } else {
                $func->error(t('Keine Ports ausgew&auml;hlt'), "index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"] ."&step=2");
            }
            $_SESSION['noc_ports'] = $_POST['noc'];
            break;

        case 4:
            if (is_array($_SESSION['noc_ports'])) {
                foreach ($_SESSION['noc_ports'] as $noc_data) {
                    $device = $db->qry_first("SELECT name, readcommunity, writecommunity, ip FROM %prefix%noc_devices WHERE id = %int%", $noc_data);



                    $status = $noc->getSNMPValue($row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.7.{$noc_data}");

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

                    if ($noc->setSNMPValue($row["ip"], $row["writecommunity"], ".1.3.6.1.2.1.2.2.1.7.{$noc_data}", "i", $newstatus)) {
                        $db->qry_first("UPDATE %prefix%noc_ports SET adminstatus=%string% WHERE portnr=%string% AND deviceid=%int%", $statusdb, $noc_data, $_GET['deviceid']);

                        $text .= $noc_data . t('Der Portstatus wurde ge&auml;ndert') . HTML_NEWLINE;
                    } else {
                        $text .= $noc_data . t('Der Port auf konnte nicht ge&auml;ndert werden.' . HTML_NEWLINE . '
											 Pr&uuml;fen sie die Einstellung der Write-Community') . HTML_NEWLINE;
                    }
                }
                
                if ($noc_error == 1) {
                    $func->error($text, "index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"]);
                } else {
                    $func->confirmation($text, "index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"]);
                }
            } elseif (isset($_SESSION['noc_ports'])) {
                $device = $db->qry_first("SELECT name, readcommunity, writecommunity, ip FROM %prefix%noc_devices WHERE id = %int%", $_SESSION['noc_ports']);



                $status = $noc->getSNMPValue($row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.7.{$_SESSION['noc_ports']}");

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

                if ($noc->setSNMPValue($row["ip"], $row["writecommunity"], ".1.3.6.1.2.1.2.2.1.7.{$_SESSION['noc_ports']}", "i", $newstatus)) {
                    $db->qry_first("UPDATE %prefix%noc_ports SET adminstatus=%string% WHERE portnr=%string% AND deviceid=%int%", $statusdb, $noc_data, $_GET['deviceid']);

                    $text .= $noc_data . t('Der Portstatus wurde ge&auml;ndert') . HTML_NEWLINE;
                } else {
                    $text .= $noc_data . t('Der Port auf konnte nicht ge&auml;ndert werden.' . HTML_NEWLINE . '
											 Pr&uuml;fen sie die Einstellung der Write-Community') . HTML_NEWLINE;
                    $noc_error = 1;
                }
            
                if ($noc_error == 1) {
                    $func->error($text, "index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"]);
                } else {
                    $func->confirmation($text, "index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"]);
                }
            } else {
                $func->error(t('Keine Ports ausgew&auml;hlt'), "index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"] ."&step=2");
            }
            break;
    }
} // If ( !$row );

// ---------------------------------------------------------------------
