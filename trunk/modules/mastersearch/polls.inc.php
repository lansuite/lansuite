<?php

	// Ab hier die Konfiguration
	$this->config['search_fields'][]  = "p.caption";
	$this->config['search_fields'][]  = "p.comment";
	$this->config['sql_statment']     = "SELECT * FROM {$config["tables"]["polls"]} AS p";
	$this->config['title']            = $lang['ms']['polls']['title'];
	$this->config['orderby']          = "p.caption,ASC";
	$this->config['userid']           = "userid";
	$this->config['linkcol']          = "pollid";
	$this->config['entrys_page']      = "10";
	// $config["size"]["table_rows"]; // Hier kanste definieren wieviele einträge du pro seite ausgegeben bekommen willst
	$this->config['no_items_caption'] = $lang['ms']['polls']['no_items_caption'];
	$this->config['no_items_link']	  = "";

	
	$z = 0;
	// Spaltenname
	$this->config['result_fields'][$z]['name']     = $lang['ms']['polls']['title_'];
	$this->config['result_fields'][$z]['sqlrow']   = "p.caption";
	$this->config['result_fields'][$z]['row']      = "caption";
	$this->config['result_fields'][$z]['width']    = "45%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['polls']['state'];
	$this->config['result_fields'][$z]['sqlrow']   = "p.pollid";
	$this->config['result_fields'][$z]['row']      = "pollid";
	$this->config['result_fields'][$z]['callback'] = "GetPollStatus";
	$this->config['result_fields'][$z]['width']    = "30%";
	$this->config['result_fields'][$z]['maxchar']  = "40";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['polls']['votes'];
	$this->config['result_fields'][$z]['sqlrow']   = "p.pollid";
	$this->config['result_fields'][$z]['row']      = "pollid";
	$this->config['result_fields'][$z]['callback'] = "GetVotesTotal";
	$this->config['result_fields'][$z]['width']    = "25%";
	$this->config['result_fields'][$z]['maxchar']  = "50";
	$z++;

?>
