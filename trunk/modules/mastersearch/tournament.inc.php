<?php

	// Ab hier die Konfiguration
	$this->config['search_fields'][]  = "t.name";
	$this->config['search_fields'][]  = "t.game";
	$this->config['sql_statment']     = "SELECT t.*, COUNT(teams.tournamentid) AS teamanz
										FROM {$config["tables"]["tournament_tournaments"]} AS t
										LEFT JOIN {$config["tables"]["t2_teams"]} AS teams ON t.tournamentid = teams.tournamentid
										";

	$this->config['sql_additional']	  = " AND t.party_id = '{$party->party_id}' GROUP BY t.tournamentid";

	$this->config['title']            = $lang['ms']['tournament']['title'];
	$this->config['orderby']          = "t.name,ASC";
	$this->config['linkcol']          = "tournamentid";
	$this->config['entrys_page']      = $config["size"]["table_rows"]; // Hier kanste definieren wieviele einträge du pro seite ausgegeben bekommen willst

	$this->config['hidden_searchform'] = true;	

	$this->config['no_items_caption'] = $lang['ms']['tournament']['no_items_caption'];
	$this->config['no_items_link']	  = "";
	
	$this->config['inputs'][1]['title']   = $lang['ms']['tournament']['state'];
	$this->config['inputs'][1]['name']    = "search_select";
	$this->config['inputs'][1]['type']    = "select";
	$this->config['inputs'][1]['options'] = array( "all"=>$lang['ms']['tournament']['all'], "open"=>$lang['ms']['tournament']['signon'], "process"=>$lang['ms']['tournament']['progress'], "closed"=>$lang['ms']['tournament']['finished']);
	$this->config['inputs'][1]['sql'][1]     = "t.status"; 
		
	$z = 0;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['tournament']['name'];
	$this->config['result_fields'][$z]['sqlrow']   = "t.name";
	$this->config['result_fields'][$z]['row'][0]    = "name";
	$this->config['result_fields'][$z]['row'][1]    = "over18";
	$this->config['result_fields'][$z]['row'][2]    = "tournamentid";
	$this->config['result_fields'][$z]['row'][3]    = "icon";
	$this->config['result_fields'][$z]['row'][4]    = "wwcl_gameid";
	$this->config['result_fields'][$z]['row'][5]    = "ngl_gamename";
	$this->config['result_fields'][$z]['callback'] = "GetTournamentName";
	$this->config['result_fields'][$z]['width']    = "40%";
	$this->config['result_fields'][$z]['maxchar']  = "12";
	$z++;
		
	$this->config['result_fields'][$z]['name']     = $lang['ms']['tournament']['start'];
	$this->config['result_fields'][$z]['sqlrow']   = "t.starttime";
	$this->config['result_fields'][$z]['row']      = "starttime";
	$this->config['result_fields'][$z]['callback'] = "GetDate";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "0";
	$z++;

	$this->config['result_fields'][$z]['name']     = $lang['ms']['tournament']['player'];
	$this->config['result_fields'][$z]['sqlrow']   = "teamanz";
	$this->config['result_fields'][$z]['row'][0]      = "teamanz";
	$this->config['result_fields'][$z]['row'][1]      = "maxteams";
	$this->config['result_fields'][$z]['row'][2]      = "tournamentid";
	$this->config['result_fields'][$z]['callback'] = "GetTournamentTeamAnz";
	$this->config['result_fields'][$z]['width']    = "12%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$z++;

	$this->config['result_fields'][$z]['name']     = $lang['ms']['tournament']['state'];
	$this->config['result_fields'][$z]['sqlrow']   = "t.status";
	$this->config['result_fields'][$z]['row']      = "status";
	$this->config['result_fields'][$z]['callback'] = "GetTournamentStatus";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "0";
	$z++;
?>
