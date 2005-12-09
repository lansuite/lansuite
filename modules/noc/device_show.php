<?php
	
	// --------------------------------------------------------------------------------------
	
	/*	Network Operations Centre
	 *
	 *	originally based on phpSMITH
	 *	
	 *
	 *	Maintainer: Joachim Garth <josch@one-network.org>
	 */
	
	// --------------------------------------------------------------------------------------

	$mastersearch = new MasterSearch( $vars, "index.php?mod=noc&action=show_device", "index.php?mod=noc&action=details_device&deviceid=", "" );
	$mastersearch->LoadConfig("noc", $lang['noc']['ms_search'], $lang['noc']['ms_result']);
	$mastersearch->PrintForm();
	$mastersearch->Search();
	$mastersearch->PrintResult();

	$templ['index']['info']['content'] .= $mastersearch->GetReturn();	 
?>
