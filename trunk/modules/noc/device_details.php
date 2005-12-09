<?php

// ---------------------------------------------------------------------

/*	Network Operations Centre
 *
 *	originally based on phpSMITH
 *	
 *
 *	Maintainer: Joachim Garth <josch@one-network.org>
 */
// ---------------------------------------------------------------------

// Get the device details
$db->query("SELECT * from {$config["tables"]["noc_devices"]} WHERE id=" . $_GET["deviceid"]);

if( !$row = $db->fetch_array() ) { 

	$func->error( $lang['noc']['device_not_exist'],"" ); 
	
} else {
	switch ($_GET['step']){
	
		default:

		// Since I'm not going to write $templ["noc"]["show"]["device"]["details"]["info"]["ip"] 10 times I'll just put it on $device_ip
		$device_ip = $row["ip"];
		$readcommunity  = $row["readcommunity"];
		
		// DISPLAYED TEXT
		$templ['noc']['show']['device']['details']['text']['caption'] 		= $lang['noc']['device_caption'];
		$templ['noc']['show']['device']['details']['text']['ip'] 			= $lang['noc']['ip'];
		$templ['noc']['show']['device']['details']['text']['descr']			= $lang['noc']['description'];
		$templ['noc']['show']['device']['details']['text']['contact'] 		= $lang['noc']['contact'];
		$templ['noc']['show']['device']['details']['text']['uptime']		= $lang['noc']['uptime'];
		$templ['noc']['show']['device']['details']['text']['location']		= $lang['noc']['location'];
		$templ['noc']['show']['device']['details']['text']['context']		= $lang['noc']['context'];
		$templ['noc']['show']['device']['details']['text']['active']		= $lang['noc']['port_active'];
		$templ['noc']['show']['device']['details']['text']['inactive']		= $lang['noc']['port_inactive'];
		$templ['noc']['show']['device']['details']['text']['off']			= $lang['noc']['port_off'];


		// DISPLAYED VALUES
		$templ["noc"]["show"]["device"]["details"]["info"]["deviceid"]	= $_GET["deviceid"];
		$templ["noc"]["show"]["device"]["details"]["info"]["ip"]		= $row["ip"];
		$templ["noc"]["show"]["device"]["details"]["info"]["caption"]	= $row["name"];
		$templ["noc"]["show"]["device"]["details"]["info"]["contact"]	= $row["sysContact"];
		$templ["noc"]["show"]["device"]["details"]["info"]["descr"]		= $row["sysDescr"];
		$templ["noc"]["show"]["device"]["details"]["info"]["uptime"]	= $row["sysUpTime"];
		$templ["noc"]["show"]["device"]["details"]["info"]["location"]	= $row["sysLocation"];



		// Choose the right Picture
		If (stristr($row["sysDescr"], "linux"))		{ $ProdPic = "ext_inc/noc/linux.gif";		}
		If (stristr($row["sysDescr"], "XP"))		{ $ProdPic = "ext_inc/noc/winxp.gif";		}
		If (stristr($row["sysDescr"], "Windows 2000"))	{ $ProdPic = "ext_inc/noc/win2000.gif";		}
		If (stristr($row["sysDescr"], "suse"))		{ $ProdPic = "ext_inc/noc/suse.gif";		}
		If (stristr($row["sysDescr"], "sun microsystems")) { $ProdPic = "ext_inc/noc/sun.gif";		}
		If (stristr($row["sysDescr"], "slack"))		{ $ProdPic = "ext_inc/noc/slackware.gif";	}
		If (stristr($row["sysDescr"], "redhat"))	{ $ProdPic = "ext_inc/noc/redhat.gif";		}
		If (stristr($row["sysDescr"], "openbsd"))	{ $ProdPic = "ext_inc/noc/openbsd.gif";		}
		If (stristr($row["sysDescr"], "nortel"))	{ $ProdPic = "ext_inc/noc/nortel.gif";		}
		If (stristr($row["sysDescr"], "netgear"))	{ $ProdPic = "ext_inc/noc/netgear.gif";		}
		If (stristr($row["sysDescr"], "mandrake"))	{ $ProdPic = "ext_inc/noc/mandrake.gif";	}
		If (stristr($row["sysDescr"], "mac"))		{ $ProdPic = "ext_inc/noc/macos.gif";		}
		If (stristr($row["sysDescr"], "longshine"))	{ $ProdPic = "ext_inc/noc/longshine.gif";	}
		If (stristr($row["sysDescr"], "lancom"))	{ $ProdPic = "ext_inc/noc/lancom.gif";		}
		If (stristr($row["sysDescr"], "kingston"))	{ $ProdPic = "ext_inc/noc/kingston.gif";	}
		If (stristr($row["sysDescr"], "hp"))		{ $ProdPic = "ext_inc/noc/hp.gif";		}
		If (stristr($row["sysDescr"], "gentoo"))	{ $ProdPic = "ext_inc/noc/gentoo.gif";		}
		If (stristr($row["sysDescr"], "freebsd"))	{ $ProdPic = "ext_inc/noc/freebsd.gif";		}
		If (stristr($row["sysDescr"], "elsa"))		{ $ProdPic = "ext_inc/noc/elsa.gif";		}
		If (stristr($row["sysDescr"], "d-link"))	{ $ProdPic = "ext_inc/noc/dlink.gif";		}
		If (stristr($row["sysDescr"], "debian"))	{ $ProdPic = "ext_inc/noc/debian.gif";		}
		If (stristr($row["sysDescr"], "cisco"))		{ $ProdPic = "ext_inc/noc/cisco.gif";		}
		If (stristr($row["sysDescr"], "batm"))		{ $ProdPic = "ext_inc/noc/batm.gif";		}
		If (stristr($row["sysDescr"], "allnet"))	{ $ProdPic = "ext_inc/noc/allnet.gif";		}
		If (stristr($row["sysDescr"], "3com"))		{ $ProdPic = "ext_inc/noc/3com.gif";		}

		If (!$ProdPic) { $ProdPic = "ext_inc/noc/switch.gif"; }


		// Set the manufacturer Picture
		$templ["noc"]["show"]["device"]["details"]["control"]["image"] = $ProdPic;

		//Mac-Addressen auslesen
		$noc->getMacAddress($row["ip"], $row["readcommunity"],$row["id"],$row["sysDescr"]);
		
		// Get the Ports and display 'em
		$db->query("SELECT name FROM {$config["tables"]["noc_devices"]} WHERE id=" . $_GET["deviceid"]);

		$row = $db->fetch_array();
		
		// Ports are all saved into 1 template variable
		$templ["noc"]["show"]["device"]["details"]["control"]["ports"] = "<tr align=\"center\">";

		$port_query = $db->query("SELECT portnr, portid, linkstatus, adminstatus, speed, type, indexname FROM {$config["tables"]["noc_ports"]} WHERE deviceid=" . $_GET["deviceid"] . " AND type != 'system' ORDER BY portnr ASC");

		$Portcount = 1;

		while($row = $db->fetch_array($port_query)) {

			$Port["LinkStatus"] = $noc->getSNMPValue( $device_ip, $readcommunity, ".1.3.6.1.2.1.2.2.1.8." . $row["portnr"] );
			$Port["AdminStatus"] =	$noc->getSNMPValue( $device_ip, $readcommunity, ".1.3.6.1.2.1.2.2.1.7." . $row["portnr"] );

			if($Port["LinkStatus"] != $row["linkstatus"]){
					$db->query_first("UPDATE {$config["tables"]["noc_ports"]} SET linkstatus='{$Port["LinkStatus"]}' WHERE portid={$row["portid"]}");
					$row["linkstatus"] = $Port["LinkStatus"];
			}

			if($Port["AdminStatus"] != $row["adminstatus"]){
					$db->query_first("UPDATE {$config["tables"]["noc_ports"]} SET adminstatus='{$Port["AdminStatus"]}' WHERE portid={$row["portid"]}");
					$row["adminstatus"] = $Port["AdminStatus"];
			}

			// LWL Ports are as double as wide as rj45 ports
			If( $row["type"] == "lwl" ) { $colspan = "2"; }
			If( $row["type"] == "rj45" ) { $colspan = "1"; }

			
			switch( $row["linkstatus"] ) {

				case "up(1)":
				case "up":
				case "1";

				If( $row["adminstatus"] == "down(2)" || $row["adminstatus"] == "down" || $row["adminstatus"] == "2") {
						$templ["noc"]["show"]["device"]["details"]["control"]["ports"] .= "<td colspan=\"" . $colspan . "\"><a class=\"menu\" href=\"index.php?mod=noc&action=port_details&portid=" . $row["portid"] . "\"><img alt='' border=0 src=\"base.php?mod=noc_port_picture&type=" . $row["type"] . "&status=failed&speed=" . $row["speed"] . "&unit=MBit&portnr=" . $row["portnr"] . "\" title=\"" . $row['indexname']  . "\"/></a></td>";
				} else {
						$templ["noc"]["show"]["device"]["details"]["control"]["ports"] .= "<td colspan=\"" . $colspan . "\"><a class=\"menu\" href=\"index.php?mod=noc&action=port_details&portid=" . $row["portid"] . "\"><img alt='' border=0 src=\"base.php?mod=noc_port_picture&type=" . $row["type"] . "&status=on&speed=" . $row["speed"] . "&unit=MBit&portnr=" . $row["portnr"] . "\" title=\"" . $row['indexname']  . "\"/></a></td>";				
				}
					
		
				break;

				case "down(2)":
				case "down":
				case "2":

				If( $row["adminstatus"] == "down(2)" || $row["adminstatus"] == "down" || $row["adminstatus"] == "2") {
						$templ["noc"]["show"]["device"]["details"]["control"]["ports"] .= "<td colspan=\"" . $colspan . "\"><a class=\"menu\" href=\"index.php?mod=noc&action=port_details&portid=" . $row["portid"] . "\"><img alt='' border=0 src=\"base.php?mod=noc_port_picture&type=" . $row["type"] . "&status=failed&speed=" . $row["speed"] . "&unit=MBit&portnr=" . $row["portnr"] . "\" title=\"" . $row['indexname']  . "\"/></a></td>";
				} else {
						$templ["noc"]["show"]["device"]["details"]["control"]["ports"] .= "<td colspan=\"" . $colspan . "\"><a class=\"menu\" href=\"index.php?mod=noc&action=port_details&portid=" . $row["portid"] . "\"><img alt='' border=0 src=\"base.php?mod=noc_port_picture&type=" . $row["type"] . "&status=off&speed=" . $row["speed"] . "&unit=MBit&portnr=" . $row["portnr"] . "\" title=\"" . $row['indexname']  . "\"/></a></td>";						
				}

				break;

				default:
				//$templ["noc"]["show"]["device"]["details"]["control"]["ports"] .= "<td colspan=\"" . $colspan . "\"><img border=0 src=\"base.php?mod=noc_port_picture&type=" . $row["type"] . "&status=off&speed=" . $row["speed"] . "&unit=MBit&portnr=" . $row["portnr"] . "\"></td>";
				break;

			} // END SWITCH($row["linkstatus"])

			If( $Portcount%16 == 0 ) { $templ["noc"]["show"]["device"]["details"]["control"]["ports"] .= "</tr><tr align=\"center\">"; }
			$Portcount++;

		} // END WHILE

		$templ["noc"]["show"]["device"]["details"]["control"]["ports"] .= "</tr>";
		$templ['noc']['show']['device']['details']['control']['changebutton'] = $dsp->FetchButton("index.php?mod=noc&action=show_device","back");
		$templ['noc']['show']['device']['details']['control']['changebutton'] .= $dsp->FetchButton("index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"] ."&step=2","edit");
	
		// DISPLAY TEMPLATE
		// eval("\$templ['index']['info']['content'] .= \"". $func->gettemplate("noc_show_device_details")."\";");
		$dsp->AddModTpl("noc","device_details");
		$dsp->AddContent();
		break;
		
		
		case 2:
		$dsp->NewContent($lang['noc']['ports_caption'],$lang['noc']['ports_subcaption']);
		$dsp->SetForm("index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"] ."&step=3");

		$db->query("SELECT portnr, portid, linkstatus, adminstatus, speed, type, indexname FROM {$config["tables"]["noc_ports"]} WHERE deviceid=" . $_GET["deviceid"] . " AND type != 'system' ORDER BY portnr ASC");

		$Portcount = 1;
		$tmp_noc = "</td></tr><tr align=\"center\">";
		while($row = $db->fetch_array()) {


			// LWL Ports are as double as wide as rj45 ports
			If( $row["type"] == "lwl" ) { $colspan = "2"; }
			If( $row["type"] == "rj45" ) { $colspan = "1"; }

			switch( $row["linkstatus"] ) {

				case "up(1)":
				case "up":
				case "1":

				If( $row["adminstatus"] == "down(2)" || $row["adminstatus"] == "down" ) {
					$tmp_noc .= "<td colspan=\"" . $colspan . "\" ><input name='noc[]' type='checkbox' value='". $row["portnr"] ."'/><img alt='' border=0 src=\"base.php?mod=noc_port_picture&type=" . $row["type"] . "&status=failed&speed=" . $row["speed"] . "&unit=MBit&portnr=" . $row["portnr"] . "\" title=\"" . $row['indexname']  . "\"/></td>\n";
				} else {
					$tmp_noc .= "<td colspan=\"" . $colspan . "\"><input name='noc[]' type='checkbox' value='". $row["portnr"] ."'/><img alt='' border=0 src=\"base.php?mod=noc_port_picture&type=" . $row["type"] . "&status=on&speed=" . $row["speed"] . "&unit=MBit&portnr=" . $row["portnr"] . "\" title=\"" . $row['indexname']  . "\"/></td>\n";
				}				
				
				break;

				case "down(2)":
				case "down":
				case "2":

				If( $row["adminstatus"] == "down(2)" || $row["adminstatus"] == "down" ) {
					$tmp_noc .= "<td colspan=\"" . $colspan . "\" ><input name='noc[]' type='checkbox' value='". $row["portnr"] ."'/><img alt='' border=0 src=\"base.php?mod=noc_port_picture&type=" . $row["type"] . "&status=failed&speed=" . $row["speed"] . "&unit=MBit&portnr=" . $row["portnr"] . "\" title=\"" . $row['indexname']  . "\"/></td>\n";
				} else {
					$tmp_noc .= "<td colspan=\"" . $colspan . "\"><input name='noc[]' type='checkbox' value='". $row["portnr"] ."'/><img alt='' border=0 src=\"base.php?mod=noc_port_picture&type=" . $row["type"] . "&status=off&speed=" . $row["speed"] . "&unit=MBit&portnr=" . $row["portnr"] . "\" title=\"" . $row['indexname']  . "\"/></td>\n";
				}

				break;

			} // END SWITCH($row["linkstatus"])

			If( $Portcount%16 == 0 ) { $tmp_noc .= "</tr><tr align=\"center\">"; }
			$Portcount++;

		} // END WHILE
		
		$dsp->AddSingleRow($tmp_noc . "<td>");
		$dsp->AddFormSubmitRow("edit");
		$dsp->AddContent();			
		break;
		
		case 3:
			if(is_array($_POST['noc'])){
				foreach ($_POST['noc'] as $noc_data){
					$ports .= $noc_data . HTML_NEWLINE;	
				}
				$func->question($lang['noc']['activate_ports'] . HTML_NEWLINE . $ports,"index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"] ."&step=4","index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"]. "&step=2");
			}elseif (isset($_POST['noc'])){
				$ports = $_POST['noc'];
				$func->question($lang['noc']['activate_ports'] . HTML_NEWLINE . $ports,"index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"] ."&step=4","index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"]. "&step=2");
			}else{
				$func->error($lang['noc']['no_ports'],"index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"] ."&step=2");
			}
			$_SESSION['noc_ports'] = $_POST['noc'];
		break;

		case 4:
	
		if(is_array($_SESSION['noc_ports'])){
			foreach ($_SESSION['noc_ports'] as $noc_data){
				$device = $db->query_first( "SELECT name, readcommunity, writecommunity, ip FROM {$config["tables"]["noc_devices"]} WHERE id=" . $noc_data );



				$status = $noc->getSNMPValue( $row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.7.{$noc_data}" );

				switch( $status ) {

					case "1":
					case "up":
					case "up(1)":	$newstatus = "2"; $statusdescr = "<font color=\"red\">" . $lang['noc']['port_inactived'] . "</font>"; $statusdb = "down(2)";
					break;

					case "2":
					case "down":
					case "down(2)":	$newstatus = "1"; $statusdescr = "<font color=\"green\">" . $lang['noc']['port_actived'] . "</font>"; $statusdb = "up(1)"; 
					break;

				}

				if($noc->setSNMPValue( $row["ip"], $row["writecommunity"], ".1.3.6.1.2.1.2.2.1.7.{$noc_data}", "i", $newstatus )){

					$db->query_first("UPDATE {$config["tables"]["noc_ports"]} SET adminstatus='$statusdb' WHERE portnr=" . $noc_data . " AND deviceid=" . $_GET['deviceid']);

					$text .= $noc_data . $lang['noc']['port_changed'] . HTML_NEWLINE;
				}else{
					$text .= $noc_data . $lang['noc']['change_port_error'] . HTML_NEWLINE;
				}
			}
				
			if($noc_error == 1){
				$func->error($text, "index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"]);
			}else{
				$func->confirmation($text, "index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"]);
			}
				
			}elseif (isset($_SESSION['noc_ports'])){
				$device = $db->query_first( "SELECT name, readcommunity, writecommunity, ip FROM {$config["tables"]["noc_devices"]} WHERE id=" . $_SESSION['noc_ports']);



				$status = $noc->getSNMPValue( $row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.7.{$_SESSION['noc_ports']}" );

				switch( $status ) {
					case "1":
					case "up":
					case "up(1)":	$newstatus = "2"; $statusdescr = "<font color=\"red\">" . $lang['noc']['port_inactived'] . "</font>"; $statusdb = "down(2)";
					break;

					case "2":
					case "down":
					case "down(2)":	$newstatus = "1"; $statusdescr = "<font color=\"green\">" . $lang['noc']['port_actived'] . "</font>"; $statusdb = "up(1)"; 
					break;

				}

				if($noc->setSNMPValue( $row["ip"], $row["writecommunity"], ".1.3.6.1.2.1.2.2.1.7.{$_SESSION['noc_ports']}", "i", $newstatus )){

					$db->query_first("UPDATE {$config["tables"]["noc_ports"]} SET adminstatus='$statusdb' WHERE portnr=" . $noc_data . " AND deviceid=" . $_GET['deviceid']);

					$text .= $noc_data . $lang['noc']['port_changed'] . HTML_NEWLINE;
				}else{
					$text .= $noc_data . $lang['noc']['change_port_error'] . HTML_NEWLINE;
					$noc_error = 1;
				}
			
				if($noc_error == 1){
					$func->error($text, "index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"]);
				}else{
					$func->confirmation($text, "index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"]);
				}
			}else{
				$func->error($lang['noc']['no_ports'],"index.php?mod=noc&action=details_device&deviceid=". $_GET["deviceid"] ."&step=2");
			}
		break;
	
	}
} // If ( !$row );

// ---------------------------------------------------------------------

?>
