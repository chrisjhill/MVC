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
		// Let's add a variable to the view
		$this->view->addVariable(
			'foo', // Name of the variable
			$this->request->get('foo') // Variable passed in via the URL
		);
	}

	/**
	 * The error action.
	 *
	 * This action is run if you try and call an action that does not exist.
	 *
	 * @access public
	 */
	public function errorAction() {
		// Do nothing
	}
}