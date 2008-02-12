<?php

// Können benötigte Klassen geladen werden
	$this->class['seat'] = new seat;
					
	// Ab hier die Konfiguration

// Diese Felder werden durchsucht
	$this->config['search_fields'][]  = "u.userid";
	$this->config['search_fields'][]  = "u.username";
	$this->config['search_fields'][]  = "u.email";
	$this->config['search_fields'][]  = "u.name";
	$this->config['search_fields'][]  = "u.firstname";
	$this->config['search_fields'][]  = "u.clan";

// Das SQL Statment ganz nach ihreren Wunsch :)
	$this->config['sql_statment']     = "SELECT * FROM {$config["tables"]["user"]} AS u ";

// WHERE Parameter											
	$this->config['sql_additional'] = "sample = '1'";
	
// Text der über der Ausgabe steht
	$this->config['title']            = "Benutzerauswahl:";

// Die Sortierreinfolge
	$this->config['orderby']          = "u.name,ASC";

// Die Spalte der Userid für der User-Icon hinter einem Usernamen
	$this->config['userid']           = "userid";

// Dieser Parameter ist die SQL-Tabellenspalte, die letztendlich an den Link angehängt wird
	$this->config['linkcol']          = "userid";

// Hier kanste definieren wieviele einträge du pro seite ausgegeben bekommen willst
	$this->config['entrys_page']      = $config["size"]["table_rows"];

// Wenn nichts gefunden wird dier Text ausgebeben	
	$this->config['no_items_caption'] = "Es wurde noch kein Equipment zur&uuml;ckgenommen. Evt. wurde aber auch das Equipment, und damit auch die Zuordnungen gel&ouml;scht.";

// Der Link der auf den "Zurück" Button gelegt wird. Wenn LEER dann wird das Button nicht angezeigt
	$this->config['no_items_link']	  = "index.php?mod=rent&action=add_stuff";

// Wenn True, ist die Ausgabe nur eine Liste, die Reihen somit nicht anklickbar
	$this->config['list_only']	  	  = false;

// Unterdrückt das "Suchen-Formular", wenn keine Ergebnisse gefunden wurden.
	$this->config['hidden_searchform'] = true;	
	
// Defeniert die Ausgabe von der CallBack-Funktion "GetDate"
// 'date' = Tag.Monat.Jahr / 'time' = Stunde:Minute / 'datetime' = date + time / 'daydatetime' = Wochentag, date + time / 'shortdaytime' = Tag kurz, Stunde:Minute
// default: 'datetime'
	$this->config['datetime_format']  = "shortdaytime";	
	
// Wenn sie ein Drop-Down Liste im Suchen-Formular haben wollen
	$this->config['inputs'][1]['title']   = "Type";
	$this->config['inputs'][1]['name']    = "search_type";
	$this->config['inputs'][1]['type']    = "select";
	$this->config['inputs'][1]['options'] = array( "all"=>"Alle", "admin"=>"Administrator", "user"=>"Benutzer");

	$z = 0;

// Spaltenname
	$this->config['result_fields'][$z]['name']     = "Benutzer";

// Hier wir die Spalte der MySQL DB angegeben die its im normal Fall
// identisch mit row, bei LEFT JOINs müssen allerdings die table mit angegeben
// werden -> u. . und u.username kannste nicht im Array -> $row['u.username'] angeben
	$this->config['result_fields'][$z]['sqlrow']   = "u.username";

// Hier ist kannst angeben wie du das Resultarray ansprichst $row['username']
	$this->config['result_fields'][$z]['row']      = "username";

// Die Breite des Tabelleneintrags (<td width="">)
	$this->config['result_fields'][$z]['width']    = "22%";

// Wenn der Ausgabestring länger als maxchar ist, wird er an der Stelle abgeschnitten und 3 Punkte angehangen
// Diesen Wert bitte immer angeben es sein den man weis, dass der Wert immer in die Spalte passt
// Der wert 0 heißt deaktiviert. Bedenke das alle Spalten nowrap sind das heißt keine Umbrüche
	$this->config['result_fields'][$z]['maxchar']  = "14";

// hier kannste sagen das es sich hier bei um einen Eintrag handelt
// Welches ein Profillink angegeben bekommen soll. 0 = deaktiviert
	$this->config['result_fields'][$z]['profil']   = "1";
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

// Eigene Callback-Routinen können derzeit nicht eingebunden werden (class_mastersearch.php). 
// Am besten uns einfach zusenden und wir bauen sie mit ein.

// Fügt ein wählbares Icon mit einem seperaten Link in die Mastersearch ein und fügt "linkcol" an
	$this->config['result_fields'][$z]['name']     = "";
	$this->config['result_fields'][$z]['sqlrow']   = "";
	$this->config['result_fields'][$z]['row']      = "";
	$this->config['result_fields'][$z]['iconlink'] = "index.php?mod=mail&action=deletemail&mailID=";
	$this->config['result_fields'][$z]['iconname'] = "arrows_delete.gif";
	$this->config['result_fields'][$z]['img_alt']  = "L&ouml;schen";
	$this->config['result_fields'][$z]['width']    = "1%";
	$z++;

// zum einbau:
// 	$this->config['result_fields'][$z]['img_alt']  = "L&ouml;schen";
?>
