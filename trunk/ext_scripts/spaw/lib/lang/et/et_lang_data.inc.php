<?php
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// English language file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// Estonian translation: Maku, maktak@phpnuke-est.net
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
    'title' => 'L�ike'
  ),
  'copy' => array(
    'title' => 'Kopeeri'
  ),
  'paste' => array(
    'title' => 'Kleebi'
  ),
  'undo' => array(
    'title' => 'Samm Tagasi'
  ),
  'redo' => array(
    'title' => 'Samm Edasi'
  ),
  'hyperlink' => array(
    'title' => 'H�perlink'
  ),
  'image_insert' => array(
    'title' => 'Lisa Pilt',
    'select' => 'Vali',
    'cancel' => 'Loobu',
    'library' => 'Teek',
    'preview' => 'Eelvaade',
    'images' => 'Pildid',
    'upload' => 'Pildi �leslaadimine',
    'upload_button' => '�leslaadimine',
    'error' => 'Viga',
    'error_no_image' => 'Palun valige pilt',
    'error_uploading' => 'Viga faili �leslaadimisega. Proovige hiljem uuesti',
    'error_wrong_type' => 'Valge pildi failit��p',
    'error_no_dir' => 'Teek ei eksisteeri f��siliselt',
  ),
  'image_prop' => array(
    'title' => 'Pildi Seaded',
    'ok' => '   OK   ',
    'cancel' => 'Loobu',
    'source' => 'L�he',
    'alt' => 'Alternatiivne Tekst',
    'align' => 'Joondamine',
    'left' => 'vasak',
    'right' => 'parem',
    'top' => '�lal',
    'middle' => 'keskel',
    'bottom' => 'p�hjas',
    'absmiddle' => 'absmiddle',
    'texttop' => 'texttop',
    'baseline' => '��rejoon',
    'width' => 'Laius',
    'height' => 'K�rgus',
    'border' => 'Serv',
    'hspace' => 'Hor. vahe',
    'vspace' => 'Vert. vahe',
    'error' => 'Viga',
    'error_width_nan' => 'Laius ei ole number',
    'error_height_nan' => 'K�rgus ei ole number',
    'error_border_nan' => 'Serv ei ole number',
    'error_hspace_nan' => 'Horisontaalide vahe ei ole number',
    'error_vspace_nan' => 'Vertikaalide vahe ei ole number',
  ),
  'hr' => array(
    'title' => 'Horisontaalide Reegel'
  ),
  'table_create' => array(
    'title' => 'Loo tabel'
  ),
  'table_prop' => array(
    'title' => 'Tabeli seaded',
    'ok' => '   OK   ',
    'cancel' => 'Loobu',
    'rows' => 'Ridu',
    'columns' => 'Tulpi',
    'width' => 'Laius',
    'height' => 'K�rgus',
    'border' => 'Serv',
    'pixels' => 'pikselit',
    'cellpadding' => 'Elemendi polsterdus',
    'cellspacing' => 'Elementide vahe',
    'bg_color' => 'Taustav�rv',
    'error' => 'Viga',
    'error_rows_nan' => 'Ridade arv ei ole number',
    'error_columns_nan' => 'Tulpade arv ei ole number',
    'error_width_nan' => 'Laius ei ole number',
    'error_height_nan' => 'K�rgus ei ole number',
    'error_border_nan' => 'Serv ei ole number',
    'error_cellpadding_nan' => 'Elemendi polsterdus ei ole number',
    'error_cellspacing_nan' => 'Elementide vahe ei ole number',
  ),
  'table_cell_prop' => array(
    'title' => 'Elemendi seaded',
    'horizontal_align' => 'Horisontaalne joondamine',
    'vertical_align' => 'Vertikaalne joondamine',
    'width' => 'Laius',
    'height' => 'K�rgus',
    'css_class' => 'CSS klass',
    'no_wrap' => 'M�hkimine v�ljas',
    'bg_color' => 'Tausta v�rv',
    'ok' => '   OK   ',
    'cancel' => 'Loobu',
    'left' => 'Vasakul',
    'center' => 'Keskel',
    'right' => 'Paremal',
    'top' => '�lal',
    'middle' => 'Keskel',
    'bottom' => 'P�hjas',
    'baseline' => '��rejoon',
    'error' => 'Viga',
    'error_width_nan' => 'Laius ei ole number',
    'error_height_nan' => 'K�rgus ei ole number',
  ),
  'table_row_insert' => array(
    'title' => 'Lisa rida'
  ),
  'table_column_insert' => array(
    'title' => 'Lisa tulp'
  ),
  'table_row_delete' => array(
    'title' => 'Kustuta rida'
  ),
  'table_column_delete' => array(
    'title' => 'Kustuta tulp'
  ),
  'table_cell_merge_right' => array(
    'title' => 'Sulandu/�hine paremale'
  ),
  'table_cell_merge_down' => array(
    'title' => 'Sulandu/�hine alla'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'Poolita element horisontaalselt'
  ),
  'table_cell_split_vertical' => array(
    'title' => 'Poolita element vertikaalselt'
  ),
  'style' => array(
    'title' => 'Stiil'
  ),
  'font' => array(
    'title' => 'Kirjastiil'
  ),
  'fontsize' => array(
    'title' => 'Suurus'
  ),
  'paragraph' => array(
    'title' => 'Paragrahv'
  ),
  'bold' => array(
    'title' => 'Rasvane'
  ),
  'italic' => array(
    'title' => 'Kaldkiri'
  ),
  'underline' => array(
    'title' => 'Allajoonitud'
  ),
  'ordered_list' => array(
    'title' => 'Korrap�rane Nimekiri'
  ),
  'bulleted_list' => array(
    'title' => 'T�ppidega Nimekiri'
  ),
  'indent' => array(
    'title' => 'S�vendatud'
  ),
  'unindent' => array(
    'title' => 'S�vendamata'
  ),
  'left' => array(
    'title' => 'Vasakul'
  ),
  'center' => array(
    'title' => 'Keskel'
  ),
  'right' => array(
    'title' => 'Paremal'
  ),
  'fore_color' => array(
    'title' => 'Pealmine v�rv'
  ),
  'bg_color' => array(
    'title' => 'Tausta v�rv'
  ),
  'design_tab' => array(
    'title' => 'L�litu WYSIWYG (kujundus) moodi'
  ),
  'html_tab' => array(
    'title' => 'L�litu HTML (kood) moodi'
  ),
  'colorpicker' => array(
    'title' => 'V�rvivalija',
    'ok' => '   OK   ',
    'cancel' => 'Loobu',
  ),
  'cleanup' => array(
    'title' => 'HTML puhastamine (eemaldab stiilid)',
    'confirm' => 'Selle tegemine eemaldab stiilid, kirjastiilid ja ebavajalikud tag-id, m�ned v�i k�ik vormindused v�ivad kaotsi minna.',
    'ok' => '   OK   ',
    'cancel' => 'Loobu',
  ),
  'toggle_borders' => array(
    'title' => 'Servad',
  ),
  'hyperlink' => array(
    'title' => 'H�perlink',
    'url' => 'URL',
    'name' => 'Nimi',
    'target' => 'Sihtm�rk',
    'title_attr' => 'Tiitel',
    'ok' => '   OK   ',
    'cancel' => 'Loobu',
  ),
  'table_row_prop' => array(
    'title' => 'Rea seaded',
    'horizontal_align' => 'Horisontaalne joondamine',
    'vertical_align' => 'Vertikaalne joondamine',
    'css_class' => 'CSS klass',
    'no_wrap' => 'M�hkimine v�ljas',
    'bg_color' => 'Tausta v�rv',
    'ok' => '   OK   ',
    'cancel' => 'Loobu',
    'left' => 'Vasakul',
    'center' => 'Keskel',
    'right' => 'Paremal',
    'top' => '�lal',
    'middle' => 'Keskel',
    'bottom' => 'P�hjas',
    'baseline' => '��rejoon',
  ),
  'symbols' => array(
    'title' => 'Spetsiaalsed t�hem�rgid',
    'ok' => '   OK   ',
    'cancel' => 'Loobu',
  ),
  'templates' => array(
    'title' => 'Mallid',
  ),
  'page_prop' => array(
    'title' => 'Lehe seaded',
    'title_tag' => 'Tiitel',
    'charset' => 'M�rgistik',
    'background' => 'Taustapilt',
    'bgcolor' => 'Taustav�rv',
    'text' => 'Teksti v�rv',
    'link' => 'Lingi v�rv',
    'vlink' => 'K�lastatud lingi v�rv',
    'alink' => 'Aktiivse lingi v�rv',
    'leftmargin' => 'Piiraja Vasemal',
    'topmargin' => 'Piiraja �lal',
    'css_class' => 'CSS klass',
    'ok' => '   OK   ',
    'cancel' => 'Loobu',
  ),
  'preview' => array(
    'title' => 'Eelvaade',
  ),
  'image_popup' => array(
    'title' => 'Pildi popup',
  ),
  'zoom' => array(
    'title' => 'Suurendus',
  ),
);
?>
