<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Spanish language file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// Spanish translation: Xavi Gil (dgil@tinet.org)
// corrected by: Antonio Paez (antonio@pentaedro.com)
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
    'title' => 'Cortar'
  ),
  'copy' => array(
    'title' => 'Copiar'
  ),
  'paste' => array(
    'title' => 'Pegar'
  ),
  'undo' => array(
    'title' => 'Deshacer'
  ),
  'redo' => array(
    'title' => 'Rehacer'
  ),
  'hyperlink' => array(
    'title' => 'Enlace'
  ),
  'image_insert' => array(
    'title' => 'Insertar imagen',
    'select' => 'Seleccionar',
    'cancel' => 'Cancelar',
    'library' => 'Libreria',
    'preview' => 'Previsualizar',
    'images' => 'Imágenes',
    'upload' => 'Subir imagen',
    'upload_button' => 'Subir',
    'error' => 'Error',
    'error_no_image' => 'Por favor, selecciona una imagen',
    'error_uploading' => 'Ocurrió un error al subir la imagen, inténtelo de nuevo',
    'error_wrong_type' => 'Tipo de imagen incorrecto.',
    'error_no_dir' => 'La librería no existe', 
  ),
  'image_prop' => array(
    'title' => 'Propiedades de la imagen',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
    'source' => 'Código',
    'alt' => 'Texto alternativo',
    'align' => 'Alineación',
    'left' => 'izquierda',
    'right' => 'derecha',
    'top' => 'superior',
    'middle' => 'medio',
    'bottom' => 'inferior',
    'absmiddle' => 'medio absoluto',
    'texttop' => 'Texto superior',
    'baseline' => 'Línea Base',
    'width' => 'Ancho',
    'height' => 'Alto',
    'border' => 'Borde',
    'hspace' => 'Espacio hor.',
    'vspace' => 'Espaco vert.',
    'error' => 'Error',
    'error_width_nan' => 'la altura debe ser un número',
    'error_height_nan' => 'el ancho debe ser un número',
    'error_border_nan' => 'el borde debe ser un número',
    'error_hspace_nan' => 'el espaciado horizontal debe ser un número',
    'error_vspace_nan' => 'la espaciado vertical debe ser un número',
  ),
  'hr' => array(
    'title' => 'Línea horizontal'
  ),
  'table_create' => array(
    'title' => 'Crear tabla'
  ),
  'table_prop' => array(
    'title' => 'Propiedades de la tabla',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
    'rows' => 'Filas',
    'columns' => 'Columnas',
    'width' => 'Ancho',
    'height' => 'Alto',
    'border' => 'Borde',
    'pixels' => 'pixels', 
    'cellpadding' => 'Borde de las celdas',
    'cellspacing' => 'Espacio entre celdas',
    'bg_color' => 'Color de fondo',
    'error' => 'Error',
    'error_rows_nan' => 'Filas debe ser un número',
    'error_columns_nan' => 'Columnas debe ser un número',
    'error_width_nan' => 'Ancho debe ser un número',
    'error_height_nan' => 'Alto debe ser un número',
    'error_border_nan' => 'Borde debe ser un número',
    'error_cellpadding_nan' => 'Relleno debe ser un número',
    'error_cellspacing_nan' => 'Espaciado debe ser un número',
  ),
  'table_cell_prop' => array(
    'title' => 'Propiedades de la celda',
    'horizontal_align' => 'Alineación horizontal',
    'vertical_align' => 'Alineación vertical',
    'width' => 'Ancho',
    'height' => 'Alto',
    'css_class' => 'Estilo CSS',
    'no_wrap' => 'No Dividir Líneas',
    'bg_color' => 'Color de fondo',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
    'left' => 'Izquierda',
    'center' => 'Centro',
    'right' => 'Derecha',
    'top' => 'Arriba',
    'middle' => 'Medio',
    'bottom' => 'Abajo',
    'baseline' => 'Línea Base',
    'error' => 'Error',
    'error_width_nan' => 'Ancho debe ser un número',
    'error_height_nan' => 'Alto debe ser un número',
    
  ),
  'table_row_insert' => array(
    'title' => 'Insertar fila'
  ),
  'table_column_insert' => array(
    'title' => 'Insertar columna'
  ),
  'table_row_delete' => array(
    'title' => 'Borrar fila'
  ),
  'table_column_delete' => array(
    'title' => 'Borrar columna'
  ),
  'table_cell_merge_right' => array(
    'title' => 'Combinar con la celda de la derecha'
  ),
  'table_cell_merge_down' => array(
    'title' => 'Combinar con la celda de abajo'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'Dividir celdas horizontalmente'
  ),
  'table_cell_split_vertical' => array(
    'title' => 'Dividir celdas verticalmente'
  ),
  'style' => array(
    'title' => 'Estilo'
  ),
  'font' => array(
    'title' => 'Fuente'
  ),
  'fontsize' => array(
    'title' => 'Tamaño'
  ),
  'paragraph' => array(
    'title' => 'Párrafo'
  ),
  'bold' => array(
    'title' => 'Negrita'
  ),
  'italic' => array(
    'title' => 'Cursiva'
  ),
  'underline' => array(
    'title' => 'Subrayado'
  ),
  'ordered_list' => array(
    'title' => 'Lista ordenada'
  ),
  'bulleted_list' => array(
    'title' => 'Lista con marca'
  ),
  'indent' => array(
    'title' => 'Sangria'
  ),
  'unindent' => array(
    'title' => 'Anular sangria'
  ),
  'left' => array(
    'title' => 'Izquierda'
  ),
  'center' => array(
    'title' => 'Centro'
  ),
  'right' => array(
    'title' => 'Derecha'
  ),
  'fore_color' => array(
    'title' => 'Color de la letra'
  ),
  'bg_color' => array(
    'title' => 'Color de fondo'
  ),
  'design_tab' => array(
    'title' => 'Cambiar a modo WYSIWYG (diseño)'
  ),
  'html_tab' => array(
    'title' => 'Cambiar a modo HTML (código)'
  ),
  'colorpicker' => array(
    'title' => 'Selecciona color',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
  ),
  // <<<<<<<<< NEW >>>>>>>>>
  'cleanup' => array(
    'title' => 'Limipiador de HTML (borra los estilos)',
    'confirm' => 'Con esta acción se borrarán todos los estilos, tipos de letra y tags menos utilizados. Algunas características de tu formato pueden desaparecer.',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
  ),
  'toggle_borders' => array(
    'title' => 'Cambiar Bordes',
  ),
  'hyperlink' => array(
    'title' => 'Enlace',
    'url' => 'URL',
    'name' => 'Nombre',
    'target' => 'Destino',
    'title_attr' => 'Título',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
  ),
  'table_row_prop' => array(
    'title' => 'Propiedades de la fila',
    'horizontal_align' => 'Alineación horizontal',
    'vertical_align' => 'Alineación vertical',
    'css_class' => 'Clase CSS',
    'no_wrap' => 'Sin separación',
    'bg_color' => 'Color de fondo',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
    'left' => 'Izquierda',
    'center' => 'Centro',
    'right' => 'Derecha',
    'top' => 'Arriba',
    'middle' => 'Al medio',
    'bottom' => 'Abajo',
    'baseline' => 'Línea de Base',
  ),
  'symbols' => array(
    'title' => 'Caracteres especiales',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
  ),
  'templates' => array(
    'title' => 'Plantillas',
  ),
  'page_prop' => array(
    'title' => 'Propiedades de la página',
    'title_tag' => 'Título',
    'charset' => 'Juego de caracteres',
    'background' => 'Imagen de fondo',
    'bgcolor' => 'Color de fondo',
    'text' => 'Color texto',
    'link' => 'Color enlaces',
    'vlink' => 'Color enlace visitado',
    'alink' => 'Color enlace activado',
    'leftmargin' => 'Margen izquierdo',
    'topmargin' => 'Margen superior',
    'css_class' => 'Clase CSS',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
  ),
  'preview' => array(
    'title' => 'Previsualizar',
  ),
  'image_popup' => array(
    'title' => 'Ventana de Imagen',
  ),
  'zoom' => array(
    'title' => 'Zoom',
  ),
);
?>

