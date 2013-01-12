<?php
namespace Core;

/**
 * All of the configurations the application has set.
 *
 * You can set, get, and remove any config variable via this class.
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       16/12/2012
 */
class Config
{
	/**
	 * The holder for all the config variables.
	 *
	 * @access private
	 * @var    array
	 * @static
	 */
	private static $_store = array();

	/**
	 * Load the users config file.
	 *
	 * This contains all of the project information as well as Core config.
	 * As such, this is an important file.
	 *
	 * @access public
	 * @param  string $projectName The project that we are working with.
	 * @static
	 */
	public static function load($projectName) {
		self::$_store = parse_ini_file(
			dirname(__FILE__) . '/../' . $projectName . '/config.ini', // Inside the users application
			true                                                       // Parse into sections
		);
	}

	/**
	 * Set a config variable.
	 *
	 * We do not want to overwrite configurations by mistake, so if a config
	 * variable exists then by default we will throw an exception. The user
	 * can force the overwrite if the really want to.
	 *
	 * @access public
	 * @param  string  $section        The section to write to
	 * @param  string  $variable       The config to write to.
	 * @param  mixed   $value          The value that we wish to give the config variable.
	 * @param  boolean $forceOverwrite Whether we want to overwrite existing config variables.
	 * @return mixed                   True if success, Exception if error.
	 * @throws Exception               If the user is overwriting config variable without forcing.
	 * @static
	 */
	public static function set($section, $variable, $value, $forceOverwrite = false) {
		if (isset(self::$_store[$section][$variable]) && ! $forceOverwrite) {
			throw new \Exception("{$variable} already exists, cannot overwrite. "
				. "To force this, please pass the boolean true as the forth parameter.");
		}

		self::$_store[$section][$variable] = $value;
		return true;
	}

	/**
	 * Get a config variable.
	 *
	 * If the config variable does not exist then the $default is returned.
	 * 
	 * @param  string $section  The section to search in.
	 * @param  string $variable The config to return.
	 * @param  mixed  $default  Return this if the config variable is not found.
	 * @return mixed
	 * @static
	 */
	public static function get($section, $variable, $default = null) {
		return isset(self::$_store[$section][$variable])
			? self::$_store[$section][$variable]
			: $default;
	}

	/**
	 * Remove a variable from the config.
	 *
	 * @access public
	 * @param  string  $section  The section to write to
	 * @param  string  $variable The config to write to.
	 * @return boolean
	 * @static
	 */
	public static function remove($section, $variable) {
		if (isset(self::$_store[$section][$variable])) {
			unset(self::$_store[$section][$variable]);
			return true;
		}

		return false;
	}
}