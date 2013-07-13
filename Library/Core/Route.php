<?php
namespace Core;

/**
 * A single route for the application.
 *
 * Routes can contain variables which are prepended by a colon. Paths are greedy
 * by default, they will grab any URL that they match irrespective of what comes
 * after the matched fragments of the request URL. Anything after the route path
 * will be parsed as a GET variable. E.g., The route path of:
 *
 * <code>foo/:bar</code>
 *
 * Will turn the request URL of:
 *
 * <code>foo/hello/my/variables/go/here/foobar</code>
 *
 * Into the following GET variables (minus controller/action indexes):
 *
 * <code>
 * array(
 *     'bar'    => 'hello',
 *     'my'     => 'variables',
 *     'go'     => 'here',
 *     'foobar' => true
 * )
 * </code>
 *
 * @copyright Copyright (c) 2012-2013 Christopher Hill
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author    Christopher Hill <cjhill@gmail.com>
 * @package   MVC
 */
class Route
{
	/**
	 * The unique name of this route.
	 *
	 * @access public
	 * @var    string
	 */
	private $name;

	/**
	 * The path that we want to match.
	 *
	 * @access public
	 * @var    string
	 */
	public $route;

	/**
	 * The regex tests for each variable in the URL.
	 *
	 * Note: If no regex test is added for a variable then we use [\w\-]+
	 *
	 * @access public
	 * @var    array
	 */
	public $paramFormats = array();

	/**
	 * To which controller/action we will dispatch this request.
	 *
	 * <code>
	 * array(
	 *     'controller' => 'Foo',
	 *     'action'     => 'bar'
	 * )
	 * </code>
	 *
	 * @access public
	 * @var    array
	 */
	public $endpoint = array();

	/**
	 * Create the route.
	 *
	 * @access public
	 * @param  string $name The unique name of the route.
	 */
	public function __construct($name) {
		$this->name = $name;
	}

	/**
	 * Set the path for this route.
	 *
	 * Paths will we be worked out relative to your path root (as defined in your
	 * projects config.ini). They can contain any combination of strings or
	 * variables. A variable is declared by starting with a colon (:) and then a
	 * series of a-z characters. The following are all examples of valid paths.
	 *
	 * <ul>
	 *     <li>foo</li>
	 *     <li>foo/bar/:acme</li>
	 *     <li>:foo/:bar/:acme</li>
	 *     <li>:foo/bar/:acme</li>
	 * </ul>
	 *
	 * To set the regex for these variables, use the setParamFormats() method.
	 *
	 * @access public
	 * @param  string     $route The path for this route.
	 * @return Core\Route        The Route, for chainability.
	 */
	public function setRoute($route) {
		$this->route = $route;
		return $this;
	}

	/**
	 * Set the regex patterns for each variable in the route.
	 *
	 * If no regex pattern is passed in for a variable then we use [\w\-]+
	 *
	 * All of your patterns will automatically start with ^, end with $, and will
	 * include the pattern modifiers i and u. So, if you were to pass in the
	 * regex pattern on \d+ then it would be evaluated as /^\d+$/iu
	 *
	 * If your path is <pre>foo/:bar/:acme</pre> then your $formats array could
	 * potentially look like:
	 *
	 * <code>
	 * array(
	 *    'bar'  => '\d+',
	 *    'acme' => '(foo|bar)'
	 * )
	 * </code>
	 *
	 * @access public
	 * @param  array      $formats The regex patterns.
	 * @return Core\Route          The Route, for chainability.
	 */
	public function setFormat($formats) {
		$this->paramFormats = $formats;
		return $this;
	}

	/**
	 * The endpoint for this route.
	 *
	 * Can receive a controller and action. If no controller name is passed in
	 * then the default 'index' controller is used. If no action name is passed
	 * in then the default 'index' action is used.
	 *
	 * <code>
	 * array(
	 *     'controller' => 'Foo',
	 *     'action'     => 'bar'
	 * )
	 * </code>
	 *
	 * @access public
	 * @param  array      $endpoint Where we will dispatch this request.
	 * @return Core\Route           The Route, for chainability.
	 */
	public function setEndpoint($endpoint) {
		// Controller and action has been set?
		if (! isset($endpoint['controller'])) { $endpoint['controller'] = 'Index'; }
		if (! isset($endpoint['action']))     { $endpoint['action']     = 'index'; }

		$this->endpoint = $endpoint;
		return $this;
	}
}