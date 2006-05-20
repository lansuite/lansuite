<?php
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Chinese gb2312 language file 
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Chinese translation: aman@wealthgrp.com.tw;aman@516888.com;aman77@pchome.com.tw
// Copyright: Solmetra (c)2003 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-03-20
// ================================================

// charset to be used in dialogs
$spaw_lang_charset = 'gb2312';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => '����'
  ),
  'copy' => array(
    'title' => '����'
  ),
  'paste' => array(
    'title' => '����'
  ),
  'undo' => array(
    'title' => '��ԭ'
  ),
  'redo' => array(
    'title' => '����ԭ'
  ),
  'hyperlink' => array(
    'title' => '������'
  ),
  'image_insert' => array(
    'title' => '����ͼƬ',
    'select' => 'ѡȡ',
    'cancel' => 'ȡ��',
    'library' => '���ϼ�',
    'preview' => 'Ԥ��',
    'images' => 'ͼƬ',
    'upload' => '�ϴ�ͼƬ',
    'upload_button' => '�ϴ�',
    'error' => '����',
    'error_no_image' => '��ѡ��ͼƬ',
    'error_uploading' => '�����ϴ���������. �������ش�',
    'error_wrong_type' => '������̬����',
    'error_no_dir' => '�Ҳ������ϼ�',
  ),
  'image_prop' => array(
    'title' => 'ͼ������',
    'ok' => '   ȷ��   ',
    'cancel' => 'ȡ��',
    'source' => '��Դ',
    'alt' => '������ʾ',
    'align' => '����',
    'left' => '��',
    'right' => '��',
    'top' => '��',
    'middle' => '��',
    'bottom' => '��',
    'absmiddle' => '��������',
    'texttop' => '���ֶ���',
    'baseline' => '��׼��',
    'width' => '���',
    'height' => '�߶�',
    'border' => '�߿���',
    'hspace' => 'ˮƽ���',
    'vspace' => '��ֱ���',
    'error' => '����',
    'error_width_nan' => '��Ȳ�������',
    'error_height_nan' => '�߶Ȳ�������',
    'error_border_nan' => '�߿��Ȳ�������',
    'error_hspace_nan' => 'ˮƽ��಻������',
    'error_vspace_nan' => '��ֱ��಻������',
  ),
  'hr' => array(
    'title' => 'ˮƽ����'
  ),
  'table_create' => array(
    'title' => '�������'
  ),
  'table_prop' => array(
    'title' => '�������',
    'ok' => '   ȷ��   ',
    'cancel' => 'ȡ��',
    'rows' => '����',
    'columns' => '����',
    'width' => '���',
    'height' => '�߶�',
    'border' => '�߿���',
    'pixels' => 'px',
    'cellpadding' => '�Ŀ���',
    'cellspacing' => '���߼��',
    'bg_color' => '������ɫ',
    'error' => '����',
    'error_rows_nan' => '������������',
    'error_columns_nan' => '������������',
    'error_width_nan' => '��Ȳ�������',
    'error_height_nan' => '�߶Ȳ�������',
    'error_border_nan' => '�߿��Ȳ�������',
    'error_cellpadding_nan' => '�Ŀ��಻������',
    'error_cellspacing_nan' => '���߼�಻������',
  ),
  'table_cell_prop' => array(
    'title' => '���������',
    'horizontal_align' => 'ˮƽ����',
    'vertical_align' => '��ֱ����',
    'width' => '���',
    'height' => '�߶�',
    'css_class' => 'CSS class',
    'no_wrap' => '���ֲ�ת��',
    'bg_color' => '������ɫ',
    'ok' => '   ȷ��   ',
    'cancel' => 'ȡ��',
    'left' => '��',
    'center' => '��',
    'right' => '��',
    'top' => '��',
    'middle' => '����',
    'bottom' => '��',
    'baseline' => '��׼��',
    'error' => '����',
    'error_width_nan' => '��Ȳ�������',
    'error_height_nan' => '�߶Ȳ�������',
    
  ),
  'table_row_insert' => array(
    'title' => '�������'
  ),
  'table_column_insert' => array(
    'title' => '����ֱ��'
  ),
  'table_row_delete' => array(
    'title' => 'ɾ������'
  ),
  'table_column_delete' => array(
    'title' => 'ɾ��ֱ��'
  ),
  'table_cell_merge_right' => array(
    'title' => '�ϲ��Ҳ�'
  ),
  'table_cell_merge_down' => array(
    'title' => '�ϲ��·�'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'ˮƽ�ָ�'
  ),
  'table_cell_split_vertical' => array(
    'title' => '��ֱ�ָ�'
  ),
  'style' => array(
    'title' => 'Style'
  ),
  'font' => array(
    'title' => '����'
  ),
  'fontsize' => array(
    'title' => '�ֺ�'
  ),
  'paragraph' => array(
    'title' => 'Paragraph'
  ),
  'bold' => array(
    'title' => '����'
  ),
  'italic' => array(
    'title' => 'б��'
  ),
  'underline' => array(
    'title' => '�ӵ���'
  ),
  'ordered_list' => array(
    'title' => '��ű���'
  ),
  'bulleted_list' => array(
    'title' => '��ű���'
  ),
  'indent' => array(
    'title' => '��������'
  ),
  'unindent' => array(
    'title' => '��������'
  ),
  'left' => array(
    'title' => '��������'
  ),
  'center' => array(
    'title' => '���ж���'
  ),
  'right' => array(
    'title' => '��������'
  ),
  'fore_color' => array(
    'title' => '������ɫ'
  ),
  'bg_color' => array(
    'title' => '������ɫ'
  ),
  'design_tab' => array(
    'title' => '�л� WYSIWYG (ֱ��) ģʽ'
  ),
  'html_tab' => array(
    'title' => '�л� HTML (Դ��) ģʽ'
  ),
  'colorpicker' => array(
    'title' => '��ɫ��',
    'ok' => '   ȷ��   ',
    'cancel' => 'ȡ��',
  ),
  // <<<<<<<<< NEW >>>>>>>>>
  'cleanup' => array(
    'title' => '���HTML (�Ƴ���ҳ��ʽ)',
    'confirm' => '�����������������е���ҳ��ʽ����ע��.',
    'ok' => '   ȷ��   ',
    'cancel' => 'ȡ��',
  ),
  'toggle_borders' => array(
    'title' => '�л�����',
  ),
  'hyperlink' => array(
    'title' => '������',
    'url' => '��ַ',
    'name' => '����',
    'target' => 'Ŀ��',
    'title_attr' => '����',
    'ok' => '   ȷ��   ',
    'cancel' => 'ȡ��',
  ),
  'table_row_prop' => array(
    'title' => '��������',
    'horizontal_align' => 'ˮƽ����',
    'vertical_align' => '��ֱ����',
    'css_class' => 'CSS class',
    'no_wrap' => '������',
    'bg_color' => '������ɫ',
    'ok' => '   ȷ��   ',
    'cancel' => 'ȡ��',
    'left' => '��',
    'center' => '��',
    'right' => '��',
    'top' => '��',
    'middle' => '����',
    'bottom' => '�ײ�',
    'baseline' => '����',
  ),
  'symbols' => array(
    'title' => '�������',
    'ok' => '   ȷ��   ',
    'cancel' => 'ȡ��',
  ),
  'symbols' => array(
    'title' => '�������',
    'ok' => '   ȷ��   ',
    'cancel' => 'ȡ��',
  ),
  'templates' => array(
    'title' => 'ģ��',
  ),
  'page_prop' => array(
    'title' => '��ҳ����',
    'title_tag' => '����',
    'charset' => '���ֱ���',
    'background' => '����ͼƬ',
    'bgcolor' => '������ɫ',
    'text' => '������ɫ',
    'link' => '������ɫ',
    'vlink' => '�ι۹���������ɫ',
    'alink' => '����ִ�е�������ɫ',
    'leftmargin' => '��߽�',
    'topmargin' => '�Ϸ��߽�',
    'css_class' => 'CSS class',
    'ok' => '   ȷ��   ',
    'cancel' => 'ȡ��',
  ),
  'preview' => array(
    'title' => 'Ԥ��',
  ),
  'image_popup' => array(
    'title' => 'ͼƬ����',
  ),
  'zoom' => array(
    'title' => 'Zoom',
  ),
);
?>