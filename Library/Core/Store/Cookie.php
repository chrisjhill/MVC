<?php
namespace Core\Store;

/**
 * Stores data within the users own cookie store.
 *
 * @copyright Copyright (c) 2012-2013 Christopher Hill
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author    Christopher Hill <cjhill@gmail.com>
 * @package   MVC
 */
class Cookie implements StorageInterface
{
	/**
	 * Check whether the variable exists in the store.
	 *
	 * @access public
	 * @param  string  $variable The name of the variable to check existence of.
	 * @return boolean           If the variable exists or not.
	 */
	public function has($variable) {
		return isset($_COOKIE[$variable]);
	}

	/**
	 * Store a variable for use.
	 *
	 * @access public
	 * @param  string  $variable  The name of the variable to store.
	 * @param  mixed   $value     The data we wish to store.
	 * @param  int     $expires   How many seconds the cookie should be kept.
	 * @param  boolean $overwrite Whether we are allowed to overwrite the variable.
	 * @return boolean            If we managed to store the variable.
	 * @throws Exception          If the variable already exists when we try not to overwrite it.
	 */
	public function put($variable, $value, $expires = 1314000, $overwrite = false) {
		// If it exists, and we do not want to overwrite, then throw exception
		if ($this->has($variable) && ! $overwrite) {
			throw new \Exception("{$variable} already exists in the store.");
		}

		setcookie($variable, $value, $expires, '/', '.');
		return $this->has($variable);
	}

	/**
	 * Return the variable's value from the store.
	 *
	 * @access public
	 * @param  string $variable The name of the variable in the store.
	 * @return mixed
	 * @throws Exception        If the variable does not exist.
	 */
	public function get($variable) {
		if (! $this->has($variable)) {
			throw new \Exception("{$variable} does not exist in the store.");
		}

		return $_COOKIE[$variable];
	}

	/**
	 * Remove the variable in the store.
	 *
	 * @access public
	 * @param  string $variable The name of the variable to remove.
	 * @throws Exception        If the variable does not exist.
	 */
	public function remove($variable) {
		if (! $this->has($variable)) {
			throw new \Exception("{$variable} does not exist in the store.");
		}

		// Remove the cookie by setting its expires in the past
		setcookie($variable, '', (time() - 3600));
	}
}