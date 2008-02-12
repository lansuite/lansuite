<?php


// Diese Felder werden durchsucht
//	$this->config['search_fields'][]  = "u.userid";
	$this->config['search_fields'][]  = "u.username";
	$this->config['search_fields'][]  = "mm.subject";
//	$this->config['search_fields'][]  = "u.name";
//	$this->config['search_fields'][]  = "u.firstname";


// Das SQL Statment ganz nach ihreren Wunsch :)
	$this->config['sql_statment']     = "SELECT mm.mailID AS mailid, 
												mm.subject,
												mm.des_status,
												mm.tx_date, 
												u.username, 
												u.userid 
											FROM {$config["tables"]["mail_messages"]} AS mm 
											LEFT JOIN {$config["tables"]["user"]} AS u 
											ON mm.FromUserID = u.userid";

// WHERE Parameter											
	$this->config['sql_additional'] = " AND mm.toUserID = '{$_SESSION['auth']['userid']}' AND mail_status = 'active'";

// Text der über der Ausgabe steht
//	$this->config['title']            = $lang['ms']['mail_in']['title'];

// Die Sortierreinfolge
	$this->config['orderby']          = "mm.tx_date DESC";

// Die Spalte der Userid für der User-Icon hinter einem Usernamen
	$this->config['userid']           = "userid";

// Dieser Parameter ist die SQL-Tabellenspalte, die letztendlich an den Link angehängt wird
	$this->config['linkcol']          = "mailid";

// Hier kanste definieren wieviele einträge du pro seite ausgegeben bekommen willst
	$this->config['entrys_page']      = $config["size"]["table_rows"];

// Wenn nichts gefunden wird dier Text ausgebeben	
	$this->config['no_items_caption'] = $lang['ms']['mail_in']['no_items_caption'];

// Der Link der auf den "Zurück" Button gelegt wird. Wenn LEER dann wird das Button nicht angezeigt
	$this->config['no_items_link']	  = "";

// Wenn True, ist die Ausgabe nur eine Liste, die Reihen somit nicht anklickbar
	$this->config['list_only']	  	  = false;
	$this->config['datetime_format']  = "shortdaytime";
	$this->config['hidden_searchform'] = true;		

	
	$z = 0;

// Spaltenname
	$this->config['result_fields'][$z]['name']     = "";
	$this->config['result_fields'][$z]['sqlrow']   = "mm.des_status";
	$this->config['result_fields'][$z]['row']      = "des_status";
	$this->config['result_fields'][$z]['callback'] = "ParseInboxMailStatus";
	$this->config['result_fields'][$z]['width']    = "6%";
	$this->config['result_fields'][$z]['maxchar']  = "0";
	$z++;

	$this->config['result_fields'][$z]['name']     = $lang['ms']['mail_in']['sent'];
	$this->config['result_fields'][$z]['sqlrow']   = "mm.tx_date";
	$this->config['result_fields'][$z]['row']      = "tx_date";
	$this->config['result_fields'][$z]['callback'] = "GetDate";
	$this->config['result_fields'][$z]['width']    = "15%";
	$this->config['result_fields'][$z]['maxchar']  = "0";
	$z++;
	
	
	$this->config['result_fields'][$z]['name']     = $lang['ms']['mail_in']['from'];
	$this->config['result_fields'][$z]['sqlrow']   = "u.username";
	$this->config['result_fields'][$z]['row']      = "username";
	$this->config['result_fields'][$z]['callback'] = "GetSenderName";
	$this->config['result_fields'][$z]['width']    = "22%";
	$this->config['result_fields'][$z]['maxchar']  = "16";
	$this->config['result_fields'][$z]['profil']   = "1";
	$z++;

	$this->config['result_fields'][$z]['name']     = $lang['ms']['mail_in']['subject'];
	$this->config['result_fields'][$z]['sqlrow']   = "mm.subject";
	$this->config['result_fields'][$z]['row']      = "subject";
	$this->config['result_fields'][$z]['width']    = "58%";
	$this->config['result_fields'][$z]['maxchar']  = "40";
	$z++;

	$this->config['result_fields'][$z]['name']     = "";
	$this->config['result_fields'][$z]['sqlrow']   = "";
	$this->config['result_fields'][$z]['row']      = "";
	$this->config['result_fields'][$z]['iconlink'] = "index.php?mod=mail&action=inbox&step=99&mailid=";
	$this->config['result_fields'][$z]['iconname'] = "arrows_delete.gif";
	$this->config['result_fields'][$z]['width']    = "1%";
	$z++;

	
?>
