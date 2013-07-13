<?php
session_start();
include dirname(__FILE__) . '/../Library/autoloader.php';
$loader = new SplClassLoader();
$loader->register();

// Start tests
class CacheTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Can we create a cached object?
	 *
	 * @access public
	 */
	public function testCreateCachedObject() {
		// Load the config file
		Core\Config::load('MyProject');
		Core\Config::set('cache', 'enable', true, true);

		// Put the cache
		Core\Cache::put('foo', 'bar');

		// The file exists?
		$this->assertTrue(Core\Cache::has('foo'));

		// And the file has the correct contents?
		$this->assertEquals(Core\Cache::get('foo'), 'bar');
	}

	/**
	 * Can we remove a cached object?
	 *
	 * @access public
	 */
	public function testRemoveCachedObject() {
		// Now remove the file
		$this->assertTrue(Core\Cache::remove('foo'));

		// The file exists?
		$this->assertFalse(Core\Cache::has('foo'));
	}

	/**
	 * Can we delete an object that does not exist?
	 *
	 * @access public
	 */
	public function testRemoveNonExistentObjectFromCache() {
		$this->assertFalse(Core\Cache::remove('foobar'));
	}
}