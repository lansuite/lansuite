<?php


include_once("modules/pdf/class_fpdf.php");

/**
 * Klasse um die Menus und die PDF-Dateien im Modul PDF  zu erzeugen.
 * Author: 			Genesis marco@chuchi.tv
 * Letzte Änderung: 5.4.2005	
 *
 */
class pdf {

	/**
	 * Daten Array um Möglich Daten anzuzeigen
	 *
	 * @var array
	 */
	var $data_type_array = array();
	/**
	 * PDF Klasse um Daten zu erzeugen
	 *
	 * @var fpdf
	 */
	var $pdf;
	/**
	 * Momentane Position
	 *
	 * @var int
	 */
	var $x,$y = 0;
	/**
	 * Start Position x-Richtung
	 *
	 * @var int
	 */
	var $start_x;
	/**
	 * Start Position y-Richtung
	 *
	 * @var int
	 */
	var $start_y;
	/**
	 * End Position x-Richtung
	 *
	 * @var int
	 */
	var $total_x;
	/**
	 * End Position y-Richtung
	 *
	 * @var int
	 */
	var $total_y;
	/**
	 * Breite des zu zeichnenden Objekts
	 *
	 * @var int
	 */
	var $object_width = 0;
	/**
	 * Höhe des zu zeichnenden Objekts
	 *
	 * @var int
	 */
	var $object_high = 0;
	/**
	 * Momentane Spalten
	 *
	 * @var int
	 */
	var $col = 1;
	/**
	 * Momentane Zeile
	 *
	 * @var int
	 */
	var $row = 1;
	/**
	 * Maximale Anzahl möglicher Spalten
	 *
	 * @var int
	 */
	var $max_col = 0;
	/**
	 * Maximale Anzahl möglicher Zeilen
	 *
	 * @var int
	 */
	var $max_row = 0;
	var $templ_id;
	
	
	/**
	 * Enter description here...
	 *
	 * @param int $templ_id
	 * @return pdf
	 */
	function pdf($templ_id){
		$this->templ_id = $templ_id;
		// Typen Array erstellen
		// Für Eintrittskarten 
		$this->data_type_array['guestcards']['user_nickname'] 	= "Nickname";
		$this->data_type_array['guestcards']['name'] 			= "Name";
		$this->data_type_array['guestcards']['firstname'] 		= "Vorname";
		$this->data_type_array['guestcards']['fullname']		= "Vorname Name";
		$this->data_type_array['guestcards']['clan'] 			= "Clan";
		$this->data_type_array['guestcards']['orientation'] 	= "Orientierung";
		$this->data_type_array['guestcards']['col']				= "Sitzkolonne";
		$this->data_type_array['guestcards']['row'] 			= "Sitzreihe";
		$this->data_type_array['guestcards']['user_seat']		= "Sitzplatz";
		$this->data_type_array['guestcards']['user_block']		= "Sitzblock";
		$this->data_type_array['guestcards']['user_ip']			= "IP-Adresse";
		$this->data_type_array['guestcards']['party_name']		= "Lanparty-Name";
		$this->data_type_array['seatcards']['user_nickname'] 	= "Nickname";
		$this->data_type_array['seatcards']['name'] 			= "Name";
		$this->data_type_array['seatcards']['firstname'] 		= "Vorname";
		$this->data_type_array['seatcards']['fullname']			= "Vorname Name";
		$this->data_type_array['seatcards']['clan'] 			= "Clan";
		$this->data_type_array['seatcards']['col']				= "Sitzkolonne";
		$this->data_type_array['seatcards']['row'] 				= "Sitzreihe";
		$this->data_type_array['seatcards']['seat']				= "Sitzplatz";
		$this->data_type_array['seatcards']['seat_block']		= "Sitzblock";
		$this->data_type_array['seatcards']['seat_ip']			= "IP-Adresse";
		$this->data_type_array['seatcards']['party_name']		= "Lanparty-Name";
		$this->data_type_array['userlist']['user_nickname'] 	= "Nickname";
		$this->data_type_array['userlist']['lastname'] 			= "Name";
		$this->data_type_array['userlist']['firstname'] 		= "Vorname";
		$this->data_type_array['userlist']['fullname']			= "Vorname Name";
		$this->data_type_array['userlist']['clan'] 				= "Clan";
		$this->data_type_array['userlist']['col']				= "Sitzkolonne";
		$this->data_type_array['userlist']['row'] 				= "Sitzreihe";
		$this->data_type_array['userlist']['user_seat']			= "Sitzplatz";
		$this->data_type_array['userlist']['user_block']		= "Sitzblock";
		$this->data_type_array['userlist']['user_ip']			= "IP-Adresse";
		$this->data_type_array['userlist']['party_name']		= "Lanparty-Name";
		$this->data_type_array['userlist']['nr']				= "fortlaufende Nummer";
	}

	
	// Menu aneigen
	/**
	 * Menu erzeugen für PDF-Daten
	 *
	 * @param unknown_type $action
	 */
	function pdf_menu($action){
		global $lang;
		switch ($action){
			case 'guestcards':
				$this->_menuUsercards($action);
			break;	
			case 'seatcards':
				$this->_menuSeatcards($action);
			break;				
			default:
				$func->error($lang['pdf']['action_error'],"index.php?mod=pdf&action=" . $action);
			break;
			case 'userlist':
				$this->_menuUserlist($action);
			break;	
		}

		
	}
	
	// PDF erstellen
	/**
	 * PDF-Dateien erzeugen
	 *
	 * @param string $action
	 */
	function pdf_make($action){
		global $lang;
		switch ($action){
			case 'guestcards':
				$this->_makeUserCard($_POST['paid'],$_POST['guest'],$_POST['op'],$_POST['orga'],$_POST['user']);
			break;	
			case 'seatcards':
				$this->_makeSeatCard($_POST['block'],$_POST['order']);
			break;	
			case 'userlist':
				$this->_makeUserlist($_POST['paid'],$_POST['guest'],$_POST['op'],$_POST['orga'],$_POST['order']);
			break;	
			default:
				$func->error($lang['pdf']['action_error'],"index.php?mod=pdf&action=" . $action);
			break;
		}

		
	}

	/**
	 * Möglich daten für diese Funtkion zurückgeben
	 *
	 * @param string $action
	 * @param string $selected
	 * @return array
	 */
	function get_data_array($action,$selected = ""){
		$data[] = array();
		foreach ($this->data_type_array[$action] as $key => $value){
			if($key == $selected){
				$data[] .= "<option selected value=\"$key\">$value</option>";
			}else {
				$data[] .= "<option value=\"$key\">$value</option>";	
			}
		}
		return $data;
	}
	
	// Interne Funktionen ***********************************************
	
	// Menus *************************	
	
	/**
	 * Menu für Besucherausweise
	 *
	 * @param string $action
	 */
	function _menuUsercards($action){
		global $lang,$dsp,$db,$config;
		
		
		$dsp->NewContent($lang["pdf"]["guestcard_caption"], $lang["pdf"]["guestcard_subcaption"]);
		$dsp->SetForm("base.php?mod=pdf&action=" .$action . "&act=print&id=" .  $this->templ_id, "", "", "");
		$dsp->AddSingleRow($lang["pdf"]["rules"]);
		
		// Array für Zahlungsstatus
		$type_array = array("null" => $lang["pdf"]["egal"],
								"1" => $lang["pdf"]["yes"],
								"0" => $lang["pdf"]["no"]
							);
		$t_array = array();
		
		while (list ($key, $val) = each ($type_array)) {
			array_push ($t_array, "<option $selected value=\"$key\">$val</option>");
		}
		// Checkboken für Benutzer
		$dsp->AddDropDownFieldRow("paid", $lang["pdf"]["paid"], $t_array, "", 1);
		$dsp->AddCheckBoxRow("guest", $lang["pdf"]["guest"], "", "", "1", "1", "0");
		$dsp->AddCheckBoxRow("op", $lang["pdf"]["op"], "", "", "1", "0", "0");
		$dsp->AddCheckBoxRow("orga", $lang["pdf"]["orga"], "", "", "1", "0", "0");
		$dsp->AddCheckBoxRow("party", $lang["pdf"]["party"],"", "", "1", "1", "0");
		
		
		// Array mit Benutzern
		$t_array = array();
		array_push ($t_array, "<option $selected value=\"null\">Alle</option>");
/*		$query = $db->query("SELECT * FROM {$config["tables"]["user"]} AS user 
							LEFT JOIN {$config['tables']['pdf_printed']} AS printed ON user.userid=printed.item_id OR printed.item_id is NULL WHERE user.type != '-1'");
*/
		$query = $db->query("SELECT * FROM {$config["tables"]["user"]} AS user WHERE user.type > 0");
		while($row = $db->fetch_array($query)) {
			if($row['item_id'] == ""){
				array_push ($t_array, "<option $selected value=\"" . $row['userid'] . "\">" . $row['username'] . "</option>");
			}else{
				array_push ($t_array, "<option $selected value=\"" . $row['userid'] . "\">" . $row['username'] . " *</option>");	
			}
		}
		$dsp->AddSingleRow($lang["pdf"]["user_caption"]);				
		$dsp->AddDropDownFieldRow("user", $lang["pdf"]["user"], $t_array, "", 1);
/*		
		// Array für Datum
		$d_array = array("<option selected value=\"null\">Alle</option>");
		
		$d_row = $db->query("SELECT time FROM {$config['tables']['pdf_printed']} GROUP BY time");
		while ($d_row_data = $db->fetch_array($d_row)){
			array_push ($d_array, "<option  value=\"" . $d_row_data['time'] . "\">" . date("m.d.y G:i",$d_row_data['time']) . "</option>");
		}
		$dsp->AddSingleRow($lang["pdf"]["date_caption"]);				
		$dsp->AddDropDownFieldRow("date", $lang["pdf"]["date"], $d_array, "", 1);
		$dsp->AddCheckBoxRow("only", $lang["pdf"]["only"], "", "", "1", "0", "0");
*/		
		// Knopf für erzeugen der PDF
		$dsp->AddFormSubmitRow("next");
		$dsp->AddBackButton("index.php?mod=pdf&action=$action","pdf/usercards"); 
		$dsp->AddContent();
	}
	

	/**
	 * Menu für Sitzplatzkarten
	 *
	 * @param string $action
	 */
	function _menuSeatcards($action){
		global $lang,$dsp,$db,$config,$party,$func;
		
		$dsp->NewContent($lang["pdf"]["seatcard_caption"], $lang["pdf"]["seatcard_subcaption"]);
		$dsp->SetForm("base.php?mod=pdf&action=" .$action . "&act=print&id=" .  $this->templ_id, "", "", "");
		$dsp->AddSingleRow($lang["pdf"]["rules"]);

		// Array mit Sitzen
		$block = array();
		array_push ($block, "<option $selected value=\"null\"></option>");
		$query = $db->query("SELECT * FROM {$config["tables"]["seat_block"]} WHERE party_id={$party->party_id} ORDER BY 'blockid'");
		
		if($db->num_rows($query) == 0){
			$func->error($lang["pdf"]["seat_error"],"index.php?mod=pdf&action=$action");
		}else{
		
			while($row = $db->fetch_array($query)) {
				array_push ($block, "<option $selected value=\"" . $row['blockid'] . "\">" . $row['name'] . "</option>");
			}
		
	
			// Dropdown für Blöcke		
			$dsp->AddDropDownFieldRow("block", $lang["pdf"]["block"], $block, "", 1);

			// Array für Sortierung	
			$order = array("<option selected value=\"row\">". $lang["pdf"]["row"] . "</option>",
							 "<option value=\"col\">". $lang["pdf"]["col"] . "</option>");
		
			// Dropdown für Sortierung		
			$dsp->AddDropDownFieldRow("order", $lang["pdf"]["order"], $order, "", 1);
			// Knopf für erzeugen der PDF
			$dsp->AddFormSubmitRow("next");
			$dsp->AddBackButton("index.php?mod=pdf&action=$action","pdf/seatcards"); 
			$dsp->AddContent();
		}
	}
	
	/**
	 * Menu für Besucherliste
	 *
	 * @param string $action
	 */
	function _menuUserlist($action){
		global $lang,$dsp,$db,$config;
		
		$dsp->NewContent($lang["pdf"]["guestlist_caption"], $lang["pdf"]["guestlist_subcaption"]);
		$dsp->SetForm("base.php?mod=pdf&action=" .$action . "&act=print&id=" .  $this->templ_id, "", "", "");
		$dsp->AddSingleRow($lang["pdf"]["rules"]);
		
		// Array für Zahlungsstatus
		$type_array = array("null" => $lang["pdf"]["egal"],
								"1" => $lang["pdf"]["yes"],
								"0" => $lang["pdf"]["no"]
							);
		$t_array = array();
		
		while (list ($key, $val) = each ($type_array)) {
			array_push ($t_array, "<option $selected value=\"$key\">$val</option>");
		}
		// Checkboken für Benutzer
		$dsp->AddDropDownFieldRow("paid", $lang["pdf"]["paid"], $t_array, "", 1);
		$dsp->AddCheckBoxRow("guest", $lang["pdf"]["guest"], "", "", "1", "1", "0");
		$dsp->AddCheckBoxRow("op", $lang["pdf"]["op"], "", "", "1", "0", "0");
		$dsp->AddCheckBoxRow("orga", $lang["pdf"]["orga"], "", "", "1", "0", "0");
		$dsp->AddCheckBoxRow("party", $lang["pdf"]["party"],"", "", "1", "1", "0");
		
		// Array für Sortierung
		$sort_array = array("username" => 	$lang["pdf"]["username"],
								"name" => 	$lang["pdf"]["name"],
							"firstname" => 	$lang["pdf"]["firstame"],
								"clan" => 	$lang["pdf"]["clan"],
								"plz" => 	$lang["pdf"]["plz"],
								"city" => 	$lang["pdf"]["city"]
							);
		$s_array = array();
		
		while (list ($key, $val) = each ($sort_array)) {
			array_push ($s_array, "<option $selected value=\"$key\">$val</option>");
		}
		
		// Knopf für erzeugen der PDF
		$dsp->AddDropDownFieldRow("order", $lang["pdf"]["order"], $s_array, "", 1);
		$dsp->AddFormSubmitRow("next");
		$dsp->AddBackButton("index.php?mod=pdf&action=$action","pdf/userlist"); 
		$dsp->AddContent();
	}

	// Erzeugung der PDF-Dateien ***********************************
	
	// 
	/**
	 * PDF erzeugen für Benutzerausweise
	 *
	 * @param string $pdf_paid
	 * @param string $pdf_normal
	 * @param string $pdf_op
	 * @param string $pdf_orga
	 * @param string $pdf_guestid
	 */
	function _makeUserCard($pdf_paid,$pdf_normal,$pdf_op,$pdf_orga,$pdf_guestid){
		define('IMAGE_PATH','ext_inc/pdf_templates/');
		global $db, $config, $func, $party;
				
		include_once("inc/classes/class_seat.php");
		$seat = new seat;
		
		$date = date('U');
				
		// abfrage String erstellen
		$pdf_sqlstring = "";
	
		// Auf Party Prüfen
		if ($_POST['party'] == "1"){
				$pdf_sqlstring .= "LEFT JOIN {$config['tables']['party_user']} AS party ON user.userid=party.user_id ";
		}			
		
		// Auf Datum prüfen
		if ($_POST['date'] != "null" && $_POST['date'] != ""){
			if($_POST['only'] == 1){
				$pdf_sqlstring .= "LEFT JOIN {$config['tables']['pdf_printed']} AS printed ON user.userid=printed.item_id WHERE printed.time = '" . $_POST['date'] . "' AND (printed.template_id='{$this->templ_id}')";
			}else{
				$pdf_sqlstring .= "LEFT JOIN {$config['tables']['pdf_printed']} AS printed ON user.userid=printed.item_id WHERE (ISNULL(printed.item_id) AND NOT(ISNULL(user.userid))) OR  user.userid=printed.item_id AND (printed.time > '" . $_POST['date'] . "' OR printed.time is NULL) AND (printed.template_id='{$this->templ_id}' OR printed.template_id is NULL)";
			}
		}else{
				// $pdf_sqlstring .= "LEFT JOIN {$config['tables']['pdf_printed']} AS printed ON user.userid=printed.item_id OR printed.item_id is NULL WHERE (printed.template_id='{$this->templ_id}' OR printed.template_id is NULL)";
				$pdf_sqlstring .= "WHERE 1";
		}

		if ($pdf_guestid == "null"){

			if ($pdf_paid == "1"){
				if ($pdf_sqlstring == ""){
					$pdf_sqlstring = "WHERE";
				}else{
					$pdf_sqlstring = $pdf_sqlstring ." AND";
				}
				$pdf_sqlstring = $pdf_sqlstring . " user.paid='1'";
			}elseif ($pdf_paid == "0"){
				if ($pdf_sqlstring == ""){
					$pdf_sqlstring = "WHERE";
				}else{
					$pdf_sqlstring = $pdf_sqlstring ." AND";
				}
				$pdf_sqlstring = $pdf_sqlstring . " user.paid='0'";
			}

			if ($pdf_normal == "1"){
				if ($pdf_sqlstring == ""){
					$pdf_sqlstring = "WHERE";
				}elseif($pdf_op == "1" || $pdf_orga == "1"){
					$pdf_sqlstring = $pdf_sqlstring . " AND (";
				}else{
					$pdf_sqlstring = $pdf_sqlstring . " AND";
				}
				
				$pdf_sqlstring = $pdf_sqlstring . " user.type='1'";
			}

			if ($pdf_op == "1"){
				if ($pdf_sqlstring == ""){
					$pdf_sqlstring = "WHERE";
				}elseif($pdf_orga == "1" && $pdf_normal != "1"){
					$pdf_sqlstring = $pdf_sqlstring . " AND (";
				}elseif($pdf_normal != "1"){
					$pdf_sqlstring = $pdf_sqlstring . " AND";
				}else{
					$pdf_sqlstring = $pdf_sqlstring . " OR";
				}
				
				$pdf_sqlstring = $pdf_sqlstring . " user.type='3'";

				if($pdf_orga != "1" && $pdf_normal == "1"){
						$pdf_sqlstring = $pdf_sqlstring . ")";
				}
			}

			if ($pdf_orga == "1"){
				if ($pdf_sqlstring == ""){
					$pdf_sqlstring = "WHERE";
				}elseif($pdf_op != "1" && $pdf_normal != "1"){
					$pdf_sqlstring = $pdf_sqlstring . " AND";
				}else{
					$pdf_sqlstring = $pdf_sqlstring . " OR";
				}	
					
				$pdf_sqlstring = $pdf_sqlstring . " user.type='2'";
					

				if($pdf_op == "1" || $pdf_normal == "1"){
						$pdf_sqlstring = $pdf_sqlstring . ")";
				}
			}

			if ($_POST['party'] == "1"){
				if ($pdf_sqlstring == ""){
					$pdf_sqlstring = "WHERE";
				}else{
					$pdf_sqlstring = $pdf_sqlstring . " AND";
				}
				$pdf_sqlstring = $pdf_sqlstring . " party.party_id='{$party->party_id}'";
			}

		}else{
			if ($pdf_sqlstring == ""){
				$pdf_sqlstring = "WHERE";
			}else{
				$pdf_sqlstring = $pdf_sqlstring . " AND";
			}
			$pdf_sqlstring = $pdf_sqlstring . " user.userid='" . $pdf_guestid . "'";
		}

		if ($pdf_sqlstring == ""){
			$pdf_sqlstring = "WHERE";
		}else{
			
			$pdf_sqlstring = $pdf_sqlstring . " AND";
		}
		$pdf_sqlstring = $pdf_sqlstring . " user.type != '-1'";

		$query = $db->query("SELECT * FROM {$config["tables"]["user"]} AS user "  . $pdf_sqlstring);

		$user_numusers = $db->num_rows($query);
		// erste Seite erstellen
		$this->_make_page();
		
		// Datenbank abfragen für momentans Template
		$templ_data = $db->query("SELECT * FROM " . $config['tables']['pdf_data'] . " WHERE template_id='" . $this->templ_id . "' AND type != 'config' AND type != 'header' AND type != 'footer' AND visible = '1' ORDER BY sort ASC");
		$templ = array();
		while ($templ_data_array = $db->fetch_array($templ_data)){
			$templ[] = array_merge($templ_data_array,$templ);
		}

		// Grösse ermitteln
		$this->_get_size($templ);
		
		// Anzahl Spallten und Reihen ermitteln
		$this->max_col = floor(($this->total_x - $this->start_x)/($this->start_x + $this->object_width));
		$this->max_row = floor(($this->total_y - $this->start_y)/($this->start_y + $this->object_high));

		// Seite füllen
		while($row = $db->fetch_array($query)) {
			unset($data);
			$data['user_nickname'] = str_replace("&gt;","",$row["username"]);
			$data['user_nickname'] = str_replace("&lt;","",$data['user_nickname']);
			$data['user_nickname'] = str_replace("&gt","",$data['user_nickname']);
			$data['user_nickname'] = str_replace("&lt","",$data['user_nickname']);
			$data['user_nickname'] = trim($data['user_nickname']);
			$data['party_name']    = $_SESSION['party_info']['name']; 	
			
			$data['name'] = $row["name"];
			$data['firstname'] = $row["firstname"];
			$data['clan'] = $row["clan"];
			$data['fullname'] = $row["firstname"] . " " . $row["name"];
			$data['userid'] = $row['userid'];
			
			// seat
			$row_seat = $db->query_first("SELECT s.blockid, col, row, ip FROM {$config["tables"]["seat_seats"]} AS s LEFT JOIN {$config["tables"]["seat_block"]} AS b ON b.blockid = s.blockid WHERE b.party_id={$party->party_id} AND s.userid='{$row["userid"]}'");
			$blockid  = $row_seat["blockid"];
			if($blockid != "") {
				$row_block = $db->query_first("SELECT orientation, name FROM {$config["tables"]["seat_block"]} WHERE blockid='$blockid'");
				$data['orientation']  = $row_block["orientation"];
				$data['col']          = $row_seat["col"];
				$data['row']          = $row_seat["row"];
				$data['user_seat']    = $seat->display_seat_index($row_block['orientation'], $row_seat['col'], $row_seat['row']);
				$data['user_block']	  = $row_block["name"];
				
			}

			$data['user_ip'] = $row_seat["ip"];

			// Neue Seite Anlegen wenn die letze voll ist
			if ($new_page){
				$this->pdf->AddPage();
				$new_page = false;
			}
						
			// Spallte und Zelle anwählen	
			$this->x = (($this->col - 1) * ($this->start_x + $this->object_width)) + $this->start_x;
			$this->y = (($this->row - 1) * ($this->start_y + $this->object_high)) + $this->start_y;
			
			// Objekte schreiben.
			$this->_write_object($templ,$data);
			// Nextes Feld auswählen
			
			if($this->col < $this->max_col){
				$this->col++;
			}else{
				$this->col = 1;
				if($this->row < $this->max_row){
					$this->row++;
				}else{
					$this->row = 1;
					$new_page = true;
				}
			}
			
			// Wenn neue Daten ausgedruckt werden Daten eintragen
			if($row['template_id'] == ""){
				$db->query_first("INSERT " . $config['tables']['pdf_printed'] . " SET template_id=" . $this->templ_id . ", item_id=" . $row['userid']  . ",time='$date'");
			}else{
				$db->query_first("UPDATE " . $config['tables']['pdf_printed'] . " SET time='$date' WHERE template_id=" . $this->templ_id . " AND item_id=" . $row['userid']);
			}			
		} // end while

		$this->pdf->Output("UserCards.pdf","D");
	}

	
	// PDF erzeugen für Sitzplatzkarten
	/**
	 * Sitzplatzkarten erzeugen
	 *
	 * @param int $block
	 * @param string $order
	 */
	function _makeSeatCard($block,$order){
		define('IMAGE_PATH','ext_inc/pdf_templates/');
		global $db, $config, $func,$party;
				
		include_once("inc/classes/class_seat.php");
		$seat = new seat;
		
		if($order == "row"){
			$sql_order = ", 'row', 'col'";
		}else{
			$sql_order = ", 'col', 'row'";
		}
		//Daten der Sitzreihen auslesen
		if($block == "null"){
			$query = $db->query("SELECT * FROM {$config["tables"]["seat_seats"]} AS s LEFT JOIN {$config["tables"]["seat_block"]} AS b ON b.blockid = s.blockid WHERE b.party_id={$party->party_id} ORDER BY 's.blockid'$sql_order");
		}else{
			$query = $db->query("SELECT * FROM {$config["tables"]["seat_seats"]} WHERE blockid='$block' ORDER BY 'blockid'$sql_order");
		}
		
		$seat_numusers = $db->num_rows($query);
		// erste Seite erstellen
		$this->_make_page();
		
		// Datenbank abfragen für momentans Template
		$templ_data = $db->query("SELECT * FROM " . $config['tables']['pdf_data'] . " WHERE template_id='" . $this->templ_id . "' AND type != 'config' AND type != 'header' AND type != 'footer' AND visible = '1' ORDER BY sort ASC");
		$templ = array();
		while ($templ_data_array = $db->fetch_array($templ_data)){
			$templ[] = array_merge($templ_data_array,$templ);
		}
	
		// Grösse ermitteln
		$this->_get_size($templ);
		
		// Anzahl Spallten und Reihen ermitteln
		$this->max_col = floor(($this->total_x - $this->start_x)/($this->start_x + $this->object_width));
		$this->max_row = floor(($this->total_y - $this->start_y)/($this->start_y + $this->object_high));
		// Seite füllen
		while($row = $db->fetch_array($query)) {
			unset($data);
			// Block abfragen und Sitzplatz abfragen
			$row_block    		  = $db->query_first("SELECT orientation, name FROM {$GLOBALS['config']['tables']['seat_block']} WHERE blockid='{$row['blockid']}'");
			$userid 			  = $row["userid"];
			$data['col']  		  = $row["col"];
			$data['row']  		  = $row["row"];
			$data['seat_block']   = $row_block['name'];
			$data['seat']    	  = $seat->display_seat_index($row_block['orientation'], $data['col'], $data['row']);
			$data['party_name']    = $_SESSION['party_info']['name']; 	
			
			$row_user = $db->query_first("SELECT * FROM {$config["tables"]["user"]} WHERE userid='$userid'");
				
			$data['user_nickname'] = str_replace("&gt;","",$row_user["username"]);
			$data['user_nickname'] = str_replace("&lt;","",$data['user_nickname']);
			$data['user_nickname'] = str_replace("&gt","",$data['user_nickname']);
			$data['user_nickname'] = str_replace("&lt","",$data['user_nickname']);
			$data['user_nickname'] = trim($data['user_nickname']);

			$data['name'] 		= $row_user["name"];
			$data['firstname'] = $row_user["firstname"];
			$data['clan'] 		= $row_user["clan"];
			$data['fullname'] = $row["firstname"] . " " . $row["name"];
			
			$data['seat_ip'] 		= $row["ip"];
	
			// Neue Seite Anlegen wenn die letze voll ist
			if ($new_page){
				$this->pdf->AddPage();
				$new_page = false;
			}
						
			// Spallte und Zelle anwählen	
			$this->x = (($this->col - 1) * ($this->start_x + $this->object_width)) + $this->start_x;
			$this->y = (($this->row - 1) * ($this->start_y + $this->object_high)) + $this->start_y;
			
			// Objekte schreiben
			$this->_write_object($templ,$data);
			// Nextes Feld auswählen
			
			if($this->col < $this->max_col){
				$this->col++;
			}else{
				$this->col = 1;
				if($this->row < $this->max_row){
					$this->row++;
				}else{
					$this->row = 1;
					$new_page = true;
				}
			}
			
		} // end while

		$this->pdf->Output("SeatCards.pdf","D");
	}
	
	
		
	/**
	 * PDF erzeugen für Besucherlisten
	 *
	 * @param string $pdf_paid
	 * @param string $pdf_normal
	 * @param string $pdf_op
	 * @param string $pdf_orga
	 * @param string $order
	 */
	function _makeUserlist($pdf_paid,$pdf_normal,$pdf_op,$pdf_orga,$order){
		define('IMAGE_PATH','ext_inc/pdf_templates/');
		global $db, $config, $func,$party;
				
		include_once("inc/classes/class_seat.php");
		$seat = new seat;
		
		// abfrage String erstellen
		$pdf_sqlstring = "";

				// Auf Party Prüfen
		if ($_POST['party'] == "1"){
				$pdf_sqlstring = "LEFT JOIN {$config['tables']['party_user']} AS party ON user.userid=party.user_id WHERE party.party_id={$party->party_id} ";
		}			

		// Bezahlstatus abfragen
		if ($pdf_paid == "1"){
			if ($pdf_sqlstring == ""){
				$pdf_sqlstring = "WHERE";
			}else{
				$pdf_sqlstring = $pdf_sqlstring ." AND";
			}
			$pdf_sqlstring = $pdf_sqlstring . " paid='1'";
		}elseif ($pdf_paid == "0"){
			if ($pdf_sqlstring == ""){
				$pdf_sqlstring = "WHERE";
			}else{
				$pdf_sqlstring = $pdf_sqlstring ." AND";
			}
			$pdf_sqlstring = $pdf_sqlstring . " paid='0'";
		}
		// Normale Nutzer abfragen
		if ($pdf_normal == "1"){
			if ($pdf_sqlstring == ""){
				$pdf_sqlstring = "WHERE";
			}elseif($pdf_op == "1" || $pdf_orga == "1"){
				$pdf_sqlstring = $pdf_sqlstring . " AND (";
			}else{
				$pdf_sqlstring = $pdf_sqlstring . " AND";
			}

			$pdf_sqlstring = $pdf_sqlstring . " user.type='1'";
		}

		if ($pdf_op == "1"){
			if ($pdf_sqlstring == ""){
				$pdf_sqlstring = "WHERE";
			}elseif($pdf_orga == "1" && $pdf_normal != "1"){
				$pdf_sqlstring = $pdf_sqlstring . " AND (";
			}elseif($pdf_normal != "1"){
				$pdf_sqlstring = $pdf_sqlstring . " AND";
			}else{
				$pdf_sqlstring = $pdf_sqlstring . " OR";
			}

			$pdf_sqlstring = $pdf_sqlstring . " user.type='3'";

			if($pdf_orga != "1" && $pdf_normal == "1"){
				$pdf_sqlstring = $pdf_sqlstring . ")";
			}
		}

		if ($pdf_orga == "1"){
			if ($pdf_sqlstring == ""){
				$pdf_sqlstring = "WHERE";
			}elseif($pdf_op != "1" && $pdf_normal != "1"){
				$pdf_sqlstring = $pdf_sqlstring . " AND";
			}else{
				$pdf_sqlstring = $pdf_sqlstring . " OR";
			}

			$pdf_sqlstring = $pdf_sqlstring . " user.type='2'";


			if($pdf_op == "1" || $pdf_normal == "1"){
				$pdf_sqlstring = $pdf_sqlstring . ")";
			}
		}

		
		if ($pdf_sqlstring == ""){
			$pdf_sqlstring = "WHERE";
		}else{
			
			$pdf_sqlstring = $pdf_sqlstring . " AND";
		}
		$pdf_sqlstring = $pdf_sqlstring . " type != '-1'";
		
		// Sortierung einstellen
		switch ($order){
			case 'username':
				$pdf_sqlstring = $pdf_sqlstring . " ORDER BY username, name ASC";
			break;
			case 'name':
				$pdf_sqlstring = $pdf_sqlstring . " ORDER BY name, firstname ASC";
			break;
			case 'firstname':
				$pdf_sqlstring = $pdf_sqlstring . " ORDER BY firstname, name ASC";
			break;
			case 'clan':
				$pdf_sqlstring = $pdf_sqlstring . " ORDER BY clan, name ASC";
			break;
			case 'plz':
				$pdf_sqlstring = $pdf_sqlstring . " ORDER BY plz, name ASC";
			break;
			case 'city':
				$pdf_sqlstring = $pdf_sqlstring . " ORDER BY city, name ASC";
			break;
			default:
			break;
		}
		
		
		$query = $db->query("SELECT * FROM {$config["tables"]["user"]} AS user " . $pdf_sqlstring);
		$user_numusers = $db->num_rows($query);
		// erste Seite erstellen
		$this->_make_page();
		
		// Datenbank abfragen für momentans Template
		$templ_data = $db->query("SELECT * FROM " . $config['tables']['pdf_data'] . " WHERE template_id='" . $this->templ_id . "' AND type != 'config' AND type != 'header' AND type != 'footer' AND visible = '1' ORDER BY sort ASC");
		$templ = array();
		while ($templ_data_array = $db->fetch_array($templ_data)){
			$templ[] = array_merge($templ_data_array,$templ);
		}
		
		// Grösse einstellen 
		$this->_get_size($templ);
		
		// Anzahl Spallten und Reihen ermitteln
		$this->max_col = floor(($this->total_x - $this->start_x)/($this->start_x + $this->object_width));
		$this->max_row = floor(($this->total_y - (2 * $this->start_y))/($this->object_high));

		// Seite füllen
		$nr = 0;
		while($row = $db->fetch_array($query)) {
			$nr = $nr + 1;
			unset($data);
			$data['user_nickname'] = str_replace("&gt;","",$row["username"]);
			$data['user_nickname'] = str_replace("&lt;","",$data['user_nickname']);
			$data['user_nickname'] = str_replace("&gt","",$data['user_nickname']);
			$data['user_nickname'] = str_replace("&lt","",$data['user_nickname']);
			$data['user_nickname'] = trim($data['user_nickname']);
			$data['party_name']    = $_SESSION['party_info']['name']; 	
			$data['nr'] = $nr;
			
			$data['lastname'] 		= $row["name"];
			$data['firstname'] 	= $row["firstname"];
			$data['fullname'] = $row["firstname"] . " " . $row["name"];
			$data['clan'] 		= $row["clan"];
			$data['city'] 		= $row["city"];
			$data['plz'] 		= $row["plz"];
			
			// seat
			$row_seat = $db->query_first("SELECT s.blockid, col, row, ip FROM {$config["tables"]["seat_seats"]} AS s LEFT JOIN {$config["tables"]["seat_block"]} AS b ON b.blockid = s.blockid WHERE b.party_id={$party->party_id} AND s.userid='{$row["userid"]}'");
			$blockid  = $row_seat["blockid"];
			if($blockid != "") {
				$row_block = $db->query_first("SELECT orientation, name FROM lansuite_seat_block WHERE blockid='$blockid'");
				$data['orientation']  = $row_block["orientation"];
				$data['col']          = $row_seat["col"];
				$data['row']          = $row_seat["row"];
				$data['user_seat']    = $seat->display_seat_index($data['orientation'], $data['col'], $data['row']);
				$data['user_block']	  = $row_block["name"];
				
			}

			$data['user_ip'] = $row_seat["ip"];

			// Spallte und Zelle anwählen	
			$this->x = (($this->col - 1) * ($this->start_x + $this->object_width)) + $this->start_x;
			$this->y = (($this->row - 1) * ($this->object_high)) + $this->start_y;

			// Neue Seite Anlegen wenn die letze voll ist
			if ($new_page){
				$this->pdf->AddPage();
				$new_page = false;
			}
									
			$this->_write_object($templ,$data);
			// Nextes Feld auswählen
			
			if($this->col < $this->max_col){
				$this->col++;
			}else{
				$this->col = 1;
				if($this->row < $this->max_row){
					$this->row++;
				}else{
					$this->row = 1;
					$new_page = true;
				}
			}

		} // end while

		$this->pdf->Output("Userlist.pdf","D");
	}
	
	// erstellen der ersten Seite

	/**
	 * Funktionen um PDF-Dateien zu erzeugen
	 * aufrufen.
	 *
	 */
	function _make_page(){
		global $db,$config;
		
		$page_data = $db->query_first("SELECT * FROM " . $config['tables']['pdf_data'] . " WHERE template_id='" . $this->templ_id . "' AND type = 'config' ORDER BY sort ASC");
		define('FPDF_FONTPATH','ext_inc/pdf_fonts/');
		if($page_data['visible'] == 1){
			$orientation = 'l';
		}else{
			$orientation = 'p';
		}
		
		$this->pdf = new FPDF($orientation,'mm',$page_data['text']);
		$this->start_x = $page_data['pos_x'];
		$this->start_y = $page_data['pos_y'];
		$this->pdf->AddPage();	
		if($page_data['visible'] == 1){
			$this->total_x = $this->pdf->fh;
			$this->total_y = $this->pdf->fw;
		}else{
			$this->total_x = $this->pdf->fw;
			$this->total_y = $this->pdf->fh;
		}
		
	}

	/**
	 * Grösse der zu Zeichnenden Objekte ermitteln
	 *
	 * @param array $templ
	 */
	function _get_size($templ){
		global $barcode;

		// Grösse aller Objekte ermitteln
		for($i = 0; $i < count($templ); $i++){
			switch ($templ[$i]['type']){
				case 'text':
					$width = $this->pdf->GetStringWidth($templ[$i]['text']);
					if($width > $this->object_width) $this->object_width = $width;
					if(($templ[$i]['fontsize']/2) > $this->object_high) $this->object_high = ($templ[$i]['fontsize']/2);
				break;
				case 'rect':
					if($templ[$i]['end_x'] > $this->object_width) $this->object_width = $templ[$i]['end_x'];
					if($templ[$i]['end_y'] > $this->object_high) $this->object_high = $templ[$i]['end_y'];
				break;
				case 'line':
					if($templ[$i]['end_x'] > $this->object_width) $this->object_width = $templ[$i]['end_x'];
					if($templ[$i]['end_y'] > $this->object_high) $this->object_high = $templ[$i]['end_y'];
				break;
				case 'image':
					if($templ[$i]['end_x'] > $this->object_width) $this->object_width = $templ[$i]['end_x'];
					if($templ[$i]['end_y'] > $this->object_high) $this->object_high = $templ[$i]['end_y'];
				break;
				
				case 'barcode':
					$imagename = mt_rand(100000,999999);
					$barcode->get_image($_SESSION['userid'],BARCODE_PATH .$imagename);
					$image = getimagesize(BARCODE_PATH .$imagename . ".png");
					if($image[0] > $this->object_width) $this->object_width = $image[0];
					if($image[1] > $this->object_high) $this->object_high = $image[1];
					$barcode->kill_image(BARCODE_PATH . $imagename);
					
				case 'data':
					$width = $this->pdf->GetStringWidth($data[$templ[$i]['text']]);
					if($width > $this->object_width) $this->object_width = $width;
					if(($templ[$i]['fontsize']/2) > $this->object_high) $this->object_high = ($templ[$i]['fontsize']/2);
				break;
			}
		}	
			
	}
	
	/**
	 * Objekte auf PDF zeichnen
	 *
	 * @param array $templ
	 * @param array $data
	 */
	function _write_object($templ,$data){
		global $barcode;

		for($i = 0; $i < count($templ); $i++){
			if($templ[$i]['user_type'] == $row['type'] || $templ[$i]['user_type'] == "0"){
				switch ($templ[$i]['type']){
					case 'text':
					$this->pdf->SetFont($templ[$i]['font'],'',$templ[$i]["fontsize"]);
					$this->pdf->SetTextColor($templ[$i]["red"],$templ[$i]["green"],$templ[$i]["blue"]);
					if($templ[$i]['end_x'] == "1"){
						$this->pdf->Text(($templ[$i]["pos_x"] - $this->pdf->GetStringWidth($templ[$i]['text'])) + $this->x,$templ[$i]["pos_y"] + $this->y,$templ[$i]['text']);
					}else{
						$this->pdf->Text($templ[$i]["pos_x"] + $this->x,$templ[$i]["pos_y"] + $this->y,$templ[$i]['text']);
					}
					break;
					case 'rect':
					$this->pdf->SetDrawColor($templ[$i]["red"],$templ[$i]["green"],$templ[$i]["blue"]);
					if($templ[$i]['fontsize'] == "1"){
						$this->pdf->SetFillColor($templ[$i]["red"],$templ[$i]["green"],$templ[$i]["blue"]);
						$this->pdf->Rect($templ[$i]['pos_x'] + $this->x,$templ[$i]['pos_y'] + $this->y,$templ[$i]['end_x'],$templ[$i]['end_y'],"FD");
					}else{
						$this->pdf->SetFillColor(255);
						$this->pdf->Rect($templ[$i]['pos_x'] + $this->x,$templ[$i]['pos_y'] + $this->y,$templ[$i]['end_x'],$templ[$i]['end_y']);
					}
					break;
					case 'line':
					$this->pdf->SetDrawColor($templ[$i]["red"],$templ[$i]["green"],$templ[$i]["blue"]);
					$this->pdf->Line($templ[$i]['pos_x'] + $this->x,$templ[$i]['pos_y'] + $this->y,$templ[$i]['end_x'] + $this->x,$templ[$i]['end_y'] + $this->y);
					break;
					case 'image':
					$this->pdf->Image(IMAGE_PATH . $templ[$i]['text'],$templ[$i]['pos_x'] + $this->x,$templ[$i]['pos_y'] + $this->y,$templ[$i]['end_x'],$templ[$i]['end_y']);
					break;
					
					case 'barcode':
					$imagename = mt_rand(100000,999999);
					$barcode->get_image($data['userid'],BARCODE_PATH . $imagename);
					$this->pdf->Image(BARCODE_PATH . $imagename . ".png",$templ[$i]['pos_x'] + $this->x,$templ[$i]['pos_y'] + $this->y);
					$barcode->kill_image(BARCODE_PATH . $imagename);
					

					case 'data':
					$this->pdf->SetFont($templ[$i]['font'],'',$templ[$i]["fontsize"]);
					$this->pdf->SetTextColor($templ[$i]["red"],$templ[$i]["green"],$templ[$i]["blue"]);
					if($templ[$i]['end_x'] == "1"){
						$this->pdf->Text(($templ[$i]["pos_x"] - $this->pdf->GetStringWidth($data[$templ[$i]['text']])) + $this->x,$templ[$i]["pos_y"] + $this->y,$data[$templ[$i]['text']]);
					}else{
						$this->pdf->Text($templ[$i]["pos_x"] + $this->x,$templ[$i]["pos_y"] + $this->y,$data[$templ[$i]['text']]);
					}

					break;
				}
			}
		}


	}
	
	function _make_header(){
		
	}
	
	function _make_footer(){
		
	}
} // END CLASS
?>
