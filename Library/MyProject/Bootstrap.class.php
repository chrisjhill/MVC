<?php
namespace MyProject;
use Core;

class Bootstrap
{
	/**
	 * A request has been initiated.
	 *
	 * This event is triggered just before the controller is loaded.
	 *
	 * <ul>
	 *     <li>controller - The name of the controller being loaded.</li>
	 *     <li>action     - The name of the action being loaded.</li>
	 * </ul>
	 *
	 * @access public
	 * @param  array $params Parameters passed into this state update.
	 * @static
	 */
	public static function initRequest($params) {
		/* Do nothing */
	}

	/**
	 * A controller has been loaded.
	 *
	 * This event is triggered just after a controller is initiated, and just
	 * before the action is loaded.
	 *
	 * <ul>
	 *     <li>controller - The Core\Controller object.</li>
	 * </ul>
	 *
	 * @access public
	 * @param  array $params Parameters passed into this state update.
	 * @static
	 */
	public static function initController($params) {
		// Add variables to the view
		$params['controller']->view->addVariable('urlRoot', Core\Config::get('path', 'root'));
	}

	/**
	 * An action is just about to be loaded.
	 *
	 * This event is triggered just before an action is loaded.
	 *
	 * <ul>
	 *     <li>controller - The Core\Controller object.</li>
	 *     <li>action     - The name of the action which is about to be loaded.</li>
	 * </ul>
	 *
	 * @access public
	 * @param  array $params Parameters passed into this state update.
	 * @static
	 */
	public static function initAction($params) {
		/* Do nothing */
	}

	/**
	 * A request has completed, and ready to be shutdown.
	 *
	 * <ul>
	 *     <li>controller - The name of the controller that was rendered.</li>
	 *     <li>action     - The name of the action that was rendered.</li>
	 * </ul>
	 *
	 * @access public
	 * @param  array $params Parameters passed into this state update.
	 * @static
	 */
	public static function initShutdown($params) {
		/* Do nothing */
	}
}