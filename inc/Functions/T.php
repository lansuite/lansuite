<?php

$translation_no_html_replace = false;

/**
 * Translates the given string into the selected Language.
 *
 * The first argument is the string to translate, the following are parameters.
 * Parameters are placeholders for dynamic data.
 *
 * Example:
 *      t('Your name is %1 and your Email is %2', $row['name'], $row['email'])
 *
 * Important is to use %1, %2, %3 for dynamic data.
 *
 * @return string
 */
function t()
{
    global $translation;

    $args = func_get_args();
    return $translation->translate($args);
}

function t_no_html()
{
    global $translation_no_html_replace;

    $args = func_get_args();
    $input = (string) array_shift($args);
    $translation_no_html_replace = true;

    $output = t($input, $args);
    $translation_no_html_replace = false;

    return $output;
}
