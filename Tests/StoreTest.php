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
		Core\StoreRequest::put('foo', 'bar');
	}

	/**
	 * Test the Store can detect it has a variable.
	 *
	 * @access public
	 */
	public function testStoreHasVariables() {
		// Variables we have set
		$this->assertTrue(Core\StoreRequest::has('foo'));

		// Variables we have not set
		$this->assertFalse(Core\StoreRequest::has('bar'));
	}

	/**
	 * Test the Store can get a variable.
	 *
	 * @access public
	 * @expectedException Exception
	 */
	public function testStoreCanGetVariables() {
		// Variables we have set
		$this->assertEquals(Core\StoreRequest::get('foo'), 'bar');

		// Variables we have not set
		$this->assertFalse(Core\StoreRequest::get('bar'));
	}


	/**
	 * Test the Store can update a variable.
	 *
	 * @access public
	 */
	public function testStoreCanUpdateVariables() {
		// Update variables
		Core\StoreRequest::put('foo', 'foo', true);

		// Have they updated?
		$this->assertEquals(Core\StoreRequest::get('foo'), 'foo');
	}

	/**
	 * Removing a config variable.
	 *
	 * @access public
	 */
	public function testStoreCanRemoveVariables() {
		// Update variables
		Core\StoreRequest::remove('foo');

		// Have they updated?
		$this->assertFalse(Core\StoreRequest::has('foo'));
	}
}