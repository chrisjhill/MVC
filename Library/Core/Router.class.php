<?php
namespace Core;

/**
 * Determines which of the supplied routes will be used for the Dispatcher.
 *
 * If no route is valid then we assume that you are using the default MVC pattern
 * of /controller/action/my/variables/go/here/foobar. This router matches from
 * first to last, so if potentially more than one route matches then we will
 * route to the first declared. This is due to exiting as soon as we locate a
 * valid route to save processing time. Routes are greedy by default, so place
 * your more specific routes first.
 *
 * All routes use the following pattern modifiers:
 *
 * <ul>
 *     <li>i: PCRE_CASELESS: matching both uppercase and lowercase.</li>
 *     <li>u: PCRE_UTF8:     Make strings UTF-8.</li>
 * </ul>
 *
 * An example of how to use this class in the index.php is as follows:
 *
 * <code>
 * <?php
 * // Global configurations
 * include dirname(__FILE__) . '/../Library/global.php';
 *
 * // Creae new Router instance
 * $router = new Core\Router();
 * $router
 *     ->addRoute('Foo')
 *     ->setRoute('foo/:bar/:acme')
 *     ->setFormat(array(
 *         'bar'  => '\d+',
 *         'acme' => '[a-z0-9]+')
 *     )
 *     ->setEndpoint(array(
 *         'controller' => 'Foo',
 *         'action'     => 'bar')
 *     );
 *
 * // Start the application
 * new Core\Front('MyProject', $router);
 * </code>
 *
 * Note: If no regex formats are supplied then we use the default of [\w\-]+ (any
 * alpha numeric character (a-z, 0-9, underscores) and dashes)
 * for the variable (:var) matching.
 *
 * Reverse routing
 * ---------------
 * URL's will often change. Defining them in a single place (the router) will
 * save you having to rewrite them in your View Helpers/Partials. It is also
 * safer because URL encoding will be taken care for you.
 *
 * @copyright Copyright (c) 2012-2013 Christopher Hill
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author    Christopher Hill <cjhill@gmail.com>
 * @package   MVC
 *
 * @see       /Library/MyProject/View/Helper/Route.class.php
 */
class Router
{
	/**
	 * A collection of Route's that have been declared.
	 *
	 * @access private
	 * @var    array
	 * @static
	 */
	private static $_routes = array();

	/**
	 * The portion of the request URL that the route has matched.
	 *
	 * @access private
	 * @var    string
	 */
	private $_routePath;

	/**
	 * Add a new route to the router.
	 *
	 * @access public
	 * @param  string                    $routeName The name of the route.
	 * @return Core\Route                           The new Route, for chainability.
	 * @throws \InvalidArgumentException            If the route name has already been declared.
	 */
	public function addRoute($routeName) {
		// We cannot allow duplicate route names for reversing reasons
		if (isset(self::$_routes[$routeName])) {
			throw new \InvalidArgumentException("The route {$routeName} has already been declared.");
		}

		self::$_routes[$routeName] = new Route($routeName);
		return self::$_routes[$routeName];
	}

	/**
	 * Start the routing procedure and find a valid route, if any.
	 *
	 * @access public
	 */
	public function route() {
		// Start the profiler
		Profiler::register('Core', 'Router');

		// First, let's look at the URL the user supplied
		$requestUrl   = array_values(array_filter(explode('/', Request::getUrl())));
		$requestRoute = null;

		// Loop over each route and test to see if they are valid
		foreach (self::$_routes as $route) {
			if ($this->routeTest($requestUrl, $route)) {
				$requestRoute = $route;
				break;
			}
		}

		// We have completed the route matching
		// Finish the setup of the request object
		Profiler::register('Core', 'Request');
		if ($requestRoute) {
			$_GET['controller'] = $route->endpoint['controller'];
			$_GET['action']     = $route->endpoint['action'];
			Request::setUrlFragments(str_replace($this->_routePath, '', Request::getUrl()));
		} else {
			Request::setUrlFragments(Request::getUrl(), true);
		}
		Profiler::deregister('Core', 'Request');

		// Inform the event listener a request has been initialised
		Event::trigger(
			'initRequest',
			array(
				'controller' => Request::get('controller'),
				'action'     => Request::get('action')
			)
		);

		// And stop the profiler
		Profiler::deregister('Core', 'Router');
		Profiler::deregister('Core', 'Front');

		// And dispatch
		Dispatcher::loadController(
			Request::get('controller'),
			Request::get('action')
		);
	}

	/**
	 * Test to see if this route is valid against the URL.
	 *
	 * @access private
	 * @param  array      $requestUrl The URL to test the route against.
	 * @param  Core\Route $route      A Route declared by the application.
	 * @return boolean
	 */
	private function routeTest($requestUrl, $route) {
		// Break apart the route URL
		$routeUrl  = array_filter(explode('/', $route->route));
		$routePath = '';

		// Loop over each part of the route
		foreach ($routeUrl as $routeFragmentId => $routeFragment) {
			// Does this fragment actually exist in the request?
			if (! isset($requestUrl[$routeFragmentId])) {
				return false;
			}

			// Request has this fragment
			// If it is a variable, does the format match?
			else if (strpos($routeFragment, ':') === 0) {
				// Get the name of this fragment
				$routeFragmentName = substr($routeFragment, 1);

				// Get the format regex test
				$regexTest = isset($route->paramFormats[$routeFragmentName])
					? $route->paramFormats[$routeFragmentName]
					: '[\w\-]+';

				// And test
				if (! preg_match("/^{$regexTest}$/iu", $requestUrl[$routeFragmentId])) {
					return false;
				}

				// Add this route declared variable to the GET request
				$_GET[$routeFragmentName] = $requestUrl[$routeFragmentId];
			}

			// This is not a regex test, so just check the strings are the same
			else if ($routeFragment != $requestUrl[$routeFragmentId]) {
				return false;
			}

			// Build up the path
			$routePath .= '/' . $requestUrl[$routeFragmentId];
		}

		// This route has passed all of the fragment tests
		$this->_routePath = $routePath;
		return true;
	}

	/**
	 * Reverse the router.
	 *
	 * Make a URL out of a route name and parameters, rather than parsing one.
	 * Note that this function does not care about URL paths!
	 *
	 * @access public
	 * @param  string    $routeName The name of the route we wish to generate a URL for.
	 * @param  array     $params    The parameters that the route requires.
	 * @return string
	 * @throws \Exception           If the route does not exist.
	 * @static
	 */
	public static function reverse($routeName, $params = array()) {
		// Does the route actually exist?
		if (! isset(self::$_routes[$routeName])) {
			throw new Exception('The route ' . $routeName . ' does not exist.');
		}

		// Create a container for the URL
		$url = self::$_routes[$routeName]->route;

		// And replace the variables in the
		foreach ($params as $variable => $value) {
			$url = str_replace(":{$variable}", urlencode($value), $url);
		}

		return $url;
	}
}