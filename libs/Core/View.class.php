<?php
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
class Core_View extends Core_ViewHelper
{
	/**
	 * Which layout we are going to use for this view.
	 *
	 * @access public
	 * @var string
	 */
	public $layout = 'default';

	/**
	 * Information on whether to cache the view or not.
	 * 
	 * @access public
	 * @var Core_Cache
	 */
	public $cache;

	/**
	 * The variables that we want to pass to this view.
	 *
	 * @access public
	 * @var array
	 */
	public $_variables = array();

	/**
	 * Add a variable to the view.
	 *
	 * These variables will be made available to the view. Any variable that has already
	 * been defined will be overwritten.
	 *
	 * @access public
	 * @param $variable string
	 * @param $value string
	 */
	public function addVariable($variable, $value) {
		$this->_variables[$variable] = $value;
	}

	/**
	 * Render the page.
	 *
	 * @access public
	 */
	public function render() {
		// Can we use a cache to speed things up?
		// If the cache object exists then it means the controller wants to use caching
		// However, the action might have disabled it
		if ($this->cache && $this->cache->cachedFileAvailable()) {
			// The cache is enabled and there is an instance of the file in cache
			$viewContent = $this->cache->getCachedFile();
		}

		// Nope, there is no cache
		else {
			// Does the view file exist?
			if (! file_exists(PATH_VIEW . $this->controller . DIRECTORY_SEPARATOR . $this->action . '.phtml')) {
				throw new Exception('The view ' . $this->action . ' does not exist in ' . $this->controller . '.');
			}

			// The view exists
			// Extract the variables that have been set
			if ($this->_variables) {
				extract($this->_variables);
			}

			// Enable object buffering
			ob_start();

			// And include the file for parsing
			include PATH_VIEW . $this->controller . DIRECTORY_SEPARATOR . $this->action . '.phtml';

			// Get the content of the view after parsing, and dispose of the buffer
			$viewContent = ob_get_contents();
			ob_end_clean();

			// If we are using the cache then save it
			if ($this->cache && $this->cache->getCacheEnabled()) {
				$this->cache->saveFileToCache($viewContent);
			}
		}

		// Include the layout
		include PATH_LAYOUT . $this->layout . '.phtml';

		// And now, the journey ends
		// We die so that we do not call other action's render()
		die();
	}
}