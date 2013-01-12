<?php
// We're in development, show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session and object buffer
session_start();

// Include the autoloader
include dirname(__FILE__) . '/autoloader.php';