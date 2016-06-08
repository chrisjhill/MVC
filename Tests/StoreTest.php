<?php
session_start();
include dirname(__FILE__) . '/../Library/autoloader.php';
$loader = new SplClassLoader();
$loader->register();

// Start tests
class StoreTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Can we create a store object?
	 *
	 * @access public
	 * @expectedException Exception
	 */
	public function testCreateCachedObject() {
		// Load the config file
		Core\Config::load('MyProject');
		Core\Config::set('cache', 'enable', true, true);

		// Put the cache
		$store = new Core\Store(new Core\Store\File());
		$store->put('foo', 'bar');

		$this->assertTrue($store->has('foo'));
		$this->assertEquals($store->get('foo'), 'bar');
		$this->assertTrue($store->remove('foo'));
		$this->assertFalse($store->has('foo'));
		$this->assertFalse($store->remove('foobar'));
	}
}