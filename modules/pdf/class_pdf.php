<?php

include_once("modules/seating/class_seat.php");
$seat2 = new seat2();

include_once("modules/pdf/class_fpdf.php");
$barcode     = new barcode_system();  // Load Barcode System

/**
 * Klasse um die Menus und die PDF-Dateien im Modul PDF  zu erzeugen.
 * Author:          Genesis marco@chuchi.tv
 * Letzte �nderung: 5.4.2005
 *
 */
class pdf
{

    /**
     * Storage of barcodes
     */
    const BARCODE_PATH ='ext_inc/barcodes/';

    /**
     * Daten Array um M�glich Daten anzuzeigen
     *
     * @var array
     */
    public $data_type_array = array();
    /**
     * PDF Klasse um Daten zu erzeugen
     *
     * @var fpdf
     */
    public $pdf;
    /**
     * Momentane Position
     *
     * @var int
     */
    public $x;
    public $y = 0;
    /**
     * Start Position x-Richtung
     *
     * @var int
     */
    public $start_x;
    /**
     * Start Position y-Richtung
     *
     * @var int
     */
    public $start_y;
    /**
     * End Position x-Richtung
     *
     * @var int
     */
    public $total_x;
    /**
     * End Position y-Richtung
     *
     * @var int
     */
    public $total_y;
    /**
     * Breite des zu zeichnenden Objekts
     *
     * @var int
     */
    public $object_width = 0;
    /**
     * H�he des zu zeichnenden Objekts
     *
     * @var int
     */
    public $object_high = 0;
    /**
     * Momentane Spalten
     *
     * @var int
     */
    public $col = 1;
    /**
     * Momentane Zeile
     *
     * @var int
     */
    public $row = 1;
    /**
     * Maximale Anzahl m�glicher Spalten
     *
     * @var int
     */
    public $max_col = 0;
    /**
     * Maximale Anzahl m�glicher Zeilen
     *
     * @var int
     */
    public $max_row = 0;
    public $templ_id;
    
    
    /**
     * Enter description here...
     *
     * @param int $templ_id
     * @return pdf
     */
    public function pdf($templ_id)
    {
        $this->templ_id = $templ_id;
        // Typen Array erstellen
        // F�r Eintrittskarten
        $this->data_type_array['guestcards']['user_nickname']   = "Nickname";
        $this->data_type_array['guestcards']['name']            = "Name";
        $this->data_type_array['guestcards']['firstname']       = "Vorname";
        $this->data_type_array['guestcards']['userid']      = "Benutzer-ID";
        $this->data_type_array['guestcards']['fullname']        = "Vorname Name";
        $this->data_type_array['guestcards']['clan']            = "Clan";
        $this->data_type_array['guestcards']['orientation']     = "Orientierung";
        $this->data_type_array['guestcards']['col']             = "Sitzkolonne";
        $this->data_type_array['guestcards']['row']             = "Sitzreihe";
        $this->data_type_array['guestcards']['user_seat']       = "Sitzplatz";
        $this->data_type_array['guestcards']['user_block']      = "Sitzblock";
        $this->data_type_array['guestcards']['user_ip']         = "IP-Adresse";
        $this->data_type_array['guestcards']['party_name']      = "Lanparty-Name";
        $this->data_type_array['guestcards']['plz']     = "PLZ";
        $this->data_type_array['guestcards']['city']        = "Ort";
        $this->data_type_array['guestcards']['birthday']        = "Geburtstag";
        $this->data_type_array['seatcards']['user_nickname']    = "Nickname";
        $this->data_type_array['seatcards']['name']             = "Name";
        $this->data_type_array['seatcards']['firstname']        = "Vorname";
        $this->data_type_array['seatcards']['fullname']         = "Vorname Name";
        $this->data_type_array['seatcards']['userid']       = "Benutzer-ID";
        $this->data_type_array['seatcards']['clan']             = "Clan";
        $this->data_type_array['seatcards']['col']              = "Sitzkolonne";
        $this->data_type_array['seatcards']['row']              = "Sitzreihe";
        $this->data_type_array['seatcards']['seat']             = "Sitzplatz";
        $this->data_type_array['seatcards']['seat_block']       = "Sitzblock";
        $this->data_type_array['seatcards']['seat_ip']          = "IP-Adresse";
        $this->data_type_array['seatcards']['party_name']       = "Lanparty-Name";
        $this->data_type_array['seatcards']['plz']      = "PLZ";
        $this->data_type_array['seatcards']['city']     = "Ort";
        $this->data_type_array['seatcards']['birthday']     = "Geburtstag";
        $this->data_type_array['userlist']['user_nickname']     = "Nickname";
        $this->data_type_array['userlist']['lastname']          = "Name";
        $this->data_type_array['userlist']['firstname']         = "Vorname";
        $this->data_type_array['userlist']['fullname']          = "Vorname Name";
        $this->data_type_array['userlist']['userid']        = "Benutzer-ID";
        $this->data_type_array['userlist']['clan']              = "Clan";
        $this->data_type_array['userlist']['col']               = "Sitzkolonne";
        $this->data_type_array['userlist']['row']               = "Sitzreihe";
        $this->data_type_array['userlist']['user_seat']         = "Sitzplatz";
        $this->data_type_array['userlist']['user_block']        = "Sitzblock";
        $this->data_type_array['userlist']['user_ip']           = "IP-Adresse";
        $this->data_type_array['userlist']['party_name']        = "Lanparty-Name";
        $this->data_type_array['userlist']['nr']                = "fortlaufende Nummer";
        $this->data_type_array['userlist']['plz']       = "PLZ";
        $this->data_type_array['userlist']['city']      = "Ort";
        $this->data_type_array['userlist']['birthday']      = "Geburtstag";
    }

    
    // Menu aneigen
    /**
     * Menu erzeugen f�r PDF-Daten
     *
     * @param unknown_type $action
     */
    public function pdf_menu($action)
    {
        global $lang;
        switch ($action) {
            case 'guestcards':
                $this->_menuUsercards($action);
                break;
            case 'seatcards':
                $this->_menuSeatcards($action);
                break;
            default:
                $func->error(t('Die von dir gew&uuml;nschte Funtkion konnte nicht ausgef&uuml;rt werden'), "index.php?mod=pdf&action=" . $action);
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
    public function pdf_make($action)
    {
        global $lang,$auth;
        switch ($action) {
            case 'guestcards':
                $this->_makeUserCard($_POST['paid'], $_POST['guest'], $_POST['op'], $_POST['orga'], $_POST['user']);
                break;
            case 'seatcards':
                $this->_makeSeatCard($_POST['block'], $_POST['order']);
                break;
            case 'userlist':
                $this->_makeUserlist($_POST['paid'], $_POST['guest'], $_POST['op'], $_POST['orga'], $_POST['order']);
                break;
            default:
                $func->error(t('Die von dir gew&uuml;nschte Funtkion konnte nicht ausgef&uuml;rt werden'), "index.php?mod=pdf&action=" . $action);
                break;
            case 'ticket':
                if ($auth["userid"] == $_GET['userid'] || $auth["type"] > 2) {
                    $this->_makeUserCard(1, 1, 1, 1, $_GET['userid']);
                }
                break;
        }
    }

    /**
     * M�glich daten f�r diese Funtkion zur�ckgeben
     *
     * @param string $action
     * @param string $selected
     * @return array
     */
    public function get_data_array($action, $selected = "")
    {
        $data[] = array();
        foreach ($this->data_type_array[$action] as $key => $value) {
            if ($key == $selected) {
                $data[] .= "<option selected value=\"$key\">$value</option>";
            } else {
                $data[] .= "<option value=\"$key\">$value</option>";
            }
        }
        return $data;
    }
    
    // Interne Funktionen ***********************************************
    
    // Menus *************************
    
    /**
     * Menu f�r Besucherausweise
     *
     * @param string $action
     */
    public function _menuUsercards($action)
    {
        global $lang,$dsp,$db;
        
        
        $dsp->NewContent(t('Besucherausweise erstellen.'), t('Hier k&ouml;nnen Karten erstellt werden die beim Einlass an die G&auml;ste ausgeh&auml;ndigt werden.'));
        $dsp->SetForm("index.php?mod=pdf&action=" .$action . "&design=base&act=print&id=" .  $this->templ_id, "", "", "");
        $dsp->AddSingleRow(t('Die Bl&auml;tter werden nach folgenden Kriterien erstellt:'));
        
        // Array f�r Zahlungsstatus
        $type_array = array("null" => t('Egal'),
                                "1" => t('Ja'),
                                "0" => t('Nein')
                            );
        $t_array = array();
        
        while (list($key, $val) = each($type_array)) {
            array_push($t_array, "<option $selected value=\"$key\">$val</option>");
        }
        // Checkboken f�r Benutzer
        $dsp->AddDropDownFieldRow("paid", t('Besucher hat bezahlt'), $t_array, "", 1);
        $dsp->AddCheckBoxRow("guest", t('Besucher ist normaler Gast'), "", "", "1", "1", "0");
        $dsp->AddCheckBoxRow("op", t('Besucher ist Superadmin'), "", "", "1", "0", "0");
        $dsp->AddCheckBoxRow("orga", t('Besucher ist Orga'), "", "", "1", "0", "0");
        $dsp->AddCheckBoxRow("party", t('Nur ausgew&auml;hlte Party'), "", "", "1", "1", "0");
        
        
        // Array mit Benutzern
        $t_array = array();
        array_push($t_array, "<option $selected value=\"null\">Alle</option>");
/*      $query = $db->qry('SELECT * FROM %prefix%user AS user
                            LEFT JOIN %prefix%pdf_printed AS printed ON user.userid=printed.item_id OR printed.item_id is NULL WHERE user.type != \'-1\'');
*/
        $query = $db->qry('SELECT * FROM %prefix%user AS user WHERE user.type > 0');
        while ($row = $db->fetch_array($query)) {
            if ($row['item_id'] == "") {
                array_push($t_array, "<option $selected value=\"" . $row['userid'] . "\">" . $row['username'] . "</option>");
            } else {
                array_push($t_array, "<option $selected value=\"" . $row['userid'] . "\">" . $row['username'] . " *</option>");
            }
        }
        $dsp->AddSingleRow(t('Benutzer mit Stern wurden schon gedruckt'));
        $dsp->AddDropDownFieldRow("user", t('Bestimmter Besucher'), $t_array, "", 1);
/*
        // Array f�r Datum
        $d_array = array("<option selected value=\"null\">Alle</option>");

        $d_row = $db->qry('SELECT time FROM %prefix%pdf_printed GROUP BY time');
        while ($d_row_data = $db->fetch_array($d_row)){
            array_push ($d_array, "<option  value=\"" . $d_row_data['time'] . "\">" . date("m.d.y G:i",$d_row_data['time']) . "</option>");
        }
        $dsp->AddSingleRow(t('Es werden alle Benutzer ausgedruck die <b>neuer</b> als das eingestellte Datum sind.HTML_NEWLINE Ausser er wurde nur dieses Datum angew&auml;hlt.'));
        $dsp->AddDropDownFieldRow("date", t('Datum der Ausdrucke'), $d_array, "", 1);
        $dsp->AddCheckBoxRow("only", t('Nur dieses Datum drucken'), "", "", "1", "0", "0");
*/
        // Knopf f�r erzeugen der PDF
        $dsp->AddFormSubmitRow(t('Weiter'));
        $dsp->AddBackButton("index.php?mod=pdf&action=$action", "pdf/usercards");
        $dsp->AddContent();
    }
    

    /**
     * Menu f�r Sitzplatzkarten
     *
     * @param string $action
     */
    public function _menuSeatcards($action)
    {
        global $lang,$dsp,$db,$party,$func;
        
        $dsp->NewContent(t('Sitzplatzkarten erstellen.'), t('Hier k&ouml;nnen sie Karten f&uuml;r die Sitzpl&auml;tze erstellen.'));
        $dsp->SetForm("index.php?mod=pdf&action=" .$action . "&design=base&act=print&id=" .  $this->templ_id, "", "", "");
        $dsp->AddSingleRow(t('Die Bl&auml;tter werden nach folgenden Kriterien erstellt:'));

        // Array mit Sitzen
        $block = array();
        array_push($block, "<option $selected value=\"null\"></option>");
        $query = $db->qry('SELECT * FROM %prefix%seat_block WHERE party_id=%int% ORDER BY blockid', $party->party_id);
        
        if ($db->num_rows($query) == 0) {
            $func->error(t('Keine Sitzpl&auml;tze vorhanden'), "index.php?mod=pdf&action=$action");
        } else {
            while ($row = $db->fetch_array($query)) {
                if ($row['name']) {
                    array_push($block, "<option $selected value=\"" . $row['blockid'] . "\">" . $row['name'] . "</option>");
                }
            }

            // Dropdown f�r Bl�cke
            $dsp->AddDropDownFieldRow("block", t('Block'), $block, "", 1);

            // Array f�r Sortierung
            $order = array("<option selected value=\"row\">". t('Reihen') . "</option>",
                             "<option value=\"col\">". t('Spalten') . "</option>");
        
            // Dropdown f�r Sortierung
            $dsp->AddDropDownFieldRow("order", t('Sortierung'), $order, "", 1);
            // Knopf f�r erzeugen der PDF
            $dsp->AddFormSubmitRow(t('Weiter'));
            $dsp->AddBackButton("index.php?mod=pdf&action=$action", "pdf/seatcards");
            $dsp->AddContent();
        }
    }
    
    /**
     * Menu f�r Besucherliste
     *
     * @param string $action
     */
    public function _menuUserlist($action)
    {
        global $lang,$dsp,$db;
        
        $dsp->NewContent(t('Besucherlist erstellen.'), t('Hier k&ouml;nnen sie Listen mit allen Besuchern erstellen'));
        $dsp->SetForm("index.php?mod=pdf&action=" .$action . "&design=base&act=print&id=" .  $this->templ_id, "", "", "");
        $dsp->AddSingleRow(t('Die Bl&auml;tter werden nach folgenden Kriterien erstellt:'));
        
        // Array f�r Zahlungsstatus
        $type_array = array("null" => t('Egal'),
                                "1" => t('Ja'),
                                "0" => t('Nein')
                            );
        $t_array = array();
        
        while (list($key, $val) = each($type_array)) {
            array_push($t_array, "<option $selected value=\"$key\">$val</option>");
        }
        // Checkboken f�r Benutzer
        $dsp->AddDropDownFieldRow("paid", t('Besucher hat bezahlt'), $t_array, "", 1);
        $dsp->AddCheckBoxRow("guest", t('Besucher ist normaler Gast'), "", "", "1", "1", "0");
        $dsp->AddCheckBoxRow("op", t('Besucher ist Superadmin'), "", "", "1", "0", "0");
        $dsp->AddCheckBoxRow("orga", t('Besucher ist Orga'), "", "", "1", "0", "0");
        $dsp->AddCheckBoxRow("party", t('Nur ausgew&auml;hlte Party'), "", "", "1", "1", "0");
        
        // Array f�r Sortierung
        $sort_array = array("username" =>   t('Nickname'),
                                "name" =>   t('Nachname'),
                            "firstname" =>  t('Vorname'),
                                "clan" =>   t('Clan'),
                                "plz" =>    t('PLZ'),
                                "city" =>   t('Ortschaft')
                            );
        $s_array = array();
        
        while (list($key, $val) = each($sort_array)) {
            array_push($s_array, "<option $selected value=\"$key\">$val</option>");
        }
        
        // Knopf f�r erzeugen der PDF
        $dsp->AddDropDownFieldRow("order", t('Sortierung'), $s_array, "", 1);
        $dsp->AddFormSubmitRow(t('Weiter'));
        $dsp->AddBackButton("index.php?mod=pdf&action=$action", "pdf/userlist");
        $dsp->AddContent();
    }

    // Erzeugung der PDF-Dateien ***********************************
    
    //
    /**
     * PDF erzeugen f�r Benutzerausweise
     *
     * @param string $pdf_paid
     * @param string $pdf_normal
     * @param string $pdf_op
     * @param string $pdf_orga
     * @param string $pdf_guestid
     */
    public function _makeUserCard($pdf_paid, $pdf_normal, $pdf_op, $pdf_orga, $pdf_guestid)
    {
        define('IMAGE_PATH', 'ext_inc/pdf_templates/');
        global $db, $func, $party, $seat2;
                
        $date = date('U');
                
        // abfrage String erstellen
        $pdf_sqlstring = "";

        // Auf Party Pr�fen
        if ($_POST['party'] == '1' or $pdf_paid) {
            $pdf_sqlstring .= "LEFT JOIN %prefix%party_user AS party ON user.userid=party.user_id";
        }
        $pdf_sqlstring .= ' WHERE user.type > -1';
        if ($_POST['party'] == '1' or $pdf_paid) {
            $pdf_sqlstring .= ' AND party.party_id = '. $party->party_id;
        }

        // Bezahlstatus abfragen
        if ($pdf_paid == '0') {
            $pdf_sqlstring .= ' AND party.paid = 0';
        } elseif ($pdf_paid == '1') {
            $pdf_sqlstring .= ' AND party.paid = 1';
        }

        if ($pdf_normal == '1' or $pdf_op == '1' or $pdf_orga == '1') {
            $pdf_sqlstring .= ' AND (1 = 0';
        }
        if ($pdf_normal == '1') {
            $pdf_sqlstring .= ' OR user.type = 1';
        }
        if ($pdf_orga == '1') {
            $pdf_sqlstring .= ' OR user.type = 2';
        }
        if ($pdf_op == '1') {
            $pdf_sqlstring .= ' OR user.type = 3';
        }
        if ($pdf_normal == '1' or $pdf_op == '1' or $pdf_orga == '1') {
            $pdf_sqlstring .= ')';
        }
        
    //Userabfragen
        if ($pdf_guestid > 0) {
            $pdf_sqlstring .= ' AND user.userid = '.$pdf_guestid.'';
        }

        $query = $db->qry('SELECT user.*, clan.name AS clan, clan.url AS clanurl FROM %prefix%user AS user
      LEFT JOIN %prefix%clan AS clan ON user.clanid = clan.clanid %plain%', $pdf_sqlstring);

        $user_numusers = $db->num_rows($query);
        // erste Seite erstellen
        $this->_make_page();
        
        // Datenbank abfragen f�r momentans Template
        $templ_data = $db->qry('SELECT * FROM %prefix%pdf_data WHERE template_id=%int% AND type != \'config\' AND type != \'header\' AND type != \'footer\' AND visible = \'1\' ORDER BY sort ASC', $this->templ_id);
        $templ = array();
        while ($templ_data_array = $db->fetch_array($templ_data)) {
            $templ[] = array_merge($templ_data_array, $templ);
        }

        // Gr�sse ermitteln
        $this->_get_size($templ);
        
        // Anzahl Spallten und Reihen ermitteln
        $this->max_col = floor(($this->total_x - $this->start_x)/($this->start_x + $this->object_width));
        $this->max_row = floor(($this->total_y - $this->start_y)/($this->start_y + $this->object_high));

        // Seite f�llen
        while ($row = $db->fetch_array($query)) {
            unset($data);
            #$data['user_nickname'] = str_replace("&gt;","",$row["username"]);
            #$data['user_nickname'] = str_replace("&lt;","",$data['user_nickname']);
            #$data['user_nickname'] = str_replace("&gt","",$data['user_nickname']);
            #$data['user_nickname'] = str_replace("&lt","",$data['user_nickname']);
            #$data['user_nickname'] = trim($data['user_nickname']);
            $data['user_nickname'] = $func->AllowHTML($row["username"]);
            $data['party_name']    = $_SESSION['party_info']['name'];
            
            $data['userid']         = $row["userid"];
            $data['name'] = $row["name"];
            $data['firstname'] = $row["firstname"];
            $data['clan'] = $func->AllowHTML($row["clan"]);
            $data['fullname'] = $row["firstname"] . " " . $row["name"];
            $data['userid'] = $row['userid'];
            $data['plz'] = $row['plz'];
            $data['city'] = $row['city'];
            $data['birthday'] = $row['birthday'];

            // seat
            $row_seat = $db->qry_first('SELECT s.blockid, col, row, ip FROM %prefix%seat_seats AS s LEFT JOIN %prefix%seat_block AS b ON b.blockid = s.blockid WHERE b.party_id=%int% AND s.userid=%int%', $party->party_id, $row["userid"]);
            $blockid  = $row_seat["blockid"];
            if ($blockid != "") {
                $row_block = $db->qry_first('SELECT orientation, name FROM %prefix%seat_block WHERE blockid=%int%', $blockid);
                $data['orientation']  = $row_block["orientation"];
                $data['col']          = $row_seat["col"];
                $data['row']          = $row_seat["row"];
                $data['user_seat']    = $seat2->CoordinateToName($row_seat['col'] + 1, $row_seat['row'], $row_block['orientation']);
                $data['user_block']   = $row_block["name"];
            }

            $data['user_ip'] = $row_seat["ip"];

            // Neue Seite Anlegen wenn die letze voll ist
            if ($new_page) {
                $this->pdf->AddPage();
                $new_page = false;
            }
                        
            // Spallte und Zelle anw�hlen
            $this->x = (($this->col - 1) * ($this->start_x + $this->object_width)) + $this->start_x;
            $this->y = (($this->row - 1) * ($this->start_y + $this->object_high)) + $this->start_y;
            
            // Objekte schreiben.
            $this->_write_object($templ, $data);
            // Nextes Feld ausw�hlen
            
            if ($this->col < $this->max_col) {
                $this->col++;
            } else {
                $this->col = 1;
                if ($this->row < $this->max_row) {
                    $this->row++;
                } else {
                    $this->row = 1;
                    $new_page = true;
                }
            }
            
            // Wenn neue Daten ausgedruckt werden Daten eintragen
            if ($row['template_id'] == "") {
                $db->qry_first('INSERT %prefix%pdf_printed SET template_id = %string%, item_id = %int%, time = %string%', $this->templ_id, $row['userid'], $date);
            } else {
                $db->qry_first('UPDATE %prefix%pdf_printed SET time = %string WHERE template_id = %string% AND item_id = %int%', $date, $this->templ_id, $row['userid']);
            }
        } // end while

        $this->pdf->Output("UserCards.pdf", "D");
    }

    
    // PDF erzeugen f�r Sitzplatzkarten
    /**
     * Sitzplatzkarten erzeugen
     *
     * @param int $block
     * @param string $order
     */
    public function _makeSeatCard($block, $order)
    {
        define('IMAGE_PATH', 'ext_inc/pdf_templates/');
        global $db, $func,$party, $seat2;
        
        if ($order == "row") {
            $sql_order = ", 'row', 'col'";
        } else {
            $sql_order = ", 'col', 'row'";
        }
        //Daten der Sitzreihen auslesen
        if ($block == "null") {
            $query = $db->qry("SELECT * FROM %prefix%seat_seats AS s
        LEFT JOIN %prefix%seat_block AS b ON b.blockid = s.blockid
        WHERE b.party_id=%int% ORDER BY 's.blockid' %plain%", $party->party_id, $sql_order);
        } else {
            $query = $db->qry("SELECT * FROM %prefix%seat_seats
      WHERE blockid=%string% ORDER BY 'blockid' %plain%", $block, $sql_order);
        }
        
        $seat_numusers = $db->num_rows($query);
        // erste Seite erstellen
        $this->_make_page();
        
        // Datenbank abfragen f�r momentans Template
        $templ_data = $db->qry("SELECT * FROM %prefix%pdf_data WHERE template_id = %int% AND type != 'config' AND type != 'header' AND type != 'footer' AND visible = '1' ORDER BY sort ASC", $this->templ_id);
        $templ = array();
        while ($templ_data_array = $db->fetch_array($templ_data)) {
            $templ[] = array_merge($templ_data_array, $templ);
        }
    
        // Gr�sse ermitteln
        $this->_get_size($templ);
        
        // Anzahl Spallten und Reihen ermitteln
        $this->max_col = floor(($this->total_x - $this->start_x)/($this->start_x + $this->object_width));
        $this->max_row = floor(($this->total_y - $this->start_y)/($this->start_y + $this->object_high));
        // Seite f�llen
        while ($row = $db->fetch_array($query)) {
            unset($data);
            // Block abfragen und Sitzplatz abfragen
            $row_block            = $db->qry_first("SELECT orientation, name FROM %prefix%seat_block WHERE blockid=%int%", $row['blockid']);
            $userid               = $row["userid"];
            $data['col']          = $row["col"];
            $data['row']          = $row["row"];
            $data['seat_block']   = $row_block['name'];
            $data['seat']         = $seat2->CoordinateToName($data['col'] + 1, $data['row'], $row_block['orientation']);
            $data['party_name']    = $_SESSION['party_info']['name'];
            
            $row_user = $db->qry_first("SELECT user.*, clan.name AS clan, clan.url AS clanurl FROM %prefix%user AS user
        LEFT JOIN %prefix%clan AS clan ON user.clanid = clan.clanid
        WHERE userid=%int%", $userid);

            #$data['user_nickname'] = str_replace("&gt;","",$row_user["username"]);
            #$data['user_nickname'] = str_replace("&lt;","",$data['user_nickname']);
            #$data['user_nickname'] = str_replace("&gt","",$data['user_nickname']);
            #$data['user_nickname'] = str_replace("&lt","",$data['user_nickname']);
            #$data['user_nickname'] = trim($data['user_nickname']);
            $data['user_nickname'] = $func->AllowHTML($row["username"]);

            $data['userid']         = $row_user["userid"];
            $data['name']       = $row_user["name"];
            $data['firstname'] = $row_user["firstname"];
            $data['clan']       = $func->AllowHTML($row_user["clan"]);
            $data['fullname'] = $row["firstname"] . " " . $row["name"];
            $data['plz'] = $row['plz'];
            $data['city'] = $row['city'];
            $data['birthday'] = $row['birthday'];

            $data['seat_ip']        = $row["ip"];
    
            // Neue Seite Anlegen wenn die letze voll ist
            if ($new_page) {
                $this->pdf->AddPage();
                $new_page = false;
            }
                        
            // Spallte und Zelle anw�hlen
            $this->x = (($this->col - 1) * ($this->start_x + $this->object_width)) + $this->start_x;
            $this->y = (($this->row - 1) * ($this->start_y + $this->object_high)) + $this->start_y;
            
            // Objekte schreiben
            $this->_write_object($templ, $data);
            // Nextes Feld ausw�hlen
            
            if ($this->col < $this->max_col) {
                $this->col++;
            } else {
                $this->col = 1;
                if ($this->row < $this->max_row) {
                    $this->row++;
                } else {
                    $this->row = 1;
                    $new_page = true;
                }
            }
        } // end while

        $this->pdf->Output("SeatCards.pdf", "D");
    }
    
    
        
    /**
     * PDF erzeugen f�r Besucherlisten
     *
     * @param string $pdf_paid
     * @param string $pdf_normal
     * @param string $pdf_op
     * @param string $pdf_orga
     * @param string $order
     */
    public function _makeUserlist($pdf_paid, $pdf_normal, $pdf_op, $pdf_orga, $order)
    {
        define('IMAGE_PATH', 'ext_inc/pdf_templates/');
        global $db, $func,$party, $seat2;
                
        // abfrage String erstellen
        $pdf_sqlstring = "";

        // Auf Party Pr�fen
        if ($_POST['party'] == '1' or $pdf_paid) {
            $pdf_sqlstring .= "LEFT JOIN %prefix%party_user AS party ON user.userid=party.user_id";
        }
        $pdf_sqlstring .= ' WHERE user.type > -1';
        if ($_POST['party'] == '1' or $pdf_paid) {
            $pdf_sqlstring .= ' AND party.party_id = '. $party->party_id;
        }

        // Bezahlstatus abfragen
        if ($pdf_paid == '0') {
            $pdf_sqlstring .= ' AND party.paid = 0';
        } elseif ($pdf_paid == '1') {
            $pdf_sqlstring .= ' AND party.paid = 1';
        }

        if ($pdf_normal == '1' or $pdf_op == '1' or $pdf_orga == '1') {
            $pdf_sqlstring .= ' AND (1 = 0';
        }
        if ($pdf_normal == '1') {
            $pdf_sqlstring .= ' OR user.type = 1';
        }
        if ($pdf_orga == '1') {
            $pdf_sqlstring .= ' OR user.type = 2';
        }
        if ($pdf_op == '1') {
            $pdf_sqlstring .= ' OR user.type = 3';
        }
        if ($pdf_normal == '1' or $pdf_op == '1' or $pdf_orga == '1') {
            $pdf_sqlstring .= ')';
        }

        // Sortierung einstellen
        switch ($order) {
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

        $query = $db->qry("SELECT user.*, clan.name AS clan, clan.url AS clanurl FROM %prefix%user AS user
      LEFT JOIN %prefix%clan AS clan ON user.clanid = clan.clanid %plain%", $pdf_sqlstring);

        $user_numusers = $db->num_rows($query);
        // erste Seite erstellen
        $this->_make_page();
        
        // Datenbank abfragen f�r momentans Template
        $templ_data = $db->qry("SELECT * FROM %prefix%pdf_data WHERE template_id = %int% AND type != 'config' AND type != 'header' AND type != 'footer' AND visible = '1' ORDER BY sort ASC", $this->templ_id);
        $templ = array();
        while ($templ_data_array = $db->fetch_array($templ_data)) {
            $templ[] = array_merge($templ_data_array, $templ);
        }
        
        // Gr�sse einstellen
        $this->_get_size($templ);
        
        // Anzahl Spallten und Reihen ermitteln
        $this->max_col = floor(($this->total_x - $this->start_x)/($this->start_x + $this->object_width));
        $this->max_row = floor(($this->total_y - (2 * $this->start_y))/($this->object_high));

        // Seite f�llen
        $nr = 0;
        while ($row = $db->fetch_array($query)) {
            $nr = $nr + 1;
            unset($data);
            #$data['user_nickname'] = str_replace("&gt;","",$row["username"]);
            #$data['user_nickname'] = str_replace("&lt;","",$data['user_nickname']);
            #$data['user_nickname'] = str_replace("&gt","",$data['user_nickname']);
            #$data['user_nickname'] = str_replace("&lt","",$data['user_nickname']);
            #$data['user_nickname'] = trim($data['user_nickname']);
            $data['user_nickname'] = $func->AllowHTML($row["username"]);
            $data['party_name']    = $_SESSION['party_info']['name'];
            $data['nr'] = $nr;
            
            $data['userid']         = $row["userid"];
            $data['lastname']       = $row["name"];
            $data['firstname']  = $row["firstname"];
            $data['fullname'] = $row["firstname"] . " " . $row["name"];
            $data['clan']       = $func->AllowHTML($row["clan"]);
            $data['plz'] = $row['plz'];
            $data['city'] = $row['city'];
            $data['birthday'] = $row['birthday'];

            // seat
            $row_seat = $db->qry_first('SELECT s.blockid, col, row, ip FROM %prefix%seat_seats AS s LEFT JOIN %prefix%seat_block AS b ON b.blockid = s.blockid WHERE b.party_id=%int% AND s.userid=%int%', $party->party_id, $row["userid"]);
            $blockid  = $row_seat["blockid"];
            if ($blockid != "") {
                $row_block = $db->qry_first("SELECT orientation, name FROM %prefix%seat_block WHERE blockid=%int%", $blockid);
                $data['orientation']  = $row_block["orientation"];
                $data['col']          = $row_seat["col"];
                $data['row']          = $row_seat["row"];
                $data['user_seat']    = $seat2->CoordinateToName($data['col'] + 1, $data['row'], $data['orientation']);
                $data['user_block']   = $row_block["name"];
            }

            $data['user_ip'] = $row_seat["ip"];

            // Spallte und Zelle anw�hlen
            $this->x = (($this->col - 1) * ($this->start_x + $this->object_width)) + $this->start_x;
            $this->y = (($this->row - 1) * ($this->object_high)) + $this->start_y;

            // Neue Seite Anlegen wenn die letze voll ist
            if ($new_page) {
                $this->pdf->AddPage();
                $new_page = false;
            }
                                    
            $this->_write_object($templ, $data);
            // Nextes Feld ausw�hlen
            
            if ($this->col < $this->max_col) {
                $this->col++;
            } else {
                $this->col = 1;
                if ($this->row < $this->max_row) {
                    $this->row++;
                } else {
                    $this->row = 1;
                    $new_page = true;
                }
            }
        } // end while

        $this->pdf->Output("Userlist.pdf", "D");
    }
    
    // erstellen der ersten Seite

    /**
     * Funktionen um PDF-Dateien zu erzeugen
     * aufrufen.
     *
     */
    public function _make_page()
    {
        global $db;
        
        $page_data = $db->qry_first("SELECT * FROM %prefix%pdf_data WHERE template_id= %int% AND type = 'config' ORDER BY sort ASC", $this->templ_id);
        define('FPDF_FONTPATH', 'ext_inc/pdf_fonts/');
        if ($page_data['visible'] == 1) {
            $orientation = 'l';
        } else {
            $orientation = 'p';
        }
        
        $this->pdf = new FPDF($orientation, 'mm', $page_data['text']);
        $this->start_x = $page_data['pos_x'];
        $this->start_y = $page_data['pos_y'];
        $this->pdf->AddPage();
        if ($page_data['visible'] == 1) {
            $this->total_x = $this->pdf->fh;
            $this->total_y = $this->pdf->fw;
        } else {
            $this->total_x = $this->pdf->fw;
            $this->total_y = $this->pdf->fh;
        }
    }

    /**
     * Gr�sse der zu Zeichnenden Objekte ermitteln
     *
     * @param array $templ
     */
    public function _get_size($templ)
    {
        global $barcode;

        // Gr�sse aller Objekte ermitteln
        for ($i = 0; $i < count($templ); $i++) {
            switch ($templ[$i]['type']) {
                case 'text':
                    $width = $this->pdf->GetStringWidth($templ[$i]['text']);
                    if ($width > $this->object_width) {
                        $this->object_width = $width;
                    }
                    if (($templ[$i]['fontsize']/2) > $this->object_high) {
                        $this->object_high = ($templ[$i]['fontsize']/2);
                    }
                    break;
                case 'rect':
                    if ($templ[$i]['end_x'] > $this->object_width) {
                        $this->object_width = $templ[$i]['end_x'];
                    }
                    if ($templ[$i]['end_y'] > $this->object_high) {
                        $this->object_high = $templ[$i]['end_y'];
                    }
                    break;
                case 'line':
                    if ($templ[$i]['end_x'] > $this->object_width) {
                        $this->object_width = $templ[$i]['end_x'];
                    }
                    if ($templ[$i]['end_y'] > $this->object_high) {
                        $this->object_high = $templ[$i]['end_y'];
                    }
                    break;
                case 'image':
                    if ($templ[$i]['end_x'] > $this->object_width) {
                        $this->object_width = $templ[$i]['end_x'];
                    }
                    if ($templ[$i]['end_y'] > $this->object_high) {
                        $this->object_high = $templ[$i]['end_y'];
                    }
                    break;
                
                case 'barcode':
                    $imagename = mt_rand(100000, 999999);
                    $barcode->get_image($_SESSION['userid'], static::BARCODE_PATH .$imagename);
                    $image = getimagesize(static::BARCODE_PATH .$imagename . ".png");
                    if (($image[0]/2) > $this->object_width) {
                        $this->object_width = $image[0];
                    }
                    if (($image[1]/2) > $this->object_high) {
                        $this->object_high = $image[1];
                    }
                    $barcode->kill_image(static::BARCODE_PATH . $imagename);
                    
                case 'data':
                    $width = $this->pdf->GetStringWidth($data[$templ[$i]['text']]);
                    if ($width > $this->object_width) {
                        $this->object_width = $width;
                    }
                    if (($templ[$i]['fontsize']/2) > $this->object_high) {
                        $this->object_high = ($templ[$i]['fontsize']/2);
                    }
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
    public function _write_object($templ, $data)
    {
        global $barcode;

        for ($i = 0; $i < count($templ); $i++) {
            if ($templ[$i]['user_type'] == $row['type'] || $templ[$i]['user_type'] == "0") {
                switch ($templ[$i]['type']) {
                    case 'text':
                        $this->pdf->SetFont($templ[$i]['font'], '', $templ[$i]["fontsize"]);
                        $this->pdf->SetTextColor($templ[$i]["red"], $templ[$i]["green"], $templ[$i]["blue"]);
                        if ($templ[$i]['end_x'] == "1") {
                            $this->pdf->Text(($templ[$i]["pos_x"] - $this->pdf->GetStringWidth($templ[$i]['text'])) + $this->x, $templ[$i]["pos_y"] + $this->y, $templ[$i]['text']);
                        } else {
                            $this->pdf->Text($templ[$i]["pos_x"] + $this->x, $templ[$i]["pos_y"] + $this->y, $templ[$i]['text']);
                        }
                        break;
                    case 'rect':
                        $this->pdf->SetDrawColor($templ[$i]["red"], $templ[$i]["green"], $templ[$i]["blue"]);
                        if ($templ[$i]['fontsize'] == "1") {
                            $this->pdf->SetFillColor($templ[$i]["red"], $templ[$i]["green"], $templ[$i]["blue"]);
                            $this->pdf->Rect($templ[$i]['pos_x'] + $this->x, $templ[$i]['pos_y'] + $this->y, $templ[$i]['end_x'], $templ[$i]['end_y'], "FD");
                        } else {
                            $this->pdf->SetFillColor(255);
                            $this->pdf->Rect($templ[$i]['pos_x'] + $this->x, $templ[$i]['pos_y'] + $this->y, $templ[$i]['end_x'], $templ[$i]['end_y']);
                        }
                        break;
                    case 'line':
                        $this->pdf->SetDrawColor($templ[$i]["red"], $templ[$i]["green"], $templ[$i]["blue"]);
                        $this->pdf->Line($templ[$i]['pos_x'] + $this->x, $templ[$i]['pos_y'] + $this->y, $templ[$i]['end_x'] + $this->x, $templ[$i]['end_y'] + $this->y);
                        break;
                    case 'image':
                        $this->pdf->Image(IMAGE_PATH . $templ[$i]['text'], $templ[$i]['pos_x'] + $this->x, $templ[$i]['pos_y'] + $this->y, $templ[$i]['end_x'], $templ[$i]['end_y']);
                        break;
                    
                    case 'barcode':
                        $imagename = mt_rand(100000, 999999);
                        $barcode->get_image($data['userid'], static::BARCODE_PATH . $imagename);
                        $this->pdf->Image(static::BARCODE_PATH . $imagename . ".png", $templ[$i]['pos_x'] + $this->x, $templ[$i]['pos_y'] + $this->y);
                        $barcode->kill_image(static::BARCODE_PATH . $imagename);
                    

                    case 'data':
                        $this->pdf->SetFont($templ[$i]['font'], '', $templ[$i]["fontsize"]);
                        $this->pdf->SetTextColor($templ[$i]["red"], $templ[$i]["green"], $templ[$i]["blue"]);
                        if ($templ[$i]['end_x'] == "1") {
                            $this->pdf->Text(($templ[$i]["pos_x"] - $this->pdf->GetStringWidth($data[$templ[$i]['text']])) + $this->x, $templ[$i]["pos_y"] + $this->y, $data[$templ[$i]['text']]);
                        } else {
                            $this->pdf->Text($templ[$i]["pos_x"] + $this->x, $templ[$i]["pos_y"] + $this->y, $data[$templ[$i]['text']]);
                        }

                        break;
                }
            }
        }
    }
    
    public function _make_header()
    {
    }
    
    public function _make_footer()
    {
    }
} // END
