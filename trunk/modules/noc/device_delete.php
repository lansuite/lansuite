<?php

/*	Network Operations Centre
 *
 *	originally based on phpSMITH
 *	
 *
 *	Maintainer: Joachim Garth <josch@one-network.org>
 */

switch( $_GET["step"] ) {

	default:
	case 1:
		$mastersearch = new MasterSearch( $vars, "index.php?mod=noc&action=delete_device", "index.php?mod=noc&action=delete_device&step=2&deviceid=", "" );
		$mastersearch->LoadConfig("noc", $lang['noc']['ms_search'], $lang['noc']['ms_result']);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();

		$templ['index']['info']['content'] .= $mastersearch->GetReturn();	 
	break;
	
	case 2:
						
		$func->question($lang['noc']['device_delete'],"index.php?mod=noc&action=delete_device&step=3&deviceid=" . $_GET["deviceid"], "index.php?mod=noc");
		 
	break;
	
	case 3:
	
		// DELETE 'em all.... 
		$check_device = $db->query_first("SELECT id FROM {$config["tables"]["noc_devices"]} WHERE id='{$_GET["deviceid"]}'");
		
		if ($check_device["id"] == "") $func->error($lang['noc']['device_not_exist'],"");
		
		else {

				$del_query1 = $db->query("DELETE FROM {$config["tables"]["noc_devices"]} WHERE id=" . $_GET["deviceid"]);
				$del_query2 = $db->query("DELETE FROM {$config["tables"]["noc_ports"]} WHERE deviceid=" . $_GET["deviceid"]);

				If ($del_query1 && $del_query2) {

					$func->confirmation($lang['noc']['delete_ok'], "");

				} else {

					$func->error($lang['noc']['delete_error'], "");

				}

		}
		
	break;


}


?>
