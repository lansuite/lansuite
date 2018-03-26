<?php

/**
* Check for common failures
*
* Small function to check against a number of dependencies that cause install.php to fail without showing any error details
* 
* @return bool check result. True if error found, false if all OK
*/
function dependencyPreCheck(){
    $errmsg = '';

    // __DIR__ will result in something like /code/inc/Functions
    // But we want the document root (/code in this example)
    $baseDirectoryParts = explode(DIRECTORY_SEPARATOR, __DIR__);
    array_pop($baseDirectoryParts);
    array_pop($baseDirectoryParts);
    $baseDirectory = implode(DIRECTORY_SEPARATOR, $baseDirectoryParts);

    // Check if composer was executed (folder ./vendor created)
    $vendorDir = $baseDirectory . DIRECTORY_SEPARATOR . 'vendor';
    if (!is_dir($vendorDir)) {
        $errmsg .= $vendorDir . ' directory is missing. Did you run composer?<br/>' .PHP_EOL;
    }

    // Check if Smarty template cache directory is writable
    $templateCacheDir = implode(DIRECTORY_SEPARATOR, [$baseDirectory, 'ext_inc', 'templates_c']);
    if (!is_writable($templateCacheDir)) {
        $errmsg .= $templateCacheDir . ' seems not to be writable by the PHP user. Please confirm that file and folder access rights are applied correctly<br/>' . PHP_EOL;
    }

    if (!empty($errmsg)) {
        return 'The following issues were found while preparing to run the installation:<br/>'. PHP_EOL . $errmsg;
    }

    return false;
}
