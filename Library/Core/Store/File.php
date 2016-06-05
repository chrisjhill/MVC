<?php
namespace Core\Store;

use Core\Config,
    Core\Request;

/**
 * Stores data within the file system.
 *
 * @copyright Copyright (c) 2012-2016 Christopher Hill
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author    Christopher Hill <cjhill@gmail.com>
 * @package   MVC
 */
class File implements StorageInterface
{
	/**
	 * Check whether the variable exists in the store.
	 *
	 * @access public
	 * @param  string  $variable The name of the variable to check existence of.
	 * @return boolean           If the variable exists or not.
	 * @static
	 */
	public static function has($variable) {
		$filePath = Config::get('path', 'base') . Config::get('path', 'cache') . $variable;

		if (
			Request::server('REQUEST_METHOD') == 'POST' || // Unique content possible
			! Config::get('cache', 'enable')            || // Caching disabled
			! file_exists($filePath)                       // Cache entry does not exist
		) {
			return false;
		}

		// Check the time the item was created to see if it is stale
		return (Request::server('REQUEST_TIME') - filemtime($filePath)) <=
			Config::get('cache', 'life');
	}

	/**
	 * Store a variable for use.
	 *
	 * @access public
	 * @param  string  $variable  The name of the variable to store.
	 * @param  mixed   $value     The data we wish to store.
	 * @param  boolean $overwrite Whether we are allowed to overwrite the variable.
	 * @return boolean            If we managed to store the variable.
	 * @throws Exception          If the variable already exists when we try not to overwrite it.
	 * @static
	 */
	public static function put($variable, $value, $overwrite = false) {
		// If it exists, and we do not want to overwrite, then throw exception
		if (self::has($variable) && ! $overwrite) {
			throw new \Exception("{$variable} already exists in the store.");
		}

		file_put_contents(
			Config::get('path', 'base') . Config::get('path', 'cache') . $variable,
			$value
		);
	}

	/**
	 * Return the variable's value from the store.
	 *
	 * @access public
	 * @param  string $variable The name of the variable in the store.
	 * @return bool
	 * @throws Exception        If the variable does not exist.
	 * @static
	 */
	public static function get($variable) {
		if (! self::has($variable)) {
			throw new \Exception("{$variable} does not exist in the store.");
		}

		return file_get_contents(
			  Config::get('path', 'base')
			. Config::get('path', 'cache')
			. $variable
		);
	}

	/**
	 * Remove the variable in the store.
	 *
	 * @access public
	 * @param  string $variable The name of the variable to remove.
	 * @return boolean          If the variable was removed successfully.
	 * @throws Exception        If the variable does not exist.
	 * @static
	 */
	public static function remove($variable) {
		if (! self::has($variable)) {
			throw new \Exception("{$variable} does not exist in the store.");
		}

		return unlink(Config::get('path', 'base') . Config::get('path', 'cache') . $variable);
	}
}