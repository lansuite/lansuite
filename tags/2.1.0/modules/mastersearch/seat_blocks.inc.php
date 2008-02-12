<?php
	// Ab hier die Konfiguration
	$this->config['search_fields'][]  = "b.name";
	$this->config['sql_statment']     = "SELECT b.blockid, b.name FROM {$config["tables"]["seat_block"]} AS b";
	$this->config['title']            = $lang['ms']['seating']['choose_block'];
	$this->config['orderby']          = "b.name, ASC";
	$this->config['userid']           = "userid";
	$this->config['linkcol']          = "blockid";
	$this->config['entrys_page']      = $config["size"]["table_rows"]; // Hier kanste definieren wieviele einträge du pro seite ausgegeben bekommen willst
	$this->config['hidden_searchform'] = true;

	$this->config['where']	= "b.party_id = {$party->party_id}";

	$this->config['no_items_caption'] = $lang['ms']['seat']['no_items_caption'];

	$z = 0;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['seating']['name'];
	$this->config['result_fields'][$z]['sqlrow']   = "b.name";
	$this->config['result_fields'][$z]['row']      = "name";
	$this->config['result_fields'][$z]['width']    = "30%";
	$this->config['result_fields'][$z]['maxchar']  = "30";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['seating']['seats'];
	$this->config['result_fields'][$z]['sqlrow']   = "b.blockid";
	$this->config['result_fields'][$z]['row']      = "blockid";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "10";
	$this->config['result_fields'][$z]['callback'] = "SeatsAvailable";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['seating']['occupied'];
	$this->config['result_fields'][$z]['sqlrow']   = "b.blockid";
	$this->config['result_fields'][$z]['row']      = "blockid";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "10";
	$this->config['result_fields'][$z]['callback'] = "SeatsOccupied";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['seating']['load'];
	$this->config['result_fields'][$z]['sqlrow']   = "b.blockid";
	$this->config['result_fields'][$z]['row']      = "blockid";
	$this->config['result_fields'][$z]['width']    = "30%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$this->config['result_fields'][$z]['callback'] = "SeatLoad";
	$z++;

?>