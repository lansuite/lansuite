<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Czech language file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// Czech translation: BrM (BrM@bridlicna.cz)
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-03-20
// ================================================

// charset to be used in dialogs
$spaw_lang_charset = 'iso-8859-2';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => 'Vyjmout'
  ),
  'copy' => array(
    'title' => 'Kopírovat'
  ),
  'paste' => array(
    'title' => 'Vložit'
  ),
  'undo' => array(
    'title' => 'Zpìt'
  ),
  'redo' => array(
    'title' => 'Znovu'
  ),
  'hyperlink' => array(
    'title' => 'Hyperlink'
  ),
  'image_insert' => array(
    'title' => 'Vložit obrázek',
    'select' => 'Vybrat',
	'delete' => 'Smazat', // new 1.0.5
    'cancel' => 'Zrušit',
    'library' => 'Knihovna',
    'preview' => 'Náhled',
    'images' => 'Obrázek',
    'upload' => 'Poslat obrázek',
    'upload_button' => 'Poslat',
    'error' => 'Chyba',
    'error_no_image' => 'Vyberte prosím obrázek',
    'error_uploading' => 'V prùbìhu uploadu došlo k chybì. Opakujte akci znovu',
    'error_wrong_type' => 'Chybný formát obrázku',
    'error_no_dir' => 'Knihovna fyzicky neexistuje',
	'error_cant_delete' => 'Obrázek nebylo možno smazat', // new 1.0.5
  ),
  'image_prop' => array(
    'title' => 'Vlastnosti obrázku',
    'ok' => '   OK   ',
    'cancel' => 'Storno',
    'source' => 'Zdroj',
    'alt' => 'Alternativní text',
    'align' => 'Zarovnání',
    'left' => 'Vlevo',
    'right' => 'Vpravo',
    'top' => 'Nahoru',
    'middle' => 'Doprostøed',
    'bottom' => 'Dolù',
    'absmiddle' => 'Absolutní støed',
    'texttop' => 'Text nahoru',
    'baseline' => 'Základní linka',
    'width' => 'Šíøka',
    'height' => 'Výška',
    'border' => 'Okraje',
    'hspace' => 'Hor. space',
    'vspace' => 'Vert. space',
    'error' => 'Chyba',
    'error_width_nan' => 'Šíøka není èíslo',
    'error_height_nan' => 'Výška není èíslo',
    'error_border_nan' => 'Okraj není èíslo',
    'error_hspace_nan' => 'Horizontální rozteè není èíslo',
    'error_vspace_nan' => 'Vertikální rozteè není èíslo',
  ),
  'hr' => array(
    'title' => 'Horizontal rule'
  ),
  'table_create' => array(
    'title' => 'Vytvoø tabulku'
  ),
  'table_prop' => array(
    'title' => 'Vlastnosti tabulky',
    'ok' => '   OK   ',
    'cancel' => 'Storno',
    'rows' => 'Øádkù',
    'columns' => 'Sloupcù',
    'width' => 'Šíøka',
    'height' => 'Výška',
    'border' => 'Okraje',
    'pixels' => 'pixelù',
    'css_class' => 'Tøída CSS', // <=== new 1.0.6
    'background' => 'Obrázek pozadí', // <=== new 1.0.6
    'cellpadding' => 'Odsazení v buòce',
    'cellspacing' => 'Vzdálenost bunìk',
    'bg_color' => 'Barva pozadí',
    'error' => 'Chyba',
    'error_rows_nan' => 'Øádky nejsou èíslo',
    'error_columns_nan' => 'Sloupce nejsou èíslo',
    'error_width_nan' => 'Šíøka není èíslo',
    'error_height_nan' => 'Výška není èíslo',
    'error_border_nan' => 'Okraje nejsou èíslo',
    'error_cellpadding_nan' => 'Odsazení v buòce není èíslo',
    'error_cellspacing_nan' => 'Vzdálenost buòek není èíslo',
  ),
  'table_cell_prop' => array(
    'title' => 'Vlastnosti buòky',
    'horizontal_align' => 'Horizontální zarovnání',
    'vertical_align' => 'Vertikální zarovnání',
    'width' => 'Šíøka',
    'height' => 'Výška',
    'css_class' => 'Tøída CSS',
    'no_wrap' => 'Nezalamovat',
    'bg_color' => 'Barva pozadí',
    'background' => 'Obrázek pozadí', // <=== new 1.0.6
    'ok' => '   OK   ',
    'cancel' => 'Zrušit',
    'left' => 'Vlevo',
    'center' => 'Na støed',
    'right' => 'Vpravo',
    'top' => 'Nahoru',
    'middle' => 'Doprostøed',
    'bottom' => 'Dolù',
    'baseline' => 'Základní linka',
    'error' => 'Chyba',
    'error_width_nan' => 'Šíøka není èíslo',
    'error_height_nan' => 'Výška není èíslo',
    
  ),
  'table_row_insert' => array(
    'title' => 'Vložit øádek'
  ),
  'table_column_insert' => array(
    'title' => 'Vložit sloupec'
  ),
  'table_row_delete' => array(
    'title' => 'Vymaž øádek'
  ),
  'table_column_delete' => array(
    'title' => 'Vymaž sloupec'
  ),
  'table_cell_merge_right' => array(
    'title' => 'Slouèit vpravo'
  ),
  'table_cell_merge_down' => array(
    'title' => 'Slouèit dolù'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'Rozdìlit buòku horizontálnì'
  ),
  'table_cell_split_vertical' => array(
    'title' => 'Rozdìlit buòku vertikálnì'
  ),
  'style' => array(
    'title' => 'Styl'
  ),
  'font' => array(
    'title' => 'Font'
  ),
  'fontsize' => array(
    'title' => 'Velikost'
  ),
  'paragraph' => array(
    'title' => 'Odstavec'
  ),
  'bold' => array(
    'title' => 'Tuèné'
  ),
  'italic' => array(
    'title' => 'Kurzíva'
  ),
  'underline' => array(
    'title' => 'Podtržení'
  ),
  'ordered_list' => array(
    'title' => 'Èíslování'
  ),
  'bulleted_list' => array(
    'title' => 'Odrážky'
  ),
  'indent' => array(
    'title' => 'Zvìtšit odsazení'
  ),
  'unindent' => array(
    'title' => 'Zmenšit odsazení'
  ),
  'left' => array(
    'title' => 'Vlevo'
  ),
  'center' => array(
    'title' => 'Na støed'
  ),
  'right' => array(
    'title' => 'Vpravo'
  ),
  'fore_color' => array(
    'title' => 'Barva popøedí'
  ),
  'bg_color' => array(
    'title' => 'Barva pozadí'
  ),
  'design_tab' => array(
    'title' => 'Pøepnout do WYSIWYG režimu'
  ),
  'html_tab' => array(
    'title' => 'Pøepnout do HTML režimu'
  ),
  'colorpicker' => array(
    'title' => 'Paleta barev',
    'ok' => '   OK   ',
    'cancel' => 'Storno',
  ),
  // <<<<<<<<< NEW >>>>>>>>>
  'cleanup' => array(
    'title' => 'Vyèištìní HTML (odstranit styly)',
    'confirm' => 'Provedením akce odstraníte všechny styly, fonty a zbyteèné tagy z aktuálního obsahu. Vaše formátování bude èásteènì èi úplnì odstranìno.',
    'ok' => '   OK   ',
    'cancel' => 'Storno',
  ),
  'toggle_borders' => array(
    'title' => 'Pøepnout okraje',
  ),
  'hyperlink' => array(
    'title' => 'Hyperlink',
    'url' => 'URL',
    'name' => 'Jméno',
    'target' => 'Cíl',
    'title_attr' => 'Popisek',
	'a_type' => 'Typ', // <=== new 1.0.6
	'type_link' => 'Odkaz', // <=== new 1.0.6
	'type_anchor' => 'Kotva', // <=== new 1.0.6
	'type_link2anchor' => 'Odkaz na kotvu', // <=== new 1.0.6
	'anchors' => 'Kotvy', // <=== new 1.0.6
    'ok' => '   OK   ',
    'cancel' => 'Storno',
  ),
  'hyperlink_targets' => array( // <=== new 1.0.5
  	'_self' => 'Stejný rámec (_self)',
	'_blank' => 'Nové okno (_blank)',
	'_top' => 'Vrchní rámec (_top)',
	'_parent' => 'Nadøazený rámec (_parent)'
  ),
  'table_row_prop' => array(
    'title' => 'Vlastnosti øádku',
    'horizontal_align' => 'Horizontální zarovnání',
    'vertical_align' => 'Vertikální zarovnání',
    'css_class' => 'Tøída CSS',
    'no_wrap' => 'Nezalamovat',
    'bg_color' => 'Barva pozadí',
    'ok' => '   OK   ',
    'cancel' => 'Storno',
    'left' => 'Vlevo',
    'center' => 'Na støed',
    'right' => 'Vpravo',
    'top' => 'Nahoru',
    'middle' => 'Doprostøed',
    'bottom' => 'Dolù',
    'baseline' => 'Základní linka',
  ),
  'symbols' => array(
    'title' => 'Speciální znaky',
    'ok' => '   OK   ',
    'cancel' => 'Storno',
  ),
  'symbols' => array(
    'title' => 'Speciální znaky',
    'ok' => '   OK   ',
    'cancel' => 'Storno',
  ),
  'templates' => array(
    'title' => 'Šablony',
  ),
  'page_prop' => array(
    'title' => 'Vlastnosti stránky',
    'title_tag' => 'Název',
    'charset' => 'Znaková sada',
    'background' => 'Obrázek pozadí',
    'bgcolor' => 'Barva pozadí',
    'text' => 'Barva textu',
    'link' => 'Barva odkazu',
    'vlink' => 'Barva navštíveného odkazu',
    'alink' => 'Barva aktivního odkazu',
    'leftmargin' => 'Levý okraj',
    'topmargin' => 'Horní okraj',
    'css_class' => 'Tøída CSS',
    'ok' => '   OK   ',
    'cancel' => 'Storno',
  ),
  'preview' => array(
    'title' => 'Náhled',
  ),
  'image_popup' => array(
    'title' => 'Odkaz na obrázek v novém oknì',
  ),
  'zoom' => array(
    'title' => 'Pøiblížení',
  ),
);
?>

