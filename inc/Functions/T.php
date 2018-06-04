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
    global $db, $translation, $func, $translation_no_html_replace;

    // Prepare function parameters
    // First argument is the input string, the following are parameters
    $args = func_get_args();
    $input = (string) array_shift($args);
    foreach ($args as $CurrentArg) {
        // If second Parameter is Array (old Style)
        if (!is_array($CurrentArg)) {
            $parameters[] = $CurrentArg;
        } else {
            $parameters = $CurrentArg;
        }
    }

    if ($input == '') {
        return '';
    }

    $key = md5($input);
    $module = '';
    if (isset($_GET['mod']) && $_GET['mod']) {
        $module = $_GET['mod'];
    }

    $trans_text = '';
    if (strlen($input) > 255) {
        $long = '_long';
    } else {
        $long = '';
    }

    if (array_key_exists($module, $translation->lang_cache) && $translation->lang_cache[$module][$key] != '') {
        // Already in memory cache ($this->lang_cache[key])
        $output = $translation->ReplaceParameters($translation->lang_cache[$module][$key], $parameters, $key);
    } else {
        // Try to read from DB
        if ($translation->language == 'de') {
            // All texts in source are in german at the moment
            $output = $translation->ReplaceParameters($input, $parameters, $key);
        } else {
            if ($db->success) {
                $trans_text = $translation->get_trans_db($key, $_GET['mod'], $long);
            }

            // If ok replace parameter
            if ($trans_text != '' && $trans_text != null) {
                $output = $translation->ReplaceParameters($trans_text, $parameters);

            // If any problem on get translations just return $input
            } else {
                $output = $translation->ReplaceParameters($input, $parameters, $key);
            }
        }
    }

    if ($translation_no_html_replace) {
        $translation_no_html_replace = false;

        // Deprecated. Should be replaced in t() by '<', '>' and '[br]'
        $output = str_replace('--lt--', '<', $output);
        $output = str_replace('--gt--', '>', $output);
        $output = str_replace('HTML_NEWLINE', '<br />', $output);

        return $func->text2html($output, 4);
    }

    return $output;
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
