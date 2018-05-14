<?php

/**
 * Used as a callback function for MasterSearch2 class.
 *
 * @param string $text
 * @return string
 */
function Text2LSCode($text)
{
    global $func;

    return $func->text2html($text);
}
