<?php
namespace Core;

/**
 * Profiles the flow of the application as code executes.
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       24/04/2013
 */
class Profiler
{
	/**
	 * A stack of traces that have occurred through the running of the application.
	 *
	 * <code>
	 * array(
	 *     array(
	 *         'type'  => 'request|controller|action|helper|parse',
	 *         'name'  => 'Controller.Action',
	 *         'start' => 123,
	 *         'end'   => 456
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
	 * When the requested came in.
	 *
	 * @access private
	 * @var    float
	 * @static
	 */
	private static $_requestStart;

	/**
	 * When the requested finished.
	 *
	 * @access private
	 * @var    float
	 * @static
	 */
	private static $_requestEnd;

	/**
	 * Set when the request has started.
	 *
	 * @access public
	 * @static
	 */
	public static function start() {
		self::$_requestStart = microtime(true);
	}

	/**
	 * Set when the request has finished.
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
	 * Return the stats for the request.
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