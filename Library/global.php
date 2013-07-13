<?php
// What status is our application in?
if ($_SERVER['APP_ENVIRONMENT'] == 'Development') {
	// Development, show all errors
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
} else {
	error_reporting(0);
	ini_set('display_errors', 0);
}

// Start the session and object buffer
session_start();

// Include the autoloader
include dirname(__FILE__) . '/autoloader.php';
$loader = new SplClassLoader();
$loader->register();