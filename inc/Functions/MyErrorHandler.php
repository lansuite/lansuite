<?php

/**
 * @param int $errno
 * @param string $errstr
 * @param string $errfile
 * @param int $errline
 * @return bool
 */
function MyErrorHandler($errno, $errstr, $errfile, $errline)
{
    global $PHPErrors, $PHPErrorsFound, $db, $auth;

    // Only show errors, which sould be reported according to error_reporting
    // Also filters @ (for @ will have error_reporting "0")
    // Why this is necessary at all?
    // From the PHP docs of "set_error_handler"
    //      It is important to remember that the standard PHP error handler is completely bypassed for the error types specified by error_types unless the callback function returns FALSE.
    // Source: https://secure.php.net/manual/en/function.set-error-handler.php
    // LanSuite is _at the moment_ (2018-01-13) not PHP Notice free.
    // We are working on this. Once this is done, we can remove the next two
    // conditions and move along.
    // Until this time we have to keep it.
    // Otherwise the system might not be usable at all.
    $rep = ini_get('error_reporting');
    if (!($rep & $errno)) {
        return false;
    }

    if (error_reporting() == 0) {
        return false;
    }

    switch ($errno) {
        case E_ERROR:
            $errors = "Error";
            break;
        case E_WARNING:
            $errors = "Warning";
            break;
        case E_PARSE:
            $errors = "Parse Error";
            break;
        case E_NOTICE:
            $errors = "Notice";
            break;
        case E_CORE_ERROR:
            $errors = "Core Error";
            break;
        case E_CORE_WARNING:
            $errors = "Core Warning";
            break;
        case E_COMPILE_ERROR:
            $errors = "Compile Error";
            break;
        case E_COMPILE_WARNING:
            $errors = "Compile Warning";
            break;
        case E_USER_ERROR:
            $errors = "User Error";
            break;
        case E_USER_WARNING:
            $errors = "User Warning";
            break;
        case E_USER_NOTICE:
            $errors = "User Notice";
            break;
        case E_STRICT:
            $errors = "Strict Notice";
            break;
        case E_RECOVERABLE_ERROR:
            $errors = "Recoverable Error";
            break;
        default:
            if ($errno == E_DEPRECATED) {
                $errors = "Deprecated";
            } elseif ($errno == E_USER_DEPRECATED) {
                $errors = "User Deprecated";
            } else {
                $errors = "Unknown error ($errno)";
            }
    }

    $err = sprintf("PHP %s: %s in %s on line %d", $errors, $errstr, $errfile, $errline);
    if (ini_get('log_errors')) {
        error_log($err);
    }

    $PHPErrors .= $err .'<br />';
    $PHPErrorsFound = 1;

    // Write to DB-Log
    if (isset($db) and $db->success) {
        $db->qry(
            '
            INSERT INTO %prefix%log
            SET date = NOW(),
                userid = %int%,
                type = 3,
                description = %string%,
                sort_tag = "PHP-Fehler"',
            (int) $auth['userid'],
            $err
        );
    }

    return true;
}