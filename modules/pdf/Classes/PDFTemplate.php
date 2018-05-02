<?php

namespace LanSuite\Module\PDF;

use LanSuite\BarcodeSystem;
use LanSuite\Module\Seating\Seat2;

class PDFTemplate
{
    /**
     * @var string
     */
    private $action;

    /**
     * @var int
     */
    private $tmpl_id;

    /**
     * pdf_tmpl constructor.
     * @param string $action
     * @param int $tmpl_id
     */
    public function __construct($action, $tmpl_id)
    {
        $this->action = $action;
        $this->tmpl_id = $tmpl_id;
    }

    /**
     * Get all templates for a topic
     *
     * @return void
     */
    public function read_List()
    {
        global $db, $dsp, $smarty;

        $data = $db->qry("SELECT * FROM %prefix%pdf_list WHERE template_type = %string%", $this->action);
        $dsp->NewContent(t('PDF erstellen'), t('Bitte eine Formatierungsform ausw&auml;hlen oder eine Neue erstellen'));

        $out = "";
        if ($db->num_rows($data) > 0) {
            while ($data_array = $db->fetch_array($data)) {
                $smarty->assign('liste', "<a href=\"index.php?mod=pdf&action=" . $this->action . "&act=start&id=" . $data_array['template_id'] . "\">" . $data_array['name'] . "</a>");
                $smarty->assign('change', "<a href=\"index.php?mod=pdf&action=" . $this->action . "&act=change&id=" . $data_array['template_id'] . "\">" . t('Vorlage &auml;ndern') . "</a>");
                $smarty->assign('delete', "<a href=\"index.php?mod=pdf&action=" . $this->action . "&delete=1&id=" . $data_array['template_id'] . "\">" . t('Vorlage l&ouml;schen') . "</a>");
                $out .= $smarty->fetch('modules/pdf/templates/liste.htm');
            }
            $dsp->AddSingleRow($out);
        } else {
            $dsp->AddSingleRow(t('Keine Vorlagen gefunden'));
        }

        $dsp->AddSingleRow("<a href=\"index.php?mod=pdf&action=" . $_GET['action'] . "&act=new\">".t('Neue Vorlage erstellen')."</a>");
        $dsp->AddBackButton("index.php?mod=pdf", "pdf/template");
    }

    /**
     * Insert data
     *
     * @return void
     */
    public function add_templ()
    {
        global $db;

        $db->qry("INSERT INTO %prefix%pdf_list ( `template_id` , `template_type` , `name` ) VALUES ('', %string%, %string%)", $this->action, $_POST['template_name']);
        $this->tmpl_id = $db->insert_id();
        
        // Create config
        $db->qry(
            "INSERT INTO %prefix%pdf_data ( `pdfid` , `template_id` , `visible` , `type` , `pos_x` , `pos_y` , `end_x` , `end_y` , `fontsize` , `font` , `red` , `green` , `blue` , `text` , `user_type` ) VALUES
  ('', %int%, %string%, 'config', %string%, %string%,'0','0','0','','0','0','0', %string%, '')",
            $this->tmpl_id,
            $_POST['landscape'],
            $_POST['rand_x'],
            $_POST['rand_y'],
            $_POST['pagesize']
        );
    }

    /**
     * Read data
     *
     * @return void
     */
    public function display_data()
    {
        global $db, $dsp, $templ, $smarty;
                  
        // Display the name
        $template = $db->qry_first("SELECT * FROM %prefix%pdf_list WHERE template_id= %int%", $this->tmpl_id);
        $dsp->NewContent(t('Vorlagen'), t('Vorlage &auml;ndern'));
        $dsp->AddDoubleRow(t('Vorlagenname'), $template['name']);
        
        // Display the configuration
        $template_config = $db->qry_first("SELECT * FROM %prefix%pdf_data WHERE template_id= %int% AND type='config'", $this->tmpl_id);
        
        $dsp->AddDoubleRow(t('Rand in x-Richtung'), $template_config['pos_x']);
        $dsp->AddDoubleRow(t('Rand in y-Richtung'), $template_config['pos_y']);
        $dsp->AddDoubleRow(t('Seitengr&ouml;sse'), $template_config['text']);

        // Display the data
        $data = $db->qry("SELECT * FROM %prefix%pdf_data WHERE template_id= %int% AND type != 'config' ORDER BY sort ASC", $this->tmpl_id);
        $templ['pdf']['action'] = $this->action;
        
        $out = "";
        while ($data_array = $db->fetch_array($data)) {
            $smarty->assign('action', $_GET['action']);
            $smarty->assign('typename', t($data_array['type']));
            $smarty->assign('itemid', $data_array['pdfid']);
            $smarty->assign('id', $this->tmpl_id);
            $description = '';
            if ($data_array['type'] == "rect") {
                $description = t('Xo') . " : " . $data_array['pos_x']. " , ";
                $description .= t('Yo') . " : " . $data_array['pos_y']. " , ";
                $description .= t('Breite') . " : " . $data_array['end_x']. " , ";
                $description .= t('H&ouml;he') . " : " . $data_array['end_y']. " , ";
                $description .= t('Sichtbar') . " : " . $data_array['visible'] . " , ";
                $description .= t('Farbe (r/g/b)') . " : " . $data_array['red'] . "/". $data_array['green'] . "/". $data_array['blue'];
            } elseif ($data_array['type'] == "line") {
                $description = t('Xo') . " : " . $data_array['pos_x']. " , ";
                $description .= t('Yo') . " : " . $data_array['pos_y']. " , ";
                $description .= t('X') . " : " . $data_array['end_x']. " , ";
                $description .= t('Y') . " : " . $data_array['end_y']. " , ";
                $description .= t('Sichtbar') . " : " . $data_array['visible'] . " , ";
                $description .= t('Farbe (r/g/b)') . " : " . $data_array['red'] . "/". $data_array['green'] . "/". $data_array['blue'];
            } elseif ($data_array['type'] == "text" || $data_array['type'] == "data") {
                $description = t('Text') . " : " . $data_array['text']. HTML_NEWLINE;
                $description .= t('Xo') . " : " . $data_array['pos_x']. " , ";
                $description .= t('Yo') . " : " . $data_array['pos_y']. " , ";
                $description .= t('Rechtsb&uuml;ndig') . " : " . $data_array['end_x']. " , ";
                $description .= t('Schriftart') . " : " . $data_array['font']. " , ";
                $description .= t('Schriftgr&ouml;sse') . " : " . $data_array['fontsize']. " , ";
                $description .= t('Sichtbar') . " : " . $data_array['visible'] . " , ";
                $description .= t('Farbe (r/g/b)') . " : " . $data_array['red'] . "/". $data_array['green'] . "/". $data_array['blue'];
            } elseif ($data_array['type'] == "multicell" || $data_array['type'] == "data") {
                $description = t('Text') . " : " . $data_array['text']. HTML_NEWLINE;
                $description .= t('Xo') . " : " . $data_array['pos_x']. " , ";
                $description .= t('Yo') . " : " . $data_array['pos_y']. " , ";
                $description .= t('Ausrichtung') . " : " . $data_array['align']. " , ";
                $description .= t('Schriftart') . " : " . $data_array['font']. " , ";
                $description .= t('Schriftgr&ouml;sse') . " : " . $data_array['fontsize']. " , ";
                $description .= t('Sichtbar') . " : " . $data_array['visible'] . " , ";
                $description .= t('Farbe (r/g/b)') . " : " . $data_array['red'] . "/". $data_array['green'] . "/". $data_array['blue'];
            } elseif ($data_array['type'] == "image") {
                $description = t('Xo') . " : " . $data_array['pos_x']. " , ";
                $description .= t('Yo') . " : " . $data_array['pos_y']. " , ";
                $description .= t('Breite') . " : " . $data_array['end_x']. " , ";
                $description .= t('Sichtbar') . " : " . $data_array['visible'] . " , ";
                $description .= t('H&ouml;he') . " : " . $data_array['end_y'];
            } elseif ($data_array['type'] == "barcode") {
                $description = t('Xo') . " : " . $data_array['pos_x']. " , ";
                $description .= t('Yo') . " : " . $data_array['pos_y']. " , ";
                $description .= t('Sichtbar') . " : " . $data_array['visible'] . " , ";
            }

            $smarty->assign('description', $description);
            $button_edit = $dsp->FetchIcon("index.php?mod=pdf&action=". $_GET['action'] ."&act=change_mask&id=". $this->tmpl_id ."&itemid=". $data_array['pdfid'], 'edit', t('Editieren'));
            $button_del = $dsp->FetchIcon("index.php?mod=pdf&action=". $_GET['action'] ."&act=change&delete_item=1&id=". $this->tmpl_id ."&itemid=". $data_array['pdfid'], 'delete', t('Löschen'));
            $smarty->assign('button_edit', $button_edit);
            $smarty->assign('button_del', $button_del);

            $out .= $smarty->fetch('modules/pdf/templates/edit_liste.htm');
        }
        $dsp->AddSingleRow($out);

        $type = [
            "<option selected value=\"rect\">" . t('Rechteck') . "</option>",
            "<option value=\"text\">" . t('Text') . "</option>",
            "<option value=\"multicell\">" . t('Multicell') . "</option>",
            "<option value=\"line\">" . t('Linie') . "</option>",
            "<option value=\"image\">" . t('Bild') . "</option>",
            "<option value=\"data\">" . t('Daten') . "</option>",
            "<option value=\"barcode\">" . t('Strichcode') . "</option>"
        ];

        $dsp->SetForm("index.php?mod=pdf&action=" . $this->action . "&act=insert_mask&id=" . $this->tmpl_id);
        $dsp->AddDropDownFieldRow('type', t('Wahl des Feldes'), $type, "");
        $dsp->AddFormSubmitRow(t('Hinzufügen'));
        $dsp->AddBackButton("index.php?mod=pdf&action=" . $this->action, "pdf/change_template");
    }

    /**
     * Show mask to create a new entry.
     *
     * @param string $object
     * @return void
     */
    public function insert_mask($object)
    {
        global $dsp;

        $barcodeSystem = new BarcodeSystem();
        $seating = new Seat2();
        $pdf_export = new PDF($this->tmpl_id, $barcodeSystem, $seating);
                              
        // Create new user type
        $user_type = [
            "<option selected value=\"0\">" . t('Alle') . "</option>",
            "<option value=\"1\">" . t('Besucher ist normaler Gast') . "</option>",
            "<option value=\"2\">" . t('Administrator') . "</option>",
            "<option value=\"3\">" . t('Superadmin') . "</option>"
        ];

        $dsp->NewContent(t('Objekt'), t('Neues Objekt erstellen'));
        $dsp->AddSingleRow(t('Erstelle ') . t($object));
        $dsp->SetForm("index.php?mod=pdf&action=" . $this->action ."&act=insert_item&object=$object&id=$this->tmpl_id");
        $help = '';
        if ($object == "rect") {
            $dsp->AddTextFieldRow("pos_x", t('Xo'), '', '');
            $dsp->AddTextFieldRow("pos_y", t('Yo'), '', '');
            $dsp->AddTextFieldRow("end_x", t('Breite'), '', '');
            $dsp->AddTextFieldRow("end_y", t('H&ouml;he'), '', '');
            $dsp->AddTextFieldRow("red", t('Rot Anteil'), '0', '');
            $dsp->AddTextFieldRow("green", t('Gr&uuml;n Anteil'), '0', '');
            $dsp->AddTextFieldRow("blue", t('Blau Anteil'), '0', '');
            $dsp->AddCheckBoxRow("fontsize", t('Gef&uuml;llt'), '', '');
            $dsp->AddDropDownFieldRow('user_type', t('Angezeigt bei:'), $user_type, "");
            $dsp->AddCheckBoxRow("visible", t('Sichtbar'), '', '', 'NULL', '1');
            $dsp->AddTextFieldRow("sort", t('Reihenfolge'), '', '');
            $help = "pdf/item_rect";
        } elseif ($object == "line") {
            $dsp->AddTextFieldRow("pos_x", t('Xo'), '', '');
            $dsp->AddTextFieldRow("pos_y", t('Yo'), '', '');
            $dsp->AddTextFieldRow("end_x", t('X'), '', '');
            $dsp->AddTextFieldRow("end_y", t('Y'), '', '');
            $dsp->AddTextFieldRow("red", t('Rot Anteil'), '0', '');
            $dsp->AddTextFieldRow("green", t('Gr&uuml;n Anteil'), '0', '');
            $dsp->AddTextFieldRow("blue", t('Blau Anteil'), '0', '');
            $dsp->AddDropDownFieldRow('user_type', t('Angezeigt bei:'), $user_type, "");
            $dsp->AddCheckBoxRow("visible", t('Sichtbar'), '', '', 'NULL', '1');
            $dsp->AddTextFieldRow("sort", t('Reihenfolge'), '', '');
            $help = "pdf/item_line";
        } elseif ($object == "text") {
            $dsp->AddTextFieldRow("text", t('Text'), '', '');
            $dsp->AddTextFieldRow("pos_x", t('Xo'), '', '');
            $dsp->AddTextFieldRow("pos_y", t('Yo'), '', '');
            $dsp->AddCheckBoxRow("end_x", t('Rechtsb&uuml;ndig'), '', '');
            $dsp->AddTextFieldRow("font", t('Schriftart'), 'Arial', '');
            $dsp->AddTextFieldRow("fontsize", t('Schriftgr&ouml;sse'), '12', '');
            $dsp->AddTextFieldRow("red", t('Rot Anteil'), '0', '');
            $dsp->AddTextFieldRow("green", t('Gr&uuml;n Anteil'), '0', '');
            $dsp->AddTextFieldRow("blue", t('Blau Anteil'), '0', '');
            $dsp->AddDropDownFieldRow('user_type', t('Angezeigt bei:'), $user_type, "");
            $dsp->AddCheckBoxRow("visible", t('Sichtbar'), '', '', 'NULL', '1');
            $dsp->AddTextFieldRow("sort", t('Reihenfolge'), '', '');
            $help = "pdf/item_text";
        } elseif ($object == "multicell") {
            $dsp->AddTextFieldRow("text", t('Text'), '', '');
            $dsp->AddTextFieldRow("pos_x", t('Xo'), '', '');
            $dsp->AddTextFieldRow("pos_y", t('Yo'), '', '');
            $dsp->AddTextFieldRow("end_x", t('Breite'), '', '');
            $dsp->AddTextFieldRow("end_y", t('H&ouml;he'), '', '');
            $dsp->AddTextFieldRow("align", t('Ausrichtung'), 'L', '');
            $dsp->AddTextFieldRow("font", t('Schriftart'), 'Arial', '');
            $dsp->AddTextFieldRow("fontsize", t('Schriftgr&ouml;sse'), '12', '');
            $dsp->AddTextFieldRow("red", t('Rot Anteil'), '0', '');
            $dsp->AddTextFieldRow("green", t('Gr&uuml;n Anteil'), '0', '');
            $dsp->AddTextFieldRow("blue", t('Blau Anteil'), '0', '');
            $dsp->AddDropDownFieldRow('user_type', t('Angezeigt bei:'), $user_type, "");
            $dsp->AddCheckBoxRow("visible", t('Sichtbar'), '', '', 'NULL', '1');
            $dsp->AddTextFieldRow("sort", t('Reihenfolge'), '', '');
            $help = "pdf/item_text";
        } elseif ($object == "data") {
            $dsp->AddDropDownFieldRow('text', t('Daten'), $pdf_export->get_data_array($this->action), "");
            $dsp->AddTextFieldRow("pos_x", t('Xo'), '', '');
            $dsp->AddTextFieldRow("pos_y", t('Yo'), '', '');
            $dsp->AddTextFieldRow("end_x", t('Breite'), '', '');
            $dsp->AddTextFieldRow("end_y", t('H&ouml;he'), '', '');
            $dsp->AddTextFieldRow("align", t('Ausrichtung'), 'L', '');
            $dsp->AddTextFieldRow("font", t('Schriftart'), 'Arial', '');
            $dsp->AddTextFieldRow("fontsize", t('Schriftgr&ouml;sse'), '12', '');
            $dsp->AddTextFieldRow("red", t('Rot Anteil'), '0', '');
            $dsp->AddTextFieldRow("green", t('Gr&uuml;n Anteil'), '0', '');
            $dsp->AddTextFieldRow("blue", t('Blau Anteil'), '0', '');
            $dsp->AddDropDownFieldRow('user_type', t('Angezeigt bei:'), $user_type, "");
            $dsp->AddCheckBoxRow("visible", t('Sichtbar'), '', '', 'NULL', '1');
            $dsp->AddTextFieldRow("sort", t('Reihenfolge'), '', '');
            $help = "pdf/item_data";
        } elseif ($object == "image") {
            $dsp->AddTextFieldRow("text", t('Datei (relativ zu ext_inc/pdf_templates/'), '', '');
            $dsp->AddTextFieldRow("pos_x", t('Xo'), '', '');
            $dsp->AddTextFieldRow("pos_y", t('Yo'), '', '');
            $dsp->AddTextFieldRow("end_x", t('Breite'), '', '');
            $dsp->AddTextFieldRow("end_y", t('H&ouml;he'), '', '');
            $dsp->AddDropDownFieldRow('user_type', t('Angezeigt bei:'), $user_type, "");
            $dsp->AddCheckBoxRow("visible", t('Sichtbar'), '', '', 'NULL', '1');
            $dsp->AddTextFieldRow("sort", t('Reihenfolge'), '', '');
            $help = "pdf/item_img";
        } elseif ($object == "barcode") {
            $dsp->AddTextFieldRow("pos_x", t('Xo'), '', '');
            $dsp->AddTextFieldRow("pos_y", t('Yo'), '', '');
            $dsp->AddDropDownFieldRow('user_type', t('Angezeigt bei:'), $user_type, "");
            $dsp->AddCheckBoxRow("visible", t('Sichtbar'), '', '', 'NULL', '1');
            $dsp->AddTextFieldRow("sort", t('Reihenfolge'), '', '');
            $help = "pdf/item_img";
        }
        $dsp->AddFormSubmitRow(t('Hinzufügen'));
        $dsp->AddBackButton("index.php?mod=pdf&action=" . $this->action, $help);
    }

    /**
     * Create edit mask
     *
     * @param int $item_id
     * @return void
     */
    public function change_mask($item_id)
    {
        global $db, $dsp;

        $barcodeSystem = new BarcodeSystem();
        $seating = new Seat2();
        $pdf_export = new PDF($this->tmpl_id, $barcodeSystem, $seating);
        $data = $db->qry_first("SELECT * FROM %prefix%pdf_data WHERE pdfid= %int%", $item_id);
                                  
        $user_type_list = [
            "0" =>  t('Alle'),
            "1" =>  t('Besucher ist normaler Gast'),
            "2" =>  t('Administrator'),
            "3" =>  t('Superadmin')
        ];

        $user_type = [];
        foreach ($user_type_list as $key => $value) {
            if ($key == $data['user_type']) {
                $user_type[$key] = "<option selected value=\"$key\">$value</option>";
            } else {
                $user_type[$key] = "<option value=\"$key\">$value</option>";
            }
        }

        // List for users
        foreach ($user_type_list as $key => $value) {
            if ($key == $data['user_type']) {
                $user_type[$key] = "<option selected value=\"$key\">$value</option>";
            } else {
                $user_type[$key] = "<option value=\"$key\">$value</option>";
            }
        }
                     
        $object = $data['type'];
        $dsp->NewContent(t('Objekt'), t('Objekt &auml;ndern'));
        $dsp->AddSingleRow(t('Ändere ') . " " . t($object));
        $dsp->SetForm("index.php?mod=pdf&action=" . $this->action ."&act=change_item&object=$object&id=$this->tmpl_id&itemid=$item_id");
        $help = '';
        if ($object == "rect") {
            $dsp->AddTextFieldRow("pos_x", t('Xo'), $data['pos_x'], '');
            $dsp->AddTextFieldRow("pos_y", t('Yo'), $data['pos_y'], '');
            $dsp->AddTextFieldRow("end_x", t('Breite'), $data['end_x'], '');
            $dsp->AddTextFieldRow("end_y", t('H&ouml;he'), $data['end_y'], '');
            $dsp->AddTextFieldRow("red", t('Rot Anteil'), $data['red'], '');
            $dsp->AddTextFieldRow("green", t('Gr&uuml;n Anteil'), $data['green'], '');
            $dsp->AddTextFieldRow("blue", t('Blau Anteil'), $data['blue'], '');
            $dsp->AddCheckBoxRow("fontsize", t('Gef&uuml;llt'), '', '', 'NULL', $data['fontsize']);
            $dsp->AddDropDownFieldRow('user_type', t('Angezeigt bei:'), $user_type, "");
            $dsp->AddCheckBoxRow("visible", t('Sichtbar'), '', '', 'NULL', $data['visible']);
            $dsp->AddTextFieldRow("sort", t('Reihenfolge'), $data['sort'], '');
            $help = "pdf/item_rect";
        } elseif ($object == "line") {
            $dsp->AddTextFieldRow("pos_x", t('Xo'), $data['pos_x'], '');
            $dsp->AddTextFieldRow("pos_y", t('Yo'), $data['pos_y'], '');
            $dsp->AddTextFieldRow("end_x", t('X'), $data['end_x'], '');
            $dsp->AddTextFieldRow("end_y", t('Y'), $data['end_y'], '');
            $dsp->AddTextFieldRow("red", t('Rot Anteil'), $data['red'], '');
            $dsp->AddTextFieldRow("green", t('Gr&uuml;n Anteil'), $data['green'], '');
            $dsp->AddTextFieldRow("blue", t('Blau Anteil'), $data['blue'], '');
            $dsp->AddDropDownFieldRow('user_type', t('Angezeigt bei:'), $user_type, "");
            $dsp->AddCheckBoxRow("visible", t('Sichtbar'), '', '', 'NULL', $data['visible']);
            $dsp->AddTextFieldRow("sort", t('Reihenfolge'), $data['sort'], '');
            $help = "pdf/item_line";
        } elseif ($object == "text") {
            $dsp->AddTextFieldRow("text", t('Text'), $data['text'], '');
            $dsp->AddTextFieldRow("pos_x", t('Xo'), $data['pos_x'], '');
            $dsp->AddTextFieldRow("pos_y", t('Yo'), $data['pos_y'], '');
            $dsp->AddCheckBoxRow("end_x", t('Rechtsb&uuml;ndig'), '', '', 'NULL', $data['end_x']);
            $dsp->AddTextFieldRow("font", t('Schriftart'), $data['font'], '');
            $dsp->AddTextFieldRow("fontsize", t('Schriftgr&ouml;sse'), $data['fontsize'], '');
            $dsp->AddTextFieldRow("red", t('Rot Anteil'), $data['red'], '');
            $dsp->AddTextFieldRow("green", t('Gr&uuml;n Anteil'), $data['green'], '');
            $dsp->AddTextFieldRow("blue", t('Blau Anteil'), $data['blue'], '');
            $dsp->AddDropDownFieldRow('user_type', t('Angezeigt bei:'), $user_type, "");
            $dsp->AddCheckBoxRow("visible", t('Sichtbar'), '', '', 'NULL', $data['visible']);
            $dsp->AddTextFieldRow("sort", t('Reihenfolge'), $data['sort'], '');
            $help = "pdf/item_text";
        } elseif ($object == "multicell") {
            $dsp->AddTextFieldRow("text", t('Text'), $data['text'], '');
            $dsp->AddTextFieldRow("pos_x", t('Xo'), $data['pos_x'], '');
            $dsp->AddTextFieldRow("pos_y", t('Yo'), $data['pos_y'], '');
            $dsp->AddTextFieldRow("end_x", t('Breite'), $data['end_x'], '');
            $dsp->AddTextFieldRow("end_y", t('H&ouml;he'), $data['end_y'], '');
            $dsp->AddTextFieldRow("align", t('Ausrichtung'), $data['align'], '');
            $dsp->AddTextFieldRow("font", t('Schriftart'), $data['font'], '');
            $dsp->AddTextFieldRow("fontsize", t('Schriftgr&ouml;sse'), $data['fontsize'], '');
            $dsp->AddTextFieldRow("red", t('Rot Anteil'), $data['red'], '');
            $dsp->AddTextFieldRow("green", t('Gr&uuml;n Anteil'), $data['green'], '');
            $dsp->AddTextFieldRow("blue", t('Blau Anteil'), $data['blue'], '');
            $dsp->AddDropDownFieldRow('user_type', t('Angezeigt bei:'), $user_type, "");
            $dsp->AddCheckBoxRow("visible", t('Sichtbar'), '', '', 'NULL', $data['visible']);
            $dsp->AddTextFieldRow("sort", t('Reihenfolge'), $data['sort'], '');
            $help = "pdf/item_text";
        } elseif ($object == "data") {
            $dsp->AddDropDownFieldRow('text', t('Daten'), $pdf_export->get_data_array($this->action, $data['text']), "");
            $dsp->AddTextFieldRow("pos_x", t('Xo'), $data['pos_x'], '');
            $dsp->AddTextFieldRow("pos_y", t('Yo'), $data['pos_y'], '');
            $dsp->AddTextFieldRow("end_x", t('Breite'), $data['end_x'], '');
            $dsp->AddTextFieldRow("end_y", t('H&ouml;he'), $data['end_y'], '');
            $dsp->AddTextFieldRow("align", t('Ausrichtung'), $data['align'], '');
            $dsp->AddTextFieldRow("font", t('Schriftart'), $data['font'], '');
            $dsp->AddTextFieldRow("fontsize", t('Schriftgr&ouml;sse'), $data['fontsize'], '');
            $dsp->AddTextFieldRow("red", t('Rot Anteil'), $data['red'], '');
            $dsp->AddTextFieldRow("green", t('Gr&uuml;n Anteil'), $data['green'], '');
            $dsp->AddTextFieldRow("blue", t('Blau Anteil'), $data['blue'], '');
            $dsp->AddDropDownFieldRow('user_type', t('Angezeigt bei:'), $user_type, "");
            $dsp->AddCheckBoxRow("visible", t('Sichtbar'), '', '', 'NULL', $data['visible']);
            $dsp->AddTextFieldRow("sort", t('Reihenfolge'), $data['sort'], '');
            $help = "pdf/item_data";
        } elseif ($object == "image") {
            $dsp->AddTextFieldRow("text", t('Datei (relativ zu ext_inc/pdf_templates/'), $data['text'], '');
            $dsp->AddTextFieldRow("pos_x", t('Xo'), $data['pos_x'], '');
            $dsp->AddTextFieldRow("pos_y", t('Yo'), $data['pos_y'], '');
            $dsp->AddTextFieldRow("end_x", t('Breite'), $data['end_x'], '');
            $dsp->AddTextFieldRow("end_y", t('H&ouml;he'), $data['end_y'], '');
            $dsp->AddDropDownFieldRow('user_type', t('Angezeigt bei:'), $user_type, "");
            $dsp->AddCheckBoxRow("visible", t('Sichtbar'), '', '', 'NULL', $data['visible']);
            $dsp->AddTextFieldRow("sort", t('Reihenfolge'), $data['sort'], '');
            $help = "pdf/item_img";
        } elseif ($object == "barcode") {
            $dsp->AddTextFieldRow("pos_x", t('Xo'), $data['pos_x'], '');
            $dsp->AddTextFieldRow("pos_y", t('Yo'), $data['pos_y'], '');
            $dsp->AddDropDownFieldRow('user_type', t('Angezeigt bei:'), $user_type, "");
            $dsp->AddCheckBoxRow("visible", t('Sichtbar'), '', '', 'NULL', $data['visible']);
            $dsp->AddTextFieldRow("sort", t('Reihenfolge'), $data['sort'], '');
            $help = "pdf/item_img";
        }
        $dsp->AddFormSubmitRow(t('&Auml;ndern'));
        $dsp->AddBackButton("index.php?mod=pdf&action=" . $this->action ."&act=change&id=" . $this->tmpl_id, $help);
    }

    /**
     * Insert an object
     *
     * @param string $object
     * @return void
     */
    public function insert_item($object)
    {
        global $db, $func;

        if ($_POST['visible'] == "checked") {
            $visible = 1;
        } else {
            $visible = 0;
        }

        if ($db->qry("INSERT INTO %prefix%pdf_data ( `template_id` , `visible` , `type` , `pos_x` , `pos_y` , `end_x` , `end_y` , `align` , `fontsize` , `font` , `red` , `green` , `blue` , `text` , `user_type` , `sort` ) 
          VALUES %plain%", "('$this->tmpl_id' , '" . $_POST['visible'] . "' , '$object', '" . $_POST['pos_x'] . "', '" . $_POST['pos_y'] . "', '" . $_POST['end_x'] . "', '" . $_POST['end_y'] . "', '" . $_POST['align'] . "', '" . $_POST['fontsize'] . "', '" . $_POST['font'] . "', '" . $_POST['red'] . "', '" . $_POST['green'] . "', '" . $_POST['blue'] . "', '" . $_POST['text'] . "', '" . $_POST['user_type'] . "', '" . $_POST['sort'] . "')")) {
            $func->confirmation(t('Die Daten wurden hinzugef&uuml;gt'), "index.php?mod=pdf&action=" . $this->action ."&act=change&id=" . $this->tmpl_id);
        } else {
            $func->error(t('Die Daten konnten nicht hinzugef&uuml;gt werden'), "index.php?mod=pdf&action=" . $this->action ."&act=change&id=" . $this->tmpl_id);
        }
    }

    /**
     * Change object
     *
     * @param int $item_id
     * @return void
     */
    public function change_item($item_id)
    {
        global $db, $func;

        if ($_POST['visible'] == "checked") {
            $visible = 1;
        } else {
            $visible = 0;
        }

        if ($db->qry("UPDATE %prefix%pdf_data SET %plain%", "  
             `visible`='" . $_POST['visible'] .
               "', `pos_x`='" . $_POST['pos_x'] .
               "', `pos_y`='" . $_POST['pos_y'] .
               "', `end_x`='" . $_POST['end_x'] .
               "', `end_y`='" . $_POST['end_y'] .
               "', `fontsize`='" . $_POST['fontsize'] .
               "', `font`='" . $_POST['font'] .
               "', `align`='" . $_POST['align'] .
               "', `red`='" . $_POST['red'] .
               "', `green`='" . $_POST['green'] .
               "', `blue`='" . $_POST['blue'] .
               "', `text`='" . $_POST['text'] .
               "', `user_type`='" . $_POST['user_type'] .
               "', `sort`='" . $_POST['sort'] .
               "' WHERE `template_id`='" . $this->tmpl_id . "' AND `pdfid`='" . $item_id . "'")) {
            $func->confirmation(t('Die Daten wurden hinzugef&uuml;gt'), "index.php?mod=pdf&action=" . $this->action ."&act=change&id=" . $this->tmpl_id);
        } else {
            $func->error(t('Die Daten konnten nicht hinzugef&uuml;gt werden'), "index.php?mod=pdf&action=" . $this->action ."&act=change&id=" . $this->tmpl_id);
        }
    }

    /**
     * Change sorting
     *
     * @param $direction
     * @param $item_id
     * @return void
     */
    public function sortorder($direction, $item_id)
    {
        global $db;
        
        if ($direction == "minus") {
            $sort = "-1";
        } else {
            $sort = "+1";
        }

        $db->qry("UPDATE %prefix%pdf_data SET sort=sort%plain% WHERE pdfid = %int%", $sort, $item_id);
    }

    /**
     * Delete template
     *
     * @return void
     */
    public function delete_templ()
    {
        global $db;
        
        $db->qry("DELETE FROM %prefix%pdf_list WHERE template_id = %int%", $this->tmpl_id);
        $db->qry("DELETE FROM %prefix%pdf_data WHERE template_id = %int%", $this->tmpl_id);
    }

    /**
     * Delete item
     *
     * @param int $itemid
     * @return void
     */
    public function delete_item($itemid)
    {
        global $db;
        
        $db->qry("DELETE FROM %prefix%pdf_data WHERE pdfid = %int%", $itemid);
    }

    /**
     * New mask for templates
     *
     * @return void
     */
    public function new_templ_mask()
    {
        global $dsp;

        $page_size = [
            "<option selected value=\"A4\">A4</option>",
            "<option value=\"A3\">A3</option>",
            "<option value=\"A5\">A5</option>"
        ];
        
        // Form for new templates
        $dsp->NewContent(t('Vorlagen'), t('Neue Vorlage erstellen'));
        $dsp->SetForm("index.php?mod=pdf&action=" . $this->action . "&act=add");
        $dsp->AddTextFieldRow("template_name", t('Vorlagenname'), '', '');
        $dsp->AddDropDownFieldRow("pagesize", t('Seitengr&ouml;sse'), $page_size, '');
        $dsp->AddTextFieldRow("rand_x", t('Rand in x-Richtung'), '', '');
        $dsp->AddTextFieldRow("rand_y", t('Rand in y-Richtung'), '', '');
        $dsp->AddCheckBoxRow("landscape", t('Querformat'), '', '');
        $dsp->AddFormSubmitRow(t('Hinzufügen'));
    }
}
