<?php
/**
 * Dispatches requests to a controller and an action. Can be called by the
 * controller live to forward to another controller.
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       15/09/2012
 */
class Core_Router
{
	/**
	 * Router constructor, try and load a controller and action.
	 *
	 * @access public
	 */
	public function __construct() {
		// Get the address the user navigated to
		Core_Url::getUrlBreakdown();

		// Try and instantiate the controller
		$this->loadController($_GET['controller']);
	}

	/**
	 * Try and load the controller.
	 *
	 * @access public
	 * @param $controller string
	 * @param $action string
	 */
	public function loadController($controller, $action = '') {
		// Format the controller name correctly
		$controller = 'Controller_' . $controller;

		// Can we load the controller?
		try {
			// Instantiate
			$controller = new $controller();
			$controller->child = $controller;
		} catch (Exception $e) {
			// Forward to the utilities 404
			die('Sorry, we were unable to load the page your requested.');
		}

		// Which action shall we run?
		$action = $action ? $action : $_GET['action'];

		// Load the action
		Core_Router::loadAction($controller, $action);

		// And now render the view
		$controller->render();
	}

	/**
	 * Try and run the action.
	 *
	 * @access public
	 * @param $controller string
	 * @param $action string
	 */
	public function loadAction($controller, $action) {
		// Does the method exist?
		if (! method_exists($controller, $action . 'Action')) {
			// Nope, doesn't exist
			// Fall back to the 404
			if ($action != 'error') {
				// There was an error with the action, and we were not running the 404 action
				// Try and run the 404 action
				Core_Router::loadAction($controller, 'error');

				// No need to go any further
				return false;
			} else if ($action == 'error') {
				// Even the 404 action does not work
				// Just die
				die('Sorry, we were unable to load the action your requested.');
			}
		}

		// Yes, it exists
		// Set the controller and action that we are heading to
		$controller->view->controller = str_replace('Controller_', '', get_class($controller));
		$controller->view->action     = $action;

		// Start the cache, if required
		$controller->cache();

		// And call the action
		$controller->{$action . 'Action'}();
	}
}