<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Catalan language file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// Catalan translation: Jordi Cat� (jordi.cata@jc-solutions.net)
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
    'title' => 'Tallar'
  ),
  'copy' => array(
    'title' => 'Copiar'
  ),
  'paste' => array(
    'title' => 'Enganxar'
  ),
  'undo' => array(
    'title' => 'Desfer'
  ),
  'redo' => array(
    'title' => 'Refer'
  ),
  'hyperlink' => array(
    'title' => 'Enlla�'
  ),
  'image_insert' => array(
    'title' => 'Afegir imatge',
    'select' => 'Seleccionar',
    'cancel' => 'Cancelar',
    'library' => 'Llibreria',
    'preview' => 'Previsualitzar',
    'images' => 'Imatges',
    'upload' => 'Pujar imatge',
    'upload_button' => 'Pujar',
    'error' => 'Error',
    'error_no_image' => 'Si us plau, selecciona una imatge',
    'error_uploading' => 'Hi ha hagut un error al pujar la imatge, intenta-ho de nou',
    'error_wrong_type' => 'Tipus de imatge incorrecte.',
    'error_no_dir' => 'La llibreria no existeix', 
  ),
  'image_prop' => array(
    'title' => 'Propietats de la imatge',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
    'source' => 'Codi',
    'alt' => 'text alternatiu',
    'align' => 'Alineaci�',
    'left' => 'esquerra',
    'right' => 'dreta',
    'top' => 'superior',
    'middle' => 'mitg',
    'bottom' => 'inferior',
    'absmiddle' => 'mitg absolut',
    'texttop' => 'text superior',
    'baseline' => 'Linea Base',
    'width' => 'ample',
    'height' => 'Al�ada',
    'border' => 'Contorn',
    'hspace' => 'Espai hor.',
    'vspace' => 'Espaco vert.',
    'error' => 'Error',
    'error_width_nan' => 'la al�ada ha de ser un n�mero',
    'error_height_nan' => 'el ample ha de ser un n�mero',
    'error_border_nan' => 'el contorn ha de ser un n�mero',
    'error_hspace_nan' => 'el espaiat horizontal ha de ser un n�mero',
    'error_vspace_nan' => 'el espaiat vertical ha de ser un n�mero',
  ),
  'hr' => array(
    'title' => 'L�nia horizontal'
  ),
  'table_create' => array(
    'title' => 'Crear taula'
  ),
  'table_prop' => array(
    'title' => 'Propietats de la taula',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
    'rows' => 'Files',
    'columns' => 'Columnes',
    'width' => 'Ample',
    'height' => 'Al�ada',
    'border' => 'Contorn',
    'pixels' => 'pixels', 
    'cellpadding' => 'Contorn de les celes',
    'cellspacing' => 'Espai entre celes',
    'bg_color' => 'Color de fons',
    'error' => 'Error',
    'error_rows_nan' => 'Files ha de ser un n�mero',
    'error_columns_nan' => 'Columnes ha de ser un n�mero',
    'error_width_nan' => 'Ample ha de ser un n�mero',
    'error_height_nan' => 'Al�ada ha de ser un n�mero',
    'error_border_nan' => 'Contorn ha de ser un n�mero',
    'error_cellpadding_nan' => 'Relleno ha de ser un n�mero',
    'error_cellspacing_nan' => 'Espaiat ha de ser un n�mero',
  ),
  'table_cell_prop' => array(
    'title' => 'Propietats de la cela',
    'horizontal_align' => 'Alineaci� horizontal',
    'vertical_align' => 'Alineaci� vertical',
    'width' => 'Ample',
    'height' => 'Al�ada',
    'css_class' => 'Estil CSS',
    'no_wrap' => 'No Dividir Linees',
    'bg_color' => 'Color de fons',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
    'left' => 'Esquerra',
    'center' => 'Centre',
    'right' => 'Dreta',
    'top' => 'Sobre',
    'middle' => 'mitg',
    'bottom' => 'Sota',
    'baseline' => 'L�nea Base',
    'error' => 'Error',
    'error_width_nan' => 'Ample ha de ser un n�mero',
    'error_height_nan' => 'Al�ada ha de ser un n�mero',
    
  ),
  'table_row_insert' => array(
    'title' => 'Insertar fila'
  ),
  'table_column_insert' => array(
    'title' => 'Insertar columna'
  ),
  'table_row_delete' => array(
    'title' => 'Esborrar fila'
  ),
  'table_column_delete' => array(
    'title' => 'Esborrar columna'
  ),
  'table_cell_merge_right' => array(
    'title' => 'Combinar amb la cela de la dreta'
  ),
  'table_cell_merge_down' => array(
    'title' => 'Combinar amb la cela de asota'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'Dividir celes horizontalment'
  ),
  'table_cell_split_vertical' => array(
    'title' => 'Dividir celes verticalment'
  ),
  'style' => array(
    'title' => 'Estil'
  ),
  'font' => array(
    'title' => 'Font'
  ),
  'fontsize' => array(
    'title' => 'Mida'
  ),
  'paragraph' => array(
    'title' => 'Par�graf'
  ),
  'bold' => array(
    'title' => 'Negreta'
  ),
  'italic' => array(
    'title' => 'Cursiva'
  ),
  'underline' => array(
    'title' => 'Subratllat'
  ),
  'ordered_list' => array(
    'title' => 'Llista ordenada'
  ),
  'bulleted_list' => array(
    'title' => 'Llista amb marca'
  ),
  'indent' => array(
    'title' => 'Sangria'
  ),
  'unindent' => array(
    'title' => 'Anular sangria'
  ),
  'left' => array(
    'title' => 'Esquerra'
  ),
  'center' => array(
    'title' => 'Centre'
  ),
  'right' => array(
    'title' => 'Dreta'
  ),
  'fore_color' => array(
    'title' => 'Color de la lletra'
  ),
  'bg_color' => array(
    'title' => 'Color de fons'
  ),
  'design_tab' => array(
    'title' => 'Cambiar a mode WYSIWYG (diseny)'
  ),
  'html_tab' => array(
    'title' => 'Cambiar a mode HTML (codi)'
  ),
  'colorpicker' => array(
    'title' => 'Selecciona color',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
  ),
  // <<<<<<<<< NEW >>>>>>>>>
  'cleanup' => array(
    'title' => 'Esborrar HTML (esborra els estils)',
    'confirm' => 'Amb aquesta acci� s\'esborraran tots els estils, tipus de lletra i tags menys utilizats. Algunes caracter�stiques del teu format poden desapareixer.',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
  ),
  'toggle_borders' => array(
    'title' => 'Cambiar Contorn',
  ),
  'hyperlink' => array(
    'title' => 'Enlla�',
    'url' => 'URL',
    'name' => 'Nom',
    'target' => 'Dest�',
    'title_attr' => 'T�tol',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
  ),
  'table_row_prop' => array(
    'title' => 'Propietats de la fila',
    'horizontal_align' => 'Alineaci� horizontal',
    'vertical_align' => 'Alineaci� vertical',
    'css_class' => 'Classe CSS',
    'no_wrap' => 'Sense separaci�',
    'bg_color' => 'Color de fons',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
    'left' => 'Esquerra',
    'center' => 'Centre',
    'right' => 'Dreta',
    'top' => 'A sobre',
    'middle' => 'Al mitg',
    'bottom' => 'A sota',
    'baseline' => 'L�nea de Base',
  ),
  'symbols' => array(
    'title' => 'car�cters especials',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
  ),
  'templates' => array(
    'title' => 'Plantilles',
  ),
  'page_prop' => array(
    'title' => 'Propietats de la p�gina',
    'title_tag' => 'T�tol',
    'charset' => 'Joc de car�cters',
    'background' => 'Imatge de fons',
    'bgcolor' => 'Color de fons',
    'text' => 'Color text',
    'link' => 'Color enlla�',
    'vlink' => 'Color enlla� visitat',
    'alink' => 'Color enlla� activat',
    'leftmargin' => 'Marge esquerra',
    'topmargin' => 'Marge superior',
    'css_class' => 'Clase CSS',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
  ),
  'preview' => array(
    'title' => 'Previsualitzar',
  ),
  'image_popup' => array(
    'title' => 'Finestra de imatge',
  ),
  'zoom' => array(
    'title' => 'Zoom',
  ),
);
?>

