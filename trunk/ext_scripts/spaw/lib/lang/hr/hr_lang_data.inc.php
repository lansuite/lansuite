<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Slovenian language file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// Croatian translation:  
//                         dragan@pfri.hr
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-03-20
// ================================================



// charset to be used in dialogs
$spaw_lang_charset = 'windows-1250';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => 'Izre�i'
  ),
  'copy' => array(
    'title' => 'Kopiraj'
  ),
  'paste' => array(
    'title' => 'Zalijepi'
  ),
  'undo' => array(
    'title' => 'Poni�ti'
  ),
  'redo' => array(
    'title' => 'Obnovi'
  ),
  'hyperlink' => array(
    'title' => 'Poveznica'
  ),
  'image_insert' => array(
    'title' => 'Unos slike',
    'select' => 'Izbor',
    'cancel' => 'Prekid',
    'library' => 'Knji�nica',
    'preview' => 'Pregled',
    'images' => 'Slike',
    'upload' => 'Unos slike',
    'upload_button' => 'Unos',
    'error' => 'Grje�ka',
    'error_no_image' => 'Izaberite sliku',
    'error_uploading' => 'Grje�ka pri unosu slike. Ponovite molim',
    'error_wrong_type' => 'Datoteka ne sadr�i sliku',
    'error_no_dir' => 'Knji�nica ne postoji / nije dostupna',
  ),
  'image_prop' => array(
    'title' => 'Naziv slike',
    'ok' => '   OK   ',
    'cancel' => 'Prekid',
    'source' => 'Izvor',
    'alt' => 'Alternativni naziv',
    'align' => 'Poravnaj',
    'left' => 'lijevo',
    'right' => 'desno',
    'top' => 'gore',
    'middle' => 'u sredinu',
    'bottom' => 'dolje',
    'absmiddle' => 'apsolutna sredina',
    'texttop' => 'na vrh teksta',
    'baseline' => 'na osnovni red',
    'width' => '�irina',
    'height' => 'Visina',
    'border' => 'Obrub',
    'hspace' => 'Vodoravni. razmak',
    'vspace' => 'Okomiti razmak',
    'error' => 'Grje�ka',
    'error_width_nan' => '�irina mora biti  broj�ana vrijednost',
    'error_height_nan' => 'Visina mora biti  broj�ana vrijednost',
    'error_border_nan' => 'Obrub mora biti  broj�ana vrijednost',
    'error_hspace_nan' => 'Vodoravni razmak  broj�ana vrijednost',
    'error_vspace_nan' => 'Okomiti razmik mora broj�ana vrijednost',
  ),
  'hr' => array(
    'title' => 'Vodoravna crta'
  ),
  'table_create' => array(
    'title' => 'Tablica'
  ),
  'table_prop' => array(
    'title' => 'Naziv tablice',
    'ok' => '   OK   ',
    'cancel' => 'Prekid',
    'rows' => 'red',
    'columns' => 'stubac',
    'width' => '�irina',
    'height' => 'Visina',
    'border' => 'Debljina obruba',
    'pixels' => 'pixela',
    'cellpadding' => 'Debljina obloge �elije',
    'cellspacing' => 'Razmak me�u �elijama',
    'bg_color' => 'Boja pozadine',
    'error' => 'Grje�ka',
    'error_rows_nan' => 'Broj redova  mora biti  broj�ana vrijednost',
    'error_columns_nan' => 'Broj stubaca  mora biti  broj�ana vrijednost',
    'error_width_nan' => '�irina mora biti  broj�ana vrijednost',
    'error_height_nan' => 'Visina mora biti  broj�ana vrijednost',
    'error_border_nan' => 'Debljina obrube mora biti  broj�ana vrijednost',
    'error_cellpadding_nan' => 'Debljina obloge �elije mora biti  broj�ana vrijednost',
    'error_cellspacing_nan' => 'Razmak me�u �elijama mora biti  broj�ana vrijednost',
  ),
  'table_cell_prop' => array(
    'title' => 'Svojstva �elije',
    'horizontal_align' => 'vodoravno poravnanje',
    'vertical_align' => 'horizontalno  poravnanje',
    'width' => '�irina',
    'height' => 'Visina',
    'css_class' => 'CSS razred',
    'no_wrap' => 'Brez prijeloma (wrap)',
    'bg_color' => 'Boja pozadine',
    'ok' => '   OK   ',
    'cancel' => 'Prekin',
    'left' => 'Lijevo',
    'center' => 'Centar',
    'right' => 'Desno',
    'top' => 'Gore',
    'middle' => 'Sredina',
    'bottom' => 'Dolje',
    'baseline' => 'Osnovna linija',
    'error' => 'Grje�ka',
    'error_width_nan' => '�irina  mora biti  broj�ana vrijednost',
    'error_height_nan' => 'Visina mora biti  broj�ana vrijednost',
  ),
  'table_row_insert' => array(
    'title' => 'Unos reda'
  ),
  'table_column_insert' => array(
    'title' => 'Unos stupca'
  ),
  'table_row_delete' => array(
    'title' => 'Brisanje reda'
  ),
  'table_column_delete' => array(
    'title' => 'Bri�anje stupca'
  ),
  'table_cell_merge_right' => array(
    'title' => 'Unos na desno'
  ),
  'table_cell_merge_down' => array(
    'title' => 'Unos ispod'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'Vodoravno podijeli �eliju'
  ),
  'table_cell_split_vertical' => array(
    'title' => 'Okomito podijeli �eliju'
  ),
  'style' => array(
    'title' => 'Stil'
  ),
  'font' => array(
    'title' => 'Font'
  ),
  'fontsize' => array(
    'title' => 'Veli�ina'
  ),
  'paragraph' => array(
    'title' => 'Odlomak'
  ),
  'bold' => array(
    'title' => 'Podebljano'
  ),
  'italic' => array(
    'title' => 'Kurziv'
  ),
  'underline' => array(
    'title' => 'Podcrtano'
  ),
  'ordered_list' => array(
    'title' => 'Numeriranje'
  ),
  'bulleted_list' => array(
    'title' => 'Grafi�ke oznake'
  ),
  'indent' => array(
    'title' => 'Pove�aj uvlaku'
  ),
  'unindent' => array(
    'title' => 'Smanji uvlaku'
  ),
  'left' => array(
    'title' => 'Lijevo'
  ),
  'center' => array(
    'title' => 'Center'
  ),
  'right' => array(
    'title' => 'Desno'
  ),
  'fore_color' => array(
    'title' => 'Boja prednjice'
  ),
  'bg_color' => array(
    'title' => 'Boja pozadine'
  ),
  'design_tab' => array(
    'title' => 'Pregled izgleda '
  ),
  'html_tab' => array(
    'title' => 'Pregled  HTML koda'
  ),
  'colorpicker' => array(
    'title' => 'Boje',
    'ok' => '   OK   ',
    'cancel' => 'Prekid',
  ),
  'cleanup' => array(
    'title' => '�i��enje HTML (odstranjivanje stilova)',
    'confirm' => 'Brisanje stilova iz HTML koda. Stilovi su djelomice ili potpuno izbrisani.',
    'ok' => '   OK   ',
    'cancel' => 'Prekid',
  ),
  'toggle_borders' => array(
    'title' => 'Preklop obruba',
  ),
  'hyperlink' => array(
    'title' => 'Poveznica',
    'url' => 'URL ( poveznica )',
    'name' => 'Naziv',
    'target' => 'Odredi�ni okvir',
    'title_attr' => 'Tekst za prikaz',
    'ok' => '   OK   ',
    'cancel' => 'Prekid',
  ),
  'table_row_prop' => array(
    'title' => 'Svojstva redova',
    'horizontal_align' => 'Vodoravno poravnanje',
    'vertical_align' => 'Okomito poravnanje',
    'css_class' => 'CSS razred',
    'no_wrap' => 'Brez prijeloma (wrap)',
    'bg_color' => 'Boja pozadine',
    'ok' => '   OK   ',
    'cancel' => 'Prekid',
    'left' => 'Lijevo',
    'center' => 'Centar',
    'right' => 'Desno',
    'top' => 'Gore',
    'middle' => 'Sredina',
    'bottom' => 'Dolje',
    'baseline' => 'Osnovna linija',
  ),
  'symbols' => array(
    'title' => 'Posebni znaci',
    'ok' => '   OK   ',
    'cancel' => 'Prekid',
  ),
  'templates' => array(
    'title' => 'Podloge',
  ),
  'page_prop' => array(
    'title' => 'Svojstva  stranice',
    'title_tag' => 'Naslov',
    'charset' => 'Prikaz slova ( charset)',
    'background' => 'Slika u pozadini',
    'bgcolor' => 'Boja ppozadine',
    'text' => 'Boja slova teksta',
    'link' => 'Boja poveznica',
    'vlink' => 'Boja posje�ene poveznice',
    'alink' => 'Boja aktivne poveznice',
    'leftmargin' => 'Margina lijevo',
    'topmargin' => 'Margina gore',
    'css_class' => 'CSS razred',
    'ok' => '   OK   ',
    'cancel' => 'Prekid',
  ),
  'preview' => array(
    'title' => 'Pregled',
  ),
  'image_popup' => array(
    'title' => 'Popup sa slikom',
  ),
  'zoom' => array(
    'title' => 'Pove�aj ( Zoom )',
  ),
);
?>

