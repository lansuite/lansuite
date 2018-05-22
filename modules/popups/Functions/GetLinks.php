<?php

/**
 * @param string $caption
 * @param string $mod
 * @param string $table
 * @param string $id
 * @param string $name
 * @param string $link
 * @return void
 */
function GetLinks($caption, $mod, $table, $id, $name, $link)
{
    global $func, $db, $dsp;

    if ($func->isModActive($mod)) {
        $out = '<select name="link" onChange="javascript:if (this.options[this.selectedIndex].value != \'\') InsertCode(opener.document.'. $_GET['form'] .'.'. $_GET['textarea'] .', \'[url='. $link .'\' + this.options[this.selectedIndex].value + \']\', \'[/url]\')">';
        $out .= '<option value="">'. t('Bitte Link auswählen') .'</option>';
        $res = $db->qry("SELECT %plain%, %plain% FROM %prefix%%plain%", $id, $name, $table);
        while ($row = $db->fetch_array($res)) {
            $out .= '<option value="'. $row[$id] .'">'. $row[$name] .'</option>';
        }
        $out .= '</select>';
        $dsp->AddDoubleRow($caption, $out);
    }
}
