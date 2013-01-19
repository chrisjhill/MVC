<?php
// Autoload any classes we have not previously included
function autoloader($class) {
	// Set the file we want to try and include
	$file = dirname(__FILE__) . '/' . str_replace('\\', '/', $class) . '.class.php';

	// Could we find the class?
	if (! file_exists($file)) {
		throw new Exception($file . ' could not be found.');
	}

	// The class exists, include it
	include $file;
}

// And register our autoloader
spl_autoload_register('autoloader');