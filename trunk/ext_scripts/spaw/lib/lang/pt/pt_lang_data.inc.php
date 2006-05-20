<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Portuguese language file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// Brazilian Translation: Fernando Jos� Karl, 
//                        fernandokarl@superig.com.br
// European Portuguese version: Ricardo Vidal
//                              rick@vidric.com
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-04-29
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
    'title' => 'Colar'
  ),
  'undo' => array(
    'title' => 'Desfazer'
  ),
  'redo' => array(
    'title' => 'Refazer'
  ),
  'hyperlink' => array(
    'title' => 'Hiperliga��o'
  ),
  'image_insert' => array(
    'title' => 'Inserir imagem',
    'select' => 'Seleccionar',
    'cancel' => 'Cancelar',
    'library' => 'Biblioteca',
    'preview' => 'Pr�-visualiza��o',
    'images' => 'Imagens',
    'upload' => 'Enviar imagem',
    'upload_button' => 'Upload',
    'error' => 'Erro',
    'error_no_image' => 'Por favor, seleccione uma imagem',
    'error_uploading' => 'Ocorreu um erro no envio do arquivo. Por favor, tente novamente',
    'error_wrong_type' => 'Tipo de arquivo de imagem inv�lido',
    'error_no_dir' => 'A bilbioteca n�o existe fisicamente',
  ),
  'image_prop' => array(
    'title' => 'Propriedades da imagem',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
    'source' => 'Fonte',
    'alt' => 'Texto alternativo',
    'align' => 'Alinhamento',
    'left' => 'esquerda',
    'right' => 'direita',
    'top' => 'superior',
    'middle' => 'meio',
    'bottom' => 'inferior',
    'absmiddle' => 'Meio absoluto',
    'texttop' => 'Topo do texto',
    'baseline' => 'Base',
    'width' => 'Comprimento',
    'height' => 'Altura',
    'border' => 'Borda',
    'hspace' => 'Espa�o hor.',
    'vspace' => 'Espa�o vert.',
    'error' => 'Erro',
    'error_width_nan' => 'Comprimento n�o � um n�mero',
    'error_height_nan' => 'Altura n�o � um n�mero',
    'error_border_nan' => 'Borda n�o � um n�mero',
    'error_hspace_nan' => 'Espa�o horizontal n�o � um n�mero',
    'error_vspace_nan' => 'Espa�o vertical n�o � um n�mero',
  ),
  'hr' => array(
    'title' => 'Linha horizontal'
  ),
  'table_create' => array(
    'title' => 'Criar tabela'
  ),
  'table_prop' => array(
    'title' => 'Propriedades da tabela',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
    'rows' => 'Linhas',
    'columns' => 'Colunas',
    'width' => 'Comprimento',
    'height' => 'Altura',
    'border' => 'Borda',
    'pixels' => 'pixeis',
    'cellpadding' => 'Espa�o dentro das c�lulas',
    'cellspacing' => 'Espa�o entre c�lulas',
    'bg_color' => 'Cor de Fundo',
    'error' => 'Erro',
    'error_rows_nan' => 'Linhas n�o � um n�mero',
    'error_columns_nan' => 'Colunas n�o � um n�mero',
    'error_width_nan' => 'Comprimento n�o � um n�mero',
    'error_height_nan' => 'Altura n�o � um n�mero',
    'error_border_nan' => 'Borda n�o � um n�mero',
    'error_cellpadding_nan' => 'Espa�o dentro das c�lulas n�o � um n�mero',
    'error_cellspacing_nan' => 'Espa�o entre c�lulas n�o � um n�mero',
  ),
  'table_cell_prop' => array(
    'title' => 'Propriedades da c�lula',
    'horizontal_align' => 'Alinh. horizontal',
    'vertical_align' => 'Alinh. vertical',
    'width' => 'Comprimento',
    'height' => 'Altura',
    'css_class' => 'Classe CSS',
    'no_wrap' => 'Sem quebras',
    'bg_color' => 'Cor de fundo',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
    'left' => 'Esquerda',
    'center' => 'Centralizado',
    'right' => 'Direita',
    'top' => 'Superior',
    'middle' => 'Meio',
    'bottom' => 'Inferior',
    'baseline' => 'Base',
    'error' => 'Erro',
    'error_width_nan' => 'Comprimento n�o � um n�mero',
    'error_height_nan' => 'Altura n�o � um n�mero',
    
  ),
  'table_row_insert' => array(
    'title' => 'Inserir linha'
  ),
  'table_column_insert' => array(
    'title' => 'Inserir coluna'
  ),
  'table_row_delete' => array(
    'title' => 'Apagar linha'
  ),
  'table_column_delete' => array(
    'title' => 'Apagar coluna'
  ),
  'table_cell_merge_right' => array(
    'title' => 'Unir direita'
  ),
  'table_cell_merge_down' => array(
    'title' => 'Unir abaixo'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'Dividir c�lulas horizontalmente'
  ),
  'table_cell_split_vertical' => array(
    'title' => 'Dividir c�lulas verticalmente'
  ),
  'style' => array(
    'title' => 'Estilo'
  ),
  'font' => array(
    'title' => 'Fonte'
  ),
  'fontsize' => array(
    'title' => 'Tamanho'
  ),
  'paragraph' => array(
    'title' => 'Par�grafo'
  ),
  'bold' => array(
    'title' => 'Negrito'
  ),
  'italic' => array(
    'title' => 'It�lico'
  ),
  'underline' => array(
    'title' => 'Sublinhado'
  ),
  'ordered_list' => array(
    'title' => 'Numera��o'
  ),
  'bulleted_list' => array(
    'title' => 'Marcadores'
  ),
  'indent' => array(
    'title' => 'Aumentar Recuo'
  ),
  'unindent' => array(
    'title' => 'Diminuir Recuo'
  ),
  'left' => array(
    'title' => 'Esquerda'
  ),
  'center' => array(
    'title' => 'Centralizado'
  ),
  'right' => array(
    'title' => 'Direita'
  ),
  'fore_color' => array(
    'title' => 'Real�ar'
  ),
  'bg_color' => array(
    'title' => 'Cor de fundo'
  ),
  'design_tab' => array(
    'title' => 'Mudar para modo WYSIWYG (design)'
  ),
  'html_tab' => array(
    'title' => 'Mudar para modo HTML (c�digo)'
  ),
  'colorpicker' => array(
    'title' => 'Selector de cores',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
  ),
  // <<<<<<<<<< NEW >>>>>>>>>>>>>>>
  'cleanup' => array(
    'title' => 'Limpeza HTML (remover estilos)',
    'confirm' => 'Ao realizar esta ac��o vai remover todos estilos, fontes e tags in�teis do conte�do. Alguma ou toda a formata��o poder� ser perdida.',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
  ),
  'toggle_borders' => array(
    'title' => 'Accionar borda',
  ),
  'hyperlink' => array(
    'title' => 'Hiperliga��o',
    'url' => 'URL',
    'name' => 'Nome',
    'target' => 'Alvo',
    'title_attr' => 'T�tulo',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
  ),
  'table_row_prop' => array(
    'title' => 'Propriedades da linha',
    'horizontal_align' => 'Alinhamento horizontal',
    'vertical_align' => 'Alinhamento vertical',
    'css_class' => 'Classe CSS',
    'no_wrap' => 'Sem quebras',
    'bg_color' => 'Cor de Fundo',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
    'left' => 'Esquerda',
    'center' => 'Center',
    'right' => 'Direita',
    'top' => 'Topo',
    'middle' => 'Meio',
    'bottom' => 'Inferior',
    'baseline' => 'Base',
  ),
  'symbols' => array(
    'title' => 'Caracteres especiais',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
  ),
  'templates' => array(
    'title' => 'Modelos',
  ),
  'page_prop' => array(
    'title' => 'Propriedades da p�gina',
    'title_tag' => 'T�tulo',
    'charset' => 'Conjunto Caracteres',
    'background' => 'Imagem de Fundo',
    'bgcolor' => 'Cor de Fundo',
    'text' => 'Cor texto',
    'link' => 'Cor link',
    'vlink' => 'Cor link visitados',
    'alink' => 'Cor link activo',
    'leftmargin' => 'Margem esquerda',
    'topmargin' => 'Margem topo',
    'css_class' => 'Classe CSS',
    'ok' => '   OK   ',
    'cancel' => 'Cancelar',
  ),
  'preview' => array(
    'title' => 'Pr�-visualiza��o',
  ),
  'image_popup' => array(
    'title' => 'Imagem popup',
  ),
  'zoom' => array(
    'title' => 'Zoom',
  ),
);
?>


