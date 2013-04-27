<?php
namespace Core;

/**
 * The front controller.
 *
 * This handles all internal processes and interacts with the users custom
 * application. It provides the bridge.
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       16/12/2012
 */
class Front
{
	/**
	 * The name of the project that we are now going to run. This is also the
	 * name of the directory that we need to run.
	 *
	 * @access private
	 * @var    string
	 */
	private $_projectName;

	/**
	 * Provides information on routes that the application accepts and understands.
	 *
	 * If no router is defined then we simply fall back to controller/action URL's.
	 *
	 * @access private
	 * @var    Router
	 */
	private $_router;

	/**
	 * Initialises the configuration and the router.
	 *
	 * @access public
	 * @param  string      $projectName The name of the project the user wishes to use.
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

		// Set the project information
		$this->_projectName = $projectName;
		$this->_router      = $router ? $router : new Router();

		// Stop the profiler before we begin the routing
		Profiler::deregister('Core', 'Front');

		// And route
		$this->route();
	}

	/**
	 * Begin the routing and dispatching to the Controller/Action.
	 *
	 * @access private
	 */
	private function route() {
		$this->_router->route();
	}
}