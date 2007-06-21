<?php

include_once( "modules/noc/class_noc.php" );
$noc = new noc();

switch( $_GET["step"] ) {
	
	// ------------------------------------------------------------------------------------
	// ERROR CHECKING
	case 3:
	
		If( $_POST["device_caption"] == "" ) { 

			$noc_error['device_caption'] = $lang['noc']['device_caption_error'];

			$_GET["step"] = 2;
			
		}
		
		If( $_POST["device_ip"] == "" ) {
		
			$noc_error['device_ip'] = $lang['noc']['device_ip_error'];
			
			$_GET["step"] = 2;
			
		} else {
		
			If( !( $func->checkIP( $_POST["device_ip"] ) ) ) {
				
				$noc_error['device_ip'] = $lang['noc']['device_ipcheck_error'];
			
				$_GET["step"] = 2;
					
			}
		
		}
		
				
		If( $_POST["device_write"] == "" ) {
			
			$noc_error['device_write'] = $lang['noc']['device_write_error'];
				
			$_GET["step"] = 2;
			
		}
		
		If( $_POST["device_read"] == "" ) {
		
			$noc_error['device_read'] = $lang['noc']['device_read_error'];
							
			$_GET["step"] = 2;
			
		}
		
		break;
		
} // END SWITCH I

// ----------------------------------------------------------------------------------------------------------

switch( $_GET["step"] ) {
	
	// --------------------------------------------------------------------------------------------------
	// Display Form
	default:
	case 1:
    include_once('modules/noc/search.inc.php');
	break;
		
	case 2:	
	
		$db->query("SELECT * FROM {$config["tables"]["noc_devices"]} WHERE id=" . $_GET["deviceid"]);
		
		if( $row = $db->fetch_array() ) {
	
			$deviceid = $row["id"];
			$device_ip = $row["ip"];
			$device_caption = $row["name"];
			$device_read = $row["readcommunity"];
			$device_write = $row["writecommunity"];

			$dsp->NewContent($lang['noc']['caption'],$lang['noc']['subcaption']);
			$dsp->SetForm("index.php?mod=noc&action=change_device&step=3&deviceid=" . $_GET["deviceid"],"noc");
			$dsp->AddTextFieldRow("device_caption",$lang['noc']['device_caption'],$device_caption,$noc_error['device_caption']);
			$dsp->AddTextFieldRow("device_ip",$lang['noc']['device_ip'],$device_ip,$noc_error['device_ip']);
			$dsp->AddTextFieldRow("device_read",$lang['noc']['device_read'],$device_read,$noc_error['device_read']);
			$dsp->AddTextFieldRow("device_write",$lang['noc']['device_write'],$device_write,$noc_error['device_write']);
		
			$dsp->AddFormSubmitRow("change");
			$dsp->AddBackButton("index.php?mod=noc", "noc"); 
			$dsp->AddContent();
	
			unset($noc_error);
					
		} else {
		
			$func->error($lang['noc']['device_not_exist'], "");
			
		}


	break;
	
	// --------------------------------------------------------------------------------------------------
	// Check and Update Device Data
	case 3:
	
		If( $noc->checkSNMPDevice( $_POST["device_ip"], $_POST["device_read"] ) != 1 ) { 
			
			$func->error($lang['noc']['connect_error'], "");
			break;
		} // END IF
		
		// ------------------------------------------------------------------------------------------
	
		// U p d a t e it, not delete and reinsert it.
		$add_query = $db->query("UPDATE {$config["tables"]["noc_devices"]} SET
			    		name = '{$_POST['device_caption']}',
			    		ip = '{$_POST['device_ip']}',
			    		readcommunity = '{$_POST['device_read']}',
			    		writecommunity = '{$_POST['device_write']}'
			    		WHERE id=" . $_GET["deviceid"]);
		
		If( $add_query == 1 ) { 
		
			$func->confirmation( $lang['noc']['change_ok'], "" ); 
		
		} else {
		
			$func->error( $lang['noc']['change_error'], "" );
		
		} // END IF
			    
	
	break;
		
} // END SWITCH II

// ---------------------------------------------------------------------------------------------------------- 

?>
