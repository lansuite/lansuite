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
$spaw_lang_charset = 'iso-8859-1';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => 'Cut'
  ),
  'copy' => array(
    'title' => 'Copy'
  ),
  'paste' => array(
    'title' => 'Paste'
  ),
  'undo' => array(
    'title' => 'Undo'
  ),
  'redo' => array(
    'title' => 'Redo'
  ),
  'image_insert' => array(
    'title' => 'Insert image',
    'select' => 'Select',
	'delete' => 'Delete', // new 1.0.5
    'cancel' => 'Cancel',
    'library' => 'Library',
    'preview' => 'Preview',
    'images' => 'Images',
    'upload' => 'Upload image',
    'upload_button' => 'Upload',
    'error' => 'Error',
    'error_no_image' => 'Please select an image',
    'error_uploading' => 'An error occured while handling file upload. Please try again later',
    'error_wrong_type' => 'Wrong image file type',
    'error_no_dir' => 'Library doesn\'t physically exist',
	'error_cant_delete' => 'Delete failed', // new 1.0.5
  ),
  'image_prop' => array(
    'title' => 'Image properties',
    'ok' => '   OK   ',
    'cancel' => 'Cancel',
    'source' => 'Source',
    'alt' => 'Alternative text',
    'align' => 'Align',
    'left' => 'left',
    'right' => 'right',
    'top' => 'top',
    'middle' => 'middle',
    'bottom' => 'bottom',
    'absmiddle' => 'absmiddle',
    'texttop' => 'texttop',
    'baseline' => 'baseline',
    'width' => 'Width',
    'height' => 'Height',
    'border' => 'Border',
    'hspace' => 'Hor. space',
    'vspace' => 'Vert. space',
    'error' => 'Error',
    'error_width_nan' => 'Width is not a number',
    'error_height_nan' => 'Height is not a number',
    'error_border_nan' => 'Border is not a number',
    'error_hspace_nan' => 'Horizontal space is not a number',
    'error_vspace_nan' => 'Vertical space is not a number',
  ),
  'hr' => array(
    'title' => 'Horizontal rule'
  ),
  'table_create' => array(
    'title' => 'Create table'
  ),
  'table_prop' => array(
    'title' => 'Table properties',
    'ok' => '   OK   ',
    'cancel' => 'Cancel',
    'rows' => 'Rows',
    'columns' => 'Columns',
    'css_class' => 'CSS class', // <=== new 1.0.6
    'width' => 'Width',
    'height' => 'Height',
    'border' => 'Border',
    'pixels' => 'pixels',
    'cellpadding' => 'Cell padding',
    'cellspacing' => 'Cell spacing',
    'bg_color' => 'Background color',
    'background' => 'Background image', // <=== new 1.0.6
    'error' => 'Error',
    'error_rows_nan' => 'Rows is not a number',
    'error_columns_nan' => 'Columns is not a number',
    'error_width_nan' => 'Width is not a number',
    'error_height_nan' => 'Height is not a number',
    'error_border_nan' => 'Border is not a number',
    'error_cellpadding_nan' => 'Cell padding is not a number',
    'error_cellspacing_nan' => 'Cell spacing is not a number',
  ),
  'table_cell_prop' => array(
    'title' => 'Cell properties',
    'horizontal_align' => 'Horizontal align',
    'vertical_align' => 'Vertical align',
    'width' => 'Width',
    'height' => 'Height',
    'css_class' => 'CSS class',
    'no_wrap' => 'No wrap',
    'bg_color' => 'Background color',
    'background' => 'Background image', // <=== new 1.0.6
    'ok' => '   OK   ',
    'cancel' => 'Cancel',
    'left' => 'Left',
    'center' => 'Center',
    'right' => 'Right',
    'top' => 'Top',
    'middle' => 'Middle',
    'bottom' => 'Bottom',
    'baseline' => 'Baseline',
    'error' => 'Error',
    'error_width_nan' => 'Width is not a number',
    'error_height_nan' => 'Height is not a number',
  ),
  'table_row_insert' => array(
    'title' => 'Insert row'
  ),
  'table_column_insert' => array(
    'title' => 'Insert column'
  ),
  'table_row_delete' => array(
    'title' => 'Delete row'
  ),
  'table_column_delete' => array(
    'title' => 'Delete column'
  ),
  'table_cell_merge_right' => array(
    'title' => 'Merge right'
  ),
  'table_cell_merge_down' => array(
    'title' => 'Merge down'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'Split cell horizontally'
  ),
  'table_cell_split_vertical' => array(
    'title' => 'Split cell vertically'
  ),
  'style' => array(
    'title' => 'Style'
  ),
  'font' => array(
    'title' => 'Font'
  ),
  'fontsize' => array(
    'title' => 'Size'
  ),
  'paragraph' => array(
    'title' => 'Paragraph'
  ),
  'bold' => array(
    'title' => 'Bold'
  ),
  'italic' => array(
    'title' => 'Italic'
  ),
  'underline' => array(
    'title' => 'Underline'
  ),
  'ordered_list' => array(
    'title' => 'Ordered list'
  ),
  'bulleted_list' => array(
    'title' => 'Bulleted list'
  ),
  'indent' => array(
    'title' => 'Indent'
  ),
  'unindent' => array(
    'title' => 'Unindent'
  ),
  'left' => array(
    'title' => 'Left'
  ),
  'center' => array(
    'title' => 'Center'
  ),
  'right' => array(
    'title' => 'Right'
  ),
  'fore_color' => array(
    'title' => 'Fore color'
  ),
  'bg_color' => array(
    'title' => 'Background color'
  ),
  'design_tab' => array(
    'title' => 'Switch to WYSIWYG (design) mode'
  ),
  'html_tab' => array(
    'title' => 'Switch to HTML (code) mode'
  ),
  'colorpicker' => array(
    'title' => 'Color picker',
    'ok' => '   OK   ',
    'cancel' => 'Cancel',
  ),
  'cleanup' => array(
    'title' => 'HTML cleanup (remove styles)',
    'confirm' => 'Performing this action will remove all styles, fonts and useless tags from the current content. Some or all your formatting may be lost.',
    'ok' => '   OK   ',
    'cancel' => 'Cancel',
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
	'type_link' => 'Link', // <=== new 1.0.6
	'type_anchor' => 'Anchor', // <=== new 1.0.6
	'type_link2anchor' => 'Link to anchor', // <=== new 1.0.6
	'anchors' => 'Anchors', // <=== new 1.0.6
    'ok' => '   OK   ',
    'cancel' => 'Cancel',
  ),
  'hyperlink_targets' => array( // <=== new 1.0.5
  	'_self' => 'same frame (_self)',
	'_blank' => 'new empty window (_blank)',
	'_top' => 'top frame (_top)',
	'_parent' => 'parent frame (_parent)'
  ),
  'table_row_prop' => array(
    'title' => 'Row properties',
    'horizontal_align' => 'Horizontal align',
    'vertical_align' => 'Vertical align',
    'css_class' => 'CSS class',
    'no_wrap' => 'No wrap',
    'bg_color' => 'Background color',
    'ok' => '   OK   ',
    'cancel' => 'Cancel',
    'left' => 'Left',
    'center' => 'Center',
    'right' => 'Right',
    'top' => 'Top',
    'middle' => 'Middle',
    'bottom' => 'Bottom',
    'baseline' => 'Baseline',
  ),
  'symbols' => array(
    'title' => 'Special characters',
    'ok' => '   OK   ',
    'cancel' => 'Cancel',
  ),
  'templates' => array(
    'title' => 'Templates',
  ),
  'page_prop' => array(
    'title' => 'Page properties',
    'title_tag' => 'Title',
    'charset' => 'Charset',
    'background' => 'Background image',
    'bgcolor' => 'Background color',
    'text' => 'Text color',
    'link' => 'Link color',
    'vlink' => 'Visited link color',
    'alink' => 'Active link color',
    'leftmargin' => 'Left margin',
    'topmargin' => 'Top margin',
    'css_class' => 'CSS class',
    'ok' => '   OK   ',
    'cancel' => 'Cancel',
  ),
  'preview' => array(
    'title' => 'Preview',
  ),
  'image_popup' => array(
    'title' => 'Image popup',
  ),
  'zoom' => array(
    'title' => 'Zoom',
  ),
  'subscript' => array( // <=== new 1.0.7
    'title' => 'Subscript',
  ),
  'superscript' => array( // <=== new 1.0.7
    'title' => 'Superscript',
  ),
);
?>