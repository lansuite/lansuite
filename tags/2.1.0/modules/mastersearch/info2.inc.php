<?php

	$this->config['search_fields'][]  = "caption";
	$this->config['search_fields'][]  = "shorttext";
	$this->config['sql_statment']     = "SELECT * FROM {$config['tables']['info']}";
					
	$this->config['orderby']          = "caption, ASC";
	$this->config['linkcol']          = "infoID";
	$this->config['entrys_page']      = $config["size"]["table_rows"];

	$this->config['hidden_searchform'] = true;	

	$this->config['no_items_caption'] = $lang['ms']['info2']['no_items_caption'];
	$this->config['no_items_link']	  = "";

	$z = 0;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['info2']['caption'];
	$this->config['result_fields'][$z]['sqlrow']   = "caption";
	$this->config['result_fields'][$z]['row']      = "caption";
	$this->config['result_fields'][$z]['width']    = "30%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$this->config['result_fields'][$z]['checkbox']   = "checkbox";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['info2']['shorttext'];
	$this->config['result_fields'][$z]['sqlrow']   = "shorttext";
	$this->config['result_fields'][$z]['row']      = "shorttext";
	$this->config['result_fields'][$z]['width']    = "60%";
	$this->config['result_fields'][$z]['maxchar']  = "50";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['info2']['active'];
	$this->config['result_fields'][$z]['sqlrow']   = "active";
	$this->config['result_fields'][$z]['row']      = "active";
	$this->config['result_fields'][$z]['width']    = "10%";
	$this->config['result_fields'][$z]['maxchar']  = "2";
	$z++;

	$this->config['action_select']['select_all']	= $lang['ms']['select_all'];
	$this->config['action_select']['select_none']	= $lang['ms']['select_none'];
	$this->config['action_select']['hr']	= "------------------------";
	$this->config['action_select']['del']	= $lang['ms']['info2']['delete'];
	$this->config['action_select']['active']	= $lang['ms']['info2']['change_active'];
	$this->config['action_secure']['del']	= 1;
?>