<?php

use LanSuite\BarcodeSystem;
use LanSuite\Module\PDF\PDF;
use LanSuite\Module\PDF\PDFTemplate;
use LanSuite\Module\Seating\Seat2;

if (isset($_GET['userid'])) {
    $_POST['user'] = $_GET['userid'];
}

// Get template ID
if (isset($_POST['id'])) {
    $templ_id = $_POST['id'];
}
if (isset($_GET['id'])) {
    $templ_id = $_GET['id'];
}

$pdf_tmpl = new PDFTemplate($_GET['action'], $templ_id);
$barcodeSystem = new BarcodeSystem();
$seating = new Seat2();
$pdf_export = new PDF($templ_id, $barcodeSystem, $seating);

switch ($_GET['act']) {
    default:
        // Delete an entry
        if (isset($_GET['delete'])) {
            $pdf_tmpl->delete_templ();
        }

        // Show the templates
        $pdf_tmpl->read_List();
        break;
    
    case 'new':
        $pdf_tmpl->new_templ_mask();
        break;
        
    case 'add':
        $pdf_tmpl->add_templ();
        
        // no break
    case 'change':
        // Delete an entry
        if (isset($_GET['delete_item'])) {
            $pdf_tmpl->delete_item($_GET['itemid']);
        }

        // Change the sorting
        if (isset($_GET['direction'])) {
            $pdf_tmpl->sortorder($_GET['direction'], $_GET['itemid']);
        }
        
        // Show the entries
        $pdf_tmpl->display_data();
        break;
    
    // Create a new field mask
    case 'insert_mask':
        $pdf_tmpl->insert_mask($_POST['type']);
        break;
    
    // Insert a new item
    case 'insert_item':
        $pdf_tmpl->insert_item($_GET['object']);
        break;
    
    // Change a field mask
    case 'change_mask':
        $pdf_tmpl->change_mask($_GET['itemid']);
        break;
    
    case 'change_item':
        $pdf_tmpl->change_item($_GET['itemid']);
        break;

    case 'start':
        $pdf_export->pdf_menu($_GET['action']);
        break;
    
    case 'print':
        $pdf_export->pdf_make($_GET['action']);
        break;
}
