<?php
namespace MyProject\Controller;
use Core;

class Index extends Core\Controller
{
	// Yes, we want to enable the cache for this file
	public $enableCacheAction = false;

	// Set the life of the cached file to 10 minutes
	public $cacheActionLife = 600;

	// Set cache for the entry point
	public $enableCacheEntry = false;

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