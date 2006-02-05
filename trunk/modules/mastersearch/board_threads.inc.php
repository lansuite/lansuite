<?php
	$this->config['search_fields'][]  = "t.caption";
	$this->config['sql_statment']     = "SELECT t.*, r.last_read FROM {$config["tables"]["board_threads"]} AS t
										LEFT JOIN {$config["tables"]["board_read_state"]} AS r ON t.tid = r.tid AND r.userid = '{$auth["userid"]}'";
	$this->config['title']            = $lang['ms']['board_threads']['title'];
	$this->config['orderby']          = "last_pid, DESC";
	$this->config['userid']           = "userid";
	$this->config['linkcol']          = "tid";
	$this->config['entrys_page']      = $config["size"]["table_rows"];
	
	$this->config['no_items_caption'] = $lang['ms']['board_threads']['no_items_caption'];
	
	$z = 0;
	// Spaltenname
	$this->config['result_fields'][$z]['iconname'] = "arrows_forum.gif";
	$this->config['result_fields'][$z]['name']     = $lang['ms']['board_bm']['title_'];
	$this->config['result_fields'][$z]['sqlrow']   = "t.caption";
	$this->config['result_fields'][$z]['row']      = "caption";
	$this->config['result_fields'][$z]['width']    = "30%";
	$this->config['result_fields'][$z]['maxchar']  = "30";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['board_threads']['new'];
	$this->config['result_fields'][$z]['sqlrow']   = "t.last_read";
	$this->config['result_fields'][$z]['row'][0]   = "last_read";
	$this->config['result_fields'][$z]['row'][1]   = "last_pid";
	$this->config['result_fields'][$z]['row'][2]   = "date";
	$this->config['result_fields'][$z]['row'][3]   = "tid";
	$this->config['result_fields'][$z]['callback'] = "NewPosts";
	$this->config['result_fields'][$z]['list_only']= "1";
	$this->config['result_fields'][$z]['width']    = "10%";
	$this->config['result_fields'][$z]['maxchar']  = "10";
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
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$this->config['result_fields'][$z]['fullprofil']   = "1";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['board_bm']['last_p'];
	$this->config['result_fields'][$z]['sqlrow']   = "t.last_pid";
	$this->config['result_fields'][$z]['row']      = "last_pid";
	$this->config['result_fields'][$z]['callback'] = "GetPostDate";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$this->config['result_fields'][$z]['ext_link'] = "pid=last";
	
	
?>
