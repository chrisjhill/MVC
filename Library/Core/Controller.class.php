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
	 * Whether we need to render this controller.
	 *
	 * @access private
	 * @var    bool
	 */
	private $_void = false;

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
	 * Do we want to use the cache for this file?
	 *
	 * @access public
	 */
	public function cache() {
		// Does the controller support entry level caching?
		if (isset($this->child->enableCacheEntry) && $this->child->enableCacheEntry) {
			// Create a new cache instance
			$cache = new Cache(
				trim($_SERVER['REQUEST_URI'], '/') == ''
					? '_entry_ ' . $_GET['controller'] . '/' . $_GET['action'] . '.phtml'
					: '_entry_ ' . trim($_SERVER['REQUEST_URI'], '/') . '.phtml',
				Config::get('path', 'view_script'),
				false
			);

			// Set the cache settings
			$cache->setCache(true)->setCacheLife(Config::get('cache', 'life'));

			// Inform the view there is a cache and the settings
			$this->view->cacheEntry = $cache;
		}		

		// Does the child allow caching?
		if (isset($this->child->enableCacheAction) && $this->child->enableCacheAction) {
			// Create a new cache instance
			$cache = new Cache(
				$this->view->controller . DS . $this->view->action . '.phtml',
				Config::get('path', 'view_script')
			);

			// Set the cache settings
			$cache->setCache(true)->setCacheLife($this->child->cacheActionLife);

			// Inform the view there is a cache and the settings
			$this->view->cacheAction = $cache;

			// Can we render now?
			if ($cache->cachedFileAvailable()) {
				$this->view->render();
			}
		}
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
		// Does this layout exist?
		if (! file_exists(Config::get('path', 'layout') . $layout . '.phtml')) {
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
			// Controller redirect
			// No longer render this controller, thank you
			$this->_void = true;

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

	/**
	 * The time has come to render the page.
	 *
	 * @access public
	 */
	public function render() {
		// Do we still wish to render this controller?
		if (! $this->_void) {
			$this->view->render();
		}
	}
}