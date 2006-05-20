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
$spaw_lang_charset = 'euc-kr';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => '�߶󳻱�'
  ),
  'copy' => array(
    'title' => '�����ϱ�'
  ),
  'paste' => array(
    'title' => '�ٿ��ֱ�'
  ),
  'undo' => array(
    'title' => '�������'
  ),
  'redo' => array(
    'title' => '�����'
  ),
  'image_insert' => array(
    'title' => '�̹��� ����',
    'select' => '����',
	'delete' => '����', // new 1.0.5
    'cancel' => '���',
    'library' => '���̺귯��',
    'preview' => '�̸�����',
    'images' => '�̹���',
    'upload' => '���ε� �̹���',
    'upload_button' => '���ε�',
    'error' => '����',
    'error_no_image' => '�̹����� ������ �ֽʽÿ�',
    'error_uploading' => '���� ���ε��� ������ �߻��Ͽ����ϴ�. ��� �� �ٽ� �õ��� �ֽʽÿ�',
    'error_wrong_type' => '�߸��� �̹��� �����Դϴ�.',
    'error_no_dir' => '���̺귯���� �������� �ʽ��ϴ�.',
	'error_cant_delete' => '���� ����', // new 1.0.5
  ),
  'image_prop' => array(
    'title' => '�̹��� �Ӽ�,
    'ok' => '   Ȯ��   ',
    'cancel' => '���',
    'source' => '�ҽ�',
    'alt' => '�׸� ����',
    'align' => '����',
    'left' => '����',
    'right' => '������',
    'top' => '���',
    'middle' => '�ߴ�',
    'bottom' => '�ϴ�',
    'absmiddle' => 'absmiddle',
    'texttop' => 'texttop',
    'baseline' => '����',
    'width' => '����',
    'height' => '����',
    'border' => '�� �β�',
    'hspace' => 'Hor. space',
    'vspace' => 'Vert. space',
    'error' => '����',
    'error_width_nan' => '���̴� ���ڷ� �Է��ϼž� �մϴ�. ',
    'error_height_nan' => '���̴� ���ڷ� �Է��ϼž� �մϴ� .',
    'error_border_nan' => '�� �β��� ���ڷ� �Է��ϼž� �մϴ�.',
    'error_hspace_nan' => '�¿��� ����',
    'error_vspace_nan' => '���� ����',
  ),
  'hr' => array(
    'title' => '���м�'
  ),
  'table_create' => array(
    'title' => 'ǥ �����'
  ),
  'table_prop' => array(
    'title' => 'ǥ �Ӽ�',
    'ok' => '   Ȯ��   ',
    'cancel' => '���',
    'rows' => '��',
    'columns' => '��',
    'css_class' => 'CSS Ŭ����', // <=== new 1.0.6
    'width' => '����',
    'height' => '����',
    'border' => '�� �β�',
    'pixels' => '�ȼ�',
    'cellpadding' => '�� ����',
    'cellspacing' => '�� ����',
    'bg_color' => '��� ��',
    'background' => '��� �׸�', // <=== new 1.0.6
    'error' => '����',
    'error_rows_nan' => '���� ���ڷ� �Է��ϼž� �մϴ�.',
    'error_columns_nan' => '���� ���ڷ� �Է��ϼž� �մϴ�.',
    'error_width_nan' => '���̴� ���ڷ� �Է��ϼž� �մϴ�.',
    'error_height_nan' => '���̴� ���ڷ� �Է��ϼž� �մϴ�.',
    'error_border_nan' => '�� �β��� ���ڷ� �Է��ϼž� �մϴ�.',
    'error_cellpadding_nan' => '�� ���̴� ���ڷ� �Է��ϼž� �մϴ�.',
    'error_cellspacing_nan' => '�� ������ ���ڷ� �Է��ϼž� �մϴ�.',
  ),
  'table_cell_prop' => array(
    'title' => '�� �Ӽ�',
    'horizontal_align' => '���� ����',
    'vertical_align' => '���� ����',
    'width' => '����',
    'height' => '����',
    'css_class' => 'CSS Ŭ����',
    'no_wrap' => '�� �ٲٱ� ����',
    'bg_color' => '��� ��',
    'background' => '��� �׸�', // <=== new 1.0.6
    'ok' => '   Ȯ��   ',
    'cancel' => '���',
    'left' => '����',
    'center' => '���',
    'right' => '������',
    'top' => '���',
    'middle' => '�ߴ�',
    'bottom' => '�ϴ�',
    'baseline' => '���ؼ�',
    'error' => '����',
    'error_width_nan' => '���̴� ���ڷ� �Է��ϼž� �մϴ�.',
    'error_height_nan' => '���̴� ���ڷ� �Է��ϼž� �մϴ�.',
  ),
  'table_row_insert' => array(
    'title' => '�� ����'
  ),
  'table_column_insert' => array(
    'title' => '�� ����'
  ),
  'table_row_delete' => array(
    'title' => '�� ����'
  ),
  'table_column_delete' => array(
    'title' => '�� ����'
  ),
  'table_cell_merge_right' => array(
    'title' => '�� ��ġ��'
  ),
  'table_cell_merge_down' => array(
    'title' => '�� ��ġ��'
  ),
  'table_cell_split_horizontal' => array(
    'title' => '�� ������'
  ),
  'table_cell_split_vertical' => array(
    'title' => '�� ������'
  ),
  'style' => array(
    'title' => '��Ÿ��'
  ),
  'font' => array(
    'title' => '�۲�'
  ),
  'fontsize' => array(
    'title' => 'ũ��'
  ),
  'paragraph' => array(
    'title' => '����'
  ),
  'bold' => array(
    'title' => '����'
  ),
  'italic' => array(
    'title' => '�����'
  ),
  'underline' => array(
    'title' => '����'
  ),
  'ordered_list' => array(
    'title' => '��ȣ ���'
  ),
  'bulleted_list' => array(
    'title' => '��ȣ ���'
  ),
  'indent' => array(
    'title' => '�鿩����'
  ),
  'unindent' => array(
    'title' => '�����'
  ),
  'left' => array(
    'title' => '���� ����'
  ),
  'center' => array(
    'title' => '��� ����'
  ),
  'right' => array(
    'title' => '������ ����'
  ),
  'fore_color' => array(
    'title' => '�����'
  ),
  'bg_color' => array(
    'title' => '����'
  ),
  'design_tab' => array(
    'title' => '���������'
  ),
  'html_tab' => array(
    'title' => 'HTML ���'
  ),
  'colorpicker' => array(
    'title' => '���� ���ñ�',
    'ok' => '   Ȯ��   ',
    'cancel' => '���',
  ),
  'cleanup' => array(
    'title' => 'HTMLû�� (��Ÿ�� ����)',
    'confirm' => '�� ����� ����ϸ�, ���� ���ܿ� ���� �۲��̳�, �±׵��� ��ü �Ǵ� �Ϻ� ������ �� �ֽ��ϴ�.',
    'ok' => '   Ȯ��   ',
    'cancel' => '���',
  ),
  'toggle_borders' => array(
    'title' => 'Toggle borders',
  ),
  'hyperlink' => array(
    'title' => 'Hyperlink',
    'url' => 'URL',
    'name' => 'Name',
    'target' => 'Target',
    'title_attr' => 'Title',
	'a_type' => 'Type', // <=== new 1.0.6
	'type_link' => '���ο���(Link)', // <=== new 1.0.6
	'type_anchor' => '�ܺο���(Anchor)', // <=== new 1.0.6
	'type_link2anchor' => 'Link to anchor', // <=== new 1.0.6
	'anchors' => 'Anchors', // <=== new 1.0.6
    'ok' => '   Ȯ��   ',
    'cancel' => '���',
  ),
  'hyperlink_targets' => array( // <=== new 1.0.5
  	'_self' => '����â (_self)',
	'_blank' => '��â (_blank)',
	'_top' => 'top frame (_top)',
	'_parent' => '�θ�â (_parent)'
  ),
  'table_row_prop' => array(
    'title' => '�� �Ӽ�',
    'horizontal_align' => '��������',
    'vertical_align' => '��������',
    'css_class' => 'CSS Ŭ����',
    'no_wrap' => '�� �ٲٱ� ����',
    'bg_color' => '����',
    'ok' => '   Ȯ��   ',
    'cancel' => '���',
    'left' => '����',
    'center' => '���',
    'right' => '������',
    'top' => '���',
    'middle' => '�ߴ�',
    'bottom' => '�ϴ�',
    'baseline' => '���ؼ�',
  ),
  'symbols' => array(
    'title' => 'Ư������',
    'ok' => '   Ȯ��   ',
    'cancel' => '���',
  ),
  'templates' => array(
    'title' => '���ø�',
  ),
  'page_prop' => array(
    'title' => '������ �Ӽ�',
    'title_tag' => 'Ÿ��Ʋ',
    'charset' => 'ĳ���� ��',
    'background' => '��� �׸�',
    'bgcolor' => '����',
    'text' => '���ڻ�',
    'link' => '��ũ ��',
    'vlink' => '�湮�� ��ũ ��',
    'alink' => 'Ȱ��ȭ�� ��ũ ��',
    'leftmargin' => '���� ����',
    'topmargin' => '��� ����',
    'css_class' => 'CSS Ŭ����',
    'ok' => '   Ȯ��   ',
    'cancel' => '���',
  ),
  'preview' => array(
    'title' => '�̸�����',
  ),
  'image_popup' => array(
    'title' => 'Image popup',
  ),
  'zoom' => array(
    'title' => 'Ȯ��',
  ),
  'subscript' => array( // <=== new 1.0.7
    'title' => '�Ʒ�÷��',
  ),
  'superscript' => array( // <=== new 1.0.7
    'title' => '��÷��',
  ),
);
?>