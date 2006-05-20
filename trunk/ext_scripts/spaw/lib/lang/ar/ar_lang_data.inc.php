<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Arabic language file
// Traslated: Mohammed Ahmed
// Gaza, Palestine
// http://www.maaking.com
// Email/MSN: m@maaking.com
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-03-20
// ================================================

// charset to be used in dialogs
$spaw_lang_charset = 'windows-1256';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => 'ŞÕ'
  ),
  'copy' => array(
    'title' => 'äÓÎ'
  ),
  'paste' => array(
    'title' => 'áÕŞ'
  ),
  'undo' => array(
    'title' => 'ÊÑÇÌÚ'
  ),
  'redo' => array(
    'title' => 'ÅÚÇÏÉ ÇáÊÑÇÌÚ'
  ),
  'image_insert' => array(
    'title' => 'ÅÏÑÇÌ ÕæÑÉ',
    'select' => 'ÅÎÊÑ',
	'delete' => 'ÍĞİ', // new 1.0.5
    'cancel' => 'ÅáÛÇÁ ÇáÃãÑ',
    'library' => 'ÇáãßÊÈÉ',
    'preview' => 'ãÚÇíäÉ',
    'images' => 'ÇáÕæÑ',
    'upload' => 'ÇÎÊÑ ÕæÑÉ ááÊÍãíá',
    'upload_button' => 'ÅÑİÚ ÇáÕæÑÉ',
    'error' => 'ÎØÃ',
    'error_no_image' => 'ãä İÖáß ÅÎÊÑ ÕæÑÉ',
    'error_uploading' => 'AÍÏË ÎØÃ ÃËäÇÁ ãÚÇáÌÉ Çáãáİ¡ ÇáÑÌÇÁ ÇáãÍÇæáÉ İíãÇ ÈÚÏ.',
    'error_wrong_type' => 'äæÚ ãáİ ÇáÕæÑÉ ÎÇØÁ.',
    'error_no_dir' => 'ãÌáÏ ãßÊÈÇÊ ÇáÕæÑ ÛíÑ ãæÌæÏ¿',
	'error_cant_delete' => 'ÎØÃ: İÔáÊ ÚãáíÉ ÇáÍĞİ', // new 1.0.5
  ),
  'image_prop' => array(
    'title' => 'ÎÕÇÆÕ ÇáÕæÑÉ',
    'ok' => '   ãæÇİŞ   ',
    'cancel' => 'ÅáÛÇÁ',
    'source' => 'ÇáãÕÏÑ',
    'alt' => 'äÕ ÈÏíá',
    'align' => 'ãÍÇĞÇå',
    'left' => 'íÓÇÑ',
    'right' => 'íãíä',
    'top' => 'ÃÚáì',
    'middle' => 'æÓØ',
    'bottom' => 'ÃÓİá',
    'absmiddle' => 'æÓØ ÇáÓØÑ',
    'texttop' => 'ÇáäÕ ÈÇáÃÚáì',
    'baseline' => 'ãÚ ÇáÎØ',
    'width' => 'ÇáÚÑÖ',
    'height' => 'ÇáØæá',
    'border' => 'Óãß ÇáÍÏæÏ',
    'hspace' => 'ÇáİÑÇÛ ÚãæÏíÇ',
    'vspace' => 'ÇáİÑÇÛ ÃİŞíÇ',
    'error' => 'ÎØÃ',
    'error_width_nan' => 'ÇáÚÑÖ áíÓ ÈÑŞã',
    'error_height_nan' => 'ÇáØæá áíÓ ÈÑŞã',
    'error_border_nan' => 'Óãß ÇáÍÏæÏ áíÓ ÈÑŞã',
    'error_hspace_nan' => 'ÍŞá ÇáİÑÇÛ ÇáÃİŞí áíÓ ÑŞã',
    'error_vspace_nan' => 'ÍŞá ÇáİÑÇÛ ÇáÚãæÏí áíÓ ÑŞã',
  ),
  'hr' => array(
    'title' => 'ÎØ ÃİŞí'
  ),
  'table_create' => array(
    'title' => 'ÅäÔÇÁ ÌÏæá'
  ),
  'table_prop' => array(
    'title' => 'ÎÕÇÆÕ ÇáÌÏæá',
    'ok' => '   ãæÇİŞ   ',
    'cancel' => 'ÅáÛÇÁ',
    'rows' => 'Õİæİ',
    'columns' => 'ÃÚãÏÉ',
    'css_class' => 'CSS ÏÇáÉ', // <=== new 1.0.6
    'width' => 'ÇáÚÑÖ',
    'height' => 'ÇáØæá',
    'border' => 'ÇáÍÏ',
    'pixels' => 'ÈíßÓá',
    'cellpadding' => 'äØÇŞ ÇáÎáíÉ',
    'cellspacing' => 'ÇáãÓÇİÉ Èíä ÇáÎáÇíÇ',
    'bg_color' => 'áæä ÇáÎáİíÉ',
    'background' => 'ÕæÑÉ ÇáÎáİíÉ', // <=== new 1.0.6
    'error' => 'ÍØÃ',
    'error_rows_nan' => 'ÇáÕİ áíÓ ÈÑŞã',
    'error_columns_nan' => 'ÇáÚãæÏ áíÓ ÈÑŞã',
    'error_width_nan' => 'ÇáÚÑÖ áíÓ ÈÑŞã',
    'error_height_nan' => 'ÇáØæá áíÓ ÈÑŞã',
    'error_border_nan' => 'ÇáÍÏ áíÓ ÈÑŞã',
    'error_cellpadding_nan' => 'äØÇŞ ÇáÎáíÉ áíÓ ÈÑŞã',
    'error_cellspacing_nan' => 'ÇáãÓÇİÉ Èíä ÇáÎáÇÇíÇ áíÓ ÈÑŞã',
  ),
  'table_cell_prop' => array(
    'title' => 'ÎÕÇÆÕ ÇáÎáíÉ',
    'horizontal_align' => 'ãÍÇĞÇå ÚãæÏíÉ',
    'vertical_align' => 'ãÍÇĞÇå ÃİŞíÉ',
    'width' => 'ÇáÚÑÖ',
    'height' => 'ÇáØæá',
    'css_class' => 'CSS ÏÇáÉ',
    'no_wrap' => 'ÈáÇ ÇáÊİÇİ',
    'bg_color' => 'áæä ÇáÎáİíÉ',
    'background' => 'ÕæÑÉ ÇáÎáİíÉ', // <=== new 1.0.6
    'ok' => '   ãæÇİŞ   ',
    'cancel' => 'ÅáÛÇÁ',
    'left' => 'íÓÇÑ',
    'center' => 'æÓØ',
    'right' => 'íãíä',
    'top' => 'ÃÚáì',
    'middle' => 'æÓØ',
    'bottom' => 'ÃÓİá',
    'baseline' => 'ÎØ ÃÓÇÓí',
    'error' => 'ÎØÃ',
    'error_width_nan' => 'ÇáÚÑÖ áíÓ ÈÑŞã',
    'error_height_nan' => 'ÇáØæá áíÓ ÈÑŞã',
  ),
  'table_row_insert' => array(
    'title' => 'ÅÏÑÇÌ Õİ'
  ),
  'table_column_insert' => array(
    'title' => 'ÅÏÑÇÌ ÚãæÏ'
  ),
  'table_row_delete' => array(
    'title' => 'ÍĞİ Õİ'
  ),
  'table_column_delete' => array(
    'title' => 'ÍĞİ ÚãæÏ'
  ),
  'table_cell_merge_right' => array(
    'title' => 'ÏãÌ íãíä'
  ),
  'table_cell_merge_down' => array(
    'title' => 'ÏãÌ íÓÇÑ'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'ÊŞÓíã ÇáÎáÇÇíÇ ÚäæÏí'
  ),
  'table_cell_split_vertical' => array(
    'title' => 'ÊŞÓíã ÇáÎáÇÇíÇ ÃİŞí'
  ),
  'style' => array(
    'title' => 'ÇáÊäÓíŞ'
  ),
  'font' => array(
    'title' => 'ÇáÎØ'
  ),
  'fontsize' => array(
    'title' => 'ÇáÍÌã'
  ),
  'paragraph' => array(
    'title' => 'ÇáİŞÑÉ'
  ),
  'bold' => array(
    'title' => 'ÃÓæÏ ÚÑíÖ'
  ),
  'italic' => array(
    'title' => 'ãÇÆá'
  ),
  'underline' => array(
    'title' => 'ÊÍÊå ÎØ'
  ),
  'ordered_list' => array(
    'title' => 'ÊÚÏÇÏ ÑŞãí'
  ),
  'bulleted_list' => array(
    'title' => 'ÊÚÏÇÏ äŞØí'
  ),
  'indent' => array(
    'title' => 'ÒíÇÏÉ ÇáãÓÇİÉ ÇáÈÇÏÆÉ'
  ),
  'unindent' => array(
    'title' => 'ÅäŞÇÕ ÇáãÓÇİÉ ÇáÒÇÆÏÉ'
  ),
  'left' => array(
    'title' => 'íÓÇÑ'
  ),
  'center' => array(
    'title' => 'æÓØ'
  ),
  'right' => array(
    'title' => 'íãíä'
  ),
  'fore_color' => array(
    'title' => 'áæä ÇáäÕ'
  ),
  'bg_color' => array(
    'title' => 'áæä ÇáÎáİíÉ'
  ),
  'design_tab' => array(
    'title' => 'ÚÑÖ ÇáÊÕãíã'
  ),
  'html_tab' => array(
    'title' => 'ÚÑÖ ßæÏ html'
  ),
  'colorpicker' => array(
    'title' => 'ÅäÊŞÇÁ Çááæä',
    'ok' => '   ãæÇİŞ   ',
    'cancel' => 'ÅáÛÇÁ ÇáÃãÑ',
  ),
  'cleanup' => array(
    'title' => 'ãÓÍ ßÇİÉ ÇáÊäÓíŞÇÊ',
    'confirm' => 'ÓíÊã ãÓÍ ßÇİÉ ÇáÊäÓíŞÇÊ æ ÇáÃßæÇÏ ÇáÊí áÇ ÊáÒã¡ æÈÚÖåÇ ŞÏ íÈŞì.',
    'ok' => '   ãæÇİŞ   ',
    'cancel' => 'ÅáÛÇÁ',
  ),
  'toggle_borders' => array(
    'title' => 'ÍÏæÏ ÇáÓíÇŞ',
  ),
  'hyperlink' => array(
    'title' => 'ÑÇÈØ ÊÔÚÈí',
    'url' => 'ÚäæÇä Çá URL',
    'name' => 'ÇáÇÓã',
    'target' => 'ÇáÅØÇÑ ÇáåÏİ',
    'title_attr' => 'ÇáÚäæÇä',
	'a_type' => 'ÇáäæÚ', // <=== new 1.0.6
	'type_link' => 'ÑÇÈØ', // <=== new 1.0.6
	'type_anchor' => 'ãÚáãÉ', // <=== new 1.0.6
	'type_link2anchor' => 'ÑÈØ ÈãÚáãÉ', // <=== new 1.0.6
	'anchors' => 'ÇáãÚáãÇÊ', // <=== new 1.0.6
    'ok' => '   ãæÇİŞ   ',
    'cancel' => 'ÇáÛÇÁ',
  ),
  'hyperlink_targets' => array( // <=== new 1.0.5
  	'_self' => 'äİÓ ÇáÇØÇÑ (_self)',
	'_blank' => 'ÕİÍÉ ÌÏíÏÉ (_blank)',
	'_top' => 'ÃÚáì (_top)',
	'_parent' => 'ÅØÇÑ ŞÑíä (_parent)'
  ),
  'table_row_prop' => array(
    'title' => 'ÎÕÇÆÕ ÇáÕİ',
    'horizontal_align' => 'ãÍÇĞÇÉ ÚãæÏíÉ',
    'vertical_align' => 'ãÍÇĞÇå ÃİŞíÉ',
    'css_class' => 'CSS ÏÇáÉ',
    'no_wrap' => 'ÈáÇ ÇáÊİÇİ',
    'bg_color' => 'áæä ÇáÎáİíÉ',
    'ok' => '   ãæÇİŞ   ',
    'cancel' => 'ÇáÛÇÁ',
    'left' => 'íÓÇÑ',
    'center' => 'ÊæÓíØ',
    'right' => 'íãíä',
    'top' => 'ÃÚáì',
    'middle' => 'æÓØ',
    'bottom' => 'ÃÓİá',
    'baseline' => 'ÎØ ÃÓÇÓí',
  ),
  'symbols' => array(
    'title' => 'ÑãæÒ ÎÇÕÉ',
    'ok' => '   ãæÇİŞ   ',
    'cancel' => 'ÇáÛÇÁ',
  ),
  'templates' => array(
    'title' => 'ÃÔßÇá ÌÇåÒÉ Templates',
  ),
  'page_prop' => array(
    'title' => 'ÎÕÇÆÕ ÇáÕİÍÉ',
    'title_tag' => 'ÇáÚäæÇä',
    'charset' => 'ÊÑãíÒ',
    'background' => 'ÕæÑÉ ÇáÎáİíÉ',
    'bgcolor' => 'áæä ÇáÎáİíÉ',
    'text' => 'áæä ÇáäÕ',
    'link' => 'áæä ÇáÑÇÈØ',
    'vlink' => 'áæä ÇáÑÇÈØ ÇáĞí Êã ÒíÇÑÊå',
    'alink' => 'áæä ÇáÑÇÈØ ÇáİÚÇá',
    'leftmargin' => 'ÇáÍÏ ÇáÃíÓÑ',
    'topmargin' => 'ÇáÍÏ ÇáÚáæí',
    'css_class' => 'CSS ÏÇáÉ',
    'ok' => '   ãæÇİŞ   ',
    'cancel' => 'ÅáÛÇÁ',
  ),
  'preview' => array(
    'title' => 'ãÚÇíäÉ',
  ),
  'image_popup' => array(
    'title' => 'ÅÏÑÇÌ ÕæÑÉ æÌÚá ÑÇÈØ áåÇ ÊÙåÑ İí äÇİĞÉ ÚäÏ ÇáÖÛØ ÚáíåÇ Ãæ Úáì ÇáÑÇÈØ',
  ),
  'zoom' => array(
    'title' => 'ÊßÈíÑ/ÊÕÛíÑ',
  ),
  'subscript' => array( // <=== new 1.0.7
    'title' => 'ÑİÚ ÇáäÕ',
  ),
  'superscript' => array( // <=== new 1.0.7
    'title' => 'ÑİÚ ÇáäÕ2',
  ),
);
?>
