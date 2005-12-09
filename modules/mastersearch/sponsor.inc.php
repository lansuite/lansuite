<?php
	$this->config['search_fields'][]  = "s.name";
	$this->config['search_fields'][]  = "s.url";
	$this->config['sql_statment']     = "SELECT * FROM {$config["tables"]["sponsor"]} s";
					
	$this->config['orderby']          = "s.name, ASC";
	$this->config['linkcol']          = "sponsorid";
	$this->config['entrys_page']      = $config["size"]["table_rows"];

	$this->config['hidden_searchform'] = true;	

	$this->config['no_items_caption'] = $lang['ms']['sponsor']['no_items_caption'];
	$this->config['no_items_link']	  = "";

	$z = 0;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['sponsor']['name'];
	$this->config['result_fields'][$z]['sqlrow']   = "s.name";
	$this->config['result_fields'][$z]['row']      = "name";
	$this->config['result_fields'][$z]['width']    = "30%";
	$this->config['result_fields'][$z]['maxchar']  = "16";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['sponsor']['url'];
	$this->config['result_fields'][$z]['sqlrow']   = "s.url";
	$this->config['result_fields'][$z]['row']      = "url";
	$this->config['result_fields'][$z]['width']    = "70%";
	$this->config['result_fields'][$z]['maxchar']  = "32";
	$z++;
?>
