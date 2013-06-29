<?php
// Start tests
class ViewTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Can we add a variable?
	 *
	 * @access public
	 */
	public function testAddVariableToView() {
		$view = new Core\View();
		$view->addVariable('foo', 'bar');
		$this->assertEquals('bar', $view->getVariable('foo'));
	}

	/**
	 * Change to a layout that does not exist.
	 *
	 * @access public
	 * @expectedException Exception
	 */
	public function testChangingLayoutToNonExistentLayout() {
		$controller = new MyProject\Controller\Index();
		$controller->setLayout('foo');
	}

	/**
	 * Change the layout.
	 *
	 * @access public
	 */
	public function testChangingLayout() {
		$controller = new MyProject\Controller\Index();
		$controller->setLayout('default');
		$this->assertEquals('default', $controller->view->layout);
	}
}