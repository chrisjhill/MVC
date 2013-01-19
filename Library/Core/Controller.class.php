<?php
namespace Core;

/**
 * Connects the Controllers to the Views.
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       15/09/2012
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
	 * @access public
	 * @param  string $layout Which layout we wish to use.
	 * @return string
	 * @throws Exception      If the layout does not exist.
	 */
	public function setLayout($layout) {
		// The location of the layout
		$templateUrlLayout = Config::get('path', 'base') . Config::get('path', 'project')
			. 'layout/' . $this->layout . '.phtml';

		// Does this layout exist?
		if (! file_exists($templateUrlLayout)) {
			throw new \Exception('Layout does not exist.');
		}

		// The layout exists, so set it
		$this->view->layout = $layout;
	}

	/**
	 * If the user wishes to forward the user to another controller.
	 *
	 * Since we no longer want to render this controller we set it as void. Then, when
	 * the rendering is called it will be ignored.
	 *
	 * This function will keep the same URL, that will not be changed between forwards.
	 *
	 * @access public
	 * @param  string $action     The action we wish to forward to.
	 * @param  string $controller The controller we wish to forward to.
	 * @throws Exception          From the Router if the controller/action does not exist.
	 */
	public function forward($action = 'index', $controller = '') {
		// Is this an controller forward or an action forward?
		// Controller forward = A new controller
		// Action redirect    = Same controller, different action
		if ($controller == '' || $controller == str_replace(Config::get('settings', 'project') . '\\Controller\\', '', get_called_class())) {
			Router::loadAction($this->child, $action);
			$this->child->render();
		} else {
			// And start a new router to the desired controller/action
			Router::loadController($controller, $action);
		}
	}

	/**
	 * Redirect the user to a new page.
	 *
	 * This will perform a header redirect, so we will change the URL, and we can also
	 * pass variables.
	 * 
	 * @access public
	 * @param  array $param Parameters for the URL View Helper.
	 */
	public function redirect($param) {
		header('Location: ' . $this->view->url($param)); exit();
	}
}