<?php

/**
 * Magic Debugfunction.
 * You can use it via three different ways.
 *
 * Just an variable. Variables name will be unknown in the output:
 *      d($config);
 *
 * Variable as string. This way the variables name can be written as well:
 *      d('$config');
 *
 * Write the variables name as a string:
 *      d('Any Text', $config);
 *
 * @return void
 */
function d() {
    global $debug, $func;

    $arg_vars = func_get_args();
    if (!isset($debug)) {
        $debug = new debug(1);
    }

    if ($arg_vars[1]) {
        $title = $arg_vars[0];
        $val = $arg_vars[1];

    } elseif (is_string($arg_vars[0]) && substr($arg_vars[0], 0, 1) == '$') {
        $title = $arg_vars[0];
        eval('global '. $arg_vars[0] .'; $val = '. $arg_vars[0] .';');

    } else {
        $title = 'Variable';
        $val = $arg_vars[0];
    }

    $information = $title . ':<br>"' . nl2br(str_replace(' ', '&nbsp;', htmlentities(print_r($val, true)))) . '"';
    $func->information(information, NO_LINK);

    if ($title == 'Variable') {
        if (is_numeric($val)) {
            $title = $val;

        } elseif (is_string($val)) {
            $title = substr($val, 0, 10);

        } else {
            $title = 'No title given';
        }
    }
    $debug->tracker('Debug point: '. $title);
}