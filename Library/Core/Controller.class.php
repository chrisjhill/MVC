<?php
namespace Core;

/**
 * Connects the Controller to the View.
 *
 * Handles common functionality between application controllers such as setting
 * the layout, forwarding, and redirecting.
 *
 * @copyright Copyright (c) 2012-2013 Christopher Hill
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author    Christopher Hill <cjhill@gmail.com>
 * @package   MVC
 */
class Controller
{
	/**
	 * The controller that we are managing.
	 *
	 * @access public
	 * @var    Controller
	 */
	public $child;

	/**
	 * Instance of the view.
	 *
	 * @access public
	 * @var    View
	 */
	public $view;

	/**
	 * The constructor for the controller.
	 *
	 * @access public
	 */
	public function __construct() {
		// Get the view instance
		$this->view = new View();
	}

	/**
	 * Change the layout from the default.
	 *
	 * If you do not want your View Script to use a layout then pass in boolean
	 * false. To set a new layout then pass in the layouts file name without
	 * the .phtml extention. Your layouts are located in your MyProject/Layout
	 * directory. For example:
	 *
	 * <code>
	 * $this->view->setLayout('default');
	 * </code>
	 *
	 * @access public
	 * @param  boolean|string $layout Which layout we wish to use.
	 * @return boolean
	 * @throws Exception              If the layout does not exist.
	 */
	public function setLayout($layout) {
		// If boolean false then do not output a layout
		if ($layout === false) {
			$this->view->layout = false;
			return true;
		}

		// The location of the layout
		$templateUrlLayout = Config::get('path', 'base') . Config::get('path', 'project')
			. 'Layout/' . $layout . '.phtml';

		// Check the layout actually exists before we set it
		if (! file_exists($templateUrlLayout)) {
			throw new \Exception("{$layout} layout does not exist.");
		}

		// The layout exists
		$this->view->layout = $layout;
	}

	/**
	 * Whilst in controller context, move to another controller/action.
	 *
	 * When forwarding to a new action or controller/action, the URL will not
	 * change, this is an internal redirect.
	 *
	 * Forwarding to an action in the same controller
	 *
	 * <code>
	 * $this->forward('newAction');
	 * </code>
	 *
	 * Forwarding to a new controller and action.
	 *
	 * <code>
	 * $this->forward('newAction', 'newController');
	 * </code>
	 *
	 * @access public
	 * @param  string    $action     The action we wish to forward to.
	 * @param  string    $controller The controller we wish to forward to.
	 * @throws Exception             From the Dispatcher if the controller/action does not exist.
	 */
	public function forward($action = 'index', $controller = '') {
		// Reregister the action in the profile
		Profiler::deregister('Action', $this->view->action);

		// Set which controller has called this function. If it is the same as
		// .. the user specified controller then we do not need to instantiate
		// .. a whole new controller, we can simply forward to its action
		$controllerCaller = str_replace(
			Config::get('settings', 'project') . '\\Controller\\',
			'',
			get_called_class()
		);

		// Calling an action in the same controller
		if ($controller == '' || $controller == $controllerCaller) {
			Dispatcher::loadAction($this->child, $action);
		}

		// Not the same controller, so dispatch to a new controller
		else {
			Profiler::deregister('Controller', $this->view->controller);
			Dispatcher::loadController($controller, $action);
		}
	}

	/**
	 * Redirect the user to a new page.
	 *
	 * Unlike the forward() function, this will perform a header redirect,
	 * causing the URL to change.
	 *
	 * @access public
	 * @param  string $url The URL that we wish to redirect to.
	 */
	public function redirect($url) {
		header('Location: ' . $url); exit();
	}
}