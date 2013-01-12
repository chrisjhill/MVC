<?php
namespace MyProject\Controller;
use Core;

class Index extends Core\Controller
{
	/**
	 * The index action
	 *
	 * This action will call the /Library/MyProject/View/Script/Index/index.phtml.
	 *
	 * @access public
	 */
	public function indexAction() {
		// Add variables to the view
		$this->view->addVariable('urlRoot', Core\Config::get('path', 'root'));
		$this->view->addVariable('statement', 'Hello world!');

		// And forward onto the next action
		$this->forward('hello');
	}

	/**
	 * The hello action
	 *
	 * @access public
	 */
	public function helloAction() {
		// Do nothing
	}

	/**
	 * The error action
	 *
	 * @access public
	 */
	public function errorAction() {
		// Do nothing
	}
}