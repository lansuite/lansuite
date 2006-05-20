<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Swedish language file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// Swedish translation: Tomas Jogin - tomas@jogin.com
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-03-20
// ================================================

// charset to be used in dialogs
$spaw_lang_charset = 'iso-8859-1';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => 'Klipp ut'
  ),
  'copy' => array(
    'title' => 'Kopiera'
  ),
  'paste' => array(
    'title' => 'Klistra in'
  ),
  'undo' => array(
    'title' => '�ngra'
  ),
  'redo' => array(
    'title' => 'G�r om'
  ),
  'hyperlink' => array(
    'title' => 'L�nk'
  ),
  'image_insert' => array(
    'title' => 'Infoga bild',
    'select' => 'Infoga',
    'cancel' => 'Avbryt',
    'library' => 'Bildbibliotek',
    'preview' => 'F�rhandsgranska',
    'images' => 'Bilder',
    'upload' => 'Ladda upp bild',
    'upload_button' => 'Ladda upp',
    'error' => 'Fel',
    'error_no_image' => 'V�lj en bild',
    'error_uploading' => 'Ett fel uppstod vid fil-uppladdningen. Var god f�rs�k igen senare.',
    'error_wrong_type' => 'Fel bildtyp',
    'error_no_dir' => 'Bildbiblioteket finns inte',
  ),
  'image_prop' => array(
    'title' => 'Bildegenskaper',
    'ok' => '   OK   ',
    'cancel' => 'Avbryt',
    'source' => 'K�lla',
    'alt' => 'Beskrivning',
    'align' => 'Justering',
    'left' => 'v�nster',
    'right' => 'h�ger',
    'top' => 'toppen',
    'middle' => 'mitten',
    'bottom' => 'botten',
    'absmiddle' => 'absmiddle',
    'texttop' => 'texttop',
    'baseline' => 'baseline',
    'width' => 'Bredd',
    'height' => 'H�jd',
    'border' => 'Kantlinje',
    'hspace' => 'Horisontell marginal',
    'vspace' => 'Vertikal marginal',
    'error' => 'Fel',
    'error_width_nan' => 'Bredd �r inte ett nummer',
    'error_height_nan' => 'H�jd �r inte ett nummer',
    'error_border_nan' => 'Kantlinje �r inte ett nummer',
    'error_hspace_nan' => 'Horisontell marginal �r inte ett nummer',
    'error_vspace_nan' => 'Vertikal marginal �r inte ett nummer',
  ),
  'hr' => array(
    'title' => 'Horisontell linje'
  ),
  'table_create' => array(
    'title' => 'Skapa tabell'
  ),
  'table_prop' => array(
    'title' => 'Tabellegenskaper',
    'ok' => '   OK   ',
    'cancel' => 'Avbryt',
    'rows' => 'Rader',
    'columns' => 'Kolumner',
    'width' => 'Bredd',
    'height' => 'H�jd',
    'border' => 'Kantlinje',
    'pixels' => 'pixlar',
    'cellpadding' => 'F�ltmarginal',
    'cellspacing' => 'F�ltavst�nd',
    'bg_color' => 'Bakgrundsf�rg',
    'error' => 'Fel',
    'error_rows_nan' => 'Rader �r inte ett nummer',
    'error_columns_nan' => 'Kolumner �r inte ett nummer',
    'error_width_nan' => 'Bredd �r inte ett nummer',
    'error_height_nan' => 'H�jd �r inte ett nummer',
    'error_border_nan' => 'Kantlinje �r inte ett nummer',
    'error_cellpadding_nan' => 'F�ltmarginal �r inte ett nummer',
    'error_cellspacing_nan' => 'F�ltavst�nd �r inte ett nummer',
  ),
  'table_cell_prop' => array(
    'title' => 'F�ltegenskaper',
    'horizontal_align' => 'Horisontell justering',
    'vertical_align' => 'Vertikal justering',
    'width' => 'Bredd',
    'height' => 'H�jd',
    'css_class' => 'CSS-klass',
    'no_wrap' => 'Ej automatisk radbrytning',
    'bg_color' => 'Bakgrundsf�rg',
    'ok' => '   OK   ',
    'cancel' => 'Avbryt',
    'left' => 'V�nster',
    'center' => 'Mitten',
    'right' => 'H�ger',
    'top' => 'Toppen',
    'middle' => 'Mitten',
    'bottom' => 'Botten',
    'baseline' => 'Baslinje',
    'error' => 'Fel',
    'error_width_nan' => 'Bredd �r inte ett nummer',
    'error_height_nan' => 'H�jd �r inte ett nummer',
  ),
  'table_row_insert' => array(
    'title' => 'Infoga rad'
  ),
  'table_column_insert' => array(
    'title' => 'Infoga kolumn'
  ),
  'table_row_delete' => array(
    'title' => 'Radera rad'
  ),
  'table_column_delete' => array(
    'title' => 'Radera kolumn'
  ),
  'table_cell_merge_right' => array(
    'title' => 'Sammanfoga till h�ger'
  ),
  'table_cell_merge_down' => array(
    'title' => 'Sammanfoga ned�t'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'Dela f�lt horisontellt'
  ),
  'table_cell_split_vertical' => array(
    'title' => 'Dela f�lt vertikalt'
  ),
  'style' => array(
    'title' => 'Stilmall'
  ),
  'font' => array(
    'title' => 'Teckensnitt'
  ),
  'fontsize' => array(
    'title' => 'Storlek'
  ),
  'paragraph' => array(
    'title' => 'Stycke'
  ),
  'bold' => array(
    'title' => 'Fetstil'
  ),
  'italic' => array(
    'title' => 'Kursiv'
  ),
  'underline' => array(
    'title' => 'Understruken'
  ),
  'ordered_list' => array(
    'title' => 'Sorterad lista'
  ),
  'bulleted_list' => array(
    'title' => 'Osorterad lista'
  ),
  'indent' => array(
    'title' => 'Indrag'
  ),
  'unindent' => array(
    'title' => 'Ta bort indrag'
  ),
  'left' => array(
    'title' => 'V�nster'
  ),
  'center' => array(
    'title' => 'Mitten'
  ),
  'right' => array(
    'title' => 'H�ger'
  ),
  'fore_color' => array(
    'title' => 'F�rgrundsf�rg'
  ),
  'bg_color' => array(
    'title' => 'Bakgrundsf�rg'
  ),
  'design_tab' => array(
    'title' => 'Byt till layout-l�ge'
  ),
  'html_tab' => array(
    'title' => 'Byt till HTML-l�ge'
  ),
  'colorpicker' => array(
    'title' => 'F�rgv�ljare',
    'ok' => '   OK   ',
    'cancel' => 'Avbryt',
  ),
  'cleanup' => array(
    'title' => 'Rensa HTML',
    'confirm' => 'Detta rensar dokumentet fr�n �verfl�diga stilformateringar och uppm�rkningar. Vissa eller alla stilformateringar kan f�rsvinna.',
    'ok' => '   OK   ',
    'cancel' => 'Avbryt',
  ),
  'toggle_borders' => array(
    'title' => 'Visa/g�m kantlinjer',
  ),
  'hyperlink' => array(
    'title' => 'Infoga l�nk',
    'url' => 'Adress',
    'name' => 'Namn',
    'target' => 'F�nster',
    'title_attr' => 'Titel',
    'ok' => '   OK   ',
    'cancel' => 'Avbryt',
  ),
  'table_row_prop' => array(
    'title' => 'Radegenskaper',
    'horizontal_align' => 'Horisontell justering',
    'vertical_align' => 'Vertikal justering',
    'css_class' => 'CSS-klass',
    'no_wrap' => 'Ej automatisk radbrytning',
    'bg_color' => 'Bakgrundsf�rg',
    'ok' => '   OK   ',
    'cancel' => 'Avbryt',
    'left' => 'V�nster',
    'center' => 'Mitten',
    'right' => 'H�ger',
    'top' => 'Toppen',
    'middle' => 'Mitten',
    'bottom' => 'Botten',
    'baseline' => 'Baslinje',
  ),
  'symbols' => array(
    'title' => 'Speciella tecken',
    'ok' => '   OK   ',
    'cancel' => 'Avbryt',
  ),
  'templates' => array(
    'title' => 'Mallar',
  ),
  'page_prop' => array(
    'title' => 'Sidegenskaper',
    'title_tag' => 'Titel',
    'charset' => 'Teckenupps�ttning',
    'background' => 'Bakgrundsbild',
    'bgcolor' => 'Bakgrundsf�rg',
    'text' => 'Textf�rg',
    'link' => 'L�nkf�rg',
    'vlink' => 'F�rg p� bes�kta l�nkar',
    'alink' => 'F�rg p� markerade l�nkar',
    'leftmargin' => 'V�nstermarginal',
    'topmargin' => 'Topmarginal',
    'css_class' => 'CSS-klass',
    'ok' => '   OK   ',
    'cancel' => 'Avbryt',
  ),
  'preview' => array(
    'title' => 'F�rhandsgranska',
  ),
  'image_popup' => array(
    'title' => 'Bild-popup',
  ),
  'zoom' => array(
    'title' => 'Zoom',
  ),
);
?>