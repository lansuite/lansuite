<?php

function smarty_block_textformat($params, $content, &$smarty)
{
    if (is_null($content)) {
        return;
    }

    $style = null;
    $indent = 0;
    $indent_first = 0;
    $indent_char = ' ';
    $wrap = 80;
    $wrap_char = "\n";
    $wrap_cut = false;
    $assign = null;
    
    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'style':
            case 'indent_char':
            case 'wrap_char':
            case 'assign':
                $$_key = (string)$_val;
                break;

            case 'indent':
            case 'indent_first':
            case 'wrap':
                $$_key = (int)$_val;
                break;

            case 'wrap_cut':
                $$_key = (bool)$_val;
                break;

            default:
                $smarty->trigger_error("textformat: unknown attribute '$_key'");
        }
    }

    if ($style == 'email') {
        $wrap = 72;
    }

    // split into paragraphs
    $paragraphs = preg_split('![\r\n][\r\n]!',$content);
    $output = '';

    foreach ($paragraphs as $paragraph) {
        if ($paragraph == '') {
            continue;
        }
        // convert mult. spaces & special chars to single space
        $paragraph = preg_replace(array('!\s+!','!(^\s+)|(\s+$)!'),array(' ',''),$paragraph);
        // indent first line
        if($indent_first > 0) {
            $paragraph = str_repeat($indent_char,$indent_first) . $paragraph;
        }
        // wordwrap sentences
        $paragraph = wordwrap($paragraph, $wrap - $indent, $wrap_char, $wrap_cut);
        // indent lines
        if($indent > 0) {
            $paragraph = preg_replace('!^!m',str_repeat($indent_char,$indent),$paragraph);
        }
        $output .= $paragraph . $wrap_char . $wrap_char;
    }

    if ($assign) {
        $smarty->assign($assign,$output);
    } else {
        return $output;
    }
}

/* vim: set expandtab: */

?>