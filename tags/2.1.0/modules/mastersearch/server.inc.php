<?php
	// Ab hier die Konfiguration
	$this->config['search_fields'][]  = "s.type";
	$this->config['search_fields'][]  = "s.caption";
	$this->config['search_fields'][]  = "s.ip";
	$this->config['search_fields'][]  = "s.port";
	$this->config['search_fields'][]  = "u.username";
	$this->config['search_fields'][]  = "s.pw";
	$this->config['search_fields'][]  = "s.scans";
	$this->config['search_fields'][]  = "s.success";
	$this->config['sql_statment']     = "SELECT s.*, u.username, u.userid FROM {$config["tables"]["server"]} AS s
										LEFT JOIN {$config["tables"]["user"]} AS u ON s.owner = u.userid ";
	$this->config['title']            = $lang['ms']['server']['title'];
	$this->config['orderby']          = "s.caption, ASC";
	$this->config['userid']           = "userid";
	$this->config['linkcol']          = "serverid";
	$this->config['entrys_page']      = $config["size"]["table_rows"]; // Hier kanste definieren wieviele einträge du pro seite ausgegeben bekommen willst

	$this->config['no_items_caption'] = $lang['ms']['server']['no_items_caption'];
	
	$z = 0;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['server']['type'];
	$this->config['result_fields'][$z]['sqlrow']   = "s.type";
	$this->config['result_fields'][$z]['row']      = "type";
	$this->config['result_fields'][$z]['callback'] = "server_type";
	$this->config['result_fields'][$z]['width']    = "10%";
	$this->config['result_fields'][$z]['maxchar']  = "15";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['server']['name'];
	$this->config['result_fields'][$z]['sqlrow']   = "s.caption";
	$this->config['result_fields'][$z]['row']      = "caption";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['server']['ip'];
	$this->config['result_fields'][$z]['sqlrow']   = "s.ip";
	$this->config['result_fields'][$z]['row']      = "ip";
	$this->config['result_fields'][$z]['width']    = "15%";
	$this->config['result_fields'][$z]['maxchar']  = "16";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['server']['port'];
	$this->config['result_fields'][$z]['sqlrow']   = "s.port";
	$this->config['result_fields'][$z]['row']      = "port";
	$this->config['result_fields'][$z]['width']    = "10%";
	$this->config['result_fields'][$z]['maxchar']  = "6";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['server']['owner'];
	$this->config['result_fields'][$z]['sqlrow']   = "u.username";
	$this->config['result_fields'][$z]['row']      = "username";
	$this->config['result_fields'][$z]['width']    = "15%";
	$this->config['result_fields'][$z]['maxchar']  = "15";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['server']['pw'];
	$this->config['result_fields'][$z]['sqlrow']   = "s.pw";
	$this->config['result_fields'][$z]['row']      = "pw";
	$this->config['result_fields'][$z]['callback'] = "server_pwicon";
	$this->config['result_fields'][$z]['width']    = "10%";
	$this->config['result_fields'][$z]['maxchar']  = "10";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['server']['state'];
	$this->config['result_fields'][$z]['sqlrow']   = "s.scans";
	$this->config['result_fields'][$z]['row']      = "scans";
	$this->config['result_fields'][$z]['callback'] = "server_status";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$z++;
	
?>
