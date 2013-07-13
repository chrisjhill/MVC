<?php
namespace Core;

/**
 * The front controller.
 *
 * This class provides the bridge between all internal processes and the application.
 *
 * @copyright Copyright (c) 2012-2013 Christopher Hill
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author    Christopher Hill <cjhill@gmail.com>
 * @package   MVC
 */
class Front
{
	/**
	 * The name of the project that we are now going to run. This is also the
	 * name of the directory that contains the application.
	 *
	 * @access private
	 * @var    string
	 */
	private $_projectName;

	/**
	 * Provides information on routes that the application accepts and understands.
	 *
	 * If no router is defined then we simply fall back to /controller/action URL.
	 *
	 * @access private
	 * @var    Core\Router
	 */
	private $_router;

	/**
	 * Initialises the application configuration and runs the router.
	 *
	 * @access public
	 * @param  string      $projectName The name of the project the user wishes to run.
	 * @param  Core\Router $router      The routes the users application requires.
	 */
	public function __construct($projectName, $router = null) {
		// Start the profiler
		Profiler::start();
		Profiler::register('Core', 'Front');

		// Load the configuration for this project
		Profiler::register('Core', 'Config');
		Config::load($projectName);
		Profiler::deregister('Core', 'Config');
		Profiler::register('Core', 'Request');
		Request::setUrl();
		Profiler::deregister('Core', 'Request');

		// Set the project information
		$this->_projectName = $projectName;
		$this->_router      = $router ?: new Router();

		// And route
		$this->route();
	}

	/**
	 * Route, and pass to the Dispatcher to run our controller/action.
	 *
	 * @access private
	 */
	private function route() {
		$this->_router->route();
	}
}