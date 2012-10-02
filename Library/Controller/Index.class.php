<?php
class Controller_Index extends Core_Controller
{
	/**
	 * Yes, we want to enable the cache for this file.
	 *
	 * @access public
	 * @var boolean
	 */
	public $enableCache = false;

	/**
	 * Set the life of the cached file to 30 seconds.
	 *
	 * @access public
	 * @var int
	 */
	public $cacheLife = 30;

	/**
	 * The index action
	 *
	 * This action will call the /libs/View/Index/index.phtml.
	 *
	 * @access public
	 */
	public function indexAction() {
		// Add a variable to the view
		$this->view->addVariable('statement', 'Hello world!');

		// And forward onto the next action
		$this->forward('hello');
	}

	/**
	 * The hello action
	 *
	 * This action will call the /libs/View/Index/hello.phtml.
	 *
	 * @access public
	 */
	public function helloAction() {
		// Do nothing
	}

	/**
	 * The error action
	 *
	 * This action will call the /libs/View/Index/error.phtml.
	 *
	 * @access public
	 */
	public function errorAction() {
		// Do nothing
	}
}