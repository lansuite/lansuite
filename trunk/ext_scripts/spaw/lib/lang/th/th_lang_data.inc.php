<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// English language file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-03-20
// ================================================

// charset to be used in dialogs
$spaw_lang_charset = 'windows-874';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => '�Ѵ'
  ),
  'copy' => array(
    'title' => '�Ѵ�͡'
  ),
  'paste' => array(
    'title' => '�ҧ'
  ),
  'undo' => array(
    'title' => '��ԡ��'
  ),
  'redo' => array(
    'title' => '�ӫ��'
  ),
  'image_insert' => array(
    'title' => '�á�ٻ',
    'select' => '���͡',
	'delete' => 'ź', // new 1.0.5
    'cancel' => '¡��ԡ',
    'library' => '�ź�����',
    'preview' => '�ʴ�������ҧ',
    'images' => '�ٻ�Ҿ',
    'upload' => '�Ѿ��Ŵ�Ҿ',
    'upload_button' => '�Ѿ��Ŵ',
    'error' => '�Դ��Ҵ!',
    'error_no_image' => '�ô�ӡ�����͡�Ҿ',
    'error_uploading' => '��ä����Դ��Ҵ��������ҧ�ӡ���Ѿ��Ŵ���. �ͧ�����ա����',
    'error_wrong_type' => '�ٻẺ���Դ',
    'error_no_dir' => '��辺�ź����'
	'error_cant_delete' => '���ź�������', // new 1.0.5
  ),
  'image_prop' => array(
    'title' => '�س���ѵ��ٻ�Ҿ',
    'ok' => '   ��ŧ   ',
    'cancel' => '¡��ԡ',
    'source' => '���觢�����',
    'alt' => '��ͤ����ҧ���͡',
    'align' => '��èѴ���˹觢�ͤ���',
    'left' => '�Դ����',
    'right' => '�Դ���',
    'top' => '�Դ���ش',
    'middle' => '��觡�ҧ',
    'bottom' => '�Դ��ҧ',
    'absmiddle' => '�����觡�ҧ�ʹ�',
    'texttop' => 'texttop',
    'baseline' => 'baseline',
    'width' => '�������ҧ',
    'height' => '�����٧',
    'border' => '����˹Ңͺ',
    'hspace' => '��ͧ��ҧ�ǹ͹',
    'vspace' => '��ͧ��ҧ�ǵ��',
    'error' => '�Դ��Ҵ!',
    'error_width_nan' => '�������ҧ������������ҵ���Ţ',
    'error_height_nan' => '�����٧������������ҵ���Ţ',
    'error_border_nan' => '����˹Ңͺ������������ҵ���Ţ',
    'error_hspace_nan' => '��ͧ��ҧ�ǹ͹���������㪤�ҵ���Ţ',
    'error_vspace_nan' => '��ͧ��ҧ�ǵ�駷��������㪤�ҵ���Ţ',
  ),
  'hr' => array(
    'title' => '����觺�÷Ѵ'
  ),
  'table_create' => array(
    'title' => '���ҧ���ҧ'
  ),
  'table_prop' => array(
    'title' => '�س���ѵԵ��ҧ',
    'ok' => '   ��ŧ   ',
    'cancel' => '¡��ԡ',
    'rows' => '��',
    'columns' => '�������',
    'css_class' => 'CSS ����', // <=== new 1.0.6
    'width' => '�������ҧ',
    'height' => '�����٧',
    'border' => '����˹Ңͺ',
    'pixels' => '�ԡ��',
    'cellpadding' => 'Cell padding',
    'cellspacing' => 'Cell spacing',
    'bg_color' => '�վ����ѧ',
    'background' => '�Ҿ�����ѧ', // <=== new 1.0.6
    'error' => '�Դ��Ҵ!',
    'error_rows_nan' => '����Ƿ��������㪤�ҵ���Ţ',
    'error_columns_nan' => '��Ҥ��������������㪤�ҵ���Ţ',
    'error_width_nan' => '��Ҥ������ҧ���������㪤�ҵ���Ţ',
    'error_height_nan' => '��Ҥ����٧���������㪤�ҵ���Ţ',
    'error_border_nan' => '��Ҥ���˹Ңͺ���������㪤�ҵ���Ţ',
    'error_cellpadding_nan' => 'Cell padding ���������㪤�ҵ���Ţ',
    'error_cellspacing_nan' => 'Cell spacing ���������㪤�ҵ���Ţ',
  ),
  'table_cell_prop' => array(
    'title' => '�س���ѵ�����',
    'horizontal_align' => '��èѴ�ǹ͹',
    'vertical_align' => '��èѴ�ǵ��',
    'width' => '�������ҧ',
    'height' => '�����٧',
    'css_class' => 'CSS ����',
    'no_wrap' => '���������ͤ���',
    'bg_color' => '�վ����ѧ',
    'background' => '�Ҿ�����ѧ', // <=== new 1.0.6
    'ok' => '   ��ŧ   ',
    'cancel' => '¡��ԡ',
    'left' => '�Դ����',
    'center' => '�Ѵ��ҧ',
    'right' => '�Դ���',
    'top' => '�Դ��',
    'middle' => '�Ѵ��ҧ',
    'bottom' => '�Դ��ҧ',
    'baseline' => 'Baseline',
    'error' => '�Դ��Ҵ!',
    'error_width_nan' => '��Ҥ������ҧ���������㪤�ҵ���Ţ',
    'error_height_nan' => '��Ҥ����٧���������㪤�ҵ���Ţ',
  ),
  'table_row_insert' => array(
    'title' => '�á��'
  ),
  'table_column_insert' => array(
    'title' => '�á�������'
  ),
  'table_row_delete' => array(
    'title' => 'ź��'
  ),
  'table_column_delete' => array(
    'title' => 'ź�������'
  ),
  'table_cell_merge_right' => array(
    'title' => '�������ҧ��ҹ���'
  ),
  'table_cell_merge_down' => array(
    'title' => '��������ҹ����'
  ),
  'table_cell_split_horizontal' => array(
    'title' => '������ҧ�ǹ͹'
  ),
  'table_cell_split_vertical' => array(
    'title' => '������ҧ�ǵ��'
  ),
  'style' => array(
    'title' => '����'
  ),
  'font' => array(
    'title' => '�ٻẺ�ѡ��'
  ),
  'fontsize' => array(
    'title' => '��Ҵ'
  ),
  'paragraph' => array(
    'title' => '���˹��'
  ),
  'bold' => array(
    'title' => '���˹�'
  ),
  'italic' => array(
    'title' => '������§'
  ),
  'underline' => array(
    'title' => '�մ�����'
  ),
  'ordered_list' => array(
    'title' => '�ѭ�ѡɳ���Ǣ���������§�Ӵ�'
  ),
  'bulleted_list' => array(
    'title' => '�ѭ�ѡɳ���Ǣ������'
  ),
  'indent' => array(
    'title' => '�Թ�繷�'
  ),
  'unindent' => array(
    'title' => '�ѹ�Թ�繷�'
  ),
  'left' => array(
    'title' => '����'
  ),
  'center' => array(
    'title' => '��ҧ'
  ),
  'right' => array(
    'title' => '���'
  ),
  'fore_color' => array(
    'title' => '�վ��˹��'
  ),
  'bg_color' => array(
    'title' => '�վ����ѧ'
  ),
  'design_tab' => array(
    'title' => '��Ѻ������� WYSIWYG (�͡Ẻ) '
  ),
  'html_tab' => array(
    'title' => '��Ѻ������� HTML (��) '
  ),
  'colorpicker' => array(
    'title' => '�ҹ��',
    'ok' => '   ��ŧ   ',
    'cancel' => '¡��ԡ',
  ),
  'cleanup' => array(
    'title' => 'HTML cleanup (¡��ԡ����)',
    'confirm' => '��á�зӹ���繡��¡��ԡ����ҹ����, �ٻẺ�ѡ����Ф���觺ҧ�ѹ����ռšѺ��������������.',
    'ok' => '   ��ŧ  ',
    'cancel' => '¡��ԡ',
  ),
  'toggle_borders' => array(
    'title' => '��Ѻ����˹Ңͺ',
  ),
  'hyperlink' => array(
    'title' => '�������ԧ��',
    'url' => 'URL',
    'name' => '����',
    'target' => '�������',
    'title_attr' => '�������ͧ',
	'a_type' => '�ٻẺ', // <=== new 1.0.6
	'type_link' => '�ԧ��', // <=== new 1.0.6
	'type_anchor' => '�ѧ����r', // <=== new 1.0.6
	'type_link2anchor' => '�ԧ����ѧ�ѧ����', // <=== new 1.0.6
	'anchors' => '�ѧ����', // <=== new 1.0.6
    'ok' => '   ��ŧ   ',
    'cancel' => '¡��ԡ',
  ),
  'hyperlink_targets' => array( // <=== new 1.0.5
  	'_self' => '������ǡѹ (_self)',
	'_blank' => '˹������ (_blank)',
	'_top' => '������ش (_top)',
	'_parent' => '�����ѡ (_parent)'
  ),
  'table_row_prop' => array(
    'title' => '�س���ѵ���',
    'horizontal_align' => '�Ѵ���§����ǹ͹',
    'vertical_align' => '�Ѵ���§����ǵ��',
    'css_class' => 'CSS ����',
    'no_wrap' => '���������ͤ���',
    'bg_color' => '�վ����ѧ',
    'ok' => '   ��ŧ  ',
    'cancel' => '¡��ԡ',
    'left' => '�Դ����',
    'center' => '��觡�ҧ',
    'right' => '�Դ���',
    'top' => '�Դ��',
    'middle' => '��觡�ҧ',
    'bottom' => '�Դ��ҧ',
    'baseline' => 'Baseline',
  ),
  'symbols' => array(
    'title' => '�ѡ��о����',
    'ok' => '   ��ŧ   ',
    'cancel' => '¡��ԡ',
  ),
  'templates' => array(
    'title' => '���ŵ',
  ),
  'page_prop' => array(
    'title' => '�س���ѵ�',
    'title_tag' => '�������ͧ',
    'charset' => 'Charset',
    'background' => '�Ҿ�����ѧ',
    'bgcolor' => '�վ����ѧ',
    'text' => '�բ�ͤ���',
    'link' => '���ԧ��'
    'vlink' => '���ԧ��������',
    'alink' => '���ԧ���ͤ�Կ',
    'leftmargin' => '���Тͺ����',
    'topmargin' => '���Тͺ��',
    'css_class' => 'CSS ����',
    'ok' => '   ��ŧ  ',
    'cancel' => '¡��ԡ',
  ),
  'preview' => array(
    'title' => '�ʴ�������ҧ',
  ),
  'image_popup' => array(
    'title' => '��ͺ�Ѿ�ٻ',
  ),
  'zoom' => array(
    'title' => '����',
  ),
);
?>