<?php

namespace LanSuite\Module\PDF;

use \LanSuite\BarcodeSystem;
use LanSuite\Module\Seating\Seat2;

/**
 * Class PDF
 */
class PDF
{

    /**
     * Storage of barcodes
     */
    const BARCODE_PATH ='ext_inc/barcodes/';

    /**
     * Data array
     *
     * @var array
     */
    private $data_type_array = [];

    /**
     * @var \FPDF
     */
    private $pdf;

    /**
     * Current position on the x axis
     *
     * @var int
     */
    private $x = 0;

    /**
     * Current position on the y axis
     *
     * @var int
     */
    private $y = 0;

    /**
     * Start position on the x axis
     *
     * @var int
     */
    private $start_x;

    /**
     * Start position on the y axis
     *
     * @var int
     */
    private $start_y;

    /**
     * End position on the x axis
     *
     * @var int
     */
    private $total_x;

    /**
     * End position on the y axis
     *
     * @var int
     */
    private $total_y;

    /**
     * Width of the object to draw
     *
     * @var int
     */
    private $object_width = 0;

    /**
     * Height of the object to draw
     *
     * @var int
     */
    private $object_high = 0;

    /**
     * Corrent column
     *
     * @var int
     */
    private $col = 1;

    /**
     * Current row
     *
     * @var int
     */
    private $row = 1;

    /**
     * Maximum number of possible columns
     *
     * @var int
     */
    private $max_col = 0;

    /**
     * Maximum number of possible rows
     *
     * @var int
     */
    private $max_row = 0;

    /**
     * Template ID
     *
     * @var int
     */
    private $templ_id;

    /**
     * @var BarcodeSystem
     */
    private $barcodeSystem = null;

    /**
     * @var Seat2
     */
    private $seating = null;

    /**
     * @param int $templ_id
     */
    public function __construct($templ_id, BarcodeSystem $barcodeSystem, Seat2 $seating)
    {
        $this->templ_id = $templ_id;
        $this->barcodeSystem = $barcodeSystem;
        $this->seating = $seating;

        $this->data_type_array['guestcards']['user_nickname']   = "Nickname";
        $this->data_type_array['guestcards']['name']            = "Name";
        $this->data_type_array['guestcards']['firstname']       = "Vorname";
        $this->data_type_array['guestcards']['userid']          = "Benutzer-ID";
        $this->data_type_array['guestcards']['fullname']        = "Vorname Name";
        $this->data_type_array['guestcards']['clan']            = "Clan";
        $this->data_type_array['guestcards']['orientation']     = "Orientierung";
        $this->data_type_array['guestcards']['col']             = "Sitzkolonne";
        $this->data_type_array['guestcards']['row']             = "Sitzreihe";
        $this->data_type_array['guestcards']['user_seat']       = "Sitzplatz";
        $this->data_type_array['guestcards']['user_block']      = "Sitzblock";
        $this->data_type_array['guestcards']['user_ip']         = "IP-Adresse";
        $this->data_type_array['guestcards']['party_name']      = "Lanparty-Name";
        $this->data_type_array['guestcards']['plz']             = "PLZ";
        $this->data_type_array['guestcards']['city']            = "Ort";
        $this->data_type_array['guestcards']['birthday']        = "Geburtstag";
        $this->data_type_array['seatcards']['user_nickname']    = "Nickname";
        $this->data_type_array['seatcards']['name']             = "Name";
        $this->data_type_array['seatcards']['firstname']        = "Vorname";
        $this->data_type_array['seatcards']['fullname']         = "Vorname Name";
        $this->data_type_array['seatcards']['userid']           = "Benutzer-ID";
        $this->data_type_array['seatcards']['clan']             = "Clan";
        $this->data_type_array['seatcards']['col']              = "Sitzkolonne";
        $this->data_type_array['seatcards']['row']              = "Sitzreihe";
        $this->data_type_array['seatcards']['seat']             = "Sitzplatz";
        $this->data_type_array['seatcards']['seat_block']       = "Sitzblock";
        $this->data_type_array['seatcards']['seat_ip']          = "IP-Adresse";
        $this->data_type_array['seatcards']['party_name']       = "Lanparty-Name";
        $this->data_type_array['seatcards']['plz']              = "PLZ";
        $this->data_type_array['seatcards']['city']             = "Ort";
        $this->data_type_array['seatcards']['birthday']         = "Geburtstag";
        $this->data_type_array['userlist']['user_nickname']     = "Nickname";
        $this->data_type_array['userlist']['lastname']          = "Name";
        $this->data_type_array['userlist']['firstname']         = "Vorname";
        $this->data_type_array['userlist']['fullname']          = "Vorname Name";
        $this->data_type_array['userlist']['userid']            = "Benutzer-ID";
        $this->data_type_array['userlist']['clan']              = "Clan";
        $this->data_type_array['userlist']['col']               = "Sitzkolonne";
        $this->data_type_array['userlist']['row']               = "Sitzreihe";
        $this->data_type_array['userlist']['user_seat']         = "Sitzplatz";
        $this->data_type_array['userlist']['user_block']        = "Sitzblock";
        $this->data_type_array['userlist']['user_ip']           = "IP-Adresse";
        $this->data_type_array['userlist']['party_name']        = "Lanparty-Name";
        $this->data_type_array['userlist']['nr']                = "fortlaufende Nummer";
        $this->data_type_array['userlist']['plz']               = "PLZ";
        $this->data_type_array['userlist']['city']              = "Ort";
        $this->data_type_array['userlist']['birthday']          = "Geburtstag";
    }

    /**
     * Create menu for PDF data
     *
     * @param string $action
     * @return void
     */
    public function pdf_menu($action)
    {
        global $func;

        switch ($action) {
            case 'guestcards':
                $this->_menuUsercards($action);
                break;

            case 'seatcards':
                $this->_menuSeatcards($action);
                break;

            case 'userlist':
                $this->_menuUserlist($action);
                break;

            case 'certificate':
                $this->_menuCertificate($action);
                break;

            default:
                $func->error(t('Die von dir gew&uuml;nschte Funktion konnte nicht ausgef&uuml;rt werden'), "index.php?mod=pdf&action=" . $action);
                break;
        }
    }

    /**
     * Create PDF file
     *
     * @param string $action
     * @return void
     */
    public function pdf_make($action)
    {
        global $func, $auth;

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

            case 'certificate':
                $this->_makeCertificate($_POST['guest'], $_POST['user']);
                break;

            case 'ticket':
                if ($auth["userid"] == $_GET['userid'] || $auth["type"] > 2) {
                    $this->_makeUserCard(1, 1, 1, 1, $_GET['userid']);
                }
                break;

            default:
                $func->error(t('Die von dir gew&uuml;nschte Funtkion konnte nicht ausgef&uuml;rt werden'), "index.php?mod=pdf&action=" . $action);
                break;
        }
    }

    /**
     * @param string $action
     * @param string $selected
     * @return array
     */
    public function get_data_array($action, $selected = "")
    {
        $data[] = [];
        foreach ($this->data_type_array[$action] as $key => $value) {
            if ($key == $selected) {
                $data[] .= "<option selected value=\"$key\">$value</option>";
            } else {
                $data[] .= "<option value=\"$key\">$value</option>";
            }
        }

        return $data;
    }

    /**
     * Menu for visitor cards
     *
     * @param string $action
     * @return void
     */
    private function _menuUsercards($action)
    {
        global $dsp, $db;

        $dsp->NewContent(t('Besucherausweise erstellen.'), t('Hier k&ouml;nnen Karten erstellt werden die beim Einlass an die G&auml;ste ausgeh&auml;ndigt werden.'));
        $dsp->SetForm("index.php?mod=pdf&action=" .$action . "&design=base&act=print&id=" .  $this->templ_id, "", "", "");
        $dsp->AddSingleRow(t('Die Bl&auml;tter werden nach folgenden Kriterien erstellt:'));

        // Payment status
        $type_array = [
            "null"  => t('Egal'),
            "1"     => t('Ja'),
            "0"     => t('Nein')
        ];
        $t_array = [];

        while (list($key, $val) = each($type_array)) {
            array_push($t_array, "<option value=\"$key\">$val</option>");
        }

        $dsp->AddDropDownFieldRow("paid", t('Besucher hat bezahlt'), $t_array, "", 1);
        $dsp->AddCheckBoxRow("guest", t('Besucher ist normaler Gast'), "", "", "1", "1", "0");
        $dsp->AddCheckBoxRow("op", t('Besucher ist Superadmin'), "", "", "1", "0", "0");
        $dsp->AddCheckBoxRow("orga", t('Besucher ist Orga'), "", "", "1", "0", "0");
        $dsp->AddCheckBoxRow("party", t('Nur ausgew&auml;hlte Party'), "", "", "1", "1", "0");

        $t_array = [];
        array_push($t_array, "<option value=\"null\">Alle</option>");
        $query = $db->qry('SELECT * FROM %prefix%user AS user WHERE user.type > 0');

        while ($row = $db->fetch_array($query)) {
            if ($row['item_id'] == "") {
                array_push($t_array, "<option value=\"" . $row['userid'] . "\">" . $row['username'] . "</option>");
            } else {
                array_push($t_array, "<option value=\"" . $row['userid'] . "\">" . $row['username'] . " *</option>");
            }
        }

        $dsp->AddSingleRow(t('Benutzer mit Stern wurden schon gedruckt'));
        $dsp->AddDropDownFieldRow("user", t('Bestimmter Besucher'), $t_array, "", 1);

        $dsp->AddFormSubmitRow(t('Weiter'));
        $dsp->AddBackButton("index.php?mod=pdf&action=$action", "pdf/usercards");
    }

    /**
     * Menu for seating cards
     *
     * @param string $action
     * @return void
     */
    private function _menuSeatcards($action)
    {
        global $dsp, $db, $party, $func;

        $dsp->NewContent(t('Sitzplatzkarten erstellen.'), t('Hier k&ouml;nnen sie Karten f&uuml;r die Sitzpl&auml;tze erstellen.'));
        $dsp->SetForm("index.php?mod=pdf&action=" .$action . "&design=base&act=print&id=" .  $this->templ_id, "", "", "");
        $dsp->AddSingleRow(t('Die Bl&auml;tter werden nach folgenden Kriterien erstellt:'));

        $block = [];
        array_push($block, "<option value=\"null\"></option>");
        $query = $db->qry('SELECT * FROM %prefix%seat_block WHERE party_id=%int% ORDER BY blockid', $party->party_id);

        if ($db->num_rows($query) == 0) {
            $func->error(t('Keine Sitzpl&auml;tze vorhanden'), "index.php?mod=pdf&action=$action");
        } else {
            while ($row = $db->fetch_array($query)) {
                if ($row['name']) {
                    array_push($block, "<option value=\"" . $row['blockid'] . "\">" . $row['name'] . "</option>");
                }
            }

            $dsp->AddDropDownFieldRow("block", t('Block'), $block, "", 1);
            $order = [
                "<option selected value=\"row\">". t('Reihen') . "</option>",
                "<option value=\"col\">". t('Spalten') . "</option>"
            ];

            $dsp->AddDropDownFieldRow("order", t('Sortierung'), $order, "", 1);
            $dsp->AddFormSubmitRow(t('Weiter'));
            $dsp->AddBackButton("index.php?mod=pdf&action=$action", "pdf/seatcards");
        }
    }

    /**
     * Menu for the visitor list
     *
     * @param string $action
     * @return void
     */
    private function _menuUserlist($action)
    {
        global $dsp;

        $dsp->NewContent(t('Besucherlist erstellen.'), t('Hier k&ouml;nnen sie Listen mit allen Besuchern erstellen'));
        $dsp->SetForm("index.php?mod=pdf&action=" .$action . "&design=base&act=print&id=" .  $this->templ_id, "", "", "");
        $dsp->AddSingleRow(t('Die Bl&auml;tter werden nach folgenden Kriterien erstellt:'));
        
        // Payment status
        $type_array = [
            "null"  => t('Egal'),
            "1"     => t('Ja'),
            "0"     => t('Nein')
        ];
        $t_array = [];

        while (list($key, $val) = each($type_array)) {
            array_push($t_array, "<option value=\"$key\">$val</option>");
        }

        $dsp->AddDropDownFieldRow("paid", t('Besucher hat bezahlt'), $t_array, "", 1);
        $dsp->AddCheckBoxRow("guest", t('Besucher ist normaler Gast'), "", "", "1", "1", "0");
        $dsp->AddCheckBoxRow("op", t('Besucher ist Superadmin'), "", "", "1", "0", "0");
        $dsp->AddCheckBoxRow("orga", t('Besucher ist Orga'), "", "", "1", "0", "0");
        $dsp->AddCheckBoxRow("party", t('Nur ausgew&auml;hlte Party'), "", "", "1", "1", "0");

        // Sorting
        $sort_array = [
            "username"  => t('Nickname'),
            "name"      => t('Nachname'),
            "firstname" => t('Vorname'),
            "clan"      => t('Clan'),
            "plz"       => t('PLZ'),
            "city"      => t('Ortschaft')
        ];
        $s_array = [];

        while (list($key, $val) = each($sort_array)) {
            array_push($s_array, "<option value=\"$key\">$val</option>");
        }

        $dsp->AddDropDownFieldRow("order", t('Sortierung'), $s_array, "", 1);
        $dsp->AddFormSubmitRow(t('Weiter'));
        $dsp->AddBackButton("index.php?mod=pdf&action=$action", "pdf/userlist");
    }

    /**
     * Menu for certificates
     *
     * @param string $action
     * @return void
     */
    private function _menuCertificate($action)
    {
        global $dsp;

        $dsp->NewContent(t('Urkunden erstellen.'), t('Hier kannst du Gewinnerurkunden f&uuml;r die Teilnehmer erstellen.'));
        $dsp->SetForm("index.php?mod=pdf&action=" .$action . "&design=base&act=print&id=" .  $this->templ_id, "", "", "");
        $dsp->AddSingleRow(t('Die Bl&auml;tter werden nach folgenden Kriterien erstellt:'));

        $dsp->AddCheckBoxRow("party", t('Nur ausgew&auml;hlte Party'), "", "", "1", "1", "0");

        // Sorting
        $sort_array = [
            "username"  => t('Nickname'),
            "name"      => t('Nachname'),
            "firstname" => t('Vorname'),
            "clan"      => t('Clan'),
            "plz"       => t('PLZ'),
            "city"      => t('Ortschaft')
        ];
        $s_array = [];

        while (list($key, $val) = each($sort_array)) {
            array_push($s_array, "<option value=\"$key\">$val</option>");
        }

        $dsp->AddDropDownFieldRow("order", t('Sortierung'), $s_array, "", 1);
        $dsp->AddFormSubmitRow(t('Weiter'));
        $dsp->AddBackButton("index.php?mod=pdf&action=$action", "pdf/certificate");
    }

    /**
     * Create PDF for visitor cards
     *
     * @param string $pdf_paid
     * @param string $pdf_normal
     * @param string $pdf_op
     * @param string $pdf_orga
     * @param string $pdf_guestid
     */
    private function _makeUserCard($pdf_paid, $pdf_normal, $pdf_op, $pdf_orga, $pdf_guestid)
    {
        global $db, $func, $party;

        define('IMAGE_PATH', 'ext_inc/pdf_templates/');

        $date = date('U');

        $pdf_sqlstring = "";

        // Check for parties
        if ($_POST['party'] == '1' || $pdf_paid) {
            $pdf_sqlstring .= "LEFT JOIN %prefix%party_user AS party ON user.userid = party.user_id";
        }
        $pdf_sqlstring .= ' WHERE user.type > -1';
        if ($_POST['party'] == '1' || $pdf_paid) {
            $pdf_sqlstring .= ' AND party.party_id = '. intval($party->party_id);
        }

        // Check for payment status
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

        // Check for the user
        if ($pdf_guestid > 0) {
            $pdf_sqlstring .= ' AND user.userid = ' . intval($pdf_guestid);
        }

        $query = $db->qry('
          SELECT 
            user.*, 
            clan.name AS clan, 
            clan.url AS clanurl 
          FROM %prefix%user AS user
          LEFT JOIN %prefix%clan AS clan 
            ON user.clanid = clan.clanid ' . $pdf_sqlstring);

        $user_numusers = $db->num_rows($query);

        // Create first page
        $this->_make_page();

        // Get current templates
        $templ_data = $db->qry('
          SELECT * 
          FROM %prefix%pdf_data 
          WHERE 
            template_id=%int% 
            AND type != \'config\' 
            AND type != \'header\' 
            AND type != \'footer\' 
            AND visible = \'1\' 
        ORDER BY sort ASC', $this->templ_id);
        $templ = [];
        while ($templ_data_array = $db->fetch_array($templ_data)) {
            $templ[] = array_merge($templ_data_array, $templ);
        }

        // Determine size
        $this->_get_size($templ);

        // Determine number of columns and rows
        $this->max_col = floor(($this->total_x - $this->start_x)/($this->start_x + $this->object_width));
        $this->max_row = floor(($this->total_y - $this->start_y)/($this->start_y + $this->object_high));

        // Fill the page
        while ($row = $db->fetch_array($query)) {
            unset($data);

            $data['user_nickname']  = $func->AllowHTML($row["username"]);
            $data['party_name']     = $_SESSION['party_info']['name'];
            $data['userid']         = $row["userid"];
            $data['name']           = $row["name"];
            $data['firstname']      = $row["firstname"];
            $data['clan']           = $func->AllowHTML($row["clan"]);
            $data['fullname']       = $row["firstname"] . " " . $row["name"];
            $data['userid']         = $row['userid'];
            $data['plz']            = $row['plz'];
            $data['city']           = $row['city'];
            $data['birthday']       = $row['birthday'];

            // Seating
            $row_seat = $db->qry_first('SELECT s.blockid, col, row, ip FROM %prefix%seat_seats AS s LEFT JOIN %prefix%seat_block AS b ON b.blockid = s.blockid WHERE b.party_id=%int% AND s.userid=%int%', $party->party_id, $row["userid"]);
            $blockid  = $row_seat["blockid"];
            if ($blockid != "") {
                $row_block = $db->qry_first('SELECT orientation, name FROM %prefix%seat_block WHERE blockid=%int%', $blockid);
                $data['orientation']  = $row_block["orientation"];
                $data['col']          = $row_seat["col"];
                $data['row']          = $row_seat["row"];
                $data['user_seat']    = $this->seating->CoordinateToName($row_seat['col'] + 1, $row_seat['row'], $row_block['orientation']);
                $data['user_block']   = $row_block["name"];
            }

            $data['user_ip'] = $row_seat["ip"];

            // Create a new page once the previous one is full
            if ($new_page) {
                $this->pdf->AddPage();
                $new_page = false;
            }

            // Select column and row
            $this->x = (($this->col - 1) * ($this->start_x + $this->object_width)) + $this->start_x;
            $this->y = (($this->row - 1) * ($this->start_y + $this->object_high)) + $this->start_y;

            $this->_write_object($templ, $data);

            // Select next field
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

            if ($row['template_id'] == "") {
                $db->qry_first('INSERT %prefix%pdf_printed SET template_id = %string%, item_id = %int%, time = %string%', $this->templ_id, $row['userid'], $date);
            } else {
                $db->qry_first('UPDATE %prefix%pdf_printed SET time = %string WHERE template_id = %string% AND item_id = %int%', $date, $this->templ_id, $row['userid']);
            }
        }

        $this->pdf->Output("UserCards.pdf", "D");
    }

    /**
     * Create PDF for seating cards
     *
     * @param int $block
     * @param string $order
     * @return void
     */
    private function _makeSeatCard($block, $order)
    {
        global $db, $func, $party;

        define('IMAGE_PATH', 'ext_inc/pdf_templates/');

        if ($order == "row") {
            $sql_order = ", 'row', 'col'";
        } else {
            $sql_order = ", 'col', 'row'";
        }

        if ($block == "null") {
            $query = $db->qry("
              SELECT * 
              FROM %prefix%seat_seats AS s
              LEFT JOIN %prefix%seat_block AS b ON b.blockid = s.blockid
              WHERE 
                b.party_id=%int% 
                AND status > 0 
                AND status < 7 
            ORDER BY 's.blockid' %plain%", $party->party_id, $sql_order);
        } else {
            $query = $db->qry("
              SELECT * 
              FROM %prefix%seat_seats
              WHERE blockid=%string% 
                AND status > 0 
                AND status < 7 
            ORDER BY 'blockid' %plain%", $block, $sql_order);
        }

        // Create first page
        $this->_make_page();

        // Get current templates
        $templ_data = $db->qry("SELECT * FROM %prefix%pdf_data WHERE template_id = %int% AND type != 'config' AND type != 'header' AND type != 'footer' AND visible = '1' ORDER BY sort ASC", $this->templ_id);
        $templ = [];
        while ($templ_data_array = $db->fetch_array($templ_data)) {
            $templ[] = array_merge($templ_data_array, $templ);
        }

        // Determine size
        $this->_get_size($templ);

        // Determine columns and rows
        $this->max_col = floor(($this->total_x - $this->start_x)/($this->start_x + $this->object_width));
        $this->max_row = floor(($this->total_y - $this->start_y)/($this->start_y + $this->object_high));

        // Fill the page
        while ($row = $db->fetch_array($query)) {
            unset($data);

            $row_block              = $db->qry_first("SELECT orientation, name FROM %prefix%seat_block WHERE blockid=%int%", $row['blockid']);
            $userid                 = $row["userid"];
            $data['col']            = $this->seating->CoordinateToNameCol($row["col"], $row_block['orientation']);
            $data['row']            = $this->seating->CoordinateToNameRow($row["row"], $row_block['orientation']);
            $data['seat_block']     = $row_block['name'];
            $data['seat']           = $this->seating->CoordinateToName($row['col'] + 1, $row['row'], $row_block['orientation']);
            $data['party_name']     = $_SESSION['party_info']['name'];

            $row_user = $db->qry_first("
              SELECT 
                user.*, 
                clan.name AS clan, 
                clan.url AS clanurl 
            FROM %prefix%user AS user
            LEFT JOIN %prefix%clan AS clan 
              ON user.clanid = clan.clanid
            WHERE userid=%int%", $userid);

            $data['user_nickname']  = $func->AllowHTML($row_user['username']);
            $data['userid']         = $row_user["userid"];
            $data['name']           = $row_user["name"];
            $data['firstname']      = $row_user["firstname"];
            $data['clan']           = $func->AllowHTML($row_user["clan"]);
            $data['fullname']       = $row["firstname"] . " " . $row["name"];
            $data['plz']            = $row['plz'];
            $data['city']           = $row['city'];
            $data['birthday']       = $row['birthday'];
            $data['seat_ip']        = $row["ip"];
    
            // Create a new page once the previous one is full
            if ($new_page) {
                $this->pdf->AddPage();
                $new_page = false;
            }

            // Select column and row
            $this->x = (($this->col - 1) * ($this->start_x + $this->object_width)) + $this->start_x;
            $this->y = (($this->row - 1) * ($this->start_y + $this->object_high)) + $this->start_y;

            $this->_write_object($templ, $data);

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
        }

        $this->pdf->Output("SeatCards.pdf", "D");
    }

    /**
     * Create PDF for visitor lists
     *
     * @param string $pdf_paid
     * @param string $pdf_normal
     * @param string $pdf_op
     * @param string $pdf_orga
     * @param string $order
     * @return void
     */
    private function _makeUserlist($pdf_paid, $pdf_normal, $pdf_op, $pdf_orga, $order)
    {
        global $db, $func, $party;

        define('IMAGE_PATH', 'ext_inc/pdf_templates/');

        $pdf_sqlstring = "";

        // Check for parties
        if ($_POST['party'] == '1' or $pdf_paid) {
            $pdf_sqlstring .= "LEFT JOIN %prefix%party_user AS party ON user.userid=party.user_id";
        }
        $pdf_sqlstring .= ' WHERE user.type > -1';
        if ($_POST['party'] == '1' or $pdf_paid) {
            $pdf_sqlstring .= ' AND party.party_id = ' . intval($party->party_id);
        }

        // Check for payment status
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

        // Create sorting
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
        }

        $query = $db->qry("
          SELECT 
            user.*, 
            clan.name AS clan, 
            clan.url AS clanurl 
          FROM %prefix%user AS user
          LEFT JOIN %prefix%clan AS clan 
            ON user.clanid = clan.clanid " . $pdf_sqlstring);

        $user_numusers = $db->num_rows($query);

        // Create first page
        $this->_make_page();

        // Get current templates
        $templ_data = $db->qry("
          SELECT * 
          FROM %prefix%pdf_data 
          WHERE 
            template_id = %int% 
            AND type != 'config' 
            AND type != 'header' 
            AND type != 'footer' 
            AND visible = '1' 
          ORDER BY sort ASC", $this->templ_id);

        $templ = [];
        while ($templ_data_array = $db->fetch_array($templ_data)) {
            $templ[] = array_merge($templ_data_array, $templ);
        }

        // Determine the size
        $this->_get_size($templ);

        // Determine the columns and rows
        $this->max_col = floor(($this->total_x - $this->start_x)/($this->start_x + $this->object_width));
        $this->max_row = floor(($this->total_y - (2 * $this->start_y))/($this->object_high));

        // Fill the pages
        $nr = 0;
        while ($row = $db->fetch_array($query)) {
            $nr = $nr + 1;
            unset($data);

            $data['user_nickname']  = $func->AllowHTML($row["username"]);
            $data['party_name']     = $_SESSION['party_info']['name'];
            $data['nr']             = $nr;
            $data['userid']         = $row["userid"];
            $data['lastname']       = $row["name"];
            $data['firstname']      = $row["firstname"];
            $data['fullname']       = $row["firstname"] . " " . $row["name"];
            $data['clan']           = $func->AllowHTML($row["clan"]);
            $data['plz']            = $row['plz'];
            $data['city']           = $row['city'];
            $data['birthday']       = $row['birthday'];

            // Seating
            $row_seat = $db->qry_first('SELECT s.blockid, col, row, ip FROM %prefix%seat_seats AS s LEFT JOIN %prefix%seat_block AS b ON b.blockid = s.blockid WHERE b.party_id=%int% AND s.userid=%int%', $party->party_id, $row["userid"]);
            $blockid  = $row_seat["blockid"];
            if ($blockid != "") {
                $row_block = $db->qry_first("SELECT orientation, name FROM %prefix%seat_block WHERE blockid=%int%", $blockid);
                $data['orientation']  = $row_block["orientation"];
                $data['col']          = $row_seat["col"];
                $data['row']          = $row_seat["row"];
                $data['user_seat']    = $this->seating->CoordinateToName($data['col'] + 1, $data['row'], $data['orientation']);
                $data['user_block']   = $row_block["name"];
            }

            $data['user_ip'] = $row_seat["ip"];

            $this->x = (($this->col - 1) * ($this->start_x + $this->object_width)) + $this->start_x;
            $this->y = (($this->row - 1) * ($this->object_high)) + $this->start_y;

            // Create a new page once the previous one is full
            if ($new_page) {
                $this->pdf->AddPage();
                $new_page = false;
            }

            $this->_write_object($templ, $data);

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
        }

        $this->pdf->Output("Userlist.pdf", "D");
    }

    /**
     * Create PDF for certificates
     *
     * @param string $pdf_normal
     * @param string $pdf_user
     * @return void
     */
    private function _makeCertificate($pdf_normal, $pdf_user)
    {
        global $db, $func, $party;

        define('IMAGE_PATH', 'ext_inc/pdf_templates/');

        $pdf_sqlstring = "";

        // Check for parties
        if ($_POST['party'] == '1') {
            $pdf_sqlstring .= "LEFT JOIN %prefix%party_user AS party ON user.userid=party.user_id";
        }
        $pdf_sqlstring .= ' WHERE user.type > -1';
        if ($_POST['party'] == '1') {
            $pdf_sqlstring .= ' AND party.party_id = ' . intval($party->party_id);
        }
        $pdf_sqlstring = $pdf_sqlstring . " ORDER BY username, name ASC";

        $query = $db->qry("
          SELECT 
            user.*, 
            clan.name AS clan, 
            clan.url AS clanurl 
          FROM %prefix%user AS user
          LEFT JOIN %prefix%clan AS clan 
            ON user.clanid = clan.clanid " . $pdf_sqlstring);

        $user_numusers = $db->num_rows($query);

        // Create first page
        $this->_make_page();

        // Get current templates
        $templ_data = $db->qry("
          SELECT * 
          FROM %prefix%pdf_data 
          WHERE 
            template_id = %int% 
            AND type != 'config' 
            AND type != 'header' 
            AND type != 'footer' 
            AND visible = '1' 
          ORDER BY sort ASC", $this->templ_id);

        $templ = [];
        while ($templ_data_array = $db->fetch_array($templ_data)) {
            $templ[] = array_merge($templ_data_array, $templ);
        }

        // Determine size
        $this->_get_size($templ);

        // Determine columns and rows
        $this->max_col = floor(($this->total_x - $this->start_x)/($this->start_x + $this->object_width));
        $this->max_row = floor(($this->total_y - (2 * $this->start_y))/($this->object_high));

        // Fill pages
        $nr = 0;
        while ($row = $db->fetch_array($query)) {
            $nr = $nr + 1;
            unset($data);

            $data['user_nickname']  = $func->AllowHTML($row["username"]);
            $data['party_name']     = $_SESSION['party_info']['name'];
            $data['nr']             = $nr;
            $data['userid']         = $row["userid"];
            $data['lastname']       = $row["name"];
            $data['firstname']      = $row["firstname"];
            $data['fullname']       = $row["firstname"] . " " . $row["name"];
            $data['clan']           = $func->AllowHTML($row["clan"]);
            $data['plz']            = $row['plz'];
            $data['city']           = $row['city'];
            $data['birthday']       = $row['birthday'];

            $this->x = (($this->col - 1) * ($this->start_x + $this->object_width)) + $this->start_x;
            $this->y = (($this->row - 1) * ($this->object_high)) + $this->start_y;

            // Create a new page once the previous one is full
            if ($new_page) {
                $this->pdf->AddPage();
                $new_page = false;
            }

            $this->_write_object($templ, $data);

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
        }

        $this->pdf->Output("Certificate.pdf", "D");
    }

    /**
     * Create a page
     *
     * @return void
     */
    private function _make_page()
    {
        global $db;

        $page_data = $db->qry_first("
          SELECT * 
          FROM %prefix%pdf_data 
          WHERE 
            template_id= %int% 
            AND type = 'config' 
          ORDER BY sort ASC", $this->templ_id);

        if ($page_data['visible'] == 1) {
            $orientation = 'l';
        } else {
            $orientation = 'p';
        }

        $this->pdf = new \FPDF($orientation, 'mm', $page_data['text']);
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
     * Determine the size of the objects to draw
     *
     * @param array $templ
     * @return void
     */
    private function _get_size($templ)
    {
        // Determine the size of all objects
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
                    $this->barcodeSystem->get_image($_SESSION['userid'], static::BARCODE_PATH .$imagename);
                    $image = getimagesize(static::BARCODE_PATH .$imagename . ".png");
                    if (($image[0]/2) > $this->object_width) {
                        $this->object_width = $image[0];
                    }
                    if (($image[1]/2) > $this->object_high) {
                        $this->object_high = $image[1];
                    }
                    $this->barcodeSystem->kill_image(static::BARCODE_PATH . $imagename);
                    
                    // no break
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
     * Draw object to PDF
     *
     * @param array $templ
     * @param array $data
     * @return void
     */
    private function _write_object($templ, $data)
    {
        for ($i = 0; $i < count($templ); $i++) {
            if ($templ[$i]['user_type'] == $templ[$i]['type'] || $templ[$i]['user_type'] == "0") {
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

                    case 'multicell':
                        $this->pdf->SetFont($templ[$i]['font'], '', $templ[$i]["fontsize"]);
                        $this->pdf->SetTextColor($templ[$i]["red"], $templ[$i]["green"], $templ[$i]["blue"]);
                        $this->pdf->SetXY($templ[$i]["pos_x"] + $this->x, $templ[$i]["pos_y"] + $this->y);
                        $this->pdf->MultiCell($templ[$i]['end_x'], $templ[$i]['end_y'], $templ[$i]['text'], "0", $templ[$i]["align"]);
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
                        $this->barcodeSystem->get_image($data['userid'], static::BARCODE_PATH . $imagename);
                        $this->pdf->Image(static::BARCODE_PATH . $imagename . ".png", $templ[$i]['pos_x'] + $this->x, $templ[$i]['pos_y'] + $this->y);
                        $this->barcodeSystem->kill_image(static::BARCODE_PATH . $imagename);

                        // no break
                    case 'data':
                        $this->pdf->SetFont($templ[$i]['font'], '', $templ[$i]["fontsize"]);
                        $this->pdf->SetTextColor($templ[$i]["red"], $templ[$i]["green"], $templ[$i]["blue"]);
                        $this->pdf->SetXY($templ[$i]["pos_x"] + $this->x, $templ[$i]["pos_y"] + $this->y);
                        $this->pdf->MultiCell($templ[$i]['end_x'], $templ[$i]['end_y'], $data[$templ[$i]['text']], "0", $templ[$i]["align"]);
                        break;
                }
            }
        }
    }
}
