<?php
					
	// Ab hier die Konfiguration
	$this->config['search_fields'][]  = "rs.caption";
	$this->config['search_fields'][]  = "rs.quantity";
	$this->config['search_fields'][]  = "rs.rented";
	$this->config['search_fields'][]  = "u.username";
	$this->config['sql_statment']     = "SELECT * FROM {$config["tables"]["rentstuff"]} AS rs LEFT JOIN {$config["tables"]["user"]} AS u ON rs.ownerid=u.userid";
	$this->config['title']            = $lang['ms']['rentdelstuff']['title'];
	$this->config['userid']           = "userid";
	$this->config['linkcol']          = "stuffid";
	$this->config['orderby']          = "rs.caption,ASC";
	$this->config['entrys_page']      = $config["size"]["table_rows"]; // Hier kanste definieren wieviele einträge du pro seite ausgegeben bekommen willst

	$this->config['no_items_caption'] = $lang['ms']['rentdelstuff']['no_items_caption'];
	$this->config['no_items_link']	  = "index.php?mod=rent&action=add_stuff";
	
	$z = 0;
	// Spaltenname
	$this->config['result_fields'][$z]['name']     = $lang['ms']['rentstuff']['title_'];
	$this->config['result_fields'][$z]['sqlrow']   = "rs.caption";
	$this->config['result_fields'][$z]['row']      = "caption";
	$this->config['result_fields'][$z]['width']    = "40%";
	$this->config['result_fields'][$z]['maxchar']  = "30";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['rentstuff']['available'];
	$this->config['result_fields'][$z]['sqlrow']   = "rs.quantity";
	$this->config['result_fields'][$z]['row']      = "quantity";
	$this->config['result_fields'][$z]['width']    = "15%";
	$this->config['result_fields'][$z]['maxchar']  = "4";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['rentstuff']['rent'];
	$this->config['result_fields'][$z]['sqlrow']   = "rs.rented";
	$this->config['result_fields'][$z]['row']      = "rented";
	$this->config['result_fields'][$z]['align']    = "right";
	$this->config['result_fields'][$z]['width']    = "15%";
	$this->config['result_fields'][$z]['maxchar']  = "4";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['rentstuff']['owner'];
	$this->config['result_fields'][$z]['sqlrow']   = "u.username";
	$this->config['result_fields'][$z]['row']      = "username";
	$this->config['result_fields'][$z]['align']    = "right";
	$this->config['result_fields'][$z]['width']    = "30%";
	$this->config['result_fields'][$z]['maxchar']  = "15";
	$this->config['result_fields'][$z]['profil']   = "1";	

?>
