<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Default toolbar data file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-03-22
// ================================================

// array to hold toolbar definitions
// first dimension - toolbar location (top, left, right, bottom)
// second dimension - toolbar row/column
// third dimension - settings/data
// fourth dimension - setting/toolbar item
// toolbar item: name - item name, type - item type (button, dropdown, separator, etc.)

$spaw_toolbar_data = array(
  'top_design' => array(
      array(
        'settings' => array(
          'align' => 'left',
          'valign' => 'top'
        ),
        'data' => array (
            array(
              'name' => 'cut',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'copy',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'paste',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'vertical_separator',
              'type' => SPAW_TBI_IMAGE
            ),
            array(
              'name' => 'undo',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'redo',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'vertical_separator',
              'type' => SPAW_TBI_IMAGE
            ),
            array(
              'name' => 'bold',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'italic',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'underline',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'vertical_separator',
              'type' => SPAW_TBI_IMAGE
            ),
            array(
              'name' => 'left',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'center',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'right',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'justify',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'vertical_separator',
              'type' => SPAW_TBI_IMAGE
            ),
            array(
              'name' => 'indent',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'unindent',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'vertical_separator',
              'type' => SPAW_TBI_IMAGE
            ),
            array(
              'name' => 'fore_color',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'bg_color',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'vertical_separator',
              'type' => SPAW_TBI_IMAGE
            ),
            array(
              'name' => 'superscript',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'subscript',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'vertical_separator',
              'type' => SPAW_TBI_IMAGE
            ),
            array(
              'name' => 'cleanup',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'toggle_borders',
              'type' => SPAW_TBI_BUTTON
            ),
        ) // data
      ),
      array(
        'settings' => array(
          'align' => 'left',
          'valign' => 'top'
        ),
        'data' => array (
            array(
              'name' => 'ordered_list',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'bulleted_list',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'vertical_separator',
              'type' => SPAW_TBI_IMAGE
            ),
            array(
              'name' => 'hyperlink',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'image_insert',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'image_prop',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'image_popup',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'hr',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'vertical_separator',
              'type' => SPAW_TBI_IMAGE
            ),
            array(
              'name' => 'table_create',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'table_prop',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'table_cell_prop',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'table_row_insert',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'table_column_insert',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'table_row_delete',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'table_column_delete',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'table_cell_merge_right',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'table_cell_merge_down',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'table_cell_split_horizontal',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'table_cell_split_vertical',
              'type' => SPAW_TBI_BUTTON
            ),
        ) // data
      ),
      array(
        'settings' => array(
          'align' => 'left',
          'valign' => 'top'
        ),
        'data' => array (
            array(
              'name' => 'style',
              'type' => SPAW_TBI_DROPDOWN
            ),
            array(
              'name' => 'vertical_separator',
              'type' => SPAW_TBI_IMAGE
            ),
            array(
              'name' => 'font',
              'type' => SPAW_TBI_DROPDOWN,
            ),
            array(
              'name' => 'vertical_separator',
              'type' => SPAW_TBI_IMAGE
            ),
            array(
              'name' => 'fontsize',
              'type' => SPAW_TBI_DROPDOWN,
            ),
            array(
              'name' => 'vertical_separator',
              'type' => SPAW_TBI_IMAGE
            ),
            array(
              'name' => 'paragraph',
              'type' => SPAW_TBI_DROPDOWN,
            ),
        ),
      ),
  ),
  'bottom_design' => array(
      array(
        'settings' => array(
          'align' => 'right',
          'valign' => 'top'
        ),
        'data' => array (
            array(
              'name' => 'design_tab_on',
              'type' => SPAW_TBI_IMAGE
            ),
            array(
              'name' => 'html_tab',
              'type' => SPAW_TBI_BUTTON
            ),
        ) // data
      )
  ),

  'bottom_html' => array(
      array(
        'settings' => array(
          'align' => 'right',
          'valign' => 'top'
        ),
        'data' => array (
            array(
              'name' => 'design_tab',
              'type' => SPAW_TBI_BUTTON
            ),
            array(
              'name' => 'html_tab_on',
              'type' => SPAW_TBI_IMAGE
            ),
        ) // data
      )
  ),
);
?>
