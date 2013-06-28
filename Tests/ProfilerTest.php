<?php
// Start tests
class ProfilerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Can we add a stack when the profiler is disabled?
	 *
	 * @access public
	 */
	public function testAddingStackToProfilerWhilstDisabled() {
		// Load the config file
		Core\Config::load('MyProject');
		Core\Config::set('profiler', 'enable', false, true);

		$this->assertFalse(Core\Profiler::register('Foo', 'bar'));
	}

	/**
	 * Start the request.
	 *
	 * @access public
	 */
	public function testRequestStarting() {
		// Test start time not set
		$stack = Core\Profiler::getProfilerData();
		$this->assertNull($stack['requestStart']);

		// Start the request
		Core\Profiler::start();

		// And test that it has been set
		$stack = Core\Profiler::getProfilerData();
		$this->assertNotNull($stack['requestStart']);
	}

	/**
	 * Stop the request.
	 *
	 * @access public
	 */
	public function testRequestStopping() {
		// Test start time not set
		$stack = Core\Profiler::getProfilerData();
		$this->assertNull($stack['requestEnd']);

		// Start the request
		Core\Profiler::stop();

		// And test that it has been set
		$stack = Core\Profiler::getProfilerData();
		$this->assertNotNull($stack['requestEnd']);
	}

	/**
	 * Can we add stacks to the profiler, and they arrange themselves correctly?
	 *
	 * @access public
	 */
	public function testAddingStackToProfile() {
		// Enable the profiler
		Core\Config::set('profiler', 'enable', true, true);

		// Add a stack
		Core\Profiler::register('Foo', 'bar');
		$stack = Core\Profiler::getProfilerData();
		$this->assertTrue(is_array($stack['stack']));
		$this->assertEquals(1, count($stack['stack']));
	}

	/**
	 * Are multiple stacks correctly ordered?
	 *
	 * @access public
	 */
	public function testAddingMultipleStacksToProfile() {
		// Add another stack
		Core\Profiler::register('Bar', 'foo');
		$stack = Core\Profiler::getProfilerData();
		$this->assertTrue(is_array($stack['stack']));
		$this->assertEquals(2, count($stack['stack']));

		// First should be Bar, then Foo
		$this->assertEquals('Foo', $stack['stack'][0]['type']);
		$this->assertEquals('Bar', $stack['stack'][1]['type']);
	}
}