<?php
namespace Core;

/**
 * Handles caching of files.
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       15/09/2012
 */
class Cache
{
	/**
	 * Is there a cached version of this file?
	 *
	 * @access public
	 * @param  string  $name What the cached object is called.
	 * @return boolean
	 */
	public static function has($name) {
		// Has the user POSTed the form?
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
	 */
	public function put($name, $content) {
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
	 */
	public function remove($name) {
		if (Cache::has($name)) {
			return unlink(Config::get('path', 'base') . Config::get('path', 'cache') . $name);
		}

		return false;
	}
}