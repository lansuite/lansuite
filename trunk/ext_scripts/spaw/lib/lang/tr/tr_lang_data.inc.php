<?php
// =========================================================
// SPAW PHP WYSIWYG editor control
// =========================================================
// Turkish language file
// =========================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Turkish translation: Zeki Erkmen, zerkmen@erdoweb.com
// Copyright: Solmetra (c)2003 All rights reserved.
// ---------------------------------------------------------
//                                www.solmetra.com
// =========================================================
// 1.0.7, 2004-12-09
// =========================================================

// charset to be used in dialogs
$spaw_lang_charset = 'iso-8859-9';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => 'Kes'
  ),
  'copy' => array(
    'title' => 'Kopyala'
  ),
  'paste' => array(
    'title' => 'Ekle'
  ),
  'undo' => array(
    'title' => 'Geri al'
  ),
  'redo' => array(
    'title' => 'Tekrarla'
  ),
  'hyperlink' => array(
    'title' => 'Link ekle'
  ),
  'image_insert' => array(
    'title' => 'Resim ekle',
    'select' => 'Resmi al',
	'delete' => 'Resmi sil', // new 1.0.5
    'cancel' => '�ptal',
    'library' => 'K�t�phane',
    'preview' => '�n izle',
    'images' => 'Resim',
    'upload' => 'Resim y�kle',
    'upload_button' => 'Y�kle',
    'error' => 'Hata',
    'error_no_image' => 'L�tfen bir resim se�iniz',
    'error_uploading' => 'Resim y�kleme i�leminde bir hata olu�tu. L�tfen biraz sonra tekrar deneyiniz.',
    'error_wrong_type' => 'Resim t�r� yanl��',
    'error_no_dir' => 'Dizinde k�t�phane bulunmuyor',
	'error_cant_delete' => 'Silme i�leminde hata olu�tu', // new 1.0.5
  ),
  'image_prop' => array(
    'title' => 'Resim ayarlar�',
    'ok' => '   OK   ',
    'cancel' => '�ptal',
    'source' => 'Kaynak',
    'alt' => 'Alternatif Metin',
    'align' => 'Konum',
    'left' => 'Sol',
    'right' => 'Sa�',
    'top' => 'Yukarda',
    'middle' => 'Ortada',
    'bottom' => 'Alt k�s�mda',
    'absmiddle' => 'Merkezde',
    'texttop' => 'Metin �st�',
    'baseline' => '�izgi �zeri',
    'width' => 'Geni�lik',
    'height' => 'Y�kseklik',
    'border' => '�erceve',
    'hspace' => 'Yatay bo�luk',
    'vspace' => 'Dikey bo�luk',
    'error' => 'Hata',
    'error_width_nan' => 'Geni�lik say� de�il',
    'error_height_nan' => 'Y�kseklik say� de�il',
    'error_border_nan' => '�erceve say� de�il',
    'error_hspace_nan' => 'Yatay bo�luk say� de�il',
    'error_vspace_nan' => 'Dikey bo�luk say� de�il',
  ),
  'hr' => array(
    'title' => 'Yatay �izgi'
  ),
  'table_create' => array(
    'title' => 'Tabela olu�tur'
  ),
  'table_prop' => array(
    'title' => 'Tabela �zellikleri',
    'ok' => '   OK   ',
    'cancel' => '�ptal et',
    'rows' => 'Sat�rlar',
    'columns' => 'Haneler',
	'css_class' => 'CSS class', // <=== new 1.0.6
    'width' => 'Geni�lik',
    'height' => 'Y�kseklik',
    'border' => '�erceve',
    'pixels' => 'Pixel',
    'cellpadding' => 'H�creyi dolumu',
    'cellspacing' => 'H�cre mesafesi',
    'bg_color' => 'Arka ekran rengi',
	'background' => 'Arka ekran resmi', // <=== new 1.0.6
    'error' => 'Hata',
    'error_rows_nan' => 'Sat�r rakam de�il',
    'error_columns_nan' => 'Hane rakam de�il',
    'error_width_nan' => 'Geni�lik rakam de�il',
    'error_height_nan' => 'Y�kseklik rakam de�il',
    'error_border_nan' => '�erceve rakam de�il',
    'error_cellpadding_nan' => 'H�cre dolumu rakam de�il',
    'error_cellspacing_nan' => 'H�cre mesafesi rakam de�il',
  ),
  'table_cell_prop' => array(
    'title' => 'H�cre �zelli�i',
    'horizontal_align' => 'Yatay konumu',
    'vertical_align' => 'Dikey konumu',
    'width' => 'Geni�lik',
    'height' => 'Y�kseklik',
    'css_class' => 'CSS s�n�f�',
    'no_wrap' => 'Paketsiz',
    'bg_color' => 'Arka ekran rengi',
	'background' => 'Arka ekran resmi', // <=== new 1.0.6
    'ok' => '   OK   ',
    'cancel' => '�ptal et',
    'left' => 'Sol',
    'center' => 'Merkezi',
    'right' => 'Sa�',
    'top' => '�st k�s�m',
    'middle' => 'Orta',
    'bottom' => 'Alt k�s�m',
    'baseline' => '�izgi �st�',
    'error' => 'Hata',
    'error_width_nan' => 'Geni�lik rakam de�il',
    'error_height_nan' => 'Y�kseklik rakam de�il',
    
  ),
  'table_row_insert' => array(
    'title' => 'Sat�r ekle'
  ),
  'table_column_insert' => array(
    'title' => 'Hane ekle'
  ),
  'table_row_delete' => array(
    'title' => 'Sat�r sil'
  ),
  'table_column_delete' => array(
    'title' => 'Hane sil'
  ),
  'table_cell_merge_right' => array(
    'title' => 'H�creyi sa� taraf ile birle�tir.'
  ),
  'table_cell_merge_down' => array(
    'title' => 'H�cereyi alt taraf ile birle�tir.'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'H�creyi yatay olarak b�l'
  ),
  'table_cell_split_vertical' => array(
    'title' => 'H�creyi dikey olarak b�l'
  ),
  'style' => array(
    'title' => 'D�zenleme'
  ),
  'font' => array(
    'title' => 'Yaz�'
  ),
  'fontsize' => array(
    'title' => 'B�y�kl���'
  ),
  'paragraph' => array(
    'title' => 'Para�raf'
  ),
  'bold' => array(
    'title' => 'Kal�n'
  ),
  'italic' => array(
    'title' => 'Yatay ince'
  ),
  'underline' => array(
    'title' => 'Alt �izgili'
  ),
  'ordered_list' => array(
    'title' => 'Numarasal'
  ),
  'bulleted_list' => array(
    'title' => 'Listesel'
  ),
  'indent' => array(
    'title' => 'D��a �ek'
  ),
  'unindent' => array(
    'title' => '��e �ek'
  ),
  'left' => array(
    'title' => 'Sol'
  ),
  'center' => array(
    'title' => 'Merkez'
  ),
  'right' => array(
    'title' => 'Sa�'
  ),
  'fore_color' => array(
    'title' => 'Yaz� rengi'
  ),
  'bg_color' => array(
    'title' => 'Arka ekran rengi'
  ),
  'design_tab' => array(
    'title' => 'Design Mod�s�ne ge�'
  ),
  'html_tab' => array(
    'title' => 'HTML Mod�s�ne ge�'
  ),
  'colorpicker' => array(
    'title' => 'Renk se�imi',
    'ok' => '   OK   ',
    'cancel' => '�ptal et',
  ),
  'cleanup' => array(
    'title' => 'HTML temizleye�i',
    'confirm' => 'Bu se�enek HTML formatlar�n� (Style) ��eri�inizden siler. Bu komutu se�mekle ya t�m ya da baz� Style blocklar� metin i�erisinden silinir ',
    'ok' => '   OK   ',
    'cancel' => '�ptal',
  ),
  'toggle_borders' => array(
    'title' => 'Toggle borders',
  ),
  'hyperlink' => array(
    'title' => 'Link ekle',
    'url' => 'URL',
    'name' => 'Ad�',
    'target' => 'Hedef',
    'title_attr' => 'Ba�l�k',
	'a_type' => 'Tip', // <=== new 1.0.6
	'type_link' => 'Link', // <=== new 1.0.6
	'type_anchor' => '�apa', // <=== new 1.0.6
	'type_link2anchor' => '�apaya link', // <=== new 1.0.6
	'anchors' => 'Anchors', // <=== new 1.0.6
    'ok' => '   OK   ',
    'cancel' => '�ptal',
  ),
  'hyperlink_targets' => array( // <=== new 1.0.5
  	'_self' => 'kendi penceresi (_self)',
	'_blank' => 'bo� yeni pencerede (_blank)',
	'_top' => 'bir �st pencerede (_top)',
	'_parent' => 'ayn� pencerede (_parent)'
  ),
  'table_row_prop' => array(
    'title' => 'Sat�r �zellikleri',
    'horizontal_align' => 'Yatay konum',
    'vertical_align' => 'Dikey konum',
    'css_class' => 'CSS Klas�',
    'no_wrap' => 'Paketsiz',
    'bg_color' => 'Arka ekran rengi',
    'ok' => '   OK   ',
    'cancel' => '�ptal',
    'left' => 'Sol',
    'center' => 'Merkez',
    'right' => 'Sa�',
    'top' => '�st',
    'middle' => 'Orta',
    'bottom' => 'Alt',
    'baseline' => '�izgi �st�',
  ),
  'symbols' => array(
    'title' => '�zel karekterler',
    'ok' => '   OK   ',
    'cancel' => '�ptal',
  ),
  'templates' => array(
    'title' => 'Kal�plar',
  ),
  'page_prop' => array(
    'title' => 'Sayfa �zelli�i',
    'title_tag' => 'Ba�l�k',
    'charset' => 'Metin Karekteri',
    'background' => 'Arka plan resmi',
    'bgcolor' => 'Arka plan rengi',
    'text' => 'Yaz� rengi',
    'link' => 'Link rengi',
    'vlink' => 'U�ran�lm�� link rengi',
    'alink' => 'Actif link rengi',
    'leftmargin' => 'Sol kenar',
    'topmargin' => '�st kenar',
    'css_class' => 'CSS Klas�',
    'ok' => '   OK   ',
    'cancel' => '�ptal',
  ),
  'preview' => array(
    'title' => '�n g�sterim',
  ),
  'image_popup' => array(
    'title' => 'Resim popup',
  ),
  'zoom' => array(
    'title' => 'B�y�lte�',
  ),
  'subscript' => array( // <=== new 1.0.7
    'title' => 'Subscript',
  ),
  'superscript' => array( // <=== new 1.0.7
    'title' => 'Superscript',
  ),
);
?>
