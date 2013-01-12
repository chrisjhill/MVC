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
	 * The file that we want to use.
	 * 
	 * @access protected
	 * @var    string
	 */
	protected $_file;

	/**
	 * Whether we can actually use the cache.
	 *
	 * Set to false by default as we should not need to use it.
	 *
	 * @access protected
	 * @var    boolean
	 */
	protected $_enableCache = false;

	/**
	 * How long we should use the cache before regenerating.
	 *
	 * Set to one hour by default.
	 *
	 * @access private
	 * @var    int
	 */
	private $_cacheLife = 3600;

	/**
	 * Whether this cache is for a specific user and not for general population.
	 *
	 * The user ID will be appended to the start of the file name, e.g.,
	 * 123_file_name.tpl
	 *
	 * @access private
	 * @var    int
	 */
	private $_cacheUser;

	/**
	 * The location that we are going to use to store the cached file.
	 *
	 * @access private
	 * @var    string
	 */
	private $_cacheLocation;

	/**
	 * Start to build the cached file.
	 *
	 * We only want to pass in a file at the moment. We do not want to give the constructor
	 * too much power, and we would rather build the cache up as we go along.
	 *
	 * @access public
	 * @param  string    $file         The file we wish to cache.
	 * @param  string    $path         The path to the file we wish to cache.
	 * @param  boolean   $performExist Check to see whether the file exists before caching.
	 * @throws Exception               If the file we want to cache does not exist.
	 */
	public function __construct($file, $path, $performExist = true) {
		// Do we actually have this file?
		if ($performExist) {
			if (! file_exists($path . $file)) {
				throw new Exception('Unable to locate the file: ' . $path . $file);
			}
		}

		// Set the file to use
		$this->_file = $file;
		$this->setCacheLocation();
	}

	/**
	 * Set whether we want to use the cache or not.
	 *
	 * @access public
	 * @param  boolean $enableCache Set whether we wish to enable to disable.
	 * @return Cache
	 */
	public function setCache($enableCache) {
		$this->_enableCache = $enableCache;
		return $this;
	}

	/**
	 * How long we should keep the copy of the cache before regenerating.
	 *
	 * Pass in seconds (3600 = one hour, 86400 = one day, etc.).
	 *
	 * @access public
	 * @param  int    $life How long we want our cached object to live for.
	 * @return Cache
	 */
	public function setCacheLife($life) {
		$this->_cacheLife = $life;
		return $this;
	}

	/**
	 * Set whether this cache is meant for a particular user.
	 *
	 * @access public
	 * @param  int    $userId The user ID for this cached object.
	 * @return Cache
	 */
	public function setUser($userId) {
		$this->_cacheUser = $userId;
		return $this;
	}

	/**
	 * Build the cache location.
	 *
	 * @access private
	 */
	private function setCacheLocation() {
		// Have we already set the cache location?
		if ($this->_cacheLocation) {
			return false;
		}

		// Build the location of the cache
		// A specific user?
		if ($this->_cacheUser) {
			// For a specific user
			$this->_cacheLocation = $this->_cacheUser . '_';
		}

		// And set the non-unique file name section
		$this->_cacheLocation = str_replace('/', '_', $this->_file);
	}

	/**
	 * Can we use the cache?
	 *
	 * @access public
	 * @return boolean
	 */
	public function cachedFileAvailable() {
		// Have we said we want to use the cache?
		if (! $this->_enableCache) {
			return false;
		}

		// Set the location of the cache file
		$this->setCacheLocation();

		// Does the file already exist?
		if (! file_exists(Config::get('path', 'cache') . $this->_cacheLocation)) {
			return false;
		}

		// The file exists, but is it too stale?
		return $_SERVER['REQUEST_TIME'] - filemtime(Config::get('path', 'cache') . $this->_cacheLocation) <= $this->_cacheLife;
	}

	/**
	 * Get the cached file that is pre-rendered.
	 * 
	 * @access public
	 * @return string
	 */
	public function getCachedFile() {
		return file_get_contents(Config::get('path', 'cache') . $this->_cacheLocation);
	}

	/**
	 * Save the file to the cache.
	 * 
	 * @access public
	 * @param  string $content The string that we wish to cache.
	 */
	public function saveFileToCache($content) {
		// If the content is nothing then something has obviously gone wrong
		// Do not cache otherwise we'll have nothing but problems
		if ($content != '') {
			file_put_contents(Config::get('path', 'cache') . $this->_cacheLocation, $content);
		}
	}

	/**
	 * Return whether the cache is enabled or not.
	 *
	 * @access public
	 * @return boolean
	 */
	public function getCacheEnabled() {
		return $this->_enableCache;
	}

	/**
	 * Return the lifespan on the file.
	 *
	 * @access public
	 * @return int
	 */
	public function getCacheLife() {
		return $this->_cacheLife;
	}
}