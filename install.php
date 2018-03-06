<?php

//raise error reporting (everything could be interesting...
error_reporting(E_ALL);

//check some known issues first to make sure that we can execute index.php...

function checkdeps(){

	$error =false;
	$errmsg='The following issues were found while preparing to run the installation:<br/>'. PHP_EOL;

	//check if composer was executed (./vendors existing)

	if (!is_dir(__DIR__ . '/vendor')) {

		$error = true;
		$errmsg .= __DIR__ . '/vendor directory is missing. Did you run composer?<br/>' .PHP_EOL;
	}

	//check if smarty-directory is writable
	if (!is_writable(__DIR__ . '/ext_inc/templates_c')) {
		$error = true;
		$errmsg .= __DIR__ . '/ext_inc/templates_c seems not to be writable by the PHP user. Please confirm that file and folder access rights are applied correctly<br/>' . PHP_EOL;
	}

	//check error state and return stored messges if problem found

	if ($error) echo $errmsg;
	return $error;
}


if (!checkdeps()){
	$_GET["mod"] = "install";
	include_once("index.php");
}
