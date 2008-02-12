<?php 
// ====================================================
// SPAW PHP WYSIWYG editor control
// ====================================================
// English language file
// ====================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// Italian translation: Omar Di Marzio - omar@networking.it
// ----------------------------------------------------
//                                www.solmetra.com
// ====================================================
// v.1.0, 2003-04-11
// ====================================================

// charset to be used in dialogs
$spaw_lang_charset = 'iso-8859-1';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => 'Taglia'
  ),
  'copy' => array(
    'title' => 'Copia'
  ),
  'paste' => array(
    'title' => 'Incolla'
  ),
  'undo' => array(
    'title' => 'Indietro'
  ),
  'redo' => array(
    'title' => 'Avanti'
  ),
  'hyperlink' => array(
    'title' => 'Hyperlink'
  ),
  'image_insert' => array(
    'title' => 'Inserisci immagine',
    'select' => 'Seleziona',
    'cancel' => 'Cancella',
    'library' => 'Libreria',
    'preview' => 'Preview',
    'images' => 'Immagini',
    'upload' => 'Upload immagine',
    'upload_button' => 'Upload',
    'error' => 'Error',
    'error_no_image' => 'Immagine non selezionata',
    'error_uploading' => 'Errore in upload. Per favore, ritenta ancora',
    'error_wrong_type' => 'Tipo immagine errata',
    'error_no_dir' => 'La directory delle immagini non esiste fisicamente',
  ),
  'image_prop' => array(
    'title' => 'Proprietà immagine',
    'ok' => '   OK   ',
    'cancel' => 'Cancella',
    'source' => 'Sorgente',
    'alt' => 'Testo alternativo (ALT TAG)',
    'align' => 'Allineamento',
    'left' => 'sinistra',
    'right' => 'destra',
    'top' => 'sopra',
    'middle' => 'metà altezza',
    'bottom' => 'sotto',
    'absmiddle' => 'centro assoluto',
    'texttop' => 'testo sopra',
    'baseline' => 'in linea alla base',
    'width' => 'Larghezza',
    'height' => 'Altezza',
    'border' => 'Bordo',
    'hspace' => 'spazio orizontale',
    'vspace' => 'spazio verticale',
    'error' => 'Errore',
    'error_width_nan' => 'Width non è un numero',
    'error_height_nan' => 'Height non è un numero',
    'error_border_nan' => 'Border non è un numero',
    'error_hspace_nan' => 'Horizontal space non è un numero',
    'error_vspace_nan' => 'Vertical space non è un numero',
  ),
  'hr' => array(
    'title' => 'Righello orizontale'
  ),
  'table_create' => array(
    'title' => 'Crea tabella'
  ),
  'table_prop' => array(
    'title' => 'proprietà tabella',
    'ok' => '   OK   ',
    'cancel' => 'Cancela',
    'rows' => 'Righe',
    'columns' => 'Colonne',
    'width' => 'Larghezza',
    'height' => 'Altezza',
    'border' => 'Bordo',
    'pixels' => 'pixel', 
    'cellpadding' => 'Cell padding',
    'cellspacing' => 'Cell spacing',
    'bg_color' => 'Colore bordo',
    'error' => 'Errore',
    'error_rows_nan' => 'Rows is not a number',
    'error_columns_nan' => 'Columns is not a number',
    'error_width_nan' => 'Width non è un numero',
    'error_height_nan' => 'Height non è un numero',
    'error_border_nan' => 'Border non è un numero',
    'error_cellpadding_nan' => 'Cell padding non è un numero',
    'error_cellspacing_nan' => 'Cell spacing non è un numero',
  ),
  'table_cell_prop' => array(
    'title' => 'Proprietà cella',
    'horizontal_align' => 'Allineamento orizontale',
    'vertical_align' => 'Allineamento verticale',
    'width' => 'Width',
    'height' => 'Height',
    'css_class' => 'classe CSS',
    'no_wrap' => 'No wrap',
    'bg_color' => 'Colore sfondo',
    'ok' => '   OK   ',
    'cancel' => 'Cancela',
    'left' => 'Sinistra',
    'center' => 'Centro',
    'right' => 'Destra',
    'top' => 'Sopra',
    'middle' => 'Metà altezza',
    'bottom' => 'Sotto',
    'baseline' => 'in linea alla base',
    'error' => 'Errore',
    'error_width_nan' => 'Width non è un numero',
    'error_height_nan' => 'Height non è un numero',
    
  ),
  'table_row_insert' => array(
    'title' => 'Inserisci righa'
  ),
  'table_column_insert' => array(
    'title' => 'Inserisci colonna'
  ),
  'table_row_delete' => array(
    'title' => 'Cancella righa'
  ),
  'table_column_delete' => array(
    'title' => 'Cancella colonna'
  ),
  'table_cell_merge_right' => array(
    'title' => 'Unisci righe'
  ),
  'table_cell_merge_down' => array(
    'title' => 'Unisci sotto'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'Spezza le celle orizontalmente'
  ),
  'table_cell_split_vertical' => array(
    'title' => 'Spezza le celle verticalmente'
  ),
  'style' => array(
    'title' => 'Stile'
  ),
  'font' => array(
    'title' => 'Carattere'
  ),
  'fontsize' => array(
    'title' => 'Dimensione'
  ),
  'paragraph' => array(
    'title' => 'Paragrapo'
  ),
  'bold' => array(
    'title' => 'Grassetto'
  ),
  'italic' => array(
    'title' => 'Corsivo'
  ),
  'underline' => array(
    'title' => 'Sottolineato'
  ),
  'ordered_list' => array(
    'title' => 'Lista ordinata'
  ),
  'bulleted_list' => array(
    'title' => 'Elenco puntato'
  ),
  'indent' => array(
    'title' => 'Rientranza a destra'
  ),
  'unindent' => array(
    'title' => 'Rientranza a sinistra'
  ),
  'left' => array(
    'title' => 'Sinistra'
  ),
  'center' => array(
    'title' => 'Centro'
  ),
  'right' => array(
    'title' => 'Destra'
  ),
  'fore_color' => array(
    'title' => 'Colore primo piano'
  ),
  'bg_color' => array(
    'title' => 'Colore di sfondo'
  ),

  'design_tab' => array(
    'title' => 'Cambia in modalità WYSIWYG (grafica)'
  ),
  'html_tab' => array(
    'title' => 'Cambia in modalità HTML (codice)'
  ),
  'colorpicker' => array(
    'title' => 'Raccoglitore di colori',
    'ok' => '   OK   ',
    'cancel' => 'Cancella',
  ),
  // <<<<<<<<< NEW >>>>>>>>>
  'cleanup' => array(
    'title' => 'HTML cleanup (remove styles)',
    'confirm' => 'Performing this action will remove all styles, fonts and useless tags from the current content. Some or all your formatting may be lost.',
    'ok' => '   OK   ',
    'cancel' => 'Cancel',
  ),
  'toggle_borders' => array(
    'title' => 'Toggle borders',
  ),
  'hyperlink' => array(
    'title' => 'Hyperlink',
    'url' => 'URL',
    'name' => 'Name',
    'target' => 'Target',
    'title_attr' => 'Title',
    'ok' => '   OK   ',
    'cancel' => 'Cancel',
  ),
  'table_row_prop' => array(
    'title' => 'Row properties',
    'horizontal_align' => 'Horizontal align',
    'vertical_align' => 'Vertical align',
    'css_class' => 'CSS class',
    'no_wrap' => 'No wrap',
    'bg_color' => 'Background color',
    'ok' => '   OK   ',
    'cancel' => 'Cancel',
    'left' => 'Left',
    'center' => 'Center',
    'right' => 'Right',
    'top' => 'Top',
    'middle' => 'Middle',
    'bottom' => 'Bottom',
    'baseline' => 'Baseline',
  ),
  'symbols' => array(
    'title' => 'Special characters',
    'ok' => '   OK   ',
    'cancel' => 'Cancel',
  ),
  'symbols' => array(
    'title' => 'Special characters',
    'ok' => '   OK   ',
    'cancel' => 'Cancel',
  ),
  'templates' => array(
    'title' => 'Templates',
  ),
  'page_prop' => array(
    'title' => 'Page properties',
    'title_tag' => 'Title',
    'charset' => 'Charset',
    'background' => 'Background image',
    'bgcolor' => 'Background color',
    'text' => 'Text color',
    'link' => 'Link color',
    'vlink' => 'Visited link color',
    'alink' => 'Active link color',
    'leftmargin' => 'Left margin',
    'topmargin' => 'Top margin',
    'css_class' => 'CSS class',
    'ok' => '   OK   ',
    'cancel' => 'Cancel',
  ),
  'preview' => array(
    'title' => 'Preview',
  ),
  'image_popup' => array(
    'title' => 'Image popup',
  ),
  'zoom' => array(
    'title' => 'Zoom',
  ),
);
?>

