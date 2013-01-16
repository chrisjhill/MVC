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