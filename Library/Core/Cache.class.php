<?php
namespace Core;

/**
 * Handles caching of files.
 *
 * @package   MVC
 * @copyright Copyright (c) 2012-2013 Christopher Hill
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author    Christopher Hill <cjhill@gmail.com>
 */
class Cache
{
	/**
	 * Is there a cached version of this file?
	 *
	 * @access public
	 * @param  string  $name What the cached object is called.
	 * @return boolean
	 * @static
	 */
	public static function has($name) {
		// Has the user POST'ed the form?
		if (Request::server('REQUEST_METHOD') == 'POST') {
			return false;
		}

		// Have we said we want to use the cache?
		else if (! Config::get('cache', 'enable')) {
			return false;
		}

		// Does the file already exist?
		if (! file_exists(Config::get('path', 'base') . Config::get('path', 'cache') . $name)) {
			return false;
		}

		// The file exists, but is it stale?
		return Request::server('REQUEST_TIME') - filemtime(Config::get('path', 'base') . Config::get('path', 'cache') . $name)
			<= Config::get('cache', 'life');
	}

	/**
	 * Get the cached file.
	 *
	 * Note: You should call has() before this method to ensure it exists.
	 *
	 * @access public
	 * @param  string  $name What the cached object is called.
	 * @return string
	 * @static
	 */
	public static function get($name) {
		return file_get_contents(Config::get('path', 'base') . Config::get('path', 'cache') . $name);
	}

	/**
	 * Save the file to the cache.
	 *
	 * @access public
	 * @param  string $name What the cached object is called.
	 * @param  string $content The string that we wish to cache.
	 * @static
	 */
	public static function put($name, $content) {
		file_put_contents(
			Config::get('path', 'base') . Config::get('path', 'cache') . $name,
			$content
		);
	}

	/**
	 * Removing a cached object.
	 *
	 * @param  string $name What the cached object is called.
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