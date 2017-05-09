<?php
include("modules/foodcenter/class_product.php");
$food = new product($_GET['id']);

if (!isset($_GET['step'])) {
    $_GET['step'] = 1;
}

// Check for errors
switch ($_GET['step']) {
    case 2:
        $food->read_post();
        if (!$food->check()) {
            $_GET['step'] = 1;
        }
        break;
}


switch ($_GET['step']) {
    default:
        $food->form_add_product($_GET['step']);
        break;
    
    case 2:
        $food->write();
        $func->confirmation(t('Das Produkt wurde hinzugefügt.'), "index.php?mod=foodcenter");
        break;
}
