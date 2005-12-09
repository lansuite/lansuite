<?php

	$this->config['search_fields'][]  = "p.comment";
	$this->config['search_type'][]  = "like";
	$this->config['search_fields'][]  = "u.username";
	$this->config['search_type'][]  = "1337";
	$this->config['search_fields'][]  = "p.ip";
	$this->config['search_type'][]  = "exact";

	$this->config['sql_statment']     = "SELECT u.username, p.* FROM {$config["tables"]["board_posts"]} AS p
		LEFT JOIN {$config["tables"]["user"]} AS u ON p.userid = u.userid
		";

	$this->config['title']            = $lang['ms']['board']['title'];
	$this->config['orderby']          = "p.date,ASC";
	$this->config['userid']           = "userid";
	$this->config['linkcol']          = "pid";
	$this->config['entrys_page']      = "50";
	$this->config['no_items_caption'] = $lang['ms']['board']['no_items_caption'];
	$this->config['no_items_link']	  = "";

	$this->config['result_fields'][0]['checkbox']   = "checkbox";
	$this->config['list_only'] = 1;

	$z = 0;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['board']['text'];
	$this->config['result_fields'][$z]['sqlrow']   = "p.comment";
	$this->config['result_fields'][$z]['row']      = "comment";
	$this->config['result_fields'][$z]['width']    = "50%";
	$this->config['result_fields'][$z]['maxchar']  = "40";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['board']['author'];
	$this->config['result_fields'][$z]['sqlrow']   = "u.username";
	$this->config['result_fields'][$z]['row']      = "username";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['board']['ip'];
	$this->config['result_fields'][$z]['sqlrow']   = "p.ip";
	$this->config['result_fields'][$z]['row']      = "ip";
	$this->config['result_fields'][$z]['width']    = "15%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['board']['date'];
	$this->config['result_fields'][$z]['sqlrow']   = "p.date";
	$this->config['result_fields'][$z]['row']      = "date";
	$this->config['result_fields'][$z]['callback'] = "GetDate";
	$this->config['result_fields'][$z]['width']    = "15%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$z++;

	$this->config['action_select']['select_all']	= $lang['ms']['select_all'];
	$this->config['action_select']['select_none']	= $lang['ms']['select_none'];
	$this->config['action_select']['hr']	= "------------------------";
	$this->config['action_select']['del']	= $lang['ms']['board']['del_post'];
	$this->config['action_secure']['del']	= 1;
#	$this->config['action_select']['ban']	= "IPs bannen";
#	$this->config['action_secure']['ban']	= 1;
?>
