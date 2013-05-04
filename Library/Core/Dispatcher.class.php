<?php
namespace Core;

/**
 * Dispatches requests to a controller and an action. Can be called by the
 * controller live to forward to another controller or action.
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       15/09/2012
 */
class Dispatcher
{
	/**
	 * Try and load the controller.
	 *
	 * @access public
	 * @param  string    $controller The controller we wish to load.
	 * @param  string    $action     The action we wish to load.
	 * @throws Exception             If we cannot load the controller.
     * @static
	 */
	public static function loadController($controllerName, $action = '') {
		// Format the controller name correctly
		$controller = Config::get('settings', 'project') . '\\Controller\\' . $controllerName;

		// Can we load the controller?
		try {
			// Instantiate
			Profiler::register('Core', 'Dispatcher');
			Profiler::register('Controller', $controllerName);
			$controller = new $controller();

			// We need to set the child to the parent so we can forward
			$controller->child = $controller;

			// Inform the bootstrap a controller has been initialised
			Bootstrap::trigger(
				'initController',
				array(
					'controller' => $controller
				)
			);

			// Call the init method, if it exists
			Profiler::deregister('Core', 'Dispatcher');
			if (method_exists($controller, 'init')) {
				Profiler::register('Controller', 'Init');
				$controller->init();
				Profiler::deregister('Controller', 'Init');
			}

			// Which action shall we run?
			$action = $action ?: Request::get('action');
		} catch (\Exception $e) {
			// Forward to the Error's 404
			Profiler::deregister('Core', 'Dispatcher');
			Profiler::deregister('Controller', $controllerName);
			Dispatcher::loadController('Error', 'notFound');
		}

		// Load the action
		Dispatcher::loadAction($controller, $action);
	}

	/**
	 * Try and run the action.
	 *
	 * @access public
	 * @param  string $controller The controller we wish to load.
	 * @param  string $action     The action we wish to load.
     * @return boolean
     * @static
	 */
	public static function loadAction($controller, $action) {
		// Start the profiler
		Profiler::register('Core', 'Dispatcher');

		// We want pretty URL's, there might be dashes
		$action = str_replace('-', '', $action);

		// Does the method exist?
		$actionExists = method_exists($controller, $action . 'Action');

		// If the method does not exist then we need to run the error action
		if (! $actionExists && $action != 'error') {
			// There was an error with the action, and we were not running the 404 action
			// Try and run the 404 action
			Profiler::deregister('Core', 'Dispatcher');
			Dispatcher::loadAction($controller, 'error');

			// No need to go any further
			return false;
		}

		// Yes, it exists
		// Let the bootstrap know
		Bootstrap::trigger(
			'initAction',
			array(
				'controller' => $controller,
				'action'     => $action
			)
		);
		Profiler::deregister('Core', 'Dispatcher');
		Profiler::register('Action', $action);

		// Set the controller and action that we are heading to
		$controller->view->controller = str_replace(
			Config::get('settings', 'project') . '\\Controller\\',
			'',
			get_class($controller)
		);
		$controller->view->action = $action;

		// And call the action
		if ($actionExists) {
			$controller->{$action . 'Action'}();
		}

		// And now render the view
		$controller->view->render();
	}
}