<?php
	
	if( $vars['search_type'] == "admin" )  {
		$sql = " AND (u.type='3')";
	} elseif( $vars['search_type'] == "user" )  {
		$sql = " AND (u.type='1')";
	}
		
	$mastersearch = new MasterSearch( $vars, "index.php?mod=mastersearch", "index.php?mod=mastersearch&userid=", $sql );
	$mastersearch->LoadConfig( "users", "Benutzerauswahl: Suche", "Benutzerauswahl: Ergebniss" );
	$mastersearch->PrintForm();
	$mastersearch->Search();
	$mastersearch->PrintResult();
	
	$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	
?>
