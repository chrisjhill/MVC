<?php
// Autoload any classes we have not previously included
function autoloader($class) {
	// Set the file we want to try and include
	$file = PATH_LIBRARY . str_replace('_', DS, $class) . '.class.php';

	// Could we find the class?
	if (! file_exists($file)) {
		throw new Exception('Unable to locate the class ' . $class);
	}

	// The class exists, include it
	include $file;
}

// And register our autoloader
spl_autoload_register('autoloader');