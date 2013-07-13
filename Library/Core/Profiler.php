<?php
namespace Core;

/**
 * Profiles the flow of the application as code executes.
 *
 * Allows the creation of a waterfall diagram documenting execution time and
 * memory management, allowing easy identification of slow code and which areas
 * are using large amounts of data. The profile nests items, so you can see how
 * a certain item got to be called.
 *
 * @copyright Copyright (c) 2012-2013 Christopher Hill
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author    Christopher Hill <cjhill@gmail.com>
 * @package   MVC
 *
 * @see       /Library/MyProject/View/Helper/Profiler.class.php
 */
class Profiler
{
	/**
	 * A stack of traces that have occurred through the running of the application.
	 *
	 * <code>
	 * array(
	 *     array(
	 *         'type'  => 'Core',
	 *         'name'  => 'Request',
	 *         'start' => 123.1,
	 *         'end'   => 123.2,
	 *         'mem'   => 1.0
	 *     ),
	 *     array(
	 *         'type'  => 'Controller',
	 *         'name'  => 'Index',
	 *         'start' => 321.5,
	 *         'end'   => 321.8,
	 *         'mem'   => 2.5
	 *     )
	 * )
	 * </code>
	 *
	 * @access private
	 * @var    array
	 * @static
	 */
	private static $_stack = array();

	/**
	 * The time we started profiling the application.
	 *
	 * @access private
	 * @var    float
	 * @static
	 */
	private static $_requestStart;

	/**
	 * The time we finished profiling the application.
	 *
	 * @access private
	 * @var    float
	 * @static
	 */
	private static $_requestEnd;

	/**
	 * Start the profiler.
	 *
	 * @access public
	 * @static
	 */
	public static function start() {
		self::$_requestStart = microtime(true);
	}

	/**
	 * Stop the profiling.
	 *
	 * @access public
	 * @static
	 */
	public static function stop() {
		self::$_requestEnd = microtime(true);
	}

	/**
	 * The start of a trace.
	 *
	 * We add new traces to the start of the array so that when we deregister
	 * them we shouldn't, hopefully, have to go through as many iterations.
	 *
	 * @access public
	 * @param  string $type The type of code that is running.
	 * @param  string $name How we will reference the trace in the stack.
	 * @static
	 */
	public static function register($type, $name) {
		// Profile this request?
		if (! Config::get('profiler', 'enable', true)) {
			return false;
		}

		array_unshift(self::$_stack, array(
			'type'  => $type,
			'name'  => $name,
			'start' => microtime(true),
			'mem'   => memory_get_usage()
		));
	}

	/**
	 * The end of a trace.
	 *
	 * @access public
	 * @param  string $type The type of code that is running.
	 * @param  string $name How we will reference the trace in the stack.
	 * @static
	 */
	public static function deregister($type, $name) {
		// Profile this request?
		if (! Config::get('profiler', 'enable', true)) {
			return false;
		}

		// Get the time here to get a more accurate trace
		$microtime = microtime(true);

		// And begin the loop
		foreach (self::$_stack as $traceId => $trace) {
			if ($trace['name'] == $name) {
				self::$_stack[$traceId]['end'] = $microtime;
				self::$_stack[$traceId]['mem'] =
					memory_get_usage() - self::$_stack[$traceId]['mem'];
				break;
			}
		}
	}

	/**
	 * Return the stack of traces we recorded for this request.
	 *
	 * @access public
	 * @return array
	 * @static
	 */
	public static function getProfilerData() {
		return array(
			'requestStart' => self::$_requestStart,
			'requestEnd'   => self::$_requestEnd,
			'stack'        => array_reverse(self::$_stack)
		);
	}
}