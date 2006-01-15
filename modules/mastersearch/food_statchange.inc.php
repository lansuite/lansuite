<?php
	// Ab hier die Konfiguration
	$this->config['search_fields'][]  = "p.caption";
	$this->config['search_type'][]    = "like";
	$this->config['search_fields'][]  = "s.supp_id";
	$this->config['search_type'][]    = "exact";
	$this->config['search_fields'][]  = "a.status";
	$this->config['search_type'][]    = "exact";
	$this->config['search_fields'][]  = "a.userid";
	$this->config['search_type'][]    = "exact";
	
	
	$this->config['sql_statment']     = "SELECT a.*, p.caption, s.* FROM {$config['tables']['food_ordering']} AS a
										LEFT JOIN {$config['tables']['food_product']} AS p ON a.productid = p.id
										LEFT JOIN {$config['tables']['food_supp']} AS s ON p.supp_id = s.supp_id";
	
	$this->config['orderby']          = "a.ordertime,ASC";

	$this->config['linkcol']          = "id";
	$this->config['entrys_page']      = $config["size"]["table_rows"]; // Hier kanste definieren wieviele einträge du pro seite ausgegeben bekommen willst

	
	$this->config['inputs'][1]['title']   = $lang['ms']['foodcenter']['status'];
	$this->config['inputs'][1]['name']    = "search_select1";
	$this->config['inputs'][1]['type']    = "select";
	$this->config['inputs'][1]['sql'][1]     = "a.status";
	$this->config['inputs'][1]['options'] = array("1"=> $lang['ms']['foodcenter']['order'], "2"=>$lang['ms']['foodcenter']['ordered'], "3"=>$lang['ms']['foodcenter']['fetch'], "4"=> $lang['ms']['foodcenter']['fetched']);
	

	$this->config['inputs'][2]['title']   = $lang['ms']['foodcenter']['suppname'];
	$this->config['inputs'][2]['name']    = "search_select2";
	$this->config['inputs'][2]['type']    = "select";
	$this->config['inputs'][2]['sql'][1]     = "s.supp_id";
	$supp = $db->query("SELECT * FROM {$config['tables']['food_supp']}");
	$supp_array['all'] = $lang['ms']['select_all'];
	while ($supprows = $db->fetch_array($supp)) {
		$supp_array[$supprows['supp_id']] = $supprows['name'];
	}
	$this->config['inputs'][2]['options'] = $supp_array;

	$this->config['inputs'][3]['title']   = $lang['ms']['foodcenter']['user'];
	$this->config['inputs'][3]['name']    = "search_select3";
	$this->config['inputs'][3]['type']    = "select";
	$this->config['inputs'][3]['sql'][1]     = "a.userid";
	$userquery = $db->query("SELECT * FROM {$config['tables']['food_ordering']} AS a LEFT JOIN {$config['tables']['user']} AS u ON a.userid=u.userid");
	$user_array['all'] = $lang['ms']['select_all'];
	while ($userrows = $db->fetch_array($userquery)) {
		$user_array[$userrows['userid']] = $userrows['username'];
	}
	$this->config['inputs'][3]['options'] = $user_array;
	
	
	$z = 0;
	// Spaltenname
	$this->config['result_fields'][$z]['name']     = $lang['ms']['foodcenter']['title'];
	$this->config['result_fields'][$z]['sqlrow']   = "p.caption";
	$this->config['result_fields'][$z]['row']      = "caption";
	if($this->vars['search_select1'] == 4){
		$this->config['result_fields'][$z]['width']    = "20%";
	}else{
		$this->config['result_fields'][$z]['width']    = "35%";
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
	$this->config['result_fields'][$z]['name']     = $lang['ms']['users']['username'];
	$this->config['result_fields'][$z]['sqlrow']   = "a.userid";
	$this->config['result_fields'][$z]['row']      = "userid";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "10";
	$this->config['result_fields'][$z]['callback'] = "GetUsername";
	
	
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['foodcenter']['orderdate'];
	$this->config['result_fields'][$z]['sqlrow']   = "a.ordertime";
	$this->config['result_fields'][$z]['row']      = "ordertime";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "10";
	$this->config['result_fields'][$z]['callback'] = "GetDate";
	
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['foodcenter']['add_product_prod_supp'];
	$this->config['result_fields'][$z]['sqlrow']   = "s.name";
	$this->config['result_fields'][$z]['row']      = "name";
	$this->config['result_fields'][$z]['width']    = "10%";
	$this->config['result_fields'][$z]['maxchar']  = "20";

	
	
	if($this->vars['search_select1'] == 4){
		$z++;
		$this->config['result_fields'][$z]['name']     = $lang['ms']['foodcenter']['suppdate'];
		$this->config['result_fields'][$z]['sqlrow']   = "a.supplytime";
		$this->config['result_fields'][$z]['row']      = "supplytime";
		$this->config['result_fields'][$z]['width']    = "10%";
		$this->config['result_fields'][$z]['maxchar']  = "20";
		$this->config['result_fields'][$z]['callback'] = "GetDate";
	}
	
	$z++;	
	$this->config['result_fields'][$z]['name']     = $lang['ms']['foodcenter']['count'];
	$this->config['result_fields'][$z]['sqlrow']   = "a.pice";
	$this->config['result_fields'][$z]['row']      = "pice";
	$this->config['result_fields'][$z]['width']    = "10%";
	$this->config['result_fields'][$z]['maxchar']  = "20";

	
	$this->config['action_select']['select_all']	= $lang['ms']['select_all'];
	$this->config['action_select']['select_none']	= $lang['ms']['select_none'];
	$this->config['action_select']['hr']	= "------------------------";
	$this->config['action_select'][5]			=	$lang['foodcenter']['ordered_status_quest'][0];
	$this->config['action_select'][4]			=	$lang['foodcenter']['ordered_status_quest'][4];
	$this->config['action_select'][3]			=	$lang['foodcenter']['ordered_status_quest'][1];
	$this->config['action_select'][2]			=	$lang['foodcenter']['ordered_status_quest'][2];
	$this->config['action_select'][1]			=	$lang['foodcenter']['ordered_status_quest'][3];
?>