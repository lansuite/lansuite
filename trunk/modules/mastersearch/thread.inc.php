<?php
	// Ab hier die Konfiguration
	$this->config['search_fields'][]  = "t.caption";
	$this->config['search_fields'][]  = "p.comment";
	$this->config['search_fields'][]  = "b.name";
	$this->config['sql_statment']     = "SELECT b.name, t.* FROM {$config["tables"]["board_threads"]} AS t
										LEFT JOIN {$config["tables"]["board_forums"]} AS b ON t.fid = b.fid
										LEFT JOIN {$config["tables"]["board_posts"]} AS p ON p.tid = t.tid";
	$this->config['title']            = $lang['ms']['thread']['title'];
	$this->config['orderby']          = "t.caption,ASC";
	$this->config['userid']           = "t.userid";
	$this->config['linkcol']          = "tid";
	$this->config['entrys_page']      = $config["size"]["table_rows"]; // Hier kanste definieren wieviele einträge du pro seite ausgegeben bekommen willst
	
	$this->config['no_items_caption'] = $lang['ms']['thread']['no_items_caption'];
	
	$z = 0;
	// Spaltenname
	$this->config['result_fields'][$z]['name']     = $lang['ms']['thread']['title_'];
	$this->config['result_fields'][$z]['sqlrow']   = "t.caption";
	$this->config['result_fields'][$z]['row']      = "caption";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$this->config['result_fields'][$z]['profil']   = "1";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['thread']['text'];
	$this->config['result_fields'][$z]['sqlrow']   = "p.comment";
	$this->config['result_fields'][$z]['row']      = "comment";
	$this->config['result_fields'][$z]['width']    = "60%";
	$this->config['result_fields'][$z]['maxchar']  = "60";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['thread']['board'];
	$this->config['result_fields'][$z]['sqlrow']   = "b.name";
	$this->config['result_fields'][$z]['row']      = "name";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
?>
