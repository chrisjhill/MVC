<?php
include dirname(__FILE__) . '/../Library/autoloader.php';

// Start tests
class ConfigTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Do we have all of the config settings that are needed?
	 *
	 * @access public
	 */
	public function testConfigCoreVariablesExist() {
		// Load the config file
		Core\Config::load('MyProject');

		// Settings
		$this->assertNotNull(Core\Config::get('settings', 'project'));

		// Paths
		$this->assertNotNull(Core\Config::get('path', 'base'));
		$this->assertNotNull(Core\Config::get('path', 'root'));
		$this->assertNotNull(Core\Config::get('path', 'project'));
		$this->assertNotNull(Core\Config::get('path', 'cache'));
		
		// Cache
		$this->assertNotNull(Core\Config::get('cache', 'enable'));
		$this->assertNotNull(Core\Config::get('cache', 'life'));
	}

	/**
	 * Are we able to set new config variables?
	 *
	 * @access public
	 */
	public function testConfigCanSetNewVariables() {
		// An existing parent index
		Core\Config::set('settings', 'foo', 'bar');
		$this->assertEquals(Core\Config::get('settings', 'foo'), 'bar');

		// A new parent index
		Core\Config::set('foo', 'bar', 'foobar');
		$this->assertEquals(Core\Config::get('foo', 'bar'), 'foobar');
	}

	/**
	 * By default you cannot overwrite config variables. You need to pass the
	 * forth variable 'true' to make sure you are not doing something bad.
	 *
	 * @access public
	 * @expectedException Exception
	 */
	public function testConfigOverwritingVariablesWithNoOverwritePassed() {
		// This should fail
		Core\Config::set('settings', 'foo', 'foobar');
		$this->assertNotEquals(Core\Config::get('settings', 'foo'), 'foobar');
	}

	/**
	 * By default you cannot overwrite config variables. You need to pass the
	 * forth variable 'true' to make sure you are not doing something bad.
	 *
	 * @access public
	 */
	public function testConfigOverwritingVariableWithOverwritePassed() {
		// This should overwrite
		Core\Config::set('settings', 'foo', 'foobar', true);
		$this->assertEquals(Core\Config::get('settings', 'foo'), 'foobar');
	}

	/**
	 * Removing a config variable.
	 *
	 * @access public
	 */
	public function testConfigRemovingVariable() {
		// Removing a config variable that does not exist...
		$this->assertFalse(Core\Config::remove('foo', 'doesNotExist'));

		// .. and one that does exist
		$this->assertTrue(Core\Config::remove('foo', 'bar'));
	}
}