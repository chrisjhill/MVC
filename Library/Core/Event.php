<?php
namespace Core;

/**
 * Pass events within the MVC to the application.
 *
 * We currently listen for a selection of events which are then passed to the
 * application to handle. These events are:
 *
 * <ul>
 *     <li>A request is initialised.</li>
 *     <li>A controller is initialised.</li>
 *     <li>An action has been called.</li>
 *     <li>We are about to shutdown (page has been rendered).</li>
 * </ul>
 *
 * @copyright Copyright (c) 2012-2013 Christopher Hill
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author    Christopher Hill <cjhill@gmail.com>
 * @package   MVC
 */
class Event
{
	/**
	 * Trigger an event call to the application.
	 *
	 * @access public
	 * @param  string $event  The event to trigger within the appliction.
	 * @param  array  $params Parameters to pass to the application.
	 * @static
	 */
	public static function trigger($event, $params) {
		// Start the profiler
		Profiler::register('Core', 'Event.' . $event);

		// Create a reference to the users event listener
		$listener = Config::get('settings', 'project') . '\\EventListener';

		// Call the appropriate event listener function
		$listener::$event($params);

		// Stop the profiler
		Profiler::deregister('Core', 'Event.' . $event);
	}
}