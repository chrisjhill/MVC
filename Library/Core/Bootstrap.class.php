<?php
namespace Core;

/**
 * Is informed when certain actions are performed in the MVC. These are:
 *
 * 1. A request is initialised.
 * 2. A controller is initialised.
 * 3. We are about to shutdown (page has been rendered).
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       17/09/2012
 */
class Bootstrap
{
	/**
	 * A request has come in, we know the controller name and the action name.
	 * 
	 * @access public
	 * @param string $controllerName The controller we have loaded.
	 * @param string $actionName     The action we have loaded.
	 * @static
	 */
	public static function initRequest($controllerName, $actionName) {
		// Create a new cache instance
		$cache = new Cache(
			trim(Request::server('REQUEST_URI'), '/') == ''
				? '_entry_ ' . Request::get('controller') . '/' . Request::get('action') . '.phtml'
				: '_entry_ ' . trim(Request::server('REQUEST_URI'), '/') . '.phtml',
			Config::get('path', 'view_script'),
			false
		);

		// Set the cache settings
		$cache->setCache(true)->setCacheLife(Config::get('cache', 'life'));

		// Can we render now?
		if ($cache->cachedFileAvailable()) {
			die($cache->getCachedFile() . ' <!-- Fetched from Bootstrap cache //-->');
		}
	}

	/**
	 * A controller has been initialised.
	 * 
	 * @access public
	 * @param  Core\Controller $controller The controller that has been initiated.
	 * @static
	 */
	public static function initController($controller) {
		/* Do nothing */
	}

	/**
	 * We have finished rendering a page and are about to shut down.
	 * 
	 * @access public
	 * @param  string $controllerName The controller that we ended up rendering.
	 * @param  string $actionName     The action that we ended up rendering.
	 * @static
	 */
	public static function initShutdown($controllerName, $actionName) {
		/* Do nothing */
	}
}