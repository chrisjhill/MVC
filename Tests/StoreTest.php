<?php
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
		Core\Store\Request::put('foo', 'bar');
	}

	/**
	 * Test the Store can detect it has a variable.
	 *
	 * @access public
	 */
	public function testStoreHasVariables() {
		// Variables we have set
		$this->assertTrue(Core\Store\Request::has('foo'));

		// Variables we have not set
		$this->assertFalse(Core\Store\Request::has('bar'));
	}

	/**
	 * Test the Store can get a variable.
	 *
	 * @access public
	 * @expectedException Exception
	 */
	public function testStoreCanGetVariables() {
		// Variables we have set
		$this->assertEquals(Core\Store\Request::get('foo'), 'bar');

		// Variables we have not set
		$this->assertFalse(Core\Store\Request::get('bar'));
	}


	/**
	 * Test the Store can update a variable.
	 *
	 * @access public
	 */
	public function testStoreCanUpdateVariables() {
		// Update variables
		Core\Store\Request::put('foo', 'foo', true);

		// Have they updated?
		$this->assertEquals(Core\Store\Request::get('foo'), 'foo');
	}

	/**
	 * Removing a config variable.
	 *
	 * @access public
	 */
	public function testStoreCanRemoveVariables() {
		// Update variables
		Core\Store\Request::remove('foo');

		// Have they updated?
		$this->assertFalse(Core\Store\Request::has('foo'));
	}
}