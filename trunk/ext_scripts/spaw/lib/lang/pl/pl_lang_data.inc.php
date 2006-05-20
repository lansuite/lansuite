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
    'title' => 'Pon�w'
  ),
  'image_insert' => array(
    'title' => 'Wstaw obrazek',
    'select' => 'Wybierz',
	'delete' => 'Usu�', // new 1.0.5
    'cancel' => 'Anuluj',
    'library' => 'Biblioteka',
    'preview' => 'Podgl�d',
    'images' => 'Obrazki',
    'upload' => 'Wy�lij obrazek',
    'upload_button' => 'Wy�lij',
    'error' => 'B��d',
    'error_no_image' => 'Prosz� wybra� obrazek',
    'error_uploading' => 'Przy wysy�aniu obrazka wyst�pi� b��d. Prosz� spr�bowa� p�niej.',
    'error_wrong_type' => 'Niew�a�ciwy typ pliku obrazka',
    'error_no_dir' => 'Brak biblioteki obrazk�w',
	'error_cant_delete' => 'B��d usuwania', // new 1.0.5
  ),
  'image_prop' => array(
    'title' => 'W�a�ciwo�ci obrazka',
    'ok' => '   OK   ',
    'cancel' => 'Anuluj',
    'source' => '�cie�ka',
    'alt' => 'Tekst alternatywny',
    'align' => 'Wyr�wnanie',
    'left' => 'left',
    'right' => 'right',
    'top' => 'top',
    'middle' => 'middle',
    'bottom' => 'bottom',
    'absmiddle' => 'absmiddle',
    'texttop' => 'texttop',
    'baseline' => 'baseline',
    'width' => 'Szeroko��',
    'height' => 'Wysoko��',
    'border' => 'Obramowanie',
    'hspace' => 'Odst�p poziomy',
    'vspace' => 'Odst�p pionowy',
    'error' => 'B��d',
    'error_width_nan' => 'Szeroko�� nie jest liczb�',
    'error_height_nan' => 'Wysoko�� nie jest liczb�',
    'error_border_nan' => 'Ramka nie jest liczb�',
    'error_hspace_nan' => 'Odst�p poziomy nie jest liczb�',
    'error_vspace_nan' => 'Odst�p pionowy nie jest liczb�',
  ),
  'hr' => array(
    'title' => 'Linia pozioma'
  ),
  'table_create' => array(
    'title' => 'Wstaw tabel�'
  ),
  'table_prop' => array(
    'title' => 'W�a�ciwo�ci tabeli',
    'ok' => '   OK   ',
    'cancel' => 'Anuluj',
    'rows' => 'Liczba wierszy',
    'columns' => 'Liczba kolumn',
    'css_class' => 'Styl CSS', // <=== new 1.0.6
    'width' => 'Szeroko��',
    'height' => 'Wysoko��',
    'border' => 'Obramowanie',
    'pixels' => 'pikseli',
    'cellpadding' => 'Margines kom�rki',
    'cellspacing' => 'Obramowanie kom�rki',
    'bg_color' => 'Kolor t�a',
    'background' => 'Obrazek t�a', // <=== new 1.0.6
    'error' => 'B��d',
    'error_rows_nan' => 'Liczba wierszy nie jest liczb�',
    'error_columns_nan' => 'Liczba kolumn nie jest liczb�',
    'error_width_nan' => 'Szeroko�� nie jest liczb�',
    'error_height_nan' => 'Wysoko�� nie jest liczb�',
    'error_border_nan' => 'Obramowanie nie jest liczb�',
    'error_cellpadding_nan' => 'Margines kom�rki nie jest liczb�',
    'error_cellspacing_nan' => 'Obramowanie kom�rki nie jest liczb�',
  ),
  'table_cell_prop' => array(
    'title' => 'W�a�ciwo�ci kom�rki',
    'horizontal_align' => 'Wyr�wnanie w poziomie',
    'vertical_align' => 'Wyr�wnanie w pionie',
    'width' => 'Szeroko��',
    'height' => 'Wysoko��',
    'css_class' => 'styl CSS',
    'no_wrap' => 'Blokuj dzielenie akapitu',
    'bg_color' => 'Kolor t�a',
    'background' => 'Obrazek t�a', // <=== new 1.0.6
    'ok' => '   OK   ',
    'cancel' => 'Anuluj',
    'left' => 'Do lewej',
    'center' => 'Do �rodka',
    'right' => 'Do prawej',
    'top' => 'Do g�ry',
    'middle' => 'Do �rodka',
    'bottom' => 'Do do�u',
    'baseline' => 'Do linii bazowej',
    'error' => 'B��d',
    'error_width_nan' => 'Szeroko�� nie jest liczb�',
    'error_height_nan' => 'Wysoko�� nie jest liczb�',
  ),
  'table_row_insert' => array(
    'title' => 'Wstaw wiersz'
  ),
  'table_column_insert' => array(
    'title' => 'Wstaw kolumn�'
  ),
  'table_row_delete' => array(
    'title' => 'Usu� wiersz'
  ),
  'table_column_delete' => array(
    'title' => 'Usu� kolumn�'
  ),
  'table_cell_merge_right' => array(
    'title' => 'Po��cz z praw�'
  ),
  'table_cell_merge_down' => array(
    'title' => 'Po��cz z doln�'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'Podziel kom�rk� w poziomie'
  ),
  'table_cell_split_vertical' => array(
    'title' => 'Podziel kom�rk� w pionie'
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
    'title' => 'Podkre�lenie'
  ),
  'ordered_list' => array(
    'title' => 'Numerowanie'
  ),
  'bulleted_list' => array(
    'title' => 'Wypunktowanie'
  ),
  'indent' => array(
    'title' => 'Zwi�ksz wci�cie'
  ),
  'unindent' => array(
    'title' => 'Zmniejsz wci�cie'
  ),
  'left' => array(
    'title' => 'Wyr�wnaj do lewej'
  ),
  'center' => array(
    'title' => 'Wy�rodkuj'
  ),
  'right' => array(
    'title' => 'Wyr�wnaj do prawej'
  ),
  'fore_color' => array(
    'title' => 'Kolor czcionki'
  ),
  'bg_color' => array(
    'title' => 'Kolor t�a'
  ),
  'design_tab' => array(
    'title' => 'Prze��cz w tryb podgl�du (WYSIWYG)'
  ),
  'html_tab' => array(
    'title' => 'Prze��cz w tryb HTML (kod)'
  ),
  'colorpicker' => array(
    'title' => 'Wyb�r koloru',
    'ok' => '   OK   ',
    'cancel' => 'Anuluj',
  ),
  'cleanup' => array(
    'title' => 'czyszczenie HTML (usuwanie styli)',
    'confirm' => 'Przeprowadzenie tej operacji usunie wszystkie style, okre�lenia czcionek i zb�dne znaczniki z bie��cej tre�ci. Cz�� lub ca�o�� formatowania mo�e zosta� utracona.',
    'ok' => '   OK   ',
    'cancel' => 'Anuluj',
  ),
  'toggle_borders' => array(
    'title' => 'Prze��cz obramowania',
  ),
  'hyperlink' => array(
    'title' => 'Odsy�acz',
    'url' => 'Adres URL',
    'name' => 'Nazwa',
    'target' => 'Okno docelowe',
    'title_attr' => 'Tytu�',
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
	'_top' => 'g�rna ramka (_top)',
	'_parent' => 'nadrz�dna ramka (_parent)'
  ),
  'table_row_prop' => array(
    'title' => 'W�a�ciwosci wiersza',
    'horizontal_align' => 'Wyr�wnanie w poziomie',
    'vertical_align' => 'Wyr�wnanie w pionie',
    'css_class' => 'styl CSS',
    'no_wrap' => 'Blokuj dzielenie akapitu',
    'bg_color' => 'Kolor t�a',
    'ok' => '   OK   ',
    'cancel' => 'Anuluj',
    'left' => 'Wyr�wnaj do lewej',
    'center' => 'Do �rodka',
    'right' => 'Do prawej',
    'top' => 'Do g�ry',
    'middle' => 'Do �rodka',
    'bottom' => 'Do do�u',
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
    'title' => 'W�a�ciwo�ci strony',
    'title_tag' => 'Tyty�',
    'charset' => 'Strona kodowa',
    'background' => 'Obraz t�a',
    'bgcolor' => 'Kolor t�a',
    'text' => 'Kolor tekstu',
    'link' => 'Kolor odsy�acza',
    'vlink' => 'Kolor wybranego odsy�acza',
    'alink' => 'Kolor aktywnego odsy�acza',
    'leftmargin' => 'Margines lewy',
    'topmargin' => 'Margines g�rny',
    'css_class' => 'Styl CSS',
    'ok' => '   OK   ',
    'cancel' => 'Anuluj',
  ),
  'preview' => array(
    'title' => 'Podgl�d',
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
    'title' => 'Indeks g�rny',
  ),
);
?>

