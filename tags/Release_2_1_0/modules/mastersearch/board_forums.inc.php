<?php

	// Ab hier die Konfiguration
	$this->config['search_fields'][]  = "f.name";
	$this->config['search_fields'][]  = "f.description";
	$this->config['sql_statment']     = "SELECT * FROM {$config["tables"]["board_forums"]} AS f";
	$this->config['title']            = $lang['ms']['board_forums']['title'];
	$this->config['orderby']          = "f.name,ASC";
	$this->config['userid']           = "userid";
	$this->config['linkcol']          = "fid";
	$this->config['entrys_page']      = "10";
	// $config["size"]["table_rows"]; // Hier kanste definieren wieviele einträge du pro seite ausgegeben bekommen willst
	$this->config['no_items_caption'] = $lang['ms']['board_forums']['no_items_caption'];
	$this->config['no_items_link']	  = "";

	
	$z = 0;
	// Spaltenname
	$this->config['result_fields'][$z]['name']     = $lang['ms']['board_forums']['name'];
	$this->config['result_fields'][$z]['sqlrow']   = "f.name";
	$this->config['result_fields'][$z]['row']      = "name";
	$this->config['result_fields'][$z]['width']    = "30%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['board_forums']['description'];
	$this->config['result_fields'][$z]['sqlrow']   = "f.description";
	$this->config['result_fields'][$z]['row']      = "description";
	$this->config['result_fields'][$z]['width']    = "55%";
	$this->config['result_fields'][$z]['maxchar']  = "40";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['board_forums']['posts'];
	$this->config['result_fields'][$z]['sqlrow']   = "f.fid";
	$this->config['result_fields'][$z]['row']      = "fid";
	$this->config['result_fields'][$z]['callback'] = "GetPostsTotal";
	$this->config['result_fields'][$z]['width']    = "15%";
	$this->config['result_fields'][$z]['maxchar']  = "50";
	$z++;

?>
