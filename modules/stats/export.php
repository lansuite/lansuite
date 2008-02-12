<?php //07.02.2005 00:30 - raphael@one-network.org

$action = $_GET['action'];

switch($action) {

	case "exportinfo":
		$templ['stats_data'] = $stats->getExportData();
		if($templ['stats_data']['start'] == 0 ) 	$templ['stats_data']['date'] = '<font color="red"><i>{$lang["stats"]["export_not_entered"]}</i></font>'; 
		else 						$templ['stats_data']['date'] = strftime("%d.%m.%Y",$templ['stats_data']['start']); 

		$templ['stats_data']['date'] .= ' - ';
												
		if($templ['stats_data']['end'] == 0) 		$templ['stats_data']['date'] .= '<font color="red"><i>{$lang["stats"]["export_not_entered"]}</i></font>';  
		else 						$templ['stats_data']['date'] .= strftime("%d.%m.%Y",$templ['stats_data']['end']);  
		
		if($templ['stats_data']['name'] == 'LanParty with LANsuite') 		$templ['stats_data']['name'] = '<font color="red"><i>{$lang["stats"]["export_not_entered"]}</i></font>';
		if($templ['stats_data']['url'] == 'http://www.one-network.org') 	$templ['stats_data']['url'] = '<font color="red"><i>{$lang["stats"]["export_not_entered"]}</i></font>';
		
		if($templ['stats_data']['plz'] == 0) 	$templ['stats_data']['plz'] = '<font color="red"><i>{$lang["stats"]["export_not_entered"]}</i></font>';
		if($templ['stats_data']['mail'] == '') 	$templ['stats_data']['mail'] = '<font color="red"><i>{$lang["stats"]["export_not_entered"]}</i></font>';
		
		
		
		eval("\$templ['index']['info']['content'] .= \"". $func->gettemplate("stats_export")."\";");
		
	
	
	break;
	
	
	
	default:
		$stats->export();
		$func->confirmation($lang["stats"]["export_success"], "");	
	
	break;
	
}//switch		
?>
