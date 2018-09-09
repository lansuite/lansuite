<?php

/**
 * @param string $name
 * @return string
 */
function NameAndDesc($name)
{
    global $line, $auth;

    if ($line['board_group']) {
        $group = '<b>'. $line['board_group'] .'</b> - ';
    }

    return '<img src="design/'. $auth['design'] .'/images/arrows_forum.gif" hspace="3" align="left" border="0">'. $group .'<b>'. $name .'</b><br />' . $line['description'];
}
