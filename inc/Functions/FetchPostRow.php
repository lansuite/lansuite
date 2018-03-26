<?php

/**
 * @param string $text
 * @return string
 */
function FetchPostRow($text) {
    global $func, $line;

    $ret = '<span id="post' . $line['commentid'] . '">' . $func->text2html($text) . '</span>';
    if ($line['signature']) {
        $ret .= '<hr size="1" width="100%" color="cccccc">';
        $ret .= $func->text2html($line['signature']);
    }

    return $ret;
}