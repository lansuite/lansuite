<?php
					
	// Ab hier die Konfiguration
	$this->config['search_fields'][]  = "ru.back_orgaid";
	$this->config['search_fields'][]  = "ru.out_orgaid";
	$this->config['search_fields'][]  = "rs.caption";
	$this->config['search_fields'][]  = "u.username";
	$this->config['sql_statment']     = "SELECT * FROM {$config["tables"]["rentuser"]} AS ru LEFT JOIN {$config["tables"]["user"]} AS u ON ru.userid=u.userid LEFT JOIN {$config["tables"]["rentstuff"]} AS rs ON ru.stuffid=rs.stuffid";
	$this->config['title']            = $lang['ms']['rentback']['title'];
	$this->config['userid']           = "userid";
	$this->config['linkcol']          = "userid";
	$this->config['orderby']          = "u.username,ASC";
	$this->config['entrys_page']      = "5";
										// $config["size"]["table_rows"]; // Hier kanste definieren wieviele einträge du pro seite ausgegeben bekommen willst
	$this->config['no_items_caption'] = $lang['ms']['rentback']['no_items_caption'];
	$this->config['no_items_link']	  = "index.php?mod=rent&action=add_stuff";
	$this->config['list_only']	  	  = true;
		
	$z = 0;
	// Spaltenname
	$this->config['result_fields'][$z]['name']     = $lang['ms']['rentback']['leaser'];
	$this->config['result_fields'][$z]['sqlrow']   = "u.username";
	$this->config['result_fields'][$z]['row']      = "username";
	$this->config['result_fields'][$z]['width']    = "25%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$this->config['result_fields'][$z]['profil']   = "1";	
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['rentback']['equipment'];
	$this->config['result_fields'][$z]['sqlrow']   = "rs.caption";
	$this->config['result_fields'][$z]['row']      = "caption";
	$this->config['result_fields'][$z]['width']    = "35%";
	$this->config['result_fields'][$z]['maxchar']  = "40";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['rentback']['lessor'];
	$this->config['result_fields'][$z]['sqlrow']   = "ru.out_orgaid";
	$this->config['result_fields'][$z]['row']      = "out_orgaid";
	$this->config['result_fields'][$z]['callback'] = "GetUsername";
	$this->config['result_fields'][$z]['align']    = "right";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['rentback']['receiver'];
	$this->config['result_fields'][$z]['sqlrow']   = "ru.back_orgaid";
	$this->config['result_fields'][$z]['row']      = "back_orgaid";
	$this->config['result_fields'][$z]['callback'] = "GetUsername";
	$this->config['result_fields'][$z]['align']    = "right";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "20";


?>
