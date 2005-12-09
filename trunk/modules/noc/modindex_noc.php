<?php

	/*	Network Operations Centre
	 *
	 *	originally based on phpSMITH
	 *	
	 *
	 *	Maintainer: Joachim Garth <josch@one-network.org>
	 */

	if( $config['environment']['snmp'] != "1" ) { $vars["action"] = "nosnmp"; }
 
	include( "modules/noc/class_noc.php" );
	$noc = new noc();
	
	// -------------------------------------------------------
	
	if( $auth['type'] > 1 ) {
	
		switch( $vars["action"] ) { 
			
			case nosnmp:
				$func->error($lang['noc']['err_nosnmp'], "" );
			break;
			
			case add_device:
				include( "device_add.php" );
			break;
			
			case delete_device:
				include( "device_delete.php" );
			break;
			
			case change_device:
				include( "device_change.php" );
			break;
			
			case details_device:
				include( "device_details.php" );
			break;
			
			case port_details:
				include( "port_details.php" );
			break;
			
			default:
			case show_device:
				include( "device_show.php" );
			break;

			case find:
				include( "find_ip.php" );
			break;

		}
	} else {
		$func->error( "ACCESS_DENIED", "" );
	}
	
	// -------------------------------------------------------

?>
