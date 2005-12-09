<?php
					
	// Ab hier die Konfiguration
	$this->config['search_fields'][]  = "n.caption";
	$this->config['search_fields'][]  = "n.text";
	$this->config['search_fields'][]  = "u.username";
	$this->config['sql_statment']     = "SELECT * FROM {$config["tables"]["news"]} n LEFT JOIN {$config["tables"]["user"]} u ON n.poster=u.userid";
	$this->config['title']            = $lang['ms']['news']['title'];
	$this->config['orderby']          = "n.date,DESC";
	$this->config['userid']           = "userid";
	$this->config['linkcol']          = "newsid";	
	$this->config['entrys_page']      = $config["size"]["table_rows"]; // Hier kanste definieren wieviele einträge du pro seite ausgegeben bekommen willst
	$this->config['no_items_caption'] = $lang['ms']['news']['no_items_caption'];
	$this->config['no_items_link']	  = "";
	
	
	$z = 0;
	// Spaltenname
	$this->config['result_fields'][$z]['name']     = $lang['ms']['news']['title_'];
	$this->config['result_fields'][$z]['sqlrow']   = "n.caption";
	$this->config['result_fields'][$z]['row']      = "caption";
	$this->config['result_fields'][$z]['width']    = "33%";
	$this->config['result_fields'][$z]['maxchar']  = "0";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['news']['author'];
	$this->config['result_fields'][$z]['sqlrow']   = "u.username";
	$this->config['result_fields'][$z]['row']      = "username";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "0";
	$this->config['result_fields'][$z]['profil']   = "1";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['news']['date'];
	$this->config['result_fields'][$z]['sqlrow']   = "n.date";
	$this->config['result_fields'][$z]['row']      = "date";
	$this->config['result_fields'][$z]['align']    = "right";
	$this->config['result_fields'][$z]['callback'] = "GetDate";
	$this->config['result_fields'][$z]['width']    = "33%";
	$this->config['result_fields'][$z]['maxchar']  = "0";
	
?>
