<?php
include dirname(__FILE__) . '/../Library/autoloader.php';

// Start tests
class StoreTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test the Store can store variables.
	 *
	 * @access public
	 */
	public function testStoreCanPutVariable() {
		// Set two variables, one in the request and one in the session
		Core\Store::put('foo', 'bar', 'request');
	}

	/**
	 * Test the Store can detect it has a variable.
	 *
	 * @access public
	 */
	public function testStoreHasVariables() {
		// Variables we have set
		$this->assertTrue(Core\Store::has('foo', 'request'));

		// Variables we have not set
		$this->assertFalse(Core\Store::has('bar', 'request'));
	}

	/**
	 * Test the Store can get a variable.
	 *
	 * @access public
	 */
	public function testStoreCanGetVariables() {
		// Variables we have set
		$this->assertEquals(Core\Store::get('foo', 'request'), 'bar');

		// Variables we have not set
		$this->assertFalse(Core\Store::get('bar', 'request'));
	}


	/**
	 * Test the Store can update a variable.
	 *
	 * @access public
	 */
	public function testStoreCanUpdateVariables() {
		// Update variables
		Core\Store::put('foo', 'foo', 'request');

		// Have they updated?
		$this->assertEquals(Core\Store::get('foo', 'request'), 'foo');
	}

	/**
	 * Removing a config variable.
	 *
	 * @access public
	 */
	public function testStoreCanRemoveVariables() {
		// Update variables
		Core\Store::remove('foo', 'request');

		// Have they updated?
		$this->assertFalse(Core\Store::has('foo', 'request'));
	}
}