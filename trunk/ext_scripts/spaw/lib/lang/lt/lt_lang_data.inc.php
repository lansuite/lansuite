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
    'title' => 'I�kirpti'
  ),
  'copy' => array(
    'title' => 'Kopijuoti'
  ),
  'paste' => array(
    'title' => '�terpti'
  ),
  'undo' => array(
    'title' => 'At�aukti'
  ),
  'redo' => array(
    'title' => 'Pakartoti'
  ),
  'image_insert' => array(
    'title' => '�terpti iliustracij�',
    'select' => 'Pasirinkti',
	'delete' => 'I�trinti', // new 1.0.5
    'cancel' => 'Nutraukti',
    'library' => 'Biblioteka',
    'preview' => 'Per�i�ra',
    'images' => 'Iliustracijos',
    'upload' => '�kelti iliustracij�',
    'upload_button' => '�kelti',
    'error' => 'Klaida',
    'error_no_image' => 'Pa�ym�kite iliustracij�',
    'error_uploading' => '�keliant iliustracij� �vyko klaida. Pabandykite dar kart� v�liau.',
    'error_wrong_type' => 'Neteisingas iliustracijos failo formatas',
    'error_no_dir' => 'Biblioteka neegzistuoja',
	'error_cant_delete' => 'I�trinti nepavyko', // new 1.0.5
  ),
  'image_prop' => array(
    'title' => 'Iliustracijos parametrai',
    'ok' => '   GERAI   ',
    'cancel' => 'Nutraukti',
    'source' => '�altinis',
    'alt' => 'Alternatyvus tekstas',
    'align' => 'Lygiavimas',
    'left' => 'left (kair�)',
    'right' => 'right (de�in�)',
    'top' => 'top (vir�us)',
    'middle' => 'middle (vidurys)',
    'bottom' => 'bottom (apa�ia)',
    'absmiddle' => 'absmiddle (bendras vidurys)',
    'texttop' => 'texttop (teksto vir�us)',
    'baseline' => 'baseline (teksto apa�ia)',
    'width' => 'Plotis',
    'height' => 'Auk�tis',
    'border' => 'R�melio plotis',
    'hspace' => 'Hor. laukelis',
    'vspace' => 'Vert. laukelis',
    'error' => 'Klaida',
    'error_width_nan' => 'Nurodytas plotis n�ra skai�ius',
    'error_height_nan' => 'Nurodytas auk�tis n�ra skai�ius',
    'error_border_nan' => 'Nurodytas r�melio plotis n�ra skai�ius',
    'error_hspace_nan' => 'Nurodytas horizontalaus laukelio plotis n�ra skai�ius',
    'error_vspace_nan' => 'Nurodytas vertikalaus laukelio plotis n�ra skai�ius',
  ),
  'hr' => array(
    'title' => 'Horizontalus skirtukas'
  ),
  'table_create' => array(
    'title' => 'Sukurti lentel�'
  ),
  'table_prop' => array(
    'title' => 'Lentel�s parametrai',
    'ok' => '   GERAI   ',
    'cancel' => 'Nutraukti',
    'rows' => 'Eilu�i�',
    'columns' => 'Stulpeli�',
    'css_class' => 'CSS klas�', // <=== new 1.0.6
    'width' => 'Plotis',
    'height' => 'Auk�tis',
    'border' => 'R�melio plotis',
    'pixels' => 'ta�k.',
    'cellpadding' => 'Laukelio atitraukimas (padding)',
    'cellspacing' => 'Tarpas tarp laukeli�',
    'bg_color' => 'Fono spalva',
    'background' => 'Fono iliustracija', // <=== new 1.0.6
    'error' => 'Klaida',
    'error_rows_nan' => 'Nurodytas eilu�i� kiekis n�ra skai�ius',
    'error_columns_nan' => 'Nurodytas stulpeli� kiekis n�ra skai�ius',
    'error_width_nan' => 'Nurodytas plotis n�ra skai�ius',
    'error_height_nan' => 'Nurodytas auk�tis n�ra skai�ius',
    'error_border_nan' => 'Nurodytas r�melio plotis n�ra skai�ius',
    'error_cellpadding_nan' => 'Nurodytas laukelio atitraukimas n�ra skai�ius',
    'error_cellspacing_nan' => 'Nurodytas tarpas tarp laukeli� n�ra skai�ius',
  ),
  'table_cell_prop' => array(
    'title' => 'Laukelio parametrai',
    'horizontal_align' => 'Vertikalus lygiavimas',
    'vertical_align' => 'Horizontalus lygiavimas',
    'width' => 'Plotis',
    'height' => 'Auk�tis',
    'css_class' => 'CSS klas�',
    'no_wrap' => 'Neperkeliamas',
    'bg_color' => 'Fono spalva',
    'background' => 'Fono iliustracija', // <=== new 1.0.6
    'ok' => '   GERAI   ',
    'cancel' => 'Nutraukti',
    'left' => 'Kair�',
    'center' => 'Centras',
    'right' => 'De�in�',
    'top' => 'Vir�us',
    'middle' => 'Vidurys',
    'bottom' => 'Apa�ia',
    'baseline' => 'Teksto apa�ia',
    'error' => 'Klaida',
    'error_width_nan' => 'Nurodytas plotis n�ra skai�ius',
    'error_height_nan' => 'Nurodytas auk�tis n�ra skai�ius',

  ),
  'table_row_insert' => array(
    'title' => '�terpti eilut�'
  ),
  'table_column_insert' => array(
    'title' => '�terpti stulpel�'
  ),
  'table_row_delete' => array(
    'title' => 'Pa�alinti eilut�'
  ),
  'table_column_delete' => array(
    'title' => 'Pa�alinti stulpel�'
  ),
  'table_cell_merge_right' => array(
    'title' => 'Sujungti laukelius � de�in�'
  ),
  'table_cell_merge_down' => array(
    'title' => 'Sujungti laukelius apa�ion'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'Padalinti laukel� horizontaliai'
  ),
  'table_cell_split_vertical' => array(
    'title' => 'Padalinti laukel� vertikaliai'
  ),
  'style' => array(
    'title' => 'Stilius'
  ),
  'font' => array(
    'title' => '�riftas'
  ),
  'fontsize' => array(
    'title' => 'Dydis'
  ),
  'paragraph' => array(
    'title' => 'Paragrafas'
  ),
  'bold' => array(
    'title' => 'Stambus �riftas (Bold)'
  ),
  'italic' => array(
    'title' => 'Kursyvas (Italic)'
  ),
  'underline' => array(
    'title' => 'Pabrauktas (Underline)'
  ),
  'ordered_list' => array(
    'title' => 'Numeruotas s�ra�as'
  ),
  'bulleted_list' => array(
    'title' => 'S�ra�as'
  ),
  'indent' => array(
    'title' => 'Stumti � de�in�'
  ),
  'unindent' => array(
    'title' => 'Stumti � kair�'
  ),
  'left' => array(
    'title' => 'Kair�'
  ),
  'center' => array(
    'title' => 'Centras'
  ),
  'right' => array(
    'title' => 'De�in�'
  ),
  'fore_color' => array(
    'title' => 'Teksto spalva'
  ),
  'bg_color' => array(
    'title' => 'Fono spalva'
  ),
  'design_tab' => array(
    'title' => 'Perjungti � grafinio redagavimo re�im�'
  ),
  'html_tab' => array(
    'title' => 'Perjungti � HTML kodo redagavimo re�im�'
  ),
  'colorpicker' => array(
    'title' => 'Spalvos pasirinkimas',
    'ok' => '   GERAI   ',
    'cancel' => 'Nutraukti'
  ),
  'cleanup' => array(
    'title' => 'HTML valymas (panaikinti stilius)',
    'confirm' => 'Atlikus �� veiksm� bus panaikinti visi tekste naudojami stiliai, �riftai ir nenaudojamos �ymos. Dalis ar visas formatavimas gali b�ti prarastas.',
    'ok' => '   GERAI   ',
    'cancel' => 'Nutraukti',
  ),
  'toggle_borders' => array(
    'title' => '�jungti/i�jungti r�melius',
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
	'type_link2anchor' => 'Nuoroda � inkar�', // <=== new 1.0.6
	'anchors' => 'Inkarai', // <=== new 1.0.6
    'ok' => '   GERAI   ',
    'cancel' => 'Nutraukti',
  ),
  'hyperlink_targets' => array( // <=== new 1.0.5
  	'_self' => 'tame pa�iame lange (_self)',
	'_blank' => 'naujame tu��iame lange (_blank)',
	'_top' => 'pagrindiniame lange (_top)',
	'_parent' => 't�viniame lange (_parent)'
  ),
  'table_row_prop' => array(
    'title' => 'Eilut�s parametrai',
    'horizontal_align' => 'Horizontalus lygiavimas',
    'vertical_align' => 'Vertikalus lygiavimas',
    'css_class' => 'CSS klas�',
    'no_wrap' => 'Neperkeliamas',
    'bg_color' => 'Fono spalva',
    'ok' => '   GERAI   ',
    'cancel' => 'Nutraukti',
    'left' => 'Kair�',
    'center' => 'Centras',
    'right' => 'De�in�',
    'top' => 'Vir�us',
    'middle' => 'Vidurys',
    'bottom' => 'Apa�ia',
    'baseline' => 'Teksto apa�ia',
  ),
  'symbols' => array(
    'title' => 'Special�s simboliai',
    'ok' => '   GERAI   ',
    'cancel' => 'At�aukti',
  ),
  'templates' => array(
    'title' => '�ablonai',
  ),
  'page_prop' => array(
    'title' => 'Puslapio parametrai',
    'title_tag' => 'Pavadinimas',
    'charset' => 'Simboli� rinkinys (Charset)',
    'background' => 'Fono paveiksliukas',
    'bgcolor' => 'Fono spalva',
    'text' => 'Teksto spalva',
    'link' => 'Nuorodos spalva',
    'vlink' => 'Aplankytos nuorodos spalva',
    'alink' => 'Aktyvios nuorodos spalva',
    'leftmargin' => 'Para�t� kair�je',
    'topmargin' => 'Para�t� vir�uje',
    'css_class' => 'CSS klas�',
    'ok' => '   GERAI   ',
    'cancel' => 'Nutraukti',
  ),
  'preview' => array(
    'title' => 'Per�i�ra',
  ),
  'image_popup' => array(
    'title' => 'I��okantis paveiksliukas',
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
