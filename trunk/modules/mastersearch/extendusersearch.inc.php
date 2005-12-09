<?php

	// Können benötigte Klassen geladen werden
	$this->class['seat'] = new seat;
					
	// Ab hier die Konfiguration
/*	$this->config['search_fields'][]  = "u.userid";
	$this->config['search_fields'][]  = "u.username";
	$this->config['search_fields'][]  = "u.email";
	$this->config['search_fields'][]  = "u.name";
	$this->config['search_fields'][]  = "u.firstname";
	$this->config['search_fields'][]  = "u.clan";
*/	$this->config['sql_statment']     = "SELECT * FROM {$config["tables"]["user"]} AS u LEFT JOIN {$config["tables"]["party_user"]} AS p ON u.userid=p.user_id ";
	$this->config['title']            = "Benutzerauswahl:";
	$this->config['orderby']          = "u.name,ASC";
	$this->config['userid']           = "userid";
	$this->config['linkcol']          = "userid";
	$this->config['entrys_page']      = 30;
	// $config["size"]["table_rows"]; // Hier kanste definieren wieviele einträge du pro seite ausgegeben bekommen willst
	$this->config['no_items_caption'] = "Es wurde keine Benutzer nach Ihren Abfrageregeln gefunden.";
	$this->config['no_items_link']	  = "index.php?mod=usrmgr&action=search&step=2";
	
	$z = 0;
	// Spaltenname
	$this->config['result_fields'][$z]['name']     = "Benutzer";
	// Hier wir die Spalte der MySQL DB angegeben die its im normal Fall
	// identisch mit row, bei LEFT JOINs müssen allerdings die table mit angegeben
	// werden -> u. . und u.username kannste nicht im Array -> $row['u.username'] angeben
	$this->config['result_fields'][$z]['sqlrow']   = "u.username";
	// Hier ist kannst angeben wie du das Resultarray ansprichst $row['username']
	$this->config['result_fields'][$z]['row']      = "username";
	// Hier natürlich selbsterklärend
	$this->config['result_fields'][$z]['width']    = "22%";
	// Diesen Wert bitte immer angeben es sein den man weris das der Wert immer in die Spalte passt
	// Der wert 0 heißt deaktiviert. Bedenke das alle Spalten nowrap sind das heißt keine Umbrüche
	$this->config['result_fields'][$z]['maxchar']  = "14";
	// hier kannste sagen das es sich hier bei um einen Eintrag handelt
	// Welches ein Profillink angegeben bekommen soll. 0 = deaktiviert
//	$this->config['result_fields'][$z]['profil']   = "1";
	$z++;
	$this->config['result_fields'][$z]['name']     = "Nachname";
	$this->config['result_fields'][$z]['sqlrow']   = "u.name";
	$this->config['result_fields'][$z]['row']      = "name";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "12";
	$z++;
	$this->config['result_fields'][$z]['name']     = "Vorname";
	$this->config['result_fields'][$z]['sqlrow']   = "u.firstname";
	$this->config['result_fields'][$z]['row']      = "firstname";
	$this->config['result_fields'][$z]['width']    = "21%";
	$this->config['result_fields'][$z]['maxchar']  = "12";
	$z++;
	$this->config['result_fields'][$z]['name']     = "Clan";
	$this->config['result_fields'][$z]['sqlrow']   = "u.clan";
	$this->config['result_fields'][$z]['row']      = "clan";
	$this->config['result_fields'][$z]['width']    = "16%";
	$this->config['result_fields'][$z]['maxchar']  = "12";
	$z++;
	// Hier klannste sehen wie ein Callback durchgeführt wird
	// Hier wir maxchar ignoriert da dies von der Function ausgeführt werden soll
	$this->config['result_fields'][$z]['name']     = "Sitzplatz";
	$this->config['result_fields'][$z]['sqlrow']   = "u.userid";
	$this->config['result_fields'][$z]['row']      = "userid";
	$this->config['result_fields'][$z]['callback'] = "GetSeat";
	$this->config['result_fields'][$z]['width']    = "21%";
	$this->config['result_fields'][$z]['maxchar']  = "0";
	$z++;
	
?>
