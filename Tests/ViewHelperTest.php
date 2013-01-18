<?php
include dirname(__FILE__) . '/../Library/autoloader.php';

// Start tests
class ViewHelperTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Can we call a View Helper and get the expected result?
	 *
	 * @access public
	 */
	public function testViewHelper() {
		// Load the config file
		Core\Config::load('MyProject');

		// Get a new View
		$view = new Core\View();

		// Load a View Helper
		$viewHelperContent = $view->test(array('testVar' => 'foo'));

		// And test
		$this->assertEquals($viewHelperContent, 'Test var: foo');
	}
}