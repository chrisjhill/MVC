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
	private static $_stack;

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
	 * The start of a trace.
	 *
	 * @access public
	 * @param  string $type The type of code that is running.
	 * @param  string $name How we will reference the trace in the stack.
	 * @static
	 */
	public static function register($type, $name) {}

	/**
	 * The end of a trace.
	 *
	 * @access public
	 * @param  string $type The type of code that is running.
	 * @param  string $name How we will reference the trace in the stack.
	 * @static
	 */
	public static function deregister($type, $name) {}
}