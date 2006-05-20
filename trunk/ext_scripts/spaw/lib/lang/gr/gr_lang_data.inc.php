<?php
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// English language file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// Greek translation: Saxinidis B. Konstantinos
//                    skva@in.gr
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-03-20
// ================================================

// charset to be used in dialogs
$spaw_lang_charset = 'iso-8859-7';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => '�������'
  ),
  'copy' => array(
    'title' => '���������'
  ),
  'paste' => array(
    'title' => '����������'
  ),
  'undo' => array(
    'title' => '��������'
  ),
  'redo' => array(
    'title' => '������� ���������'
  ),
  'hyperlink' => array(
    'title' => '�����������'
  ),
  'image_insert' => array(
    'title' => '�������� �������',
    'select' => '�������',
    'cancel' => '�����',
    'library' => '����������',
    'preview' => '�������������',
    'images' => '�������',
    'upload' => '������� �������',
    'upload_button' => '�������',
    'error' => '�����',
    'error_no_image' => '�������� �������� ��� ������',
    'error_uploading' => '��� ����� ��� �� �������������� ������ �����������.  �������� ����������� ���� ��������',
    'error_wrong_type' => '����������� ����� ������� �������',
    'error_no_dir' => '� ���������� ��� ������������� �� ������ ����� � ��� �������',
  ),
  'image_prop' => array(
    'title' => '��������� �������',
    'ok' => '   OK   ',
    'cancel' => '�����',
    'source' => '���������',
    'alt' => '����������� �������',
    'align' => '������������',
    'left' => '��������',
    'right' => '�����',
    'full' => 'justify',
    'top' => '����',
    'middle' => '����',
    'bottom' => '����',
    'absmiddle' => 'abs����',
    'texttop' => 'texttop',
    'baseline' => 'baseline',
    'width' => '������',
    'height' => '����',
    'border' => '����������',
    'hspace' => '����. space',
    'vspace' => '�����. space',
    'error' => '�����',
    'error_width_nan' => '�� ������ ��� ����� ���� �������',
    'error_height_nan' => '���� ��� ����� ���� �������',
    'error_border_nan' => 'Border ��� ����� ���� �������',
    'error_hspace_nan' => '�� ��������� �������� ��� ����� ���� �������',
    'error_vspace_nan' => '�� ������ �������� ��� ����� ���� �������',
  ),
  'hr' => array(
    'title' => '���������� �������'
  ),
  'table_create' => array(
    'title' => '������������ ������'
  ),
  'table_prop' => array(
    'title' => '��������� ������',
    'ok' => '   OK   ',
    'cancel' => '�����',
    'rows' => '������',
    'columns' => '������',
    'width' => '������',
    'height' => '����',
    'border' => '����������',
    'pixels' => 'pixels',
    'cellpadding' => '������� ������',
    'cellspacing' => '�������� ������',
    'bg_color' => 'Background �����',
    'error' => '�����',
    'error_rows_nan' => '�� ������ ��� ����� ���� �������',
    'error_columns_nan' => '�� ������ ��� ����� ���� �������',
    'error_width_nan' => '�� ������ ��� ����� ���� �������',
    'error_height_nan' => '���� ��� ����� ���� �������',
    'error_border_nan' => '�� ���������� ��� ����� ���� �������',
    'error_cellpadding_nan' => '�� ������� ������ ��� ����� ���� �������',
    'error_cellspacing_nan' => '�� �������� ������ ��� ����� ���� �������',
  ),
  'table_cell_prop' => array(
    'title' => ' ��������� ������',
    'horizontal_align' => '��������� ������������',
    'vertical_align' => '������ ������������',
    'width' => '������',
    'height' => '����',
    'css_class' => 'CSS class',
    'no_wrap' => 'No wrap',
    'bg_color' => 'Background color',
    'ok' => '   OK   ',
    'cancel' => '�����',
    'left' => '��������',
    'center' => '������',
    'right' => '�����',
    'full' => 'Justify',
    'top' => 'Top',
    'middle' => 'Middle',
    'bottom' => 'Bottom',
    'baseline' => 'Baseline',
    'error' => '�����',
    'error_width_nan' => '�� ������ ��� ����� ���� �������',
    'error_height_nan' => '���� ��� ����� ���� �������',
  ),
  'table_row_insert' => array(
    'title' => '�������� ������'
  ),
  'table_column_insert' => array(
    'title' => '�������� ������'
  ),
  'table_row_delete' => array(
    'title' => '�������� ������'
  ),
  'table_column_delete' => array(
    'title' => '�������� ������'
  ),
  'table_cell_merge_right' => array(
    'title' => 'Merge �����'
  ),
  'table_cell_merge_down' => array(
    'title' => 'Merge ����'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'Split ������ ���������'
  ),
  'table_cell_split_vertical' => array(
    'title' => 'Split ������ ������'
  ),
  'style' => array(
    'title' => 'Style'
  ),
  'font' => array(
    'title' => '�������������'
  ),
  'fontsize' => array(
    'title' => '�������'
  ),
  'paragraph' => array(
    'title' => '����������'
  ),
  'bold' => array(
    'title' => '������'
  ),
  'italic' => array(
    'title' => 'Italic'
  ),
  'underline' => array(
    'title' => '�����������'
  ),
  'ordered_list' => array(
    'title' => '��������'
  ),
  'bulleted_list' => array(
    'title' => '���������'
  ),
  'indent' => array(
    'title' => '�����'
  ),
  'unindent' => array(
    'title' => '����� �����'
  ),
  'left' => array(
    'title' => '��������'
  ),
  'center' => array(
    'title' => '������'
  ),
  'right' => array(
    'title' => '�����'
  ),
  'full' => array(
    'title' => 'Justify'
  ),
  'fore_color' => array(
    'title' => 'Fore color'
  ),
  'bg_color' => array(
    'title' => 'Background color'
  ),
  'design_tab' => array(
    'title' => '������ �� WYSIWYG (design) mode'
  ),
  'html_tab' => array(
    'title' => '������ �� HTML (code) mode'
  ),
  'colorpicker' => array(
    'title' => 'Color picker',
    'ok' => '   OK   ',
    'cancel' => '�����',
  ),
  'cleanup' => array(
    'title' => 'HTML ���������� (����������� styles)',
    'confirm' => '� �������� ���� �� ��������� ��� �� styles, fonts and useless tags ��� �� ������������ content. ������ � ��� ���� ��  ������������� ��� ������ �� ������.',
    'ok' => '   OK   ',
    'cancel' => '�����',
  ),
  'toggle_borders' => array(
    'title' => '���� �������������',
  ),
  'hyperlink' => array(
    'title' => '�����������',
    'url' => 'URL',
    'name' => '�����',
    'target' => '����',
    'title_attr' => '������',
    'ok' => '   OK   ',
    'cancel' => '�����',
  ),
  'table_row_prop' => array(
    'title' => '��������� ������',
    'horizontal_align' => '��������� ������������',
    'vertical_align' => '������ ������������',
    'css_class' => 'CSS class',
    'no_wrap' => 'No wrap',
    'bg_color' => 'Background color',
    'ok' => '   OK   ',
    'cancel' => '�����',
    'left' => '��������',
    'center' => '������',
    'right' => '�����',
    'full' => 'Justify',
    'top' => '����',
    'middle' => '����',
    'bottom' => '����',
    'baseline' => 'Baseline',
  ),
  'symbols' => array(
    'title' => '������� ����������',
    'ok' => '   OK   ',
    'cancel' => '�����',
  ),
  'templates' => array(
    'title' => 'Templates',
  ),
  'page_prop' => array(
    'title' => '��������� �������',
    'title_tag' => '������',
    'charset' => 'Charset',
    'background' => 'Background ������',
    'bgcolor' => 'Background �����',
    'text' => '����� ��������',
    'link' => '����� link',
    'vlink' => '����� link ��� ������������',
    'alink' => '����� ������� link ',
    'leftmargin' => '��������� ��������',
    'topmargin' => '���� ���������',
    'css_class' => 'CSS class',
    'ok' => '   OK   ',
    'cancel' => '�����',
  ),
  'preview' => array(
    'title' => '�������������',
  ),
  'image_popup' => array(
    'title' => 'Image popup',
  ),
  'zoom' => array(
    'title' => 'Zoom',
  ),
);
?>