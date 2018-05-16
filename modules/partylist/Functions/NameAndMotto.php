<?php

/**
 * @param string $name
 * @return string
 */
function NameAndMotto($name)
{
    global $line;

    return $name .HTML_NEWLINE. $line['motto'];
}
