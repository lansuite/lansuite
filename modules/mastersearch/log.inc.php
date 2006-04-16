<?php

	// Ab hier die Konfiguration
	$this->config['search_fields'][]  = "l.description";
	$this->config['search_type'][]  = "like";
	$this->config['search_fields'][]  = "l.sort_tag";
	$this->config['search_type'][]  = "like";
	$this->config['search_fields'][]  = "l.userid";
	$this->config['search_type'][]  = "exact";

	$this->config['sql_statment']     = "SELECT * FROM {$config["tables"]["log"]} AS l";
	
	$this->config['title']            = $lang['ms']['log']['title'];
	$this->config['orderby']          = "l.date, DESC";
	$this->config['userid']           = "userid";
	$this->config['linkcol']          = "logid";
	$this->config['entrys_page']      = $config["size"]["table_rows"]; // Hier kanste definieren wieviele einträge du pro seite ausgegeben bekommen willst
	
	$this->config['inputs'][1]['title']   = $lang['ms']['log']['priority'];
	$this->config['inputs'][1]['name']    = "search_select1";
	$this->config['inputs'][1]['type']    = "select";
	$this->config['inputs'][1]['sql'][1]     = "l.type";
	$this->config['inputs'][1]['options'] = array("all"=>"Alle", "1"=>"Niedrig", "2"=>"Normal", "3"=>"Hoch");

	$z = 0;
	// Spaltenname
	
	$this->config['result_fields'][$z]['name']     = $lang['ms']['log']['gourp'];
	$this->config['result_fields'][$z]['sqlrow']   = "l.sort_tag";
	$this->config['result_fields'][$z]['row']      = "sort_tag";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "30";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['log']['message'];
	$this->config['result_fields'][$z]['sqlrow']   = "l.description";
	$this->config['result_fields'][$z]['row']      = "description";
	$this->config['result_fields'][$z]['width']    = "60%";
	$this->config['result_fields'][$z]['maxchar']  = "80";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['log']['date'];
	$this->config['result_fields'][$z]['sqlrow']   = "l.date";
	$this->config['result_fields'][$z]['row']      = "date";
	$this->config['result_fields'][$z]['callback'] = "GetDate";
	$this->config['result_fields'][$z]['width']    = "15%";
	$this->config['result_fields'][$z]['maxchar']  = "12";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['log']['user'];
	$this->config['result_fields'][$z]['sqlrow']   = "l.userid";
	$this->config['result_fields'][$z]['row']      = "userid";
	$this->config['result_fields'][$z]['callback'] = "GetUserName";
 	$this->config['result_fields'][$z]['width']    = "5%";
	$this->config['result_fields'][$z]['maxchar']  = "12";
	$z++;
	
?>
