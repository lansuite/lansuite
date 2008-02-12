<?php

	// Ab hier die Konfiguration
	$this->config['search_fields'][]  = "g.caption";
	$this->config['sql_statment']     = "SELECT * FROM {$config["tables"]["gallery"]} AS g";

	$this->config['sql_additional']	  = " AND status > '0'";
	$this->config['sql_additional']   .= ($_SESSION['auth']['type'] <=1) ? " AND status <= 3" : "";

	$this->config['title']            = $lang['ms']['gallery']['title'];
	$this->config['orderby']          = "g.caption,ASC";
	$this->config['userid']           = "userid";
	$this->config['linkcol']          = "galleryid";
	$this->config['entrys_page']      = "10";
	// $config["size"]["table_rows"]; // Hier kanste definieren wieviele einträge du pro seite ausgegeben bekommen willst
	$this->config['no_items_caption'] = $lang['ms']['gallery']['no_items_caption'];
	$this->config['no_items_link']	  = "";

	
	$z = 0;
	// Spaltenname
	$this->config['result_fields'][$z]['name']     = $lang['ms']['gallery']['name'];
	$this->config['result_fields'][$z]['sqlrow']   = "g.caption";
	$this->config['result_fields'][$z]['row']      = "caption";
	$this->config['result_fields'][$z]['width']    = "60%";
	$this->config['result_fields'][$z]['maxchar']  = "50";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['gallery']['anz'];
	$this->config['result_fields'][$z]['sqlrow']   = "g.galleryid";
	$this->config['result_fields'][$z]['row']      = "galleryid";
	$this->config['result_fields'][$z]['callback'] = "GetPicTotal";
	$this->config['result_fields'][$z]['width']    = "25%";
	$this->config['result_fields'][$z]['maxchar']  = "50";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['gallery']['state'];
	$this->config['result_fields'][$z]['sqlrow']   = "g.galleryid";
	$this->config['result_fields'][$z]['row']      = "galleryid";
	$this->config['result_fields'][$z]['callback'] = "GetGalleryStatus";
	$this->config['result_fields'][$z]['width']    = "15%";
	$this->config['result_fields'][$z]['maxchar']  = "50";
	$z++;

?>
