<?php

	// Ab hier die Konfiguration
	$this->config['search_fields'][]  = "noc.ip";
	$this->config['search_fields'][]  = "noc.name";
	$this->config['sql_statment']     = "SELECT * FROM {$config["tables"]["noc_devices"]} AS noc ";
	$this->config['title']            = $lang['ms']['noc']['title'];
	$this->config['orderby']          = "noc.name, ASC";
#	$this->config['userid']           = "userid";
	$this->config['linkcol']          = "id";
	$this->config['entrys_page']      = $config["size"]["table_rows"]; // Hier kanste definieren wieviele einträge du pro seite ausgegeben bekommen willst

	$this->config['no_items_caption'] = $lang['ms']['noc']['no_items_caption'];

	$z = 0;
	// Spaltenname
	$this->config['result_fields'][$z]['name']     = $lang['ms']['noc']['id'];
	$this->config['result_fields'][$z]['sqlrow']   = "noc.id";
	$this->config['result_fields'][$z]['row']      = "id";
	$this->config['result_fields'][$z]['width']    = "10%";
	$this->config['result_fields'][$z]['maxchar']  = "6";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['noc']['name'];
	$this->config['result_fields'][$z]['sqlrow']   = "noc.name";
	$this->config['result_fields'][$z]['row']      = "name";
	$this->config['result_fields'][$z]['width']    = "50%";
	$this->config['result_fields'][$z]['maxchar']  = "30";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['noc']['ip'];
	$this->config['result_fields'][$z]['sqlrow']   = "noc.ip";
	$this->config['result_fields'][$z]['row']      = "ip";
	$this->config['result_fields'][$z]['width']    = "40%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$z++;
	
?>
