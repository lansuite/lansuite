<?php
include_once("modules/pdf/class_templ_pdf.php");
include_once("modules/pdf/class_pdf.php");

if (isset($_GET['userid'])) {
    $_POST['user'] = $_GET['userid'];
}

//Template ID Laden
if (isset($_POST['id'])) {
    $templ_id = $_POST['id'];
}
if (isset($_GET['id'])) {
    $templ_id = $_GET['id'];
}


$pdf_tmpl = new pdf_tmpl($_GET['action'], $templ_id);
$pdf_export = new pdf($templ_id);

switch ($_GET['act']) {
    default:
        // Eintrag l�schen
        if (isset($_GET['delete'])) {
            $pdf_tmpl->delete_templ();
        }
        // Vorlagen ausgeben
        $pdf_tmpl->read_List();
        break;
    
    case 'new':
        $pdf_tmpl->new_templ_mask();
        break;
        
    case 'add':
        $pdf_tmpl->add_templ();
        
    case 'change':
        // Eintrag l�schen
        if (isset($_GET['delete_item'])) {
            $pdf_tmpl->delete_item($_GET['itemid']);
        }
        // Reihenfolge �ndern
        if (isset($_GET['direction'])) {
            $pdf_tmpl->sortorder($_GET['direction'], $_GET['itemid']);
        }
        
        // Eintr�ge anzeigen
        $pdf_tmpl->display_data();
        break;
    
    // Neues Feld anlegen
    case 'insert_mask':
        $pdf_tmpl->insert_mask($_POST['type']);
        break;
    
    // Neues Feld eintragen
    case 'insert_item':
        $pdf_tmpl->insert_item($_GET['object']);
        break;
    
    // Feld �ndern
    case 'change_mask':
        $pdf_tmpl->change_mask($_GET['itemid']);
        break;
    
    case 'change_item':
        $pdf_tmpl->change_item($_GET['itemid']);
        break;
    
    // Ausgabe vorbereiten
    case 'start':
        $pdf_export->pdf_menu($_GET['action']);
        break;
    
    case 'print':
        $pdf_export->pdf_make($_GET['action']);
        break;
}
