<?php

// Raise error reporting - could be interesting and this is automatically reset when index.php is executed
error_reporting(E_ALL);

// Execute install.php only if dependencies are met
$preCheckResult = dependencyPreCheck();
if ($preCheckResult) {
    // Issue(s) found, show text to user
    echo $preCheckResult;
} else {
    // No issues found, continue execution
    $_GET["mod"] = "install";
    include_once("index.php");
}
