<?php
namespace Core;

/**
 * Interface for creating, getting, and removing items from the cache.
 *
 * Sample usage:
 *
 * <code>
 * $cache     = new Core\Cache($StorageInterface);
 * $cacheItem = $cache->get('foo');
 *
 * if ($cacheItem) {
 *     echo $cacheItem;
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
	 * How we interact with our cache items.
	 *
	 * @access private
	 * @var    StorageInterface
	 */
	private $_storageInterface;

	/**
	 * Setup the cache by stating which StorageInterface we wish to use.
	 *
	 * @access public
	 * @param  StorageInterface $storageInterface Which interface to interact with cache items.
	 */
	public function __construct($storageInterface) {
		$this->_storageInterface = $storageInterface;
	}

	/**
	 * Determines if the item is already cached, and that it is valid.
	 *
	 * @access public
	 * @param  string  $variable The name of the cached item.
	 * @return boolean
	 * @static
	 */
	public function has($variable) {
		return $this->_storageInterface->has($variable);
	}

	/**
	 * Get a cached item.
	 *
	 * @access public
	 * @param  string $variable The name of the cached item.
	 * @return string
	 * @static
	 */
	public function get($variable) {
		return $this->_storageInterface->get($variable);
	}

	/**
	 * Save an item to the cache.
	 *
	 * @access public
	 * @param  string $variable The name of the cached item.
	 * @param  string $content  The content that we wish to cache.
	 * @static
	 */
	public function put($variable, $content) {
		return $this->_storageInterface->put($variable, $content);
	}

	/**
	 * Remove an item from the cache.
	 *
	 * @param  string $variable The name of the cached item.
	 * @return boolean          Whether the cached object was successfully removed.
	 * @static
	 */
	public function remove($variable) {
		return $this->_storageInterface->remove($variable);
	}
}