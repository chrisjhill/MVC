<?php
namespace Core;

/**
 * Handles all of the request based aspects of the system.
 *
 * @copyright Copyright (c) 2012-2013 Christopher Hill
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author    Christopher Hill <cjhill@gmail.com>
 * @package   MVC
 */
class Request
{
	/**
	 * The URL that the user has visited.
	 *
	 * @access private
	 * @var    string
	 */
	private static $_url;

	/**
	 * A single entry point to the $_GET superglobal
	 *
	 * @access private
	 * @var    array
	 * @static
	 */
	private static $_get;

	/**
	 * A single entry point to the $_POST superglobal
	 *
	 * @access private
	 * @var    array
	 * @static
	 */
	private static $_post;

	/**
	 * A single entry point to the $_SERVER superglobal.
	 *
	 * @access private
	 * @var    array
	 * @static
	 */
	private static $_server;

	/**
	 * Return a breakdown of the URL into their sections.
	 *
	 * @access public
	 * @return array
     * @static
	 */
	public static function setUrl($url = null) {
		// Set the URL
		self::$_url = $url ?: $_SERVER['REQUEST_URI'];

		// We want to remove the path root from the front request URL, stripping
		// .. out all of the information this class does not care about.
		if (strpos(self::$_url, Config::get('path', 'root')) === 0) {
			self::$_url = '/' . substr(self::$_url, strlen(Config::get('path', 'root')));
		}
	}

	/**
	 * Break a URL down into its relevant parts.
	 *
	 * This class will break it down into controller and index, and then all of
	 * the GET parameters. If we are using a custom route then there will be no
	 * controller/action in the URL.
	 *
	 * @access public
	 * @param  string  $url                 The URL to parse.
	 * @param  boolean $setControllerAction Whether we need a controller/action from the URL.
	 * @static
	 */
	public static function setUrlFragments($url = null, $setControllerAction = false) {
		// Breakdown the request the user made into manageable fragments
		$urlFragments = array_filter(explode('/', $url ?: self::$_url));

		// Chunk them into variable => value
		$url          = array();
		$urlFragments = array_chunk($urlFragments, 2);

		// If this is a basic route (not custom) then grab the controller/action
		if ($setControllerAction) {
			$url = array(
				'controller' => isset($urlFragments[0][0]) ? ucfirst($urlFragments[0][0]) : 'Index',
				'action'     => isset($urlFragments[0][1]) ? $urlFragments[0][1]          : 'index'
			);

			// And remove the first chunk so it is not set in the GET array
			unset($urlFragments[0]);
		}

		// The URL is now comprised of only GET variables, so set them
		// If a variable has no specified value then it is set to boolean true
		foreach ($urlFragments as $urlFragment) {
			$url[$urlFragment[0]] = isset($urlFragment[1])
				? $urlFragment[1]
				: true;
		}

		// Merge the route GETs with the explicitly stated GETs (?var=val)
		// We give priority to GET variables set via /var/value
		$_GET = array_merge($_GET, $url);

		// We want all requests to the GET and POST to be through this class to
		// .. provide a common interface, so we unset the globals.
		self::$_get    = $_GET;
		self::$_post   = $_POST;
		self::$_server = $_SERVER;
		unset($_GET, $_POST, $_SERVER);
	}

	/**
	 * Return the URL that the user has visited.
	 *
	 * @access public
	 * @param  string $replaceSlashes What to replace '/' with.
	 * @return string
	 * @static
	 */
	public static function getUrl($replaceSlashes = null) {
		// Do we need to replace forward slashes with something?
		if ($replaceSlashes && self::$_url) {
			return str_replace('/', $replaceSlashes, self::$_url);
		}

		// Is the URL empty?
		else if (empty(self::$_url)) {
			return 'index';
		}

		return self::$_url;
	}

	/**
	 * Get a single GET variable.
	 *
	 * @access public
	 * @param  string $variable The variable we wish to return.
	 * @param  mixed  $default  If the variable is not found, this is returned.
	 * @return mixed
	 * @static
	 */
	public static function get($variable, $default = null) {
		return isset(self::$_get[$variable])
			? self::$_get[$variable]
			: $default;
	}

	/**
	 * Get a single POST variable.
	 *
	 * @access public
	 * @param  string $variable The variable we wish to return.
	 * @param  mixed  $default  If the variable is not found, this is returned.
	 * @return mixed
	 * @static
	 */
	public static function post($variable, $default = null) {
		return isset(self::$_post[$variable])
			? self::$_post[$variable]
			: $default;
	}

	/**
	 * Get a single SERVER variable.
	 *
	 * @access public
	 * @param  string $variable The variable we wish to return.
	 * @param  mixed  $default  If the variable is not found, this is returned.
	 * @return mixed
	 * @static
	 */
	public static function server($variable, $default = null) {
		return isset(self::$_server[$variable])
			? self::$_server[$variable]
			: $default;
	}

	/**
	 * Check whether the users request was a standard request, or via Ajax.
	 *
	 * @access public
	 * @return boolean
	 * @static
	 */
	public static function isAjax() {
		return isset(self::$_server['HTTP_X_REQUESTED_WITH'])
			&& strtolower(self::$_server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}
}