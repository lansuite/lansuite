<?php
	$this->config['search_fields'][]  = "t.caption";
	$this->config['sql_statment']     = "SELECT * FROM {$config["tables"]["board_bookmark"]} AS b
										LEFT JOIN {$config["tables"]["board_threads"]} AS t ON t.tid = b.tid
										";
	$this->config['title']            = $lang['ms']['board_bm']['title'];
	$this->config['orderby']          = "t.caption, ASC";
	$this->config['userid']           = "userid";
	$this->config['linkcol']          = "tid";
	$this->config['entrys_page']      = $config["size"]["table_rows"];
	
	$this->config['no_items_caption'] = $lang['ms']['board_bm']['no_items_caption'];
	
	$z = 0;
	// Spaltenname
	$this->config['result_fields'][$z]['iconname'] = "arrows_forum.gif";
	$this->config['result_fields'][$z]['name']     = $lang['ms']['board_bm']['title_'];
	$this->config['result_fields'][$z]['sqlrow']   = "t.caption";
	$this->config['result_fields'][$z]['row']      = "caption";
	$this->config['result_fields'][$z]['width']    = "40%";
	$this->config['result_fields'][$z]['maxchar']  = "30";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['board_bm']['hits'];
	$this->config['result_fields'][$z]['sqlrow']   = "t.views";
	$this->config['result_fields'][$z]['row']      = "views";
	$this->config['result_fields'][$z]['width']    = "10%";
	$this->config['result_fields'][$z]['maxchar']  = "6";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['board_bm']['replys'];
	$this->config['result_fields'][$z]['sqlrow']   = "t.posts";
	$this->config['result_fields'][$z]['row']      = "posts";
	$this->config['result_fields'][$z]['width']    = "10%";
	$this->config['result_fields'][$z]['maxchar']  = "6";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['board_bm']['created'];
	$this->config['result_fields'][$z]['sqlrow']   = "t.date";
	$this->config['result_fields'][$z]['row']      = "date";
	$this->config['result_fields'][$z]['callback'] = "GetDate";
	$this->config['result_fields'][$z]['width']    = "15%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$this->config['result_fields'][$z]['profil']   = "1";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['board_bm']['last_p'];
	$this->config['result_fields'][$z]['sqlrow']   = "t.last_pid";
	$this->config['result_fields'][$z]['row']      = "last_pid";
	$this->config['result_fields'][$z]['callback'] = "GetPostDate";
	$this->config['result_fields'][$z]['width']    = "15%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['board_bm']['email'];
	$this->config['result_fields'][$z]['sqlrow']   = "b.email";
	$this->config['result_fields'][$z]['row']      = "email";
	$this->config['result_fields'][$z]['width']    = "10%";
	$this->config['result_fields'][$z]['maxchar']  = "10";
?>
