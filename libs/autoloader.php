<?php
// Autoload any classes we have not previously included
function autoloader($class) {
	// Could we find the class?
	if (! file_exists($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.class.php')) {
		throw new Exception('Unable to locate the file you requested.');
	}

	// The class exists, include it
	include $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.class.php';
}

// And register our autoloader
spl_autoload_register('autoloader');