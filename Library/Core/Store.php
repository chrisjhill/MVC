<?php
namespace Core;

/**
 * Interface for creating, getting, and removing items from a store.
 *
 * Sample usage:
 *
 * <code>
 * $store     = new Core\Store($StorageInterface);
 * $storeItem = $store->get('foo');
 *
 * if ($storeItem) {
 *     echo $storeItem;
 * }
 * </code>
 *
 * @copyright Copyright (c) 2012-2013 Christopher Hill
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author    Christopher Hill <cjhill@gmail.com>
 * @package   MVC
 */
class Store implements Store\StorageInterface
{
	/**
	 * How we interact with our store items.
	 *
	 * @access private
	 * @var    StorageInterface
	 */
	private $_storageInterface;

	/**
	 * Setup the store by stating which StorageInterface we wish to use.
	 *
	 * @access public
	 * @param  StorageInterface $storageInterface Which interface to interact with store items.
	 */
	public function __construct($storageInterface) {
		$this->_storageInterface = $storageInterface;
	}

	/**
	 * Check whether the variable exists in the store.
	 *
	 * @access public
	 * @param  string  $variable The name of the variable to check existence of.
	 * @return boolean           If the variable exists or not.
	 */
	public function has($variable) {
		return $this->_storageInterface->has($variable);
	}

	/**
	 * Store a variable for use.
	 *
	 * @access public
	 * @param  string  $variable  The name of the variable to store.
	 * @param  mixed   $value     The data we wish to store.
	 * @param  boolean $overwrite Whether we are allowed to overwrite the variable.
	 * @return boolean            If we managed to store the variable.
	 */
	public function put($variable, $value, $overwrite = false) {
		return $this->_storageInterface->put($variable, $value);
	}

	/**
	 * Return the variable's value from the store.
	 *
	 * @access public
	 * @param  string $variable The name of the variable in the store.
	 * @return mixed
	 */
	public function get($variable) {
		return $this->_storageInterface->get($variable);
	}

	/**
	 * Remove the variable in the store.
	 *
	 * @access public
	 * @param  string $variable The name of the variable to remove.
	 * @return boolean          If the variable was removed successfully.
	 */
	public function remove($variable) {
		return $this->_storageInterface->remove($variable);
	}
}