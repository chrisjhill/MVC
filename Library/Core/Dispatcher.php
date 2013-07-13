<?php
namespace Core;

/**
 * Runs a specified controller and action.
 *
 * This is initially called by the Router once it finds a valid route, but it
 * can also be called by a controller to forward to another controller/action.
 *
 * @copyright Copyright (c) 2012-2013 Christopher Hill
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author    Christopher Hill <cjhill@gmail.com>
 * @package   MVC
 */
class Dispatcher
{
	/**
	 * Load a controller and it's respective action.
	 *
	 * If no action is passed in then we use the action as defined by the URL. If
	 * no action is specified in the URL then we use the default og 'index'.
	 *
	 * @access public
	 * @param  string $controllerName Name of the controller we wish to load.
	 * @param  string $action         Name of the action we wish to load.
     * @static
	 */
	public static function loadController($controllerName, $action = '') {
		// Work out the full namespace path for this controller
		$controller = Config::get('settings', 'project') . '\\Controller\\' . $controllerName;

		// If the controller does not exist the autoloader will throw an Exception
		try {
			// Try and create a new instance of the controller
			Profiler::register('Core', 'Dispatcher');
			Profiler::register('Controller', $controllerName);
			$controller = new $controller();

			// Let the Core controller be aware of its child
			$controller->child = $controller;

			// Inform the event listener a controller has been initialised
			Event::trigger('initController', array('controller' => $controller));
			Profiler::deregister('Core', 'Dispatcher');

			// If the controller has an init function then call it first
			if (is_callable(array($controller, 'init'))) {
				Profiler::register('Controller', 'init');
				$controller->init();
				Profiler::deregister('Controller', 'init');
			}

			// Controller fully initialised, now run its action
			$action = $action ?: Request::get('action');
		} catch (\Exception $e) {
			// If this is the error controller then there is no hope
			if ($controllerName == 'Error') {
				die('Sorry, an error occurred whilst processing your request.');
			}

			// Controller does not exist, forward to the error controller
			Profiler::deregister('Core', 'Dispatcher');
			Profiler::deregister('Controller', $controllerName);
			Dispatcher::loadController('Error', 'notFound');
		}

		// Load the action
		Dispatcher::loadAction($controller, $action);
	}

	/**
	 * Load a controllers action, and ask the View to render it.
	 *
	 * @access public
	 * @param  object  $controller Controller object that we want to load the action for.
	 * @param  string  $action     Name of the action we wish to load.
     * @static
	 */
	public static function loadAction($controller, $action) {
		// Start the profiler
		Profiler::register('Core', 'Dispatcher');

		// In order to have pretty URL's we allow the basic routing to contain
		// .. dashes in their action names which will be removed here. It allows
		// .. /index/hello-world to be routed to /index/helloworld.
		$action         = str_replace('-', '', $action);
		$actionExists   = is_callable(array($controller, $action . 'Action'));
		$controllerName = str_replace(
			Config::get('settings', 'project') . '\\Controller\\',
			'',
			get_class($controller)
		);

		// Make sure that the controller has the action
		if (! $actionExists) {
			// If this is the error controller then there is no hope
			if ($controllerName == 'Error') {
				die('Sorry, an error occurred whilst processing your request.');
			}

			// Controllers can have their own error function for finer grain control
			else if ($action != 'error') {
				Profiler::deregister('Core', 'Dispatcher');
				Dispatcher::loadAction($controller, 'error');
			}

			// Controller does not have an error function, run Error controller
			else {
				Profiler::deregister('Core', 'Dispatcher');
				Profiler::deregister('Controller', $controllerName);
				Dispatcher::loadController('Error', 'notFound');
			}
		}

		// We are able to call the controllers action
		Event::trigger('initAction', array(
			'controller' => $controller,
			'action'     => $action
		));
		Profiler::deregister('Core', 'Dispatcher');
		Profiler::register('Action', $action);

		// Set the controller and action that we are heading to
		$controller->view->controller = $controllerName;
		$controller->view->action     = $action;

		// Call and render this action
		$controller->{$action . 'Action'}();
		$controller->view->render();
	}
}