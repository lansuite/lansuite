<?php

function smarty_function_html_table($params, &$smarty)
{
    $table_attr = 'border="1"';
    $tr_attr = '';
    $td_attr = '';
    $cols = 3;
    $rows = 3;
    $trailpad = '&nbsp;';
    $vdir = 'down';
    $hdir = 'right';
    $inner = 'cols';

    if (!isset($params['loop'])) {
        $smarty->trigger_error("html_table: missing 'loop' parameter");
        return;
    }

    foreach ($params as $_key=>$_value) {
        switch ($_key) {
            case 'loop':
                $$_key = (array)$_value;
                break;

            case 'cols':
            case 'rows':
                $$_key = (int)$_value;
                break;

            case 'table_attr':
            case 'trailpad':
            case 'hdir':
            case 'vdir':
                $$_key = (string)$_value;
                break;

            case 'tr_attr':
            case 'td_attr':
                $$_key = $_value;
                break;
        }
    }

    $loop_count = count($loop);
    if (empty($params['rows'])) {
        /* no rows specified */
        $rows = ceil($loop_count/$cols);
    } elseif (empty($params['cols'])) {
        if (!empty($params['rows'])) {
            /* no cols specified, but rows */
            $cols = ceil($loop_count/$rows);
        }
    }

    $output = "<table $table_attr>\n";

    for ($r=0; $r<$rows; $r++) {
        $output .= "<tr" . smarty_function_html_table_cycle('tr', $tr_attr, $r) . ">\n";
        $rx =  ($vdir == 'down') ? $r*$cols : ($rows-1-$r)*$cols;

        for ($c=0; $c<$cols; $c++) {
            $x =  ($hdir == 'right') ? $rx+$c : $rx+$cols-1-$c;
            if ($inner!='cols') {
                /* shuffle x to loop over rows*/
                $x = floor($x/$cols) + ($x%$cols)*$rows;
            }

            if ($x<$loop_count) {
                $output .= "<td" . smarty_function_html_table_cycle('td', $td_attr, $c) . ">" . $loop[$x] . "</td>\n";
            } else {
                $output .= "<td" . smarty_function_html_table_cycle('td', $td_attr, $c) . ">$trailpad</td>\n";
            }
        }
        $output .= "</tr>\n";
    }
    $output .= "</table>\n";
    
    return $output;
}

function smarty_function_html_table_cycle($name, $var, $no) {
    if(!is_array($var)) {
        $ret = $var;
    } else {
        $ret = $var[$no % count($var)];
    }
    
    return ($ret) ? ' '.$ret : '';
}


/* vim: set expandtab: */

?>