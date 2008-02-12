<?php

function smarty_function_popup_init($params, &$smarty)
{
    $zindex = 1000;
    
    if (!empty($params['zindex'])) {
        $zindex = $params['zindex'];
    }
    
    if (!empty($params['src'])) {
        return '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:'.$zindex.';"></div>' . "\n"
         . '<script type="text/javascript" language="JavaScript" src="'.$params['src'].'"></script>' . "\n";
    } else {
        $smarty->trigger_error("popup_init: missing src parameter");
    }
}

/* vim: set expandtab: */

?>