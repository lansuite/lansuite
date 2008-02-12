<?php

include_once( "modules/noc/class_noc.php" );
$noc = new noc();

 
switch( $_GET["step"] ) {


	default:
	case 1:
		// Get all the Port data
		$row = $db->query_first( "SELECT * FROM {$config["tables"]["noc_ports"]} WHERE portid=" . $_GET["portid"] );
		
		if($row["portid"] == "") $func->error($lang['noc']['port_not_exist'],"");
		
		else {
				
		// Is the Port Enabled? No? Is it connected? 
		switch( $row["linkstatus"] ) {
			
			case "1":
			case "up":
			case "up(1)":
				If( $row["adminstatus"] == "down(2)" || $row["adminstatus"] == "down" || $row["adminstatus"] == "2") {
					$linkstatus = "<font color=\"red\">" . $lang['noc']['port_off'] . "</font>"; 
				} else { 
					$linkstatus = "<font color=\"green\">" . $lang['noc']['port_active'] . "</font>";
				}
			break;
			
			case "2":
			case "down":
			case "down(2)":
				If( $row["adminstatus"] == "down(2)" || $row["adminstatus"] == "down" || $row["adminstatus"] == "2") {
					$linkstatus = "<font color=\"red\">" . $lang['noc']['port_off'] . "</font>"; 
				} else { 
					$linkstatus = "<font color=\"red\">" . $lang['noc']['port_inactive'] . "</font>"; 
				}
			break;
			
		}
		
		// We assume that of course 1 PetaByte = 1024 Terabyte = 1024² Gigabyte = 1024³ Megabyte = 1024 * 1024 * 1024 * 1024 Kilobyte = 1024 * 1024 * 1024 * 1024 * 1024 Byte = 1024 * 1024 * 1024 * 1024 * 1024 * 8 Bit
		// Clear, right?
		$bytesIn  = round( $row["bytesIn"]  / ( 1024 * 1024 ), 2 ) . " MBytes";
		$bytesOut = round( $row["bytesOut"] / ( 1024 * 1024 ), 2 ) . " MBytes";
		
		
		$dsp->NewContent($lang['noc']['port_caption'],$lang['noc']['port_subcaption']);
		$dsp->SetForm("index.php?mod=noc&action=port_details&step=2&portid=" . $_GET['portid'],"noc");

		// Template Variables
		$dsp->AddDoubleRow($lang['noc']['portnr'], $row["portnr"]);
		$dsp->AddDoubleRow($lang['noc']['mac'], nl2br($row["mac"]));
		$dsp->AddDoubleRow($lang['noc']['ip'], $row["ip"]);
		$dsp->AddDoubleRow($lang['noc']['linkstatus'], $linkstatus);
		$dsp->AddFormSubmitRow("edit");
		$dsp->AddDoubleRow($lang['noc']['speed'],$row["speed"]. " MBit/s (entspricht ~ " . round($row["speed"] / 8,2) . " MBytes/s)");
		$dsp->AddDoubleRow($lang['noc']['bytesIn'],$bytesIn);
		$dsp->AddDoubleRow($lang['noc']['bytesOut'],$bytesOut);
		$dsp->AddBackButton("index.php?mod=noc&action=details_device&deviceid=" . $row["deviceid"]);
		$dsp->AddContent();
	}//port exists
	
	break;
	
	case 2:
	
	$func->question($lang['noc']['change_port'],
				"index.php?mod=noc&action=port_details&portid={$_GET["portid"]}&step=3",
			  	"index.php?mod=noc&action=port_details&portid={$_GET["portid"]}");
	
	break;
	
	
	// 3 stands for change the port "status" (deactivate it, regulate the speed, and so on)
	case 3:
	
		$port = $db->query_first( "SELECT portid, deviceid, portnr, adminstatus FROM {$config["tables"]["noc_ports"]} WHERE portid=" . $_GET["portid"] );
		
		if($port["portid"] == "") $func->error($lang['noc']['port_not_exist']	,""); 
		
		else {

			$device = $db->query_first( "SELECT name, readcommunity, writecommunity, ip FROM {$config["tables"]["noc_devices"]} WHERE id=" . $port['deviceid'] );



			$status = $noc->getSNMPValue( $device["ip"], $device["readcommunity"], ".1.3.6.1.2.1.2.2.1.7.{$port["portnr"]}" );

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

			if($noc->setSNMPValue( $device["ip"], $device["writecommunity"], ".1.3.6.1.2.1.2.2.1.7.{$port["portnr"]}", "i", $newstatus )){
				
				$db->query_first("UPDATE {$config["tables"]["noc_ports"]} SET adminstatus='$statusdb' WHERE portid=" . $_GET["portid"]);
			
				$func->confirmation($lang['noc']['port_changed'], "index.php?mod=noc&action=port_details&portid={$_GET["portid"]}");
			}else{
				$func->error($lang['noc']['change_port_error'],"index.php?mod=noc&action=port_details&portid={$_GET["portid"]}");
			}
		}//port exists
		
	break;

}

?>
