<?php
/**
 * Handles the storing of data for the application, for both page loads
 * and session based data.
 *
 * We currently support persistence via:
 *  1. 'request' - this variable will exist only for this one request.
 *  2. 'session' - this variable will exist only for the users session.
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       16/11/2012
 *
 * @todo  This needs to be broken down into StoreSession and StoreRequest.
 */
class Store
{
	/**
	 * Page based variables.
	 *
	 * Note: These variable will only be available for the single page load unless
	 * you specify $persistence as 'session'.
	 *
	 * @access public
	 * @var    array
	 * @static
	 */
	private static $_store;

	/**
	 * Returns true or false as to if the variable exists in the storage medium.
	 * 
	 * @access public
	 * @param  string  $variable    The name of the variable we wish to see if exists.
	 * @param  string  $persistence How this variable will persist.
	 * @return boolean
	 * @static
	 */
	public static function has($variable, $persistence = 'request') {
		// Does this variable exist in the session?
		if ($persistence == 'session') {
			return isset($_SESSION['store'][$variable]);
		}

		// A local variable
		return isset(self::$_store[$variable]);
	}

	/**
	 * Store a variable for use later on.
	 *
	 * If you pass in an array or object then it will be serialized and
	 * unserialized automatically when storing in the session.
	 * 
	 * @access public
	 * @param  string  $variable    The name of the variable we wish to store.
	 * @param  string  $value       The value of the variable.
	 * @param  string  $persistence How this variable should persist.
	 * @return boolean
	 * @static
	 */
	public static function put($variable, $value, $persistence = 'request') {
		// Do we need to store this variable in the session?
		if ($persistence == 'session') {
			$_SESSION['store'][$variable] = is_array($value) || is_object($value)
				? serialize($value)
				: $value;
		}

		// A local variable
		self::$_store[$variable] = $value;
	}

	/**
	 * Return the variable we were asked to store.
	 *
	 * This function will return boolean false if the variable does not exist in
	 * the store.
	 * 
	 * @access public
	 * @param  string $variable    The variable that we wish to get.
	 * @param  string $persistence How How this variable persists. 
	 * @return string
	 * @static
	 */
	public static function get($variable, $persistence = 'request') {
		// Do we need to fetch from the session?
		if ($persistence == 'session') {
			// Stored in the session, but does it exist?
			if (! isset($_SESSION['store'][$variable])) {
				return false;
			}

			// Since we do not know if this variable has been seriablized we need to unserialize it
			// PHP will return boolean false if the variable cannot be unserialized
			// It will also throw an E_NOTICE, so we need to error supress it with the @
			// However, the value might also be boolean false, so check for that also
			$value = @unserialize($_SESSION['store'][$variable]);

			// And return
			return $value !== false && $_SESSION['store'][$variable] != 'b:0;'
				? $value
				: $_SESSION['store'][$variable];
		}

		// A local variable
		return isset(self::$_store[$variable])
			? self::$_store[$variable]
			: false;
	}

	/**
	 * Removes a variable from the store.
	 * 
	 * @access public
	 * @param  string $variable    The variable that we wish to remove.
	 * @param  string $persistence How this variable currently persists.
	 * @static
	 */
	public static function remove($variable, $persistence = 'request') {
		// Do we need to remove it from the session?
		if ($persistence == 'session') {
			unset($_SESSION['store'][$variable]);
		}

		// A local variable
		unset(self::$_store[$variable]);
	}
}