<?php

/**
* Check for common failures
*
* Small function to check against a number of dependencies that cause install.php to fail without showing any error details
* 
* @return bool check result. True if error found, false if all OK
*/
function dependencyPreCheck(){
	// Initialize variable holding error messages - if something was found, empty otherwise
	$errmsg = '';

	// Check if composer was executed (folder ./vendor created)
	if (!is_dir(__DIR__ . '/vendor')) {
		$errmsg .= __DIR__ . '/vendor directory is missing. Did you run composer?<br/>' .PHP_EOL;
	}

	// Check if Smarty-directory is writable
	if (!is_writable(__DIR__ . '/ext_inc/templates_c')) {
		$errmsg .= __DIR__ . '/ext_inc/templates_c seems not to be writable by the PHP user. Please confirm that file and folder access rights are applied correctly<br/>' . PHP_EOL;
	}

	// Check error state and return stored messages if problem found
	if (!empty($errmsg) {
		return 'The following issues were found while preparing to run the installation:<br/>'. PHP_EOL . $errmsg;
	} else {
		return false;
	}
}
?>