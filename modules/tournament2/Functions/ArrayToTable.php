<?php

/**
 * @param array $a
 * @return string
 */
function array_to_table($a)
{
    if (!empty($a)) {
        function remove($var)
        {
            if ($var=='cellstyle') {
                return false;
            } else {
                return true;
            }
        }
        $colums=array_filter(array_keys($a[0]), "remove");
        $t='<div overflow:auto;"><table style="width:100%;" border="0" cellspacing="0" cellpadding="2">';
        $t.='<tr><th class="mastersearch2_result_row_key" style="border-bottom: 1px solid #000000;">'.implode('</th><th class="mastersearch2_result_row_key" style="border-bottom: 1px solid #000000;">', $colums).'</th></tr>';
        foreach ($a as $row) {
            $cellstyle = $row['cellstyle'];
            unset($row['cellstyle']);
            $t.= '<tr><td style="border-bottom: 1px solid #000000;'.$cellstyle.'">'.implode('</td><td style="border-bottom: 1px solid #000000;'.$cellstyle.'">', $row).'</td></tr>';
        }
        $t.='</table></div>';
    } else {
        $t = '';
    }
    return $t;
}
