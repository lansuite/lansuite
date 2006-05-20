<?php
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Lithuanian language file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Translated: Martynas Majeris martynas@solmetra.lt
//						 Saulius Okunevicius saulius@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-04-04
// ================================================

// charset to be used in dialogs
$spaw_lang_charset = 'windows-1257';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => 'Iðkirpti'
  ),
  'copy' => array(
    'title' => 'Kopijuoti'
  ),
  'paste' => array(
    'title' => 'Áterpti'
  ),
  'undo' => array(
    'title' => 'Atðaukti'
  ),
  'redo' => array(
    'title' => 'Pakartoti'
  ),
  'image_insert' => array(
    'title' => 'Áterpti iliustracijà',
    'select' => 'Pasirinkti',
	'delete' => 'Iðtrinti', // new 1.0.5
    'cancel' => 'Nutraukti',
    'library' => 'Biblioteka',
    'preview' => 'Perþiûra',
    'images' => 'Iliustracijos',
    'upload' => 'Ákelti iliustracijà',
    'upload_button' => 'Ákelti',
    'error' => 'Klaida',
    'error_no_image' => 'Paþymëkite iliustracijà',
    'error_uploading' => 'Ákeliant iliustracijà ávyko klaida. Pabandykite dar kartà vëliau.',
    'error_wrong_type' => 'Neteisingas iliustracijos failo formatas',
    'error_no_dir' => 'Biblioteka neegzistuoja',
	'error_cant_delete' => 'Iðtrinti nepavyko', // new 1.0.5
  ),
  'image_prop' => array(
    'title' => 'Iliustracijos parametrai',
    'ok' => '   GERAI   ',
    'cancel' => 'Nutraukti',
    'source' => 'Ðaltinis',
    'alt' => 'Alternatyvus tekstas',
    'align' => 'Lygiavimas',
    'left' => 'left (kairë)',
    'right' => 'right (deðinë)',
    'top' => 'top (virðus)',
    'middle' => 'middle (vidurys)',
    'bottom' => 'bottom (apaèia)',
    'absmiddle' => 'absmiddle (bendras vidurys)',
    'texttop' => 'texttop (teksto virðus)',
    'baseline' => 'baseline (teksto apaèia)',
    'width' => 'Plotis',
    'height' => 'Aukðtis',
    'border' => 'Rëmelio plotis',
    'hspace' => 'Hor. laukelis',
    'vspace' => 'Vert. laukelis',
    'error' => 'Klaida',
    'error_width_nan' => 'Nurodytas plotis nëra skaièius',
    'error_height_nan' => 'Nurodytas aukðtis nëra skaièius',
    'error_border_nan' => 'Nurodytas rëmelio plotis nëra skaièius',
    'error_hspace_nan' => 'Nurodytas horizontalaus laukelio plotis nëra skaièius',
    'error_vspace_nan' => 'Nurodytas vertikalaus laukelio plotis nëra skaièius',
  ),
  'hr' => array(
    'title' => 'Horizontalus skirtukas'
  ),
  'table_create' => array(
    'title' => 'Sukurti lentelæ'
  ),
  'table_prop' => array(
    'title' => 'Lentelës parametrai',
    'ok' => '   GERAI   ',
    'cancel' => 'Nutraukti',
    'rows' => 'Eiluèiø',
    'columns' => 'Stulpeliø',
    'css_class' => 'CSS klasë', // <=== new 1.0.6
    'width' => 'Plotis',
    'height' => 'Aukðtis',
    'border' => 'Rëmelio plotis',
    'pixels' => 'taðk.',
    'cellpadding' => 'Laukelio atitraukimas (padding)',
    'cellspacing' => 'Tarpas tarp laukeliø',
    'bg_color' => 'Fono spalva',
    'background' => 'Fono iliustracija', // <=== new 1.0.6
    'error' => 'Klaida',
    'error_rows_nan' => 'Nurodytas eiluèiø kiekis nëra skaièius',
    'error_columns_nan' => 'Nurodytas stulpeliø kiekis nëra skaièius',
    'error_width_nan' => 'Nurodytas plotis nëra skaièius',
    'error_height_nan' => 'Nurodytas aukðtis nëra skaièius',
    'error_border_nan' => 'Nurodytas rëmelio plotis nëra skaièius',
    'error_cellpadding_nan' => 'Nurodytas laukelio atitraukimas nëra skaièius',
    'error_cellspacing_nan' => 'Nurodytas tarpas tarp laukeliø nëra skaièius',
  ),
  'table_cell_prop' => array(
    'title' => 'Laukelio parametrai',
    'horizontal_align' => 'Vertikalus lygiavimas',
    'vertical_align' => 'Horizontalus lygiavimas',
    'width' => 'Plotis',
    'height' => 'Aukðtis',
    'css_class' => 'CSS klasë',
    'no_wrap' => 'Neperkeliamas',
    'bg_color' => 'Fono spalva',
    'background' => 'Fono iliustracija', // <=== new 1.0.6
    'ok' => '   GERAI   ',
    'cancel' => 'Nutraukti',
    'left' => 'Kairë',
    'center' => 'Centras',
    'right' => 'Deðinë',
    'top' => 'Virðus',
    'middle' => 'Vidurys',
    'bottom' => 'Apaèia',
    'baseline' => 'Teksto apaèia',
    'error' => 'Klaida',
    'error_width_nan' => 'Nurodytas plotis nëra skaièius',
    'error_height_nan' => 'Nurodytas aukðtis nëra skaièius',

  ),
  'table_row_insert' => array(
    'title' => 'Áterpti eilutæ'
  ),
  'table_column_insert' => array(
    'title' => 'Áterpti stulpelá'
  ),
  'table_row_delete' => array(
    'title' => 'Paðalinti eilutæ'
  ),
  'table_column_delete' => array(
    'title' => 'Paðalinti stulpelá'
  ),
  'table_cell_merge_right' => array(
    'title' => 'Sujungti laukelius á deðinæ'
  ),
  'table_cell_merge_down' => array(
    'title' => 'Sujungti laukelius apaèion'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'Padalinti laukelá horizontaliai'
  ),
  'table_cell_split_vertical' => array(
    'title' => 'Padalinti laukelá vertikaliai'
  ),
  'style' => array(
    'title' => 'Stilius'
  ),
  'font' => array(
    'title' => 'Ðriftas'
  ),
  'fontsize' => array(
    'title' => 'Dydis'
  ),
  'paragraph' => array(
    'title' => 'Paragrafas'
  ),
  'bold' => array(
    'title' => 'Stambus ðriftas (Bold)'
  ),
  'italic' => array(
    'title' => 'Kursyvas (Italic)'
  ),
  'underline' => array(
    'title' => 'Pabrauktas (Underline)'
  ),
  'ordered_list' => array(
    'title' => 'Numeruotas sàraðas'
  ),
  'bulleted_list' => array(
    'title' => 'Sàraðas'
  ),
  'indent' => array(
    'title' => 'Stumti á deðinæ'
  ),
  'unindent' => array(
    'title' => 'Stumti á kairæ'
  ),
  'left' => array(
    'title' => 'Kairë'
  ),
  'center' => array(
    'title' => 'Centras'
  ),
  'right' => array(
    'title' => 'Deðinë'
  ),
  'fore_color' => array(
    'title' => 'Teksto spalva'
  ),
  'bg_color' => array(
    'title' => 'Fono spalva'
  ),
  'design_tab' => array(
    'title' => 'Perjungti á grafinio redagavimo reþimà'
  ),
  'html_tab' => array(
    'title' => 'Perjungti á HTML kodo redagavimo reþimà'
  ),
  'colorpicker' => array(
    'title' => 'Spalvos pasirinkimas',
    'ok' => '   GERAI   ',
    'cancel' => 'Nutraukti'
  ),
  'cleanup' => array(
    'title' => 'HTML valymas (panaikinti stilius)',
    'confirm' => 'Atlikus ðá veiksmà bus panaikinti visi tekste naudojami stiliai, ðriftai ir nenaudojamos þymos. Dalis ar visas formatavimas gali bûti prarastas.',
    'ok' => '   GERAI   ',
    'cancel' => 'Nutraukti',
  ),
  'toggle_borders' => array(
    'title' => 'Ájungti/iðjungti rëmelius',
  ),
  'hyperlink' => array(
    'title' => 'Nuoroda',
    'url' => 'Adresas',
    'name' => 'Vardas',
    'target' => 'Kur atidaryti',
    'title_attr' => 'Pavadinimas',
	'a_type' => 'Tipas', // <=== new 1.0.6
	'type_link' => 'Nuoroda', // <=== new 1.0.6
	'type_anchor' => 'Inkaras', // <=== new 1.0.6
	'type_link2anchor' => 'Nuoroda á inkarà', // <=== new 1.0.6
	'anchors' => 'Inkarai', // <=== new 1.0.6
    'ok' => '   GERAI   ',
    'cancel' => 'Nutraukti',
  ),
  'hyperlink_targets' => array( // <=== new 1.0.5
  	'_self' => 'tame paèiame lange (_self)',
	'_blank' => 'naujame tuðèiame lange (_blank)',
	'_top' => 'pagrindiniame lange (_top)',
	'_parent' => 'tëviniame lange (_parent)'
  ),
  'table_row_prop' => array(
    'title' => 'Eilutës parametrai',
    'horizontal_align' => 'Horizontalus lygiavimas',
    'vertical_align' => 'Vertikalus lygiavimas',
    'css_class' => 'CSS klasë',
    'no_wrap' => 'Neperkeliamas',
    'bg_color' => 'Fono spalva',
    'ok' => '   GERAI   ',
    'cancel' => 'Nutraukti',
    'left' => 'Kairë',
    'center' => 'Centras',
    'right' => 'Deðinë',
    'top' => 'Virðus',
    'middle' => 'Vidurys',
    'bottom' => 'Apaèia',
    'baseline' => 'Teksto apaèia',
  ),
  'symbols' => array(
    'title' => 'Specialûs simboliai',
    'ok' => '   GERAI   ',
    'cancel' => 'Atðaukti',
  ),
  'templates' => array(
    'title' => 'Ðablonai',
  ),
  'page_prop' => array(
    'title' => 'Puslapio parametrai',
    'title_tag' => 'Pavadinimas',
    'charset' => 'Simboliø rinkinys (Charset)',
    'background' => 'Fono paveiksliukas',
    'bgcolor' => 'Fono spalva',
    'text' => 'Teksto spalva',
    'link' => 'Nuorodos spalva',
    'vlink' => 'Aplankytos nuorodos spalva',
    'alink' => 'Aktyvios nuorodos spalva',
    'leftmargin' => 'Paraðtë kairëje',
    'topmargin' => 'Paraðtë virðuje',
    'css_class' => 'CSS klasë',
    'ok' => '   GERAI   ',
    'cancel' => 'Nutraukti',
  ),
  'preview' => array(
    'title' => 'Perþiûra',
  ),
  'image_popup' => array(
    'title' => 'Iððokantis paveiksliukas',
  ),
  'zoom' => array(
    'title' => 'Mastelis',
  ),
  'subscript' => array( // <=== new 1.0.7
    'title' => 'Nuleistas tekstas',
  ),
  'superscript' => array( // <=== new 1.0.7
    'title' => 'Pakeltas tekstas',
  ),
);
?>
