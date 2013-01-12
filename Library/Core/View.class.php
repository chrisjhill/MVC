<?php
namespace Core;

/**
 * Handles the main functionality of the view including the parsing,
 * caching, variable storage.
 *
 * Also controls which layout will be shown and provides the means for
 * View Helpers to be called.
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       15/09/2012
 */
class View extends ViewHelper
{
	/**
	 * Which layout we are going to use for this view.
	 *
	 * @access public
	 * @var    string
	 */
	public $layout = 'default';

	/**
	 * Information on whether to cache the view or not.
	 * 
	 * @access public
	 * @var    Cache
	 */
	public $cacheAction;

	/**
	 * Information on whether to cache the the whole page including layout.
	 *
	 * If we have a cached entry point then we exit as soon as possible - in the
	 * Bootstrap initRequest() method which is called as soon as we know the
	 * controller and action.
	 * 
	 * @access public
	 * @var    Cache
	 */
	public $cacheEntry;

	/**
	 * The variables that we want to pass to this view.
	 *
	 * @access public
	 * @var    array
	 */
	public $_variables = array();

	/**
	 * Add a variable to the view.
	 *
	 * These variables will be made available to the view. Any variable that has already
	 * been defined will be overwritten.
	 *
	 * @access public
	 * @param  string $variable The variable we wish to add to the view.
	 * @param  string $value    The value of the variable.
	 */
	public function addVariable($variable, $value) {
		$this->_variables[$variable] = $value;
	}

	/**
	 * Returns a set variable if it exists.
	 *
	 * @access public
	 * @param  string $variable The variable that we wish to retrieve from the view.
	 * @return mixed
	 */
	public function getVariable($variable) {
		return isset($this->_variables[$variable])
			? $this->_variables[$variable]
			: false;
	}

	/**
	 * Render the page.
	 *
	 * @access public
	 * @throws Exception If the view does not exist.
	 */
	public function render() {
		// Can we use a cache to speed things up?
		// If the cache object exists then it means the controller wants to use caching
		// However, the action might have disabled it
		if ($this->cacheAction && $this->cacheAction->cachedFileAvailable()) {
			// The cache is enabled and there is an instance of the file in cache
			$viewContent = $this->cacheAction->getCachedFile();
		}

		// Nope, there is no cache
		else {
			// Set the action location we need to run
			$urlAction = Config::get('path', 'base') . Config::get('path', 'view_script')
				. $this->controller . '/' . $this->action . '.phtml';

			// Does the view file exist?
			if (! file_exists($urlAction)) {
				throw new \Exception('The view ' . $this->action . ' does not exist in ' . $this->controller);
			}

			// The view exists
			// Extract the variables that have been set
			if ($this->_variables) {
				extract($this->_variables);
			}

			// Enable object buffering
			ob_start();

			// And include the file for parsing
			include $urlAction;

			// Get the content of the view after parsing, and dispose of the buffer
			$viewContent = ob_get_contents();
			ob_end_clean();

			// If we are using the cache then save it
			if ($this->cacheAction && $this->cacheAction->getCacheEnabled()) {
				$this->cacheAction->saveFileToCache($viewContent . ' <!-- Cache generated: ' .  date('r'). ' //-->');
			}
		}

		// Now start to wrap the view content in the layout
		// Enable object buffering
		ob_start();

		// Include the layout
		include Config::get('path', 'base') . Config::get('path', 'layout')
			. $this->layout . '.phtml';

		// Get the content of the view after parsing, and dispose of the buffer
		$fullContent = ob_get_contents();
		ob_end_clean();

		// If we are using the cache then save it
		if ($this->cacheEntry && $this->cacheEntry->getCacheEnabled()) {
			$this->cacheEntry->saveFileToCache($fullContent . ' <!-- Cache generated: ' .  date('r'). ' //-->');
		}

		// Inform the bootstrap that we are about to shutdown
		Bootstrap::initShutdown($this->controller, $this->action);

		// And now, the journey ends
		// We die so that we do not call other action's render()
		die($fullContent);
	}
}