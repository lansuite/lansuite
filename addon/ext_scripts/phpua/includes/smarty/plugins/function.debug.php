<?php

function smarty_function_debug($params, &$smarty)
{
    if($params['output']) {
        $smarty->assign('_smarty_debug_output',$params['output']);
    }
    require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.display_debug_console.php');
    return smarty_core_display_debug_console(null, $smarty);
}

/* vim: set expandtab: */

?>