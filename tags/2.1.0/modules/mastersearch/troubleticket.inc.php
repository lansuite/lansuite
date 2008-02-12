<?php
	// Ab hier die Konfiguration
	$this->config['search_fields'][]  = "t.caption";
	$this->config['search_fields'][]  = "t.status";
	$this->config['search_fields'][]  = "u.username";
	$this->config['sql_statment']     = "SELECT * FROM {$config["tables"]["troubleticket"]} AS t
										LEFT JOIN {$config["tables"]["user"]} AS u ON t.target_userid = u.userid ";
	$this->config['title']            = $lang['ms']['troubleticket']['title'];
	$this->config['orderby']          = "t.caption, ASC";
	$this->config['userid']           = "ttid";
	$this->config['linkcol']          = "ttid";
	$this->config['entrys_page']      = $config["size"]["table_rows"]; // Hier kanste definieren wieviele einträge du pro seite ausgegeben bekommen willst

	$this->config['no_items_caption'] = $lang['ms']['troubleticket']['no_items_caption'];
	
	$z = 0;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['troubleticket']['title_'];
	$this->config['result_fields'][$z]['sqlrow']   = "t.caption";
	$this->config['result_fields'][$z]['row']      = "caption";
	$this->config['result_fields'][$z]['width']    = "50%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['troubleticket']['belongsto'];
	$this->config['result_fields'][$z]['sqlrow']   = "u.username";
	$this->config['result_fields'][$z]['row']      = "username";
	$this->config['result_fields'][$z]['width']    = "25%";
	$this->config['result_fields'][$z]['maxchar']  = "16";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['troubleticket']['state'];
	$this->config['result_fields'][$z]['sqlrow']   = "t.status";
	$this->config['result_fields'][$z]['row']      = "status";
	$this->config['result_fields'][$z]['callback'] = "tt_status";
	$this->config['result_fields'][$z]['width']    = "25%";
	$this->config['result_fields'][$z]['maxchar']  = "16";
	$z++;
	
?>
