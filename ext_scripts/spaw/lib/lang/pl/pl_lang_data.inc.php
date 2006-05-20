<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Polish language file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// polish bersion by Slawomir Jasinski slav123@gmail.com
// v.1.0.7, 2004-10-13
// ================================================

// charset to be used in dialogs
$spaw_lang_charset = 'iso-8859-2';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => 'Wytnij'
  ),
  'copy' => array(
    'title' => 'Kopiuj'
  ),
  'paste' => array(
    'title' => 'Wklej'
  ),
  'undo' => array(
    'title' => 'Cofnij'
  ),
  'redo' => array(
    'title' => 'Ponów'
  ),
  'image_insert' => array(
    'title' => 'Wstaw obrazek',
    'select' => 'Wybierz',
	'delete' => 'Usuñ', // new 1.0.5
    'cancel' => 'Anuluj',
    'library' => 'Biblioteka',
    'preview' => 'Podgl±d',
    'images' => 'Obrazki',
    'upload' => 'Wy¶lij obrazek',
    'upload_button' => 'Wy¶lij',
    'error' => 'B³±d',
    'error_no_image' => 'Proszê wybraæ obrazek',
    'error_uploading' => 'Przy wysy³aniu obrazka wyst±pi³ b³±d. Proszê spróbowaæ pó¼niej.',
    'error_wrong_type' => 'Niew³a¶ciwy typ pliku obrazka',
    'error_no_dir' => 'Brak biblioteki obrazków',
	'error_cant_delete' => 'B³±d usuwania', // new 1.0.5
  ),
  'image_prop' => array(
    'title' => 'W³a¶ciwo¶ci obrazka',
    'ok' => '   OK   ',
    'cancel' => 'Anuluj',
    'source' => '¦cie¿ka',
    'alt' => 'Tekst alternatywny',
    'align' => 'Wyrównanie',
    'left' => 'left',
    'right' => 'right',
    'top' => 'top',
    'middle' => 'middle',
    'bottom' => 'bottom',
    'absmiddle' => 'absmiddle',
    'texttop' => 'texttop',
    'baseline' => 'baseline',
    'width' => 'Szeroko¶æ',
    'height' => 'Wysoko¶æ',
    'border' => 'Obramowanie',
    'hspace' => 'Odstêp poziomy',
    'vspace' => 'Odstêp pionowy',
    'error' => 'B³±d',
    'error_width_nan' => 'Szeroko¶æ nie jest liczb±',
    'error_height_nan' => 'Wysoko¶æ nie jest liczb±',
    'error_border_nan' => 'Ramka nie jest liczb±',
    'error_hspace_nan' => 'Odstêp poziomy nie jest liczb±',
    'error_vspace_nan' => 'Odstêp pionowy nie jest liczb±',
  ),
  'hr' => array(
    'title' => 'Linia pozioma'
  ),
  'table_create' => array(
    'title' => 'Wstaw tabelê'
  ),
  'table_prop' => array(
    'title' => 'W³a¶ciwo¶ci tabeli',
    'ok' => '   OK   ',
    'cancel' => 'Anuluj',
    'rows' => 'Liczba wierszy',
    'columns' => 'Liczba kolumn',
    'css_class' => 'Styl CSS', // <=== new 1.0.6
    'width' => 'Szeroko¶æ',
    'height' => 'Wysoko¶æ',
    'border' => 'Obramowanie',
    'pixels' => 'pikseli',
    'cellpadding' => 'Margines komórki',
    'cellspacing' => 'Obramowanie komórki',
    'bg_color' => 'Kolor t³a',
    'background' => 'Obrazek t³a', // <=== new 1.0.6
    'error' => 'B³±d',
    'error_rows_nan' => 'Liczba wierszy nie jest liczb±',
    'error_columns_nan' => 'Liczba kolumn nie jest liczb±',
    'error_width_nan' => 'Szeroko¶æ nie jest liczb±',
    'error_height_nan' => 'Wysoko¶æ nie jest liczb±',
    'error_border_nan' => 'Obramowanie nie jest liczb±',
    'error_cellpadding_nan' => 'Margines komórki nie jest liczb±',
    'error_cellspacing_nan' => 'Obramowanie komórki nie jest liczb±',
  ),
  'table_cell_prop' => array(
    'title' => 'W³a¶ciwo¶ci komórki',
    'horizontal_align' => 'Wyrównanie w poziomie',
    'vertical_align' => 'Wyrównanie w pionie',
    'width' => 'Szeroko¶æ',
    'height' => 'Wysoko¶æ',
    'css_class' => 'styl CSS',
    'no_wrap' => 'Blokuj dzielenie akapitu',
    'bg_color' => 'Kolor t³a',
    'background' => 'Obrazek t³a', // <=== new 1.0.6
    'ok' => '   OK   ',
    'cancel' => 'Anuluj',
    'left' => 'Do lewej',
    'center' => 'Do ¶rodka',
    'right' => 'Do prawej',
    'top' => 'Do góry',
    'middle' => 'Do ¶rodka',
    'bottom' => 'Do do³u',
    'baseline' => 'Do linii bazowej',
    'error' => 'B³±d',
    'error_width_nan' => 'Szeroko¶æ nie jest liczb±',
    'error_height_nan' => 'Wysoko¶æ nie jest liczb±',
  ),
  'table_row_insert' => array(
    'title' => 'Wstaw wiersz'
  ),
  'table_column_insert' => array(
    'title' => 'Wstaw kolumnê'
  ),
  'table_row_delete' => array(
    'title' => 'Usuñ wiersz'
  ),
  'table_column_delete' => array(
    'title' => 'Usuñ kolumnê'
  ),
  'table_cell_merge_right' => array(
    'title' => 'Po³±cz z praw±'
  ),
  'table_cell_merge_down' => array(
    'title' => 'Po³±cz z doln±'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'Podziel komórkê w poziomie'
  ),
  'table_cell_split_vertical' => array(
    'title' => 'Podziel komórkê w pionie'
  ),
  'style' => array(
    'title' => 'Styl'
  ),
  'font' => array(
    'title' => 'Czcionka'
  ),
  'fontsize' => array(
    'title' => 'Rozmiar'
  ),
  'paragraph' => array(
    'title' => 'Akapit'
  ),
  'bold' => array(
    'title' => 'Pogrubienie'
  ),
  'italic' => array(
    'title' => 'Kursywa'
  ),
  'underline' => array(
    'title' => 'Podkre¶lenie'
  ),
  'ordered_list' => array(
    'title' => 'Numerowanie'
  ),
  'bulleted_list' => array(
    'title' => 'Wypunktowanie'
  ),
  'indent' => array(
    'title' => 'Zwiêksz wciêcie'
  ),
  'unindent' => array(
    'title' => 'Zmniejsz wciêcie'
  ),
  'left' => array(
    'title' => 'Wyrównaj do lewej'
  ),
  'center' => array(
    'title' => 'Wy¶rodkuj'
  ),
  'right' => array(
    'title' => 'Wyrównaj do prawej'
  ),
  'fore_color' => array(
    'title' => 'Kolor czcionki'
  ),
  'bg_color' => array(
    'title' => 'Kolor t³a'
  ),
  'design_tab' => array(
    'title' => 'Prze³±cz w tryb podgl±du (WYSIWYG)'
  ),
  'html_tab' => array(
    'title' => 'Prze³±cz w tryb HTML (kod)'
  ),
  'colorpicker' => array(
    'title' => 'Wybór koloru',
    'ok' => '   OK   ',
    'cancel' => 'Anuluj',
  ),
  'cleanup' => array(
    'title' => 'czyszczenie HTML (usuwanie styli)',
    'confirm' => 'Przeprowadzenie tej operacji usunie wszystkie style, okre¶lenia czcionek i zbêdne znaczniki z bie¿±cej tre¶ci. Czê¶æ lub ca³o¶æ formatowania mo¿e zostaæ utracona.',
    'ok' => '   OK   ',
    'cancel' => 'Anuluj',
  ),
  'toggle_borders' => array(
    'title' => 'Prze³±cz obramowania',
  ),
  'hyperlink' => array(
    'title' => 'Odsy³acz',
    'url' => 'Adres URL',
    'name' => 'Nazwa',
    'target' => 'Okno docelowe',
    'title_attr' => 'Tytu³',
	'a_type' => 'Typ', // <=== new 1.0.6
	'type_link' => 'Link', // <=== new 1.0.6
	'type_anchor' => 'Kotwica', // <=== new 1.0.6
	'type_link2anchor' => 'Adres kotwicy', // <=== new 1.0.6
    'anchors' => 'Kotwice', // <=== new 1.0.6
    'ok' => '   OK   ',
    'cancel' => 'Anuluj',
  ),
  'hyperlink_targets' => array( // <=== new 1.0.5
  	'_self' => 'to samo (_self)',
	'_blank' => 'nowe okno (_blank)',
	'_top' => 'górna ramka (_top)',
	'_parent' => 'nadrzêdna ramka (_parent)'
  ),
  'table_row_prop' => array(
    'title' => 'W³a¶ciwosci wiersza',
    'horizontal_align' => 'Wyrównanie w poziomie',
    'vertical_align' => 'Wyrównanie w pionie',
    'css_class' => 'styl CSS',
    'no_wrap' => 'Blokuj dzielenie akapitu',
    'bg_color' => 'Kolor t³a',
    'ok' => '   OK   ',
    'cancel' => 'Anuluj',
    'left' => 'Wyrównaj do lewej',
    'center' => 'Do ¶rodka',
    'right' => 'Do prawej',
    'top' => 'Do góry',
    'middle' => 'Do ¶rodka',
    'bottom' => 'Do do³u',
    'baseline' => 'Do linii bazowej',
  ),
  'symbols' => array(
    'title' => 'Znaki specjalne',
    'ok' => '   OK   ',
    'cancel' => 'Anuluj',
  ),
  'templates' => array(
    'title' => 'Szablony',
  ),
  'page_prop' => array(
    'title' => 'W³a¶ciwo¶ci strony',
    'title_tag' => 'Tyty³',
    'charset' => 'Strona kodowa',
    'background' => 'Obraz t³a',
    'bgcolor' => 'Kolor t³a',
    'text' => 'Kolor tekstu',
    'link' => 'Kolor odsy³acza',
    'vlink' => 'Kolor wybranego odsy³acza',
    'alink' => 'Kolor aktywnego odsy³acza',
    'leftmargin' => 'Margines lewy',
    'topmargin' => 'Margines górny',
    'css_class' => 'Styl CSS',
    'ok' => '   OK   ',
    'cancel' => 'Anuluj',
  ),
  'preview' => array(
    'title' => 'Podgl±d',
  ),
  'image_popup' => array(
    'title' => 'Image popup',
  ),
  'zoom' => array(
    'title' => 'Zoom',
  ),
  'subscript' => array( // <=== new 1.0.7
    'title' => 'Indeks dolny',
  ),
  'superscript' => array( // <=== new 1.0.7
    'title' => 'Indeks górny',
  ),
);
?>

