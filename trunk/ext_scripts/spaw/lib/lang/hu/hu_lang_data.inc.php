<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Hungarian language file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Translated: Bagoly S�ndor Zsigmond, sasa@networldtrading.com
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
    'title' => 'Kiv�g�s'
  ),
  'copy' => array(
    'title' => 'M�sol�s'
  ),
  'paste' => array(
    'title' => 'Beilleszt�s'
  ),
  'undo' => array(
    'title' => 'Visszavon�s'
  ),
  'redo' => array(
    'title' => 'M�gis'
  ),
  'hyperlink' => array(
    'title' => 'Hiperhivatkoz�s'
  ),
  'image_insert' => array(
    'title' => 'K�p besz�r�s',
    'select' => 'Kiv�laszt',
	'delete' => 'T�r�l', // new 1.0.5
    'cancel' => 'M�gse',
    'library' => 'K�nyvt�r',
    'preview' => 'El�n�zet',
    'images' => 'K�pek',
    'upload' => 'K�p felt�lt�se',
    'upload_button' => 'Felt�lt�s',
    'error' => 'Hiba',
    'error_no_image' => 'K�rem v�lasszon k�pet',
    'error_uploading' => 'Hiba l�pett fel a felt�lt�s folyamat�ban. K�rj�k pr�b�lja k�s�bb.',
    'error_wrong_type' => 'Hib�s k�pt�pus',
    'error_no_dir' => 'A k�nyvt�r fizikailag nem l�tezik',
	'error_cant_delete' => 'Nem lehet t�r�lni', // new 1.0.5
  ),
  'image_prop' => array(
    'title' => 'K�p tulajdons�gai',
    'ok' => '   OK   ',
    'cancel' => 'M�gse',
    'source' => 'Forr�s',
    'alt' => 'Alternat�v sz�veg',
    'align' => 'Igaz�t�s',
    'left' => 'Balra',
    'right' => 'Jobbra',
    'top' => 'Tetej�re',
    'middle' => 'K�z�pre',
    'bottom' => 'Alj�ra',
    'absmiddle' => 'Teljesen k�z�pre',
    'texttop' => 'Teljesen a tetej�re',
    'baseline' => 'Alapvonalra',
    'width' => 'Sz�less�g',
    'height' => 'Magass�g',
    'border' => 'Szeg�ly',
    'hspace' => 'V�zszintes hely',
    'vspace' => 'F�gg�leges hely',
    'error' => 'Hiba',
    'error_width_nan' => 'Sz�less�g nem egy sz�m',
    'error_height_nan' => 'Magass�g nem egy sz�m',
    'error_border_nan' => 'Szeg�ly nem egy sz�m',
    'error_hspace_nan' => 'V�zszintes hely nem egy sz�m',
    'error_vspace_nan' => 'F�gg�leges hely nem egy sz�m',
  ),
  'hr' => array(
    'title' => 'V�zszintes vonal'
  ),
  'table_create' => array(
    'title' => 'T�bl�zatot l�trehoz'
  ),
  'table_prop' => array(
    'title' => 'T�bl�zat tulajdons�gai',
    'ok' => '   OK   ',
    'cancel' => 'M�gse',
    'rows' => 'Sorok',
    'columns' => 'Oszlopok',
    'width' => 'Sz�less�g',
    'height' => 'Magass�g',
    'border' => 'Szeg�ly',
    'pixels' => 'pixel',
    'cellpadding' => 'Cella kit�lt�se',
    'cellspacing' => 'Cell�k k�z�tti hely',
    'bg_color' => 'H�tt�rsz�n',
    'error' => 'Hiba',
    'error_rows_nan' => 'Sorok nem egy sz�m',
    'error_columns_nan' => 'Oszlopok nem egy sz�m',
    'error_width_nan' => 'Sz�less�g nem egy sz�m',
    'error_height_nan' => 'Magass�g nem egy sz�m',
    'error_border_nan' => 'Szeg�ly nem egy sz�m',
    'error_cellpadding_nan' => 'Cella kit�lt�se nem egy sz�m',
    'error_cellspacing_nan' => 'Cell�k k�z�tti hely nem egy sz�m',
  ),
  'table_cell_prop' => array(
    'title' => 'Cella tulajdons�gai',
    'horizontal_align' => 'V�zszintesre z�r�s',
    'vertical_align' => 'F�gg�legesre z�r�s',
    'width' => 'Sz�less�g',
    'height' => 'Magass�g',
    'css_class' => 'CSS oszt�ly',
    'no_wrap' => 'Nincs csomagol�s',
    'bg_color' => 'H�tt�rsz�n',
    'ok' => '   OK   ',
    'cancel' => 'M�gse',
    'left' => 'Balra z�r�s',
    'center' => 'K�z�pre z�r�s',
    'right' => 'Jobbra z�r�s',
    'top' => 'Tetej�re',
    'middle' => 'K�z�pre',
    'bottom' => 'Alj�ra',
    'baseline' => 'Alapvonal',
    'error' => 'Hiba',
    'error_width_nan' => 'Sz�less�g nem egy sz�m',
    'error_height_nan' => 'Magass�g nem egy sz�m',
  ),
  'table_row_insert' => array(
    'title' => 'Sor besz�r�s'
  ),
  'table_column_insert' => array(
    'title' => 'Oszlop besz�r�s'
  ),
  'table_row_delete' => array(
    'title' => 'Sor t�rl�se'
  ),
  'table_column_delete' => array(
    'title' => 'Oszlop t�rl�se'
  ),
  'table_cell_merge_right' => array(
    'title' => 'Cell�k egyes�t�se jobbra'
  ),
  'table_cell_merge_down' => array(
    'title' => 'Cell�k egyes�t�se lefele'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'Cell�k v�zszintes sz�tszak�t�sa '
  ),
  'table_cell_split_vertical' => array(
    'title' => 'Cell�k f�gg�leges sz�tkasz�t�sa'
  ),
  'style' => array(
    'title' => 'St�lus'
  ),
  'font' => array(
    'title' => 'Bet�t�pus'
  ),
  'fontsize' => array(
    'title' => 'M�ret'
  ),
  'paragraph' => array(
    'title' => 'Bekezd�s'
  ),
  'bold' => array(
    'title' => 'F�lk�v�r'
  ),
  'italic' => array(
    'title' => 'D�lt'
  ),
  'underline' => array(
    'title' => 'Al�h�zott'
  ),
  'ordered_list' => array(
    'title' => 'Sz�moz�s'
  ),
  'bulleted_list' => array(
    'title' => 'Felsorol�s'
  ),
  'indent' => array(
    'title' => 'Beh�z�s n�vel�se'
  ),
  'unindent' => array(
    'title' => 'Beh�z�s cs�kkent�se'
  ),
  'left' => array(
    'title' => 'Balra igaz�t�s'
  ),
  'center' => array(
    'title' => 'K�z�pre igaz�t�s'
  ),
  'right' => array(
    'title' => 'Jobbra igaz�t�s'
  ),
  'fore_color' => array(
    'title' => 'Sz�n'
  ),
  'bg_color' => array(
    'title' => 'H�tt�rsz�n'
  ),
  'design_tab' => array(
    'title' => 'V�lt�s a WYSWYG (design) m�dra'
  ),
  'html_tab' => array(
    'title' => 'V�lt�s a HTML (k�d) m�dra'
  ),
  'colorpicker' => array(
    'title' => 'Sz�nv�laszt�',
    'ok' => '   OK   ',
    'cancel' => 'M�gse',
  ),
  'cleanup' => array(
    'title' => 'HTML tiszt�t�s (st�lusokat megsz�ntet)',
    'confirm' => 'Ezzel a cselekedettel t�rli az alkalmazott st�lusokat, bet�t�pusokat �s a f�l�sleges adatokat a jelen dokumentumban. Valamennyi vagy minden form�z�s el fog veszni.',
    'ok' => '   OK   ',
    'cancel' => 'M�gse',
  ),
  'toggle_borders' => array(
    'title' => 'Szeg�ly megmutat�sa',
  ),
  'hyperlink' => array(
    'title' => 'Hiperhivatkoz�s',
    'url' => 'Hivatkozott c�m (URL)',
    'name' => 'N�v',
    'target' => 'C�l',
    'title_attr' => 'C�m',
    'ok' => '   OK   ',
    'cancel' => 'M�gse',
  ),
  'hyperlink_targets' => array( // <=== new 1.0.5
  	'_self' => 'saj�t keret (_self)',
	'_blank' => '�j keret (_blank)',
	'_top' => 'legfels� keret (_top)',
	'_parent' => 'f� keret (_parent)'
  ),
  'table_row_prop' => array(
    'title' => 'Sor tulajdons�gai',
    'horizontal_align' => 'V�zszintes igaz�t�s',
    'vertical_align' => 'F�gg�eges igaz�t�s',
    'css_class' => 'CSS oszt�ly',
    'no_wrap' => 'Nincs csomagol�s',
    'bg_color' => 'H�tt�rsz�n',
    'ok' => '   OK   ',
    'cancel' => 'M�gse',
    'left' => 'Balra',
    'center' => 'K�z�pre',
    'right' => 'Jobbra',
    'top' => 'Tetej�re',
    'middle' => 'K�z�pre',
    'bottom' => 'Alj�ra',
    'baseline' => 'Alapvonalra',
  ),
  'symbols' => array(
    'title' => 'Speci�lis karakterek',
    'ok' => '   OK   ',
    'cancel' => 'M�gse',
  ),
  'templates' => array(
    'title' => 'Sablonok',
  ),
  'page_prop' => array(
    'title' => 'Oldal tulajdons�gok',
    'title_tag' => 'C�me',
    'charset' => 'Karakter t�pus',
    'background' => 'H�tt�rk�p',
    'bgcolor' => 'H�tt�rsz�n',
    'text' => 'Sz�veg sz�ne',
    'link' => 'Hivatkoz�s sz�ne',
    'vlink' => 'L�togatott hivatkoz�s sz�ne',
    'alink' => 'Akt�v hivatkoz�s sz�ne',
    'leftmargin' => 'Bal marg�',
    'topmargin' => 'Tet� marg�',
    'css_class' => 'CSS oszt�ly',
    'ok' => '   OK   ',
    'cancel' => 'M�gse',
  ),
  'preview' => array(
    'title' => 'El�n�zet',
  ),
  'image_popup' => array(
    'title' => 'El�ugr� k�p',
  ),
  'zoom' => array(
    'title' => 'Nagy�t�s',
  ),
);
?>

