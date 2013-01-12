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
	 * @param  array $params Paramaters passed into this state update.
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
	 * @param  array $params Paramaters passed into this state update.
	 */
	public static function initController($params) {
		/* Do nothing */
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
	 * @param  array $params Paramaters passed into this state update.
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
	 * @param  array $params Paramaters passed into this state update.
	 */
	public static function initShutdown($params) {
		/* Do nothing */
	}
}