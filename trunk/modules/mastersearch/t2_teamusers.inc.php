<?php

	// K�nnen ben�tigte Klassen geladen werden
	$this->class['seat'] = new seat;

	// Ab hier die Konfiguration
	$this->config['search_fields'][]  = "u.userid";
	$this->config['search_fields'][]  = "u.username";
	$this->config['search_fields'][]  = "u.email";
	$this->config['search_fields'][]  = "u.name";
	$this->config['search_fields'][]  = "u.firstname";
	$this->config['search_fields'][]  = "u.clan";
	$this->config['where_available']  = TRUE;

	$this->config['sql_statment']     = "SELECT * FROM {$config["tables"]["user"]} AS u
										 LEFT JOIN {$config["tables"]["party_user"]} AS p ON p.user_id=u.userid
										WHERE p.party_id={$party->party_id} 
										"; 

	$this->config['no_items_caption'] = $lang['ms']['t2_teamusers']['no_items_caption'];
	$this->config['no_items_link']	  = "";	
	$this->config['title']            = $lang['ms']['t2_teamusers']['title'];
	$this->config['orderby']          = "u.name,ASC";
	$this->config['userid']           = "userid";
	$this->config['linkcol']          = "userid";
	$this->config['entrys_page']      = $config["size"]["table_rows"]; // Hier kanste definieren wieviele eintr�ge du pro seite ausgegeben bekommen willst
		
	$z = 0;
	// Spaltenname
	$this->config['result_fields'][$z]['name']     = $lang['ms']['users']['user'];
	// Hier wir die Spalte der MySQL DB angegeben die ist im normal Fall
	// identisch mit row, bei LEFT JOINs m�ssen allerdings die table mit angegeben
	// werden -> u. . und u.username kannste nicht im Array -> $row['u.username'] angeben
	$this->config['result_fields'][$z]['sqlrow']   = "u.username";
	// Hier ist kannst angeben wie du das Resultarray ansprichst $row['username']
	$this->config['result_fields'][$z]['row']      = "username";
	// Hier nat�rlich selbsterkl�rend
	$this->config['result_fields'][$z]['width']    = "22%";
	// Diesen Wert bitte immer angeben es sein den man weris das der Wert immer in die Spalte passt
	// Der wert 0 hei�t deaktiviert. Bedenke das alle Spalten nowrap sind das hei�t keine Umbr�che
	$this->config['result_fields'][$z]['maxchar']  = "14";
	// hier kannste sagen das es sich hier bei um einen Eintrag handelt
	// Welches ein Profillink angegeben bekommen soll. 0 = deaktiviert
	$this->config['result_fields'][$z]['profil']   = "1";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['users']['lastname'];
	$this->config['result_fields'][$z]['sqlrow']   = "u.name";
	$this->config['result_fields'][$z]['row']      = "name";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "12";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['users']['firstname'];
	$this->config['result_fields'][$z]['sqlrow']   = "u.firstname";
	$this->config['result_fields'][$z]['row']      = "firstname";
	$this->config['result_fields'][$z]['width']    = "21%";
	$this->config['result_fields'][$z]['maxchar']  = "12";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['users']['clan'];
	$this->config['result_fields'][$z]['sqlrow']   = "u.clan";
	$this->config['result_fields'][$z]['row']      = "clan";
	$this->config['result_fields'][$z]['width']    = "16%";
	$this->config['result_fields'][$z]['maxchar']  = "12";
	$z++;
	// Hier klannste sehen wie ein Callback durchgef�hrt wird
	// Hier wir maxchar ignoriert da dies von der Function ausgef�hrt werden soll
	$this->config['result_fields'][$z]['name']     = $lang['ms']['users']['seat'];
	$this->config['result_fields'][$z]['sqlrow']   = "u.userid";
	$this->config['result_fields'][$z]['row']      = "userid";
	$this->config['result_fields'][$z]['callback'] = "GetSeat";
	$this->config['result_fields'][$z]['width']    = "21%";
	$this->config['result_fields'][$z]['maxchar']  = "0";
	$z++;
	
?>
