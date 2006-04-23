<?php

include_once( "modules/noc/class_noc.php" );
$noc = new noc();

// STEPS: 1 = Display Form -- 2 = Register Device

// --------------------------------------------------------------------------------------------

switch( $_GET["step"] ) {

	// ------------------------------------------------------------------------------------
	// ERROR CHECKING
	case 2:
	
		If( $_POST["device_caption"] == "" ) { 

			$noc_error['device_caption'] = $lang['noc']['device_caption_error'];

			$_GET["step"] = 1;
			
		}
		
		If( $_POST["device_ip"] == "" ) {
		
			$noc_error['device_ip'] = $lang['noc']['device_ip_error'];
			
			$_GET["step"] = 1;
			
		} else {
		
			If( !( $func->checkIP( $_POST["device_ip"] ) ) ) {
				
				$noc_error['device_ip'] = $lang['noc']['device_ipcheck_error'];
			
				$_GET["step"] = 1;
					
			}
		
		}
		
				
		If( $_POST["device_write"] == "" ) {
			
			$noc_error['device_write'] = $lang['noc']['device_write_error'];
				
			$_GET["step"] = 1;
			
		}
		
		If( $_POST["device_read"] == "" ) {
		
			$noc_error['device_read'] = $lang['noc']['device_read_error'];
							
			$_GET["step"] = 1;
			
		}
		
		break;
		
} // END SWITCH I

// -------------------------------------------------------------------------------------------

switch( $_GET["step"] ) {
	
	// ------------------------------------------------------------------------------------
	// Display Form
	default:	
	case 1:	
		
		$dsp->NewContent($lang['noc']['caption'],$lang['noc']['subcaption']);
		$dsp->SetForm("index.php?mod=noc&action=add_device&step=2","noc");
		$dsp->AddTextFieldRow("device_caption",$lang['noc']['device_caption'],$_POST['device_caption'],$noc_error['device_caption']);
		$dsp->AddTextFieldRow("device_ip",$lang['noc']['device_ip'],$_POST['device_ip'],$noc_error['device_ip']);
		$dsp->AddTextFieldRow("device_read",$lang['noc']['device_read'],$_POST['device_read'],$noc_error['device_read']);
		$dsp->AddTextFieldRow("device_write",$lang['noc']['device_write'],$_POST['device_write'],$noc_error['device_write']);
		
		$dsp->AddFormSubmitRow("add");
		$dsp->AddBackButton("index.php?mod=noc", "noc"); 
		$dsp->AddContent();
	
		unset($noc_error);
	break;
	
	// ------------------------------------------------------------------------------------
	// Store Everything, print confirmation
	case 2:

		If( $noc->checkSNMPDevice( $_POST["device_ip"], $_POST["device_read"] ) != 1 ) { 
			
			$func->error($lang['noc']['connect_error'],"index.php?mod=noc&action=add_device&step=1");
			break;
		}

		// Fetched Vars from SNMP from tha device
		$sysDescr 	= $noc->getSNMPValue($_POST["device_ip"], $_POST["device_read"], ".1.3.6.1.2.1.1.1.0"	);
		$sysContact 	= $noc->getSNMPValue($_POST["device_ip"], $_POST["device_read"], ".1.3.6.1.2.1.1.4.0" 	);
		$sysUpTime 	= $noc->getSNMPValue($_POST["device_ip"], $_POST["device_read"], ".1.3.6.1.2.1.1.3.0"  	);
		$sysLocation 	= $noc->getSNMPValue($_POST["device_ip"], $_POST["device_read"], ".1.3.6.1.2.1.1.6.0"	);
		$sysName 	= $noc->getSNMPValue($_POST["device_ip"], $_POST["device_read"], ".1.3.6.1.2.1.1.5.0"	);
		$ports	= $noc->getSNMPwalk($_POST["device_ip"], $_POST["device_read"], ".1.3.6.1.2.1.2.2.1.1"	);
		$numport = count($ports);
		
		// Store the device into a SQL table
		$add_query = $db->query("INSERT INTO {$config["tables"]["noc_devices"]} SET
					name 		= '{$_POST['device_caption']}',
					ip 		= '{$_POST['device_ip']}',
					readcommunity	= '{$_POST['device_read']}',
					writecommunity	= '{$_POST['device_write']}',
					sysDescr 	= '$sysDescr',
					sysContact 	= '$sysContact',
					sysUpTime 	= '$sysUpTime',
					sysLocation 	= '$sysLocation',
					sysName 	= '$sysName',
					ports		= '$numport'
					");


		$db->query( "SELECT id, ip, readcommunity FROM {$config["tables"]["noc_devices"]} WHERE name=\"" . $_POST["device_caption"] . "\"" );

		$row = $db->fetch_array();

		For($ActualPort=0;$ActualPort < count($ports);$ActualPort++) {

			$Port[$ActualPort]["deviceid"] = $row["id"];

			$Port[$ActualPort]["PortNr"] =
				$noc->getSNMPValue( $row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.1." . $ports[$ActualPort] );

			$Port[$ActualPort]["BytesIn"] =
				$noc->getSNMPValue( $row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.10." . $ports[$ActualPort] );

			$Port[$ActualPort]["BytesOut"] =
				$noc->getSNMPValue( $row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.16." . $ports[$ActualPort] );

			$Port[$ActualPort]["Speed"] =
				$noc->getSNMPValue( $row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.5." . $ports[$ActualPort] ) / ( 1000 * 1000 );

			$Port[$ActualPort]["LinkStatus"] =
				$noc->getSNMPValue( $row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.8." . $ports[$ActualPort] );

			$Port[$ActualPort]["AdminStatus"] =
				$noc->getSNMPValue( $row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.7." . $ports[$ActualPort] );

			//$Port[$ActualPort]["MACAddress"] =
			//	$noc->getSNMPValue( $row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.6." . $ports[$ActualPort]);

			$Port[$ActualPort]["Type"] =
				$noc->getSNMPValue( $row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.3." . $ports[$ActualPort]);

			$Port[$ActualPort]["indexname"] =
				$noc->getSNMPValue( $row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.2." . $ports[$ActualPort]);

			//$Port[$ActualPort]["IPAddress"] =
			//	$noc->MACtoIP( $Port[$ActualPort]["MACAddress"], $row["ip"], $row["readcommunity"] );
			
			$Port[$ActualPort]["ifSpecific"] =
				$noc->getSNMPValue( $row["ip"], $row["readcommunity"], ".1.3.6.1.2.1.2.2.1.22." . $ports[$ActualPort]);
			
			// For W2k Local-Loopback Ports
			If( $Port[$ActualPort]["LinkStatus"] == ""  ) { $Port[$ActualPort]["LinkStatus"]  = "up(1)"; }
			If( $Port[$ActualPort]["AdminStatus"] == "" ) { $Port[$ActualPort]["AdminStatus"] = "up(1)"; }
			
			// Type definieren
			switch ($Port[$ActualPort]["Type"]){
				case "ethernetCsmacd(6)":
				case "6":
				case "ethernetCsmacd":
					if($Port[$ActualPort]["ifSpecific"] != "zeroDotZero"){
						$Port[$ActualPort]["Type"] = 'rj45';
					}
				break;
				
				case "fibreChannel(56)":
				case "56":
				case "fibreChannel":
					if($Port[$ActualPort]["ifSpecific"] != "zeroDotZero"){
						$Port[$ActualPort]["Type"] = 'rj45';
					}
					
				break;
				
				default:
					$Port[$ActualPort]["Type"] = 'system';
				break;
			}
			// Save it all
				$add_query = $db->query("INSERT INTO {$config["tables"]["noc_ports"]} SET
							portnr = '". $Port[$ActualPort]["PortNr"] . "',
							bytesIn = '". $Port[$ActualPort]["BytesIn"] ."',
							bytesOut = '". $Port[$ActualPort]["BytesOut"] ."',
							speed = '". $Port[$ActualPort]["Speed"] ."',
							mac = '" . $Port[$ActualPort]["MACAddress"] . "',
							ip = '" . $Port[$ActualPort]["IPAddress"] . "',
							adminstatus = '". $Port[$ActualPort]["AdminStatus"]. "',
							linkstatus = '". $Port[$ActualPort]["LinkStatus"] ."',
							deviceid = '". $Port[$ActualPort]["deviceid"] ."',
							type = '". $Port[$ActualPort]["Type"] ."',
							indexname = '". $Port[$ActualPort]["indexname"] . "' 
							");
		} // END FOR

		$noc->getMacAddress($row["ip"], $row["readcommunity"],$row["id"],$sysDescr);
			
		If( $add_query == 1 ) { 
		
			$confirmationtext = $lang['noc']['add_ok'];
			
			If ($_POST['device_write'] == "private") {

				$confirmationtext .= $lang['noc']['write_warning'];
				
			}
			
			If ($_POST['device_read'] == "public") {
				
				$confirmationtext .= $lang['noc']['read_warning'];
			
			}
		
			$func->confirmation( $confirmationtext, "" ); 
		
		} else {
		
			$func->error( $lang['noc']['add_error'], "" );
		
		}
	
	break;
	
	// ------------------------------------------------------------------------------------

} // END SWITCH II

?>
