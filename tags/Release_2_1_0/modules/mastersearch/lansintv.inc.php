<?php
	$this->config['search_fields'][]  = "lansintv.pfad";
	$this->config['sql_statment']     = "SELECT lansintv.pfad, lansintv.votes, lansintv.uploader, u.username, u.userid
										FROM {$config['tables']['lansintv']} AS lansintv
										LEFT JOIN {$config["tables"]["user"]} AS u ON lansintv.uploader = u.userid";
	$this->config['title']            = $lang['ms']['lansintv']['title'];
	$this->config['orderby']          = "lansintv.pfad, ASC";
	$this->config['userid']           = "userid";
	$this->config['linkcol']          = "pfad";
	$this->config['entrys_page']      = $config["size"]["table_rows"];

	$this->config['no_items_caption'] = $lang['ms']['lansintv']['no_items_caption'];

	$z = 0;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['lansintv']['clip'];
	$this->config['result_fields'][$z]['sqlrow']   = "lansintv.pfad";
	$this->config['result_fields'][$z]['row']      = "pfad";
	$this->config['result_fields'][$z]['width']    = "70%";
	$this->config['result_fields'][$z]['maxchar']  = "60";
	$this->config['result_fields'][$z]['profil']   = "0";
	$z++;

	$this->config['result_fields'][$z]['name']     = $lang['ms']['lansintv']['votes'];
	$this->config['result_fields'][$z]['sqlrow']   = "lansintv.votes";
	$this->config['result_fields'][$z]['row']      = "votes";
	$this->config['result_fields'][$z]['width']    = "5%";
	$this->config['result_fields'][$z]['maxchar']  = "5";
	$this->config['result_fields'][$z]['profil']   = "0";
	$z++;

	$this->config['result_fields'][$z]['name']     = $lang['ms']['lansintv']['uploader'];
	$this->config['result_fields'][$z]['sqlrow']   = "u.username";
	$this->config['result_fields'][$z]['row']      = "username";
	$this->config['result_fields'][$z]['width']    = "25%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$this->config['result_fields'][$z]['profil']   = "1";
	$z++;
?>
