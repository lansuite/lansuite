<?php
	// Ab hier die Konfiguration
	$this->config['search_fields'][]  = "p.caption";
	$this->config['search_type'][]    = "like";
	$this->config['search_fields'][]  = "a.status";
	$this->config['search_type'][]    = "exact";
	
	$this->config['sql_statment']     = "SELECT a.*, p.caption FROM {$config['tables']['food_ordering']} AS a
										LEFT JOIN {$config['tables']['food_product']} AS p ON a.productid = p.id";
	
	$this->config['orderby']          = "a.ordertime,ASC";

	$this->config['linkcol']          = "id";
	$this->config['entrys_page']      = $config["size"]["table_rows"]; // Hier kanste definieren wieviele einträge du pro seite ausgegeben bekommen willst
	$this->config['list_only']		  = true; 

	
	$this->config['inputs'][1]['title']   = $lang['ms']['foodcenter']['status'];
	$this->config['inputs'][1]['name']    = "search_select1";
	$this->config['inputs'][1]['type']    = "select";
	$this->config['inputs'][1]['sql'][1]     = "a.status";
	$this->config['inputs'][1]['options'] = array("1"=> $lang['ms']['foodcenter']['order'], "2"=>$lang['ms']['foodcenter']['ordered'], "3"=>$lang['ms']['foodcenter']['fetch'], "4"=> $lang['ms']['foodcenter']['fetched']);
	
		
	$z = 0;
	// Spaltenname
	$this->config['result_fields'][$z]['name']     = $lang['ms']['foodcenter']['title'];
	$this->config['result_fields'][$z]['sqlrow']   = "p.caption";
	$this->config['result_fields'][$z]['row']      = "caption";
	if($this->vars['search_select1'] == 4){
		$this->config['result_fields'][$z]['width']    = "25%";
	}else{
		$this->config['result_fields'][$z]['width']    = "45%";
	}
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$this->config['result_fields'][$z]['checkbox']   = "checkbox";

	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['foodcenter']['caption'];
	$this->config['result_fields'][$z]['sqlrow']   = "a.opts";
	$this->config['result_fields'][$z]['row']      = "opts";
	$this->config['result_fields'][$z]['width']    = "25%";
	$this->config['result_fields'][$z]['maxchar']  = "10";
	$this->config['result_fields'][$z]['callback'] = "GetFoodoption";

	
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['foodcenter']['orderdate'];
	$this->config['result_fields'][$z]['sqlrow']   = "a.ordertime";
	$this->config['result_fields'][$z]['row']      = "ordertime";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "10";
	$this->config['result_fields'][$z]['callback'] = "GetDate";
	
	if($this->vars['search_select1'] == 4){
		$z++;
		$this->config['result_fields'][$z]['name']     = $lang['ms']['foodcenter']['suppdate'];
		$this->config['result_fields'][$z]['sqlrow']   = "a.supplytime";
		$this->config['result_fields'][$z]['row']      = "supplytime";
		$this->config['result_fields'][$z]['width']    = "20%";
		$this->config['result_fields'][$z]['maxchar']  = "20";
		$this->config['result_fields'][$z]['callback'] = "GetDate";
	}
	
	$z++;	
	$this->config['result_fields'][$z]['name']     = $lang['ms']['foodcenter']['count'];
	$this->config['result_fields'][$z]['sqlrow']   = "a.pice";
	$this->config['result_fields'][$z]['row']      = "pice";
	$this->config['result_fields'][$z]['width']    = "10%";
	$this->config['result_fields'][$z]['maxchar']  = "20";

?>