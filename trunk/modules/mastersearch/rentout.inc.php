<?php
					
	// Ab hier die Konfiguration
	$this->config['search_fields'][]  = "u.username";
//	$this->config['search_fields'][]  = "ru.userid";
//	$this->config['search_fields'][]  = "u.username";
	$this->config['sql_statment']     = "SELECT ru.userid, u.username FROM {$config["tables"]["rentuser"]} AS ru LEFT JOIN {$config["tables"]["user"]} AS u ON ru.userid=u.userid LEFT JOIN {$config["tables"]["rentstuff"]} AS rs ON ru.stuffid=rs.stuffid ";
	$this->config['title']            = $lang['ms']['rentout']['title'];
	$this->config['userid']           = "userid";
	$this->config['linkcol']          = "userid";
	$this->config['orderby']          = "u.username,ASC";
	$this->config['entrys_page']      = "5";
										// $config["size"]["table_rows"]; // Hier kanste definieren wieviele einträge du pro seite ausgegeben bekommen willst
	$this->config['no_items_caption'] = $lang['ms']['rentout']['no_items_caption'];
	$this->config['no_items_link']	  = "index.php?mod=rent&action=rentstuff";
	
	$z = 0;
	// Spaltenname
	$this->config['result_fields'][$z]['name']     = $lang['ms']['rentback']['leaser'];
	$this->config['result_fields'][$z]['sqlrow']   = "u.username";
	$this->config['result_fields'][$z]['row']      = "username";
	$this->config['result_fields'][$z]['width']    = "60%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$this->config['result_fields'][$z]['profil']   = "1";	
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['rentout']['number'];
	$this->config['result_fields'][$z]['sqlrow']   = "userid";
	$this->config['result_fields'][$z]['row']      = "userid";
	$this->config['result_fields'][$z]['callback'] = "GetRentTotal";
	$this->config['result_fields'][$z]['width']    = "40%";
	$this->config['result_fields'][$z]['maxchar']  = "0";
	$z++;


?>
