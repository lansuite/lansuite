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
$spaw_lang_charset = 'iso-2022-jp';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => '$B@Z$j<h$j(J'
  ),
  'copy' => array(
    'title' => '$B%3%T!<(J'
  ),
  'paste' => array(
    'title' => '$BE=$jIU$1(J'
  ),
  'undo' => array(
    'title' => '$B85$KLa$9(J'
  ),
  'redo' => array(
    'title' => '$B$d$jD>$9(J'
  ),
  'hyperlink' => array(
    'title' => '$B%O%$%Q!<%j%s%/(J'
  ),
  'image_insert' => array(
    'title' => '$B%$%a!<%8$NA^F~(J',
    'select' => ' $BA*Br$9$k(J ',
    'cancel' => '$B%-%c%s%;%k(J',
    'library' => '$B%i%$%V%i%j(J',
    'preview' => '$B%W%l%S%e!<(J',
    'images' => '$B%$%a!<%8(J',
    'upload' => '$B%"%C%W%m!<%I(J',
    'upload_button' => '$B%"%C%W%m!<%I(J',
    'error' => '$B%(%i!<(J',
    'error_no_image' => '$B%$%a!<%8$r;XDj$7$F2<$5$$(J',
    'error_uploading' => '$B%"%C%W%m!<%ICf$K%(%i!<$,5/$3$j$^$7$?!#>/$7$7$F$+$i$b$&0lEY<B9T$7$F$_$F$/$@$5$$!#(J',
    'error_wrong_type' => '$B%$%a!<%8%U%!%$%k$G$O$"$j$^$;$s(J',
    'error_no_dir' => '$B%i%$%V%i%j$,8+$D$+$j$^$;$s(J',
  ),
  'image_prop' => array(
    'title' => '$B%$%a!<%8$N%W%m%Q%F%#(J',
    'ok' => '    OK    ',
    'cancel' => '$B%-%c%s%;%k(J',
    'source' => '$B;2>H@h(J',
    'alt' => '$BBeBX%F%-%9%H(J',
    'align' => '$B9TB7$((J',
    'left' => '$B:8(J',
    'right' => '$B1&(J',
    'top' => '$B>e(J',
    'middle' => '$BCf1{(J',
    'bottom' => '$B2<(J',
    'absmiddle' => '$BCf1{(J($B@dBPE*(J)',
    'texttop' => '$B>e(J($B@dBPE*(J)',
    'baseline' => '$B%Y!<%9%i%$%s(J',
    'width' => '$BI}(J',
    'height' => '$B9b$5(J',
    'border' => '$B%\!<%@!<(J',
    'hspace' => '$B2#4V3V(J',
    'vspace' => '$B=D4V3V(J',
    'error' => '$B%(%i!<(J',
    'error_width_nan' => '$BI}$rF~NO$7$F2<$5$$(J',
    'error_height_nan' => '$B9b$5$rF~NO$7$F2<$5$$(J',
    'error_border_nan' => '$B%\!<%@!<$rF~NO$7$F2<$5$$(J',
    'error_hspace_nan' => '$B2#4V3V$rF~NO$7$F2<$5$$(J',
    'error_vspace_nan' => '$B=D4V3V$rF~NO$7$F2<$5$$(J',
  ),
  'hr' => array(
    'title' => '$B6h@Z$j@~(J'
  ),
  'table_create' => array(
    'title' => '$B%F!<%V%k$N:n@.(J'
  ),
  'table_prop' => array(
    'title' => '$B%F!<%V%k$N%W%m%Q%F%#(J',
    'ok' => '    OK    ',
    'cancel' => '$B%-%c%s%;%k(J',
    'rows' => '$B9T(J',
    'columns' => '$BNs(J',
    'width' => '$BI}(J',
    'height' => '$B9b$5(J',
    'border' => '$B%\!<%@!<(J',
    'pixels' => '$B%T%/%;%k(J',
    'cellpadding' => '$B%;%kFbM>Gr(J',
    'cellspacing' => '$B%;%kFb4V3V(J',
    'bg_color' => '$BGX7J?'(J',
    'error' => '$B%(%i!<(J',
    'error_rows_nan' => '$B9T$rF~NO$7$F2<$5$$(J',
    'error_columns_nan' => '$BNs$rF~NO$7$F2<$5$$(J',
    'error_width_nan' => '$BI}$rF~NO$7$F2<$5$$(J',
    'error_height_nan' => '$B9b$5$rF~NO$7$F2<$5$$(J',
    'error_border_nan' => '$B%\!<%@!<$rF~NO$7$F2<$5$$(J',
    'error_cellpadding_nan' => '$B%;%kFbM>Gr$rF~NO$7$F2<$5$$(J',
    'error_cellspacing_nan' => '$B%;%kFb4V3V$rF~NO$7$F2<$5$$(J',
  ),
  'table_cell_prop' => array(
    'title' => '$B%;%k$N%W%m%Q%F%#(J',
    'horizontal_align' => '$B2#B7$((J',
    'vertical_align' => '$B=DB7$((J',
    'width' => '$BI}(J',
    'height' => '$B9b$5(J',
    'css_class' => 'CSS $B%/%i%9(J',
    'no_wrap' => '$B@^$jJV$5$J$$(J',
    'bg_color' => '$BGX7J?'(J',
    'ok' => '    OK    ',
    'cancel' => '$B%-%c%s%;%k(J',
    'left' => '$B:8(J',
    'center' => '$BCf1{(J',
    'right' => '$B1&(J',
    'top' => '$B>e(J',
    'middle' => '$BCf1{(J',
    'bottom' => '$B2<(J',
    'baseline' => '$B%Y!<%9%i%$%s(J',
    'error' => '$B%(%i!<(J',
    'error_width_nan' => '$BI}$rF~NO$7$F2<$5$$(J',
    'error_height_nan' => '$B9b$5$rF~NO$7$F2<$5$$(J',
  ),
  'table_row_insert' => array(
    'title' => '$B9T$NA^F~(J'
  ),
  'table_column_insert' => array(
    'title' => '$BNs$NA^F~(J'
  ),
  'table_row_delete' => array(
    'title' => '$B9T$N:o=|(J'
  ),
  'table_column_delete' => array(
    'title' => '$BNs$N:o=|(J'
  ),
  'table_cell_merge_right' => array(
    'title' => '$B1&$NNs$H7k9g(J'
  ),
  'table_cell_merge_down' => array(
    'title' => '$B2<$N9T$H7k9g(J'
  ),
  'table_cell_split_horizontal' => array(
    'title' => '$B9T$rJ,3d(J'
  ),
  'table_cell_split_vertical' => array(
    'title' => '$BNs$rJ,3d(J'
  ),
  'style' => array(
    'title' => '$B%9%?%$%k(J'
  ),
  'font' => array(
    'title' => '$B%U%)%s%H(J'
  ),
  'fontsize' => array(
    'title' => '$B%5%$%:(J'
  ),
  'paragraph' => array(
    'title' => '$BCJMn(J'
  ),
  'bold' => array(
    'title' => '$BB@;z(J'
  ),
  'italic' => array(
    'title' => '$B<PBN(J'
  ),
  'underline' => array(
    'title' => '$B2<@~(J'
  ),
  'ordered_list' => array(
    'title' => '$BHV9f%j%9%H(J'
  ),
  'bulleted_list' => array(
    'title' => '$B%j%9%H(J'
  ),
  'indent' => array(
    'title' => '$B%$%s%G%s%HDI2C(J'
  ),
  'unindent' => array(
    'title' => '$B%$%s%G%s%H:o=|(J'
  ),
  'left' => array(
    'title' => '$B:8B7$((J'
  ),
  'center' => array(
    'title' => '$BCf1{B7$((J'
  ),
  'right' => array(
    'title' => '$B1&B7$((J'
  ),
  'fore_color' => array(
    'title' => '$BJ8;z?'(J'
  ),
  'bg_color' => array(
    'title' => '$BGX7J?'(J'
  ),
  'design_tab' => array(
    'title' => 'WYSIWYG ($B%G%6%$%s(J) $B%b!<%I$X(J'
  ),
  'html_tab' => array(
    'title' => 'HTML ($B%3!<%I(J) $B%b!<%I$X(J'
  ),
  'colorpicker' => array(
    'title' => 'Color picker',
    'ok' => '    OK   ',
    'cancel' => '$B%-%c%s%;%k(J',
  ),
  'cleanup' => array(
    'title' => 'HTML$B%/%j!<%s%"%C%W(J ($B%9%?%$%k$N:o=|(J)',
    'confirm' => '$B<B9T$9$k$H!"$9$Y$F$N%9%?%$%k$d%U%)%s%H$d=EJ#$7$?%?%0$r=|5n$7$^$9!#>l9g$K$h$C$F$O$"$J$?$N0U?^$7$J$$7k2L$K$J$k$3$H$b$"$j$^$9$N$G$*5$$rIU$12<$5$$!#(J',
    'ok' => '    OK    ',
    'cancel' => '$B%-%c%s%;%k(J',
  ),
  'toggle_borders' => array(
    'title' => '$B%\!<%@!<$N@Z$jBX$((J',
  ),
  'hyperlink' => array(
    'title' => '$B%O%$%Q!<%j%s%/(J',
    'url' => 'URL',
    'name' => '$B%5%$%HL>(J',
    'target' => '$B%?!<%2%C%H(J',
    'title_attr' => '$B%?%$%H%k(J',
    'ok' => '    OK    ',
    'cancel' => '$B%-%c%s%;%k(J',
  ),
  'table_row_prop' => array(
    'title' => '$B9T$N%W%m%Q%F%#(J',
    'horizontal_align' => '$B2#B7$((J',
    'vertical_align' => '$B=DB7$((J',
    'css_class' => 'CSS $B%/%i%9(J',
    'no_wrap' => '$B@^$jJV$5$J$$(J',
    'bg_color' => '$BGX7J?'(J',
    'ok' => '    OK    ',
    'cancel' => '$B%-%c%s%;%k(J',
    'left' => '$B:8(J',
    'center' => '$BCf1{(J',
    'right' => '$B1&(J',
    'top' => '$B>e(J',
    'middle' => '$BCf1{(J',
    'bottom' => '$B2<(J',
    'baseline' => '$B%Y!<%9%i%$%s(J',
  ),
  'symbols' => array(
    'title' => '$BFC<lJ8;z(J',
    'ok' => '    OK    ',
    'cancel' => '$B%-%c%s%;%k(J',
  ),
  'templates' => array(
    'title' => '$B%F%s%W%l!<%H(J',
  ),
  'page_prop' => array(
    'title' => '$B%Z!<%8$N%W%m%Q%F%#(J',
    'title_tag' => '$B%?%$%H%k(J',
    'charset' => '$BJ8;z%3!<%I(J',
    'background' => '$BGX7J%$%a!<%8(J',
    'bgcolor' => '$BGX7J?'(J',
    'text' => '$BJ8;z?'(J',
    'link' => '$B%j%s%/?'(J',
    'vlink' => '$BK,Ld:Q$_%j%s%/?'(J',
    'alink' => '$B%"%/%F%#%V%j%s%/?'(J',
    'leftmargin' => '$B:8%^!<%8%s(J',
    'topmargin' => '$B>e%^!<%8%s(J',
    'css_class' => 'CSS $B%/%i%9(J',
    'ok' => '    OK    ',
    'cancel' => '$B%-%c%s%;%k(J',
  ),
  'preview' => array(
    'title' => '$B%W%l%S%e!<(J',
  ),
  'image_popup' => array(
    'title' => '$B%$%a!<%8$N%]%C%W%"%C%W(J',
  ),
  'zoom' => array(
    'title' => '$B3HBg(J',
  ),
);
?>

