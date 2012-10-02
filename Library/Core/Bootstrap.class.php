<?php
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
class Core_Bootstrap
{
	/**
	 * A request has come in, we know the controller name and the action name.
	 * 
	 * @access public
	 * @param $controllerName string
	 * @param $actionName string
	 * @static
	 */
	public static function initRequest($controllerName, $actionName) {
	}

	/**
	 * A controller has been initialised.
	 * 
	 * @access public
	 * @param $controller Core_Controller
	 * @static
	 */
	public static function initController($controller) {
	}

	/**
	 * We have finished rendering a page and are about to shut down.
	 * 
	 * @access public
	 * @param $controllerName string
	 * @param $actionName string
	 * @static
	 */
	public static function initShutdown($controllerName, $actionName) {
	}
}