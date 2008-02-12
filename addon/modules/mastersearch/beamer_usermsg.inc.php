<?
// Die SQL-Abfrage. Wichtig ist, dass hier nur die Einträge ausgewählt werden und die Tabelle bestimmt wird.
// Also nur SELECT, FROM und JOIN
// Kein: WHERE, GROUP BY, ORDER BY, HAVING, LIMIT, ...
$this->config['sql_statment']     = "SELECT m.mID, m.uID, m.bID, m.text, m.msgtyp, b.bezeichnung, IF(m.msgtyp='loop', '{$lang['beamer']['search']['loop']}','{$lang['beamer']['search']['once']}') AS msgtyp FROM {$config["tables"]["beamer_msg"]} AS m LEFT JOIN {$config["tables"]["beamer_beamer"]} AS b ON b.bID = m.bID";

// Hier wird angegeben in welchen SQL-Feldern gesucht werden soll.
// Diese Felder werden mit OR verknüpft (also Zeichenkette in Feld1, oder in Feld2)
$this->config['search_fields'][]  = "m.text";
$this->config['search_type'][]  = "like";

$z=1;
$this->config['inputs'][$z]['title']   = $lang['beamer']['search']['beamer'];
$this->config['inputs'][$z]['name']    = "search_select2";
$this->config['inputs'][$z]['type']    = "select";
$this->config['inputs'][$z]['sql'][1]  = "m.bID";

	$row = $db->query("SELECT * FROM {$config['tables']['beamer_beamer']} ORDER BY bezeichnung");
	$data = array("all" => $lang['beamer']['search']['all']);
	while ($res = $db->fetch_array($row)){
		if(is_array($data)){
			$data[$res['bID']] = $res['bezeichnung'];
		}
	}
$this->config['inputs'][$z]['options'] = $data;
$z++;

// Der Titel der Suche.
$this->config['title']            = "User-MSG löschen:";
// ORDER BY - Teil des SQL-Aufrufes
$this->config['orderby']          = "m.text, ASC";
// Name des Userid-Feldes. Ist wichtig, wenn zu einem Benutzernamen das Profil-icon angezeigt werden soll
$this->config['userid']           = "uID";
// Der Wert dieses SQL-Feldes wird hinter die jeweilige Ziel-URL gehängt
$this->config['linkcol']          = "mID";
// Wie viele EintrÃƒÂ¤ge werden pro Seite angezeigt?
$this->config['entrys_page']      = "50";
// Welcher Text wird ausgegeben, wenn keine Einträge vorhanden sind?
$this->config['no_items_caption'] = "Es sind keine User-MSGs vorhanden.";
// Wird ein Link ausgegeben, wenn keine Einträge vorhanden sind?
$this->config['no_items_link']	  = "";

// Sollen mehrere Einträge ausgewählt werden können?
//$this->config['result_fields'][0]['checkbox']   = "checkbox";
// Sind die Einträge anklickbar?
//$this->config['list_only'] = 1;

// Hier wird angegeben, welche Felder in der Ausgabe zu sehen sind
$z = 0;
$this->config['result_fields'][$z]['name']     = $lang['beamer']['search']['text']; // Spalten-Überschrift
$this->config['result_fields'][$z]['sqlrow']   = "m.text"; // SQL-Spalte, die hier ausgegeben werden soll
$this->config['result_fields'][$z]['row']      = "text"; // Spaltenname, oder "x."
$this->config['result_fields'][$z]['width']    = "40%"; // Breite der HTML-Spalte
$this->config['result_fields'][$z]['maxchar']  = "40"; // Maximale Zeichenlänge, nach der Abgeschnitten wird
$z++;
$this->config['result_fields'][$z]['name']     = $lang['beamer']['search']['beamer'];
$this->config['result_fields'][$z]['sqlrow']   = "b.bezeichnung";
$this->config['result_fields'][$z]['row']      = "bezeichnung";
$this->config['result_fields'][$z]['width']    = "15%";
$this->config['result_fields'][$z]['maxchar']  = "20";
$z++;
$this->config['result_fields'][$z]['name']     = $lang['beamer']['search']['ersteller'];
$this->config['result_fields'][$z]['sqlrow']   = "m.uID";
$this->config['result_fields'][$z]['row']      = "uID";
$this->config['result_fields'][$z]['callback'] = "GetUsername";
$this->config['result_fields'][$z]['width']    = "16%";
$this->config['result_fields'][$z]['maxchar']  = "20";
$z++;
$this->config['result_fields'][$z]['name']     = $lang['beamer']['search']['typ'];
$this->config['result_fields'][$z]['sqlrow']   = "m.msgtyp";
$this->config['result_fields'][$z]['row']      = "msgtyp";
$this->config['result_fields'][$z]['width']    = "7%";
$this->config['result_fields'][$z]['maxchar']  = "20";
$z++;
?>