<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Bulgarian language file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// Translated: Atanas Tchobanov, atanas@webdressy.com
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-04-10
// ================================================

// charset to be used in dialogs
$spaw_lang_charset = 'windows-1251';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => '������'
  ),
  'copy' => array(
    'title' => '�������'
  ),
  'paste' => array(
    'title' => '������'
  ),
  'undo' => array(
    'title' => '������'
  ),
  'redo' => array(
    'title' => '�������'
  ),
  'hyperlink' => array(
    'title' => '����'
  ),
  'image_insert' => array(
    'title' => '������ ��������',
    'select' => '������',
    'cancel' => '������',
    'library' => '����������',
    'preview' => '�������',
    'images' => '��������',
    'upload' => '������� ��������',
    'upload_button' => '�������',
    'error' => '������',
    'error_no_image' => '�������� ��������',
    'error_uploading' => '������ ��� �����������. ��������� ���.',
    'error_wrong_type' => '���������� ��� ��������',
    'error_no_dir' => '������������ �� ����������',
  ),
  'image_prop' => array(
    'title' => '��������� �� ����������',
    'ok' => '������',
    'cancel' => '������',
    'source' => '��������',
    'alt' => '������ ��������',
    'align' => '������������',
    'left' => '������ (left)',
    'right' => '������� (right)',
    'top' => '���� (top)',
    'middle' => '� ������� (middle)',
    'bottom' => '���� (bottom)',
    'absmiddle' => '���. ������ (absmiddle)',
    'texttop' => '������ (texttop)',
    'baseline' => '������ (baseline)',
    'width' => '������',
    'height' => '��������',
    'border' => '�����',
    'hspace' => '���. ����������',
    'vspace' => '����. ����������',
    'error' => '������',
    'error_width_nan' => '�������� ������ �� � ������� ��������',
    'error_height_nan' => '���������� ������ �� � ������� ��������',
    'error_border_nan' => '������� ������ �� � ������� ��������',
    'error_hspace_nan' => '�������������� ������ ������ �� �� ������� ��������',
    'error_vspace_nan' => '������������ ������ ������ �� �� ������� ��������',
  ),
  'hr' => array(
    'title' => '������������ �����'
  ),
  'table_create' => array(
    'title' => '������ �������'
  ),
  'table_prop' => array(
    'title' => '��������� �� ���������',
    'ok' => '������',
    'cancel' => '������',
    'rows' => '������',
    'columns' => '������',
    'width' => '������',
    'height' => '��������',
    'border' => '�����',
    'pixels' => '����.',
    'cellpadding' => '������ �� �������',
    'cellspacing' => '���������� ����� ��������',
    'bg_color' => '���� �� ����',
    'error' => '������',
    'error_rows_nan' => '�������� ������ �� �� ������� ��������',
    'error_columns_nan' => '�������� ������ �� �� ������� ��������',
    'error_width_nan' => '�������� ������ �� � ������� ��������',
    'error_height_nan' => '���������� ������ �� � ������� ��������',
    'error_border_nan' => '������� ������ �� � ������� ��������',
    'error_cellpadding_nan' => '�������� �� ������� ������ �� � ������� ��������',
    'error_cellspacing_nan' => '������������ ����� �������� ������ �� � ������� ��������',
  ),
  'table_cell_prop' => array(
    'title' => '��������� �� ��������',
    'horizontal_align' => '������������ ������������',
    'vertical_align' => '���������� ������������',
    'width' => '������',
    'height' => '��������',
    'css_class' => '����',
    'no_wrap' => '��� �������',
    'bg_color' => '���� �� ����',
    'ok' => '������',
    'cancel' => '������',
    'left' => '������',
    'center' => '� �������',
    'right' => '�������',
    'top' => '������',
    'middle' => '� �������',
    'bottom' => '������',
    'baseline' => '�� �������� ����� �� ������',
    'error' => '������',
    'error_width_nan' => '�������� ������ �� � ������� ��������',
    'error_height_nan' => '���������� ������ �� � ������� ��������',
    
  ),
  'table_row_insert' => array(
    'title' => '������ ���'
  ),
  'table_column_insert' => array(
    'title' => '������ ������'
  ),
  'table_row_delete' => array(
    'title' => '�������� ���'
  ),
  'table_column_delete' => array(
    'title' => '�������� ������'
  ),
  'table_cell_merge_right' => array(
    'title' => '������� �������'
  ),
  'table_cell_merge_down' => array(
    'title' => '������� ������'
  ),
  'table_cell_split_horizontal' => array(
    'title' => '������� ������������'
  ),
  'table_cell_split_vertical' => array(
    'title' => '������� ����������'
  ),
  'style' => array(
    'title' => '����'
  ),
  'font' => array(
    'title' => '�����'
  ),
  'fontsize' => array(
    'title' => '������'
  ),
  'paragraph' => array(
    'title' => '��������'
  ),
  'bold' => array(
    'title' => '�������'
  ),
  'italic' => array(
    'title' => '������'
  ),
  'underline' => array(
    'title' => '���������'
  ),
  'ordered_list' => array(
    'title' => '������������ ������'
  ),
  'bulleted_list' => array(
    'title' => '��������� ������'
  ),
  'indent' => array(
    'title' => '������� �������'
  ),
  'unindent' => array(
    'title' => '������ �������'
  ),
  'left' => array(
    'title' => '������������ ������'
  ),
  'center' => array(
    'title' => '������������ �� �������'
  ),
  'right' => array(
    'title' => '������������ �������'
  ),
  'fore_color' => array(
    'title' => '���� �� ������'
  ),
  'bg_color' => array(
    'title' => '���� �� ����'
  ),
  'design_tab' => array(
    'title' => '��������� � ����� �� ���������� (WYSIWYG)'
  ),
  'html_tab' => array(
    'title' => '��������� � ����� �� ����������� �� ���� (HTML)'
  ),
  'colorpicker' => array(
    'title' => '����� �� ����',
    'ok' => '������',
    'cancel' => '������',
  ),
  'cleanup' => array(
    'title' => '��������� �� HTML',
    'confirm' => '���� �������� �������� ������ �������, �������� � ������� ������ �� ������������ � ���������. ������������� ���� �� ���� �������� �������� ��� ������.',
    'ok' => '������',
    'cancel' => '������',
  ),
  'toggle_borders' => array(
    'title' => '������ �������',
  ),
  'hyperlink' => array(
    'title' => '����',
    'url' => '�����',
    'name' => '���',
    'target' => '���',
    'title_attr' => '��������',
    'ok' => '������',
    'cancel' => '������',
  ),
  'table_row_prop' => array(
    'title' => '��������� �� ����',
    'horizontal_align' => '������������ ������������',
    'vertical_align' => '���������� ������������',
    'css_class' => '����',
    'no_wrap' => '��� �������',
    'bg_color' => '���� �� ����',
    'ok' => '������',
    'cancel' => '������',
    'left' => '������',
    'center' => '� �������',
    'right' => '�������',
    'top' => '������',
    'middle' => '� �������',
    'bottom' => '������',
    'baseline' => '�� �������� ����� �� ������',
  ),
  'symbols' => array(
    'title' => '����. �������',
    'ok' => '������',
    'cancel' => '������',
  ),
  'templates' => array(
    'title' => '�������� ������',
  ),
  'page_prop' => array(
    'title' => '��������� �� ����������',
    'title_tag' => '��������',
    'charset' => '������ �������',
    'background' => '������ ��������',
    'bgcolor' => '���� �� ����',
    'text' => '���� �� ������',
    'link' => '���� �� �����',
    'vlink' => '���� �� ���������� �������',
    'alink' => '���� �� ��������� �������',
    'leftmargin' => '������ ������',
    'topmargin' => '������ ������',
    'css_class' => '����',
    'ok' => '������',
    'cancel' => '������',
  ),
  'preview' => array(
    'title' => '������������� �������',
  ),
  'image_popup' => array(
    'title' => 'Popup ��������',
  ),
  'zoom' => array(
    'title' => '����������',
  ),
);
?>

