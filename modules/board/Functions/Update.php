<?php

/**
 * @param int $id
 * @return bool
 */
function Update($id)
{
    if (!$_POST['board_group']) {
        $_POST['board_group'] = $_POST['group_new'];
    }

    return true;
}
