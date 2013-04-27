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
	static function trigger($state, $params) {
		// Start the profiler
		Profiler::register('Core', 'Bootstrap.' . $state);

		// Create a reference to the users bootstrap
		$bootstrap = Config::get('settings', 'project') . '\\Bootstrap';

		// Call the projects own bootstrap so they can handle these events
		switch ($state) {
			case 'initRequest'    : $bootstrap::initRequest($params);    break;
			case 'initController' : $bootstrap::initController($params); break;
			case 'initAction'     : $bootstrap::initAction($params);     break;
			case 'initShutdown'   : $bootstrap::initShutdown($params);   break;
		}

		// Stop the profiler
		Profiler::deregister('Core', 'Bootstrap.' . $state);
	}
}