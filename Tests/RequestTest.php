<?php
// Start tests
class RequestTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Is the URL breakdown working?
	 *
	 * @access public
	 */
	public function testUrlBreakdown() {
		// Set some POST variables before we breakdown the URL
		$_POST['bar']      = 'foo';
		$_SERVER['foobar'] = 'barfoo';

		// Load the config file
		Core\Request::setUrlFragments('/index/foo/bar/foobar', true);

		// Controller okay?
		$this->assertEquals(Core\Request::get('controller'), 'Index');

		// Action okay?
		$this->assertEquals(Core\Request::get('action'), 'foo');
	}

	/**
	 * Testing that getting the URL works as expected.
	 *
	 * @access public
	 */
	public function testGetUrl() {
		// Get the standard URL with no replacements
		Core\Request::setUrl('/index/foo/bar/foobar');
		$this->assertEquals(Core\Request::getUrl(), '/index/foo/bar/foobar');

		// And try replacing slashes with underscores
		$this->assertEquals(Core\Request::getUrl('_'), '_index_foo_bar_foobar');
	}

	/**
	 * Testing GET variables exist.
	 *
	 * @access public
	 */
	public function testGetVariables() {
		// A variable that exists
		$this->assertEquals(Core\Request::get('bar'), 'foobar');

		// A variable that does not exist
		$this->assertNull(Core\Request::get('doesNotExist'));

		// A variable that does not exist with a default return value
		$this->assertEquals(Core\Request::get('doesNotExist', 'foo'), 'foo');
	}

	/**
	 * Testing POST variables exist.
	 *
	 * @access public
	 */
	public function testPostVariables() {
		// A variable that exists
		$this->assertEquals(Core\Request::post('bar'), 'foo');

		// A variable that does not exist
		$this->assertNull(Core\Request::post('doesNotExist'));

		// A variable that does not exist with a default return value
		$this->assertEquals(Core\Request::post('doesNotExist', 'foo'), 'foo');
	}

	/**
	 * Testing SERVER variables exist.
	 *
	 * @access public
	 */
	public function testServerVariables() {
		// A variable that exists
		$this->assertEquals(Core\Request::server('foobar'), 'barfoo');

		// A variable that does not exist
		$this->assertNull(Core\Request::server('doesNotExist'));

		// A variable that does not exist with a default return value
		$this->assertEquals(Core\Request::server('doesNotExist', 'foo'), 'foo');
	}
}