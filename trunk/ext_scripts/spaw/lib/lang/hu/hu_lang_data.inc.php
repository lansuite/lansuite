<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Hungarian language file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Translated: Bagoly Sándor Zsigmond, sasa@networldtrading.com
// Copyright: Solmetra (c)2003 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-05-20
// ================================================

// charset to be used in dialogs
$spaw_lang_charset = 'iso-8859-2';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => 'Kivágás'
  ),
  'copy' => array(
    'title' => 'Másolás'
  ),
  'paste' => array(
    'title' => 'Beillesztés'
  ),
  'undo' => array(
    'title' => 'Visszavonás'
  ),
  'redo' => array(
    'title' => 'Mégis'
  ),
  'hyperlink' => array(
    'title' => 'Hiperhivatkozás'
  ),
  'image_insert' => array(
    'title' => 'Kép beszúrás',
    'select' => 'Kiválaszt',
	'delete' => 'Töröl', // new 1.0.5
    'cancel' => 'Mégse',
    'library' => 'Könyvtár',
    'preview' => 'Elõnézet',
    'images' => 'Képek',
    'upload' => 'Kép feltöltése',
    'upload_button' => 'Feltöltés',
    'error' => 'Hiba',
    'error_no_image' => 'Kérem válasszon képet',
    'error_uploading' => 'Hiba lépett fel a feltöltés folyamatában. Kérjük próbálja késõbb.',
    'error_wrong_type' => 'Hibás képtípus',
    'error_no_dir' => 'A könyvtár fizikailag nem létezik',
	'error_cant_delete' => 'Nem lehet törölni', // new 1.0.5
  ),
  'image_prop' => array(
    'title' => 'Kép tulajdonságai',
    'ok' => '   OK   ',
    'cancel' => 'Mégse',
    'source' => 'Forrás',
    'alt' => 'Alternatív szöveg',
    'align' => 'Igazítás',
    'left' => 'Balra',
    'right' => 'Jobbra',
    'top' => 'Tetejére',
    'middle' => 'Középre',
    'bottom' => 'Aljára',
    'absmiddle' => 'Teljesen középre',
    'texttop' => 'Teljesen a tetejére',
    'baseline' => 'Alapvonalra',
    'width' => 'Szélesség',
    'height' => 'Magasság',
    'border' => 'Szegély',
    'hspace' => 'Vízszintes hely',
    'vspace' => 'Függõleges hely',
    'error' => 'Hiba',
    'error_width_nan' => 'Szélesség nem egy szám',
    'error_height_nan' => 'Magasság nem egy szám',
    'error_border_nan' => 'Szegély nem egy szám',
    'error_hspace_nan' => 'Vízszintes hely nem egy szám',
    'error_vspace_nan' => 'Függõleges hely nem egy szám',
  ),
  'hr' => array(
    'title' => 'Vízszintes vonal'
  ),
  'table_create' => array(
    'title' => 'Táblázatot létrehoz'
  ),
  'table_prop' => array(
    'title' => 'Táblázat tulajdonságai',
    'ok' => '   OK   ',
    'cancel' => 'Mégse',
    'rows' => 'Sorok',
    'columns' => 'Oszlopok',
    'width' => 'Szélesség',
    'height' => 'Magasság',
    'border' => 'Szegély',
    'pixels' => 'pixel',
    'cellpadding' => 'Cella kitöltése',
    'cellspacing' => 'Cellák közötti hely',
    'bg_color' => 'Háttérszín',
    'error' => 'Hiba',
    'error_rows_nan' => 'Sorok nem egy szám',
    'error_columns_nan' => 'Oszlopok nem egy szám',
    'error_width_nan' => 'Szélesség nem egy szám',
    'error_height_nan' => 'Magasság nem egy szám',
    'error_border_nan' => 'Szegély nem egy szám',
    'error_cellpadding_nan' => 'Cella kitöltése nem egy szám',
    'error_cellspacing_nan' => 'Cellák közötti hely nem egy szám',
  ),
  'table_cell_prop' => array(
    'title' => 'Cella tulajdonságai',
    'horizontal_align' => 'Vízszintesre zárás',
    'vertical_align' => 'Függõlegesre zárás',
    'width' => 'Szélesség',
    'height' => 'Magasság',
    'css_class' => 'CSS osztály',
    'no_wrap' => 'Nincs csomagolás',
    'bg_color' => 'Háttérszín',
    'ok' => '   OK   ',
    'cancel' => 'Mégse',
    'left' => 'Balra zárás',
    'center' => 'Középre zárás',
    'right' => 'Jobbra zárás',
    'top' => 'Tetejére',
    'middle' => 'Középre',
    'bottom' => 'Aljára',
    'baseline' => 'Alapvonal',
    'error' => 'Hiba',
    'error_width_nan' => 'Szélesség nem egy szám',
    'error_height_nan' => 'Magasság nem egy szám',
  ),
  'table_row_insert' => array(
    'title' => 'Sor beszúrás'
  ),
  'table_column_insert' => array(
    'title' => 'Oszlop beszúrás'
  ),
  'table_row_delete' => array(
    'title' => 'Sor törlése'
  ),
  'table_column_delete' => array(
    'title' => 'Oszlop törlése'
  ),
  'table_cell_merge_right' => array(
    'title' => 'Cellák egyesítése jobbra'
  ),
  'table_cell_merge_down' => array(
    'title' => 'Cellák egyesítése lefele'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'Cellák vízszintes szétszakítása '
  ),
  'table_cell_split_vertical' => array(
    'title' => 'Cellák függõleges szétkaszítása'
  ),
  'style' => array(
    'title' => 'Stílus'
  ),
  'font' => array(
    'title' => 'Betûtípus'
  ),
  'fontsize' => array(
    'title' => 'Méret'
  ),
  'paragraph' => array(
    'title' => 'Bekezdés'
  ),
  'bold' => array(
    'title' => 'Félkövér'
  ),
  'italic' => array(
    'title' => 'Dõlt'
  ),
  'underline' => array(
    'title' => 'Aláhúzott'
  ),
  'ordered_list' => array(
    'title' => 'Számozás'
  ),
  'bulleted_list' => array(
    'title' => 'Felsorolás'
  ),
  'indent' => array(
    'title' => 'Behúzás növelése'
  ),
  'unindent' => array(
    'title' => 'Behúzás csökkentése'
  ),
  'left' => array(
    'title' => 'Balra igazítás'
  ),
  'center' => array(
    'title' => 'Középre igazítás'
  ),
  'right' => array(
    'title' => 'Jobbra igazítás'
  ),
  'fore_color' => array(
    'title' => 'Szín'
  ),
  'bg_color' => array(
    'title' => 'Háttérszín'
  ),
  'design_tab' => array(
    'title' => 'Váltás a WYSWYG (design) módra'
  ),
  'html_tab' => array(
    'title' => 'Váltás a HTML (kód) módra'
  ),
  'colorpicker' => array(
    'title' => 'Színválasztó',
    'ok' => '   OK   ',
    'cancel' => 'Mégse',
  ),
  'cleanup' => array(
    'title' => 'HTML tisztítás (stílusokat megszüntet)',
    'confirm' => 'Ezzel a cselekedettel törli az alkalmazott stílusokat, betûtípusokat és a fölösleges adatokat a jelen dokumentumban. Valamennyi vagy minden formázás el fog veszni.',
    'ok' => '   OK   ',
    'cancel' => 'Mégse',
  ),
  'toggle_borders' => array(
    'title' => 'Szegély megmutatása',
  ),
  'hyperlink' => array(
    'title' => 'Hiperhivatkozás',
    'url' => 'Hivatkozott cím (URL)',
    'name' => 'Név',
    'target' => 'Cél',
    'title_attr' => 'Cím',
    'ok' => '   OK   ',
    'cancel' => 'Mégse',
  ),
  'hyperlink_targets' => array( // <=== new 1.0.5
  	'_self' => 'saját keret (_self)',
	'_blank' => 'új keret (_blank)',
	'_top' => 'legfelsõ keret (_top)',
	'_parent' => 'fõ keret (_parent)'
  ),
  'table_row_prop' => array(
    'title' => 'Sor tulajdonságai',
    'horizontal_align' => 'Vízszintes igazítás',
    'vertical_align' => 'Függõeges igazítás',
    'css_class' => 'CSS osztály',
    'no_wrap' => 'Nincs csomagolás',
    'bg_color' => 'Háttérszín',
    'ok' => '   OK   ',
    'cancel' => 'Mégse',
    'left' => 'Balra',
    'center' => 'Középre',
    'right' => 'Jobbra',
    'top' => 'Tetejére',
    'middle' => 'Középre',
    'bottom' => 'Aljára',
    'baseline' => 'Alapvonalra',
  ),
  'symbols' => array(
    'title' => 'Speciális karakterek',
    'ok' => '   OK   ',
    'cancel' => 'Mégse',
  ),
  'templates' => array(
    'title' => 'Sablonok',
  ),
  'page_prop' => array(
    'title' => 'Oldal tulajdonságok',
    'title_tag' => 'Címe',
    'charset' => 'Karakter típus',
    'background' => 'Háttérkép',
    'bgcolor' => 'Háttérszín',
    'text' => 'Szöveg színe',
    'link' => 'Hivatkozás színe',
    'vlink' => 'Látogatott hivatkozás színe',
    'alink' => 'Aktív hivatkozás színe',
    'leftmargin' => 'Bal margó',
    'topmargin' => 'Tetõ margó',
    'css_class' => 'CSS osztály',
    'ok' => '   OK   ',
    'cancel' => 'Mégse',
  ),
  'preview' => array(
    'title' => 'Elõnézet',
  ),
  'image_popup' => array(
    'title' => 'Elõugró kép',
  ),
  'zoom' => array(
    'title' => 'Nagyítás',
  ),
);
?>

