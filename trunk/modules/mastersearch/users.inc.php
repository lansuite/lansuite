<?php

	// Ab hier die Konfiguration
	$this->config['search_fields'][]  = "u.userid";
	$this->config['search_type'][]  = "exact";
	$this->config['search_fields'][]  = "u.username";
	$this->config['search_type'][]  = "1337";
	$this->config['search_fields'][]  = "u.email";
	$this->config['search_type'][]  = "like";
	$this->config['search_fields'][]  = "u.name";
	$this->config['search_type'][]  = "1337";
	$this->config['search_fields'][]  = "u.firstname";
	$this->config['search_type'][]  = "1337";
	$this->config['search_fields'][]  = "u.clan";
	$this->config['search_type'][]  = "1337";

	$this->config['sql_statment']     = "SELECT * FROM {$config["tables"]["user"]} AS u LEFT JOIN {$config["tables"]["party_user"]} AS p ON u.userid = p.user_id ";
	
	$this->config['title']            = $lang['ms']['users']['title'];
	$this->config['orderby']          = "u.name,ASC";
	$this->config['userid']           = "userid";
	$this->config['linkcol']          = "userid";
	$this->config['entrys_page']      = $config["size"]["table_rows"]; // Hier kanste definieren wieviele einträge du pro seite ausgegeben bekommen willst

	$this->config["orderby_dropdown"]["u.username"]	= $lang['ms']['users']['username'];
	$this->config["orderby_dropdown"]["u.firstname"]	= $lang['ms']['users']['firstname'];
	$this->config["orderby_dropdown"]["u.name"]	= $lang['ms']['users']['lastname'];
	$this->config["orderby_dropdown"]["u.clan"]	= $lang['ms']['users']['clan'];
	$this->config["orderby_dropdown"]["u.email"]	= $lang['ms']['users']['email'];
	$this->config["orderby_dropdown"]["u.userid"]	= $lang['ms']['users']['userid'];
	$this->config["orderby_dropdown"]["u.logins "]	= $lang['ms']['users']['logins'];
	$this->config["orderby_dropdown"]["u.changedate"]	= $lang['ms']['users']['changedate'];
	
	$this->config['inputs'][1]['title']   = $lang['ms']['users']['type'];
	$this->config['inputs'][1]['name']    = "search_select1";
	$this->config['inputs'][1]['type']    = "select";
	$this->config['inputs'][1]['sql'][1]     = "u.type";
	$this->config['inputs'][1]['options'] = array( "all"=>"Alle", "1"=>"Benutzer", "2"=>"Administrator", "3"=>"Operator", "2;3"=>"Administrator und Operator");

	$this->config['inputs'][2]['title']   = $lang['ms']['users']['signedon'];
	$this->config['inputs'][2]['name']    = "search_select2";
	$this->config['inputs'][2]['type']    = "select";
	$this->config['inputs'][2]['sql'][1]     = "p.party_id";
	$this->config['inputs'][2]['options'] = array("all" => "Alle", "{$party->party_id}" => "Angemeldet");

	$this->config['inputs'][3]['title']   = $lang['ms']['users']['paid'];
	$this->config['inputs'][3]['name']    = "search_select3";
	$this->config['inputs'][3]['type']    = "select";
	$this->config['inputs'][3]['sql'][1]     = "p.party_id = {$party->party_id} AND p.paid";
	$this->config['inputs'][3]['options'] = array("all" => "Alle", "0" => "Nicht bezahlt", "1;2" => "Bezahlt");

	$this->config['inputs'][4]['title']   = $lang['ms']['users']['checkedin'];
	$this->config['inputs'][4]['name']    = "search_select4";
	$this->config['inputs'][4]['type']    = "select";
	$this->config['inputs'][4]['sql'][1]     = "p.party_id = {$party->party_id} AND p.checkin";
	$this->config['inputs'][4]['options'] = array("all" => "Alle", "0" => "Nicht eingecheckt", "!0" => "Eingecheckt");
	
	$this->config['inputs'][5]['title']   = $lang['ms']['users']['group'];
	$this->config['inputs'][5]['name']    = "search_select5";
	$this->config['inputs'][5]['type']    = "select";
	$this->config['inputs'][5]['sql'][1]     = "u.group_id";
	
	$row = $db->query("SELECT * FROM {$config['tables']['party_usergroups']}");
	$data = array("all" => "Alle", "0" => $lang['class_party']['drowpdown_no_group']);
	while ($res = $db->fetch_array($row)){
		if(is_array($data)){
			$data[$res['group_id']] = $res['group_name'];
		}
	}
	$this->config['inputs'][5]['options'] = $data;
	$this->config['no_items_caption'] = $lang['ms']['users']['no_items_caption'];

	$z = 0;
	// Spaltenname
	$this->config['result_fields'][$z]['name']     = $lang['ms']['users']['user'];
	$this->config['result_fields'][$z]['sqlrow']   = "u.username";
	$this->config['result_fields'][$z]['row']      = "username";
	$this->config['result_fields'][$z]['width']    = "22%";
	$this->config['result_fields'][$z]['maxchar']  = "14";
	$this->config['result_fields'][$z]['profil']   = "1";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['users']['lastname'];
	$this->config['result_fields'][$z]['sqlrow']   = "u.name";
	$this->config['result_fields'][$z]['row']      = "name";
	$this->config['result_fields'][$z]['callback'] = "CheckName";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "12";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['users']['firstname'];
	$this->config['result_fields'][$z]['sqlrow']   = "u.firstname";
	$this->config['result_fields'][$z]['row']      = "firstname";
	$this->config['result_fields'][$z]['callback'] = "CheckName";
	$this->config['result_fields'][$z]['width']    = "21%";
	$this->config['result_fields'][$z]['maxchar']  = "12";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['users']['clan'];
	$this->config['result_fields'][$z]['sqlrow']   = "u.clan";
	$this->config['result_fields'][$z]['row']      = "clan";
	$this->config['result_fields'][$z]['width']    = "16%";
	$this->config['result_fields'][$z]['maxchar']  = "12";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['users']['group'];
	$this->config['result_fields'][$z]['sqlrow']   = "u.group_id";
	$this->config['result_fields'][$z]['row']      = "group_id";
	$this->config['result_fields'][$z]['callback'] = "GetGroup";
	$this->config['result_fields'][$z]['width']    = "14%";
	$this->config['result_fields'][$z]['maxchar']  = "10";
	$z++;	
	// Hier klannste sehen wie ein Callback durchgeführt wird
	// Hier wir maxchar ignoriert da dies von der Function ausgeführt werden soll
	$this->config['result_fields'][$z]['name']     = $lang['ms']['users']['seat'];
	$this->config['result_fields'][$z]['sqlrow']   = "u.userid";
	$this->config['result_fields'][$z]['row']      = "userid";
	$this->config['result_fields'][$z]['callback'] = "GetSeat";
	$this->config['result_fields'][$z]['width']    = "7%";
	$this->config['result_fields'][$z]['maxchar']  = "0";
	$z++;
	
?>
