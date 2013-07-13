<?php
namespace Core;

/**
 * Interface for creating, getting, and removing items from the cache.
 *
 * Sample usage:
 *
 * <code>
 * if (Core\Cache::has('foo')) {
 *     echo Core\Cache::get('foo');
 * } else {
 *     $var = 'Hello World!';
 *     Core\Cache::put('foo', $var);
 *     echo Core\Cache::get('foo');
 * }
 * </code>
 *
 * @copyright Copyright (c) 2012-2013 Christopher Hill
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author    Christopher Hill <cjhill@gmail.com>
 * @package   MVC
 */
class Cache
{
	/**
	 * Determines if the item is already cached, and that it is valid.
	 *
	 * @access public
	 * @param  string  $name The name of the cached item.
	 * @return boolean
	 * @static
	 */
	public static function has($name) {
		// Do not cache POST'ed requests as it may effect the output
		if (Request::server('REQUEST_METHOD') == 'POST') {
			return false;
		}

		// If the cache is disabled then do not cache
		else if (! Config::get('cache', 'enable')) {
			return false;
		}

		// If the file does not exist then it has not been cached
		if (! file_exists(Config::get('path', 'base') . Config::get('path', 'cache') . $name)) {
			return false;
		}

		// Check the time the item was created to see if it is stale
		return Request::server('REQUEST_TIME') - filemtime(Config::get('path', 'base') . Config::get('path', 'cache') . $name)
			<= Config::get('cache', 'life');
	}

	/**
	 * Get a cached item.
	 *
	 * Note: You should call has() before get()'ing and item to ensure it exists.
	 *
	 * @access public
	 * @param  string $name The name of the cached item.
	 * @return string
	 * @static
	 */
	public static function get($name) {
		return file_get_contents(Config::get('path', 'base') . Config::get('path', 'cache') . $name);
	}

	/**
	 * Save an item to the cache.
	 *
	 * @access public
	 * @param  string $name    The name of the cached item.
	 * @param  string $content The content that we wish to cache.
	 * @static
	 */
	public static function put($name, $content) {
		file_put_contents(
			Config::get('path', 'base') . Config::get('path', 'cache') . $name,
			$content
		);
	}

	/**
	 * Remove an item from the cache.
	 *
	 * @param  string $name The name of the cached item.
	 * @return boolean      Whether the cached object was successfully removed.
	 * @static
	 */
	public static function remove($name) {
		if (Cache::has($name)) {
			return unlink(Config::get('path', 'base') . Config::get('path', 'cache') . $name);
		}

		return false;
	}
}