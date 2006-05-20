<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Japanese file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Japanese Translation: DigiPower <http://pwr.jp/>
// Copyright: Solmetra (c)2003 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-03-20
// ================================================

// charset to be used in dialogs
$spaw_lang_charset = 'EUC-JP';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => '�ڤ���'
  ),
  'copy' => array(
    'title' => '���ԡ�'
  ),
  'paste' => array(
    'title' => 'Ž���դ�'
  ),
  'undo' => array(
    'title' => '�����᤹'
  ),
  'redo' => array(
    'title' => '���ľ��'
  ),
  'hyperlink' => array(
    'title' => '�ϥ��ѡ����'
  ),
  'image_insert' => array(
    'title' => '���᡼��������',
    'select' => ' ���򤹤� ',
    'cancel' => '����󥻥�',
    'library' => '�饤�֥��',
    'preview' => '�ץ�ӥ塼',
    'images' => '���᡼��',
    'upload' => '���åץ���',
    'upload_button' => '���åץ���',
    'error' => '���顼',
    'error_no_image' => '���᡼������ꤷ�Ʋ�����',
    'error_uploading' => '���åץ�����˥��顼��������ޤ������������Ƥ���⤦���ټ¹Ԥ��ƤߤƤ���������',
    'error_wrong_type' => '���᡼���ե�����ǤϤ���ޤ���',
    'error_no_dir' => '�饤�֥�꤬���Ĥ���ޤ���',
  ),
  'image_prop' => array(
    'title' => '���᡼���Υץ�ѥƥ�',
    'ok' => '    OK    ',
    'cancel' => '����󥻥�',
    'source' => '������',
    'alt' => '���إƥ�����',
    'align' => '��·��',
    'left' => '��',
    'right' => '��',
    'top' => '��',
    'middle' => '���',
    'bottom' => '��',
    'absmiddle' => '���(����Ū)',
    'texttop' => '��(����Ū)',
    'baseline' => '�١����饤��',
    'width' => '��',
    'height' => '�⤵',
    'border' => '�ܡ�����',
    'hspace' => '���ֳ�',
    'vspace' => '�Ĵֳ�',
    'error' => '���顼',
    'error_width_nan' => '�������Ϥ��Ʋ�����',
    'error_height_nan' => '�⤵�����Ϥ��Ʋ�����',
    'error_border_nan' => '�ܡ����������Ϥ��Ʋ�����',
    'error_hspace_nan' => '���ֳ֤����Ϥ��Ʋ�����',
    'error_vspace_nan' => '�Ĵֳ֤����Ϥ��Ʋ�����',
  ),
  'hr' => array(
    'title' => '���ڤ���'
  ),
  'table_create' => array(
    'title' => '�ơ��֥�κ���'
  ),
  'table_prop' => array(
    'title' => '�ơ��֥�Υץ�ѥƥ�',
    'ok' => '    OK    ',
    'cancel' => '����󥻥�',
    'rows' => '��',
    'columns' => '��',
    'width' => '��',
    'height' => '�⤵',
    'border' => '�ܡ�����',
    'pixels' => '�ԥ�����',
    'cellpadding' => '������;��',
    'cellspacing' => '������ֳ�',
    'bg_color' => '�طʿ�',
    'error' => '���顼',
    'error_rows_nan' => '�Ԥ����Ϥ��Ʋ�����',
    'error_columns_nan' => '������Ϥ��Ʋ�����',
    'error_width_nan' => '�������Ϥ��Ʋ�����',
    'error_height_nan' => '�⤵�����Ϥ��Ʋ�����',
    'error_border_nan' => '�ܡ����������Ϥ��Ʋ�����',
    'error_cellpadding_nan' => '������;������Ϥ��Ʋ�����',
    'error_cellspacing_nan' => '������ֳ֤����Ϥ��Ʋ�����',
  ),
  'table_cell_prop' => array(
    'title' => '����Υץ�ѥƥ�',
    'horizontal_align' => '��·��',
    'vertical_align' => '��·��',
    'width' => '��',
    'height' => '�⤵',
    'css_class' => 'CSS ���饹',
    'no_wrap' => '�ޤ��֤��ʤ�',
    'bg_color' => '�طʿ�',
    'ok' => '    OK    ',
    'cancel' => '����󥻥�',
    'left' => '��',
    'center' => '���',
    'right' => '��',
    'top' => '��',
    'middle' => '���',
    'bottom' => '��',
    'baseline' => '�١����饤��',
    'error' => '���顼',
    'error_width_nan' => '�������Ϥ��Ʋ�����',
    'error_height_nan' => '�⤵�����Ϥ��Ʋ�����',
  ),
  'table_row_insert' => array(
    'title' => '�Ԥ�����'
  ),
  'table_column_insert' => array(
    'title' => '�������'
  ),
  'table_row_delete' => array(
    'title' => '�Ԥκ��'
  ),
  'table_column_delete' => array(
    'title' => '��κ��'
  ),
  'table_cell_merge_right' => array(
    'title' => '������ȷ��'
  ),
  'table_cell_merge_down' => array(
    'title' => '���ιԤȷ��'
  ),
  'table_cell_split_horizontal' => array(
    'title' => '�Ԥ�ʬ��'
  ),
  'table_cell_split_vertical' => array(
    'title' => '���ʬ��'
  ),
  'style' => array(
    'title' => '��������'
  ),
  'font' => array(
    'title' => '�ե����'
  ),
  'fontsize' => array(
    'title' => '������'
  ),
  'paragraph' => array(
    'title' => '����'
  ),
  'bold' => array(
    'title' => '����'
  ),
  'italic' => array(
    'title' => '����'
  ),
  'underline' => array(
    'title' => '����'
  ),
  'ordered_list' => array(
    'title' => '�ֹ�ꥹ��'
  ),
  'bulleted_list' => array(
    'title' => '�ꥹ��'
  ),
  'indent' => array(
    'title' => '����ǥ���ɲ�'
  ),
  'unindent' => array(
    'title' => '����ǥ�Ⱥ��'
  ),
  'left' => array(
    'title' => '��·��'
  ),
  'center' => array(
    'title' => '���·��'
  ),
  'right' => array(
    'title' => '��·��'
  ),
  'fore_color' => array(
    'title' => 'ʸ����'
  ),
  'bg_color' => array(
    'title' => '�طʿ�'
  ),
  'design_tab' => array(
    'title' => 'WYSIWYG (�ǥ�����) �⡼�ɤ�'
  ),
  'html_tab' => array(
    'title' => 'HTML (������) �⡼�ɤ�'
  ),
  'colorpicker' => array(
    'title' => 'Color picker',
    'ok' => '    OK   ',
    'cancel' => '����󥻥�',
  ),
  'cleanup' => array(
    'title' => 'HTML���꡼�󥢥å� (��������κ��)',
    'confirm' => '�¹Ԥ���ȡ����٤ƤΥ��������ե���Ȥ��ʣ�������������ޤ������ˤ�äƤϤ��ʤ��ΰտޤ��ʤ���̤ˤʤ뤳�Ȥ⤢��ޤ��ΤǤ������դ���������',
    'ok' => '    OK    ',
    'cancel' => '����󥻥�',
  ),
  'toggle_borders' => array(
    'title' => '�ܡ��������ڤ��ؤ�',
  ),
  'hyperlink' => array(
    'title' => '�ϥ��ѡ����',
    'url' => 'URL',
    'name' => '������̾',
    'target' => '�������å�',
    'title_attr' => '�����ȥ�',
    'ok' => '    OK    ',
    'cancel' => '����󥻥�',
  ),
  'table_row_prop' => array(
    'title' => '�ԤΥץ�ѥƥ�',
    'horizontal_align' => '��·��',
    'vertical_align' => '��·��',
    'css_class' => 'CSS ���饹',
    'no_wrap' => '�ޤ��֤��ʤ�',
    'bg_color' => '�طʿ�',
    'ok' => '    OK    ',
    'cancel' => '����󥻥�',
    'left' => '��',
    'center' => '���',
    'right' => '��',
    'top' => '��',
    'middle' => '���',
    'bottom' => '��',
    'baseline' => '�١����饤��',
  ),
  'symbols' => array(
    'title' => '�ü�ʸ��',
    'ok' => '    OK    ',
    'cancel' => '����󥻥�',
  ),
  'templates' => array(
    'title' => '�ƥ�ץ졼��',
  ),
  'page_prop' => array(
    'title' => '�ڡ����Υץ�ѥƥ�',
    'title_tag' => '�����ȥ�',
    'charset' => 'ʸ��������',
    'background' => '�طʥ��᡼��',
    'bgcolor' => '�طʿ�',
    'text' => 'ʸ����',
    'link' => '��󥯿�',
    'vlink' => 'ˬ��Ѥߥ�󥯿�',
    'alink' => '�����ƥ��֥�󥯿�',
    'leftmargin' => '���ޡ�����',
    'topmargin' => '��ޡ�����',
    'css_class' => 'CSS ���饹',
    'ok' => '    OK    ',
    'cancel' => '����󥻥�',
  ),
  'preview' => array(
    'title' => '�ץ�ӥ塼',
  ),
  'image_popup' => array(
    'title' => '���᡼���Υݥåץ��å�',
  ),
  'zoom' => array(
    'title' => '����',
  ),
);
?>

