<?php
namespace Core\Store;

/**
 * Stores data within Memcache.
 *
 * @copyright Copyright (c) 2012-2013 Christopher Hill
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author    Christopher Hill <cjhill@gmail.com>
 * @package   MVC
 */
class Memcache implements StorageInterface
{
	/**
	 * The reference to the Memcache server.
	 *
	 * @access private
	 * @var    \Memcache
	 */
	private $_memcache;

	/**
	 * Setup Memcache for storing data.
	 *
	 * @access public
	 * @param  string $host The location of the Memcache server.
	 * @param  string $port The port the Memcache server lives on.
	 */
	public static function seup($host, $port) {
		self::_memcache = new \Memcache();
		self::_memcache->addServer($host, $port);
	}

	/**
	 * Check whether the variable exists in the store.
	 *
	 * @access public
	 * @param  string  $variable The name of the variable to check existence of.
	 * @return boolean           If the variable exists or not.
	 * @static
	 */
	public static function has($variable) {
		return (bool)self::$_memcache->get($variable);
	}

	/**
	 * Store a variable for use.
	 *
	 * @access public
	 * @param  string  $variable The name of the variable to store.
	 * @param  mixed   $value    The data we wish to store.
	 * @return boolean           If we managed to store the variable.
	 * @throws Exception         If the variable already exists when we try not to overwrite it.
	 * @static
	 */
	public static function put($variable, $value, $overwrite = false) {
		// If it exists, and we do not want to overwrite, then throw exception
		if (self::has($variable) && ! $overwrite) {
			throw new \Exception("{$variable} already exists in the store.");
		}

		return self::$_memcache->set($variable, $value);
	}

	/**
	 * Return the variable's value from the store.
	 *
	 * @access public
	 * @param  string $variable The name of the variable in the store.
	 * @return mixed
	 * @throws Exception        If the variable does not exist.
	 * @static
	 */
	public static function get($variable) {
		// If it exists, and we do not want to overwrite, then throw exception
		if (! self::has($variable)) {
			throw new \Exception("{$variable} does not exist in the store.");
		}

		return (bool)self::$_memcache->get($variable);
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
		// If it exists, and we do not want to overwrite, then throw exception
		if (! self::has($variable)) {
			throw new \Exception("{$variable} does not exist in the store.");
		}

		return (bool)self::$_memcache->delete($variable);
	}
}