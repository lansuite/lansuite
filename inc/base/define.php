<?php

/**
 * Version of LANSuite
 */
define('LANSUITE_VERSION', '5.0-dev');

define('LANSUITE_MINIMUM_PHP_VERSION', '8.1.0');
define('LANSUITE_MINIMUM_MYSQL_VERSION', '5.7.0');

define('VALID_LS', true);

define('HTML_NEWLINE', '<br/>');
define('HTML_FONT_ERROR', '<font class="error">');
define('HTML_FONT_END', '</font>');

// Authentication types
define('LS_AUTH_TYPE_ANONYMOUS', 0);
define('LS_AUTH_TYPE_USER', 1);
define('LS_AUTH_TYPE_ADMIN', 2);
define('LS_AUTH_TYPE_SUPERADMIN', 3);

// Authentication logged in/out
define('LS_AUTH_LOGIN_LOGGED_OUT', 0);
define('LS_AUTH_LOGIN_LOGGED_IN', 1);