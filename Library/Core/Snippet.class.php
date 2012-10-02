<?php
/**
 * Return snippets of HTML with variable replacement.
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       15/09/2012
 */
class Core_Snippet extends Core_Cache
{
	/**
	 * The variables that we wish to replace.
	 *
	 * <code>
	 * array(
	 *     'foo'    => 'bar',
	 *     'foobar' => 'The replacement string'
	 * )
	 * </code>
	 *
	 * @access private
	 * @var array
	 */
	private $_variable = array();

	/**
	 * Start to build the snippet.
	 *
	 * We only want to pass in a snippet file at the moment. We do not want to give the
	 * constructor too much power, and we would rather build the snippet up as we go along.
	 *
	 * @access public
	 * @param $file string
	 * @param $path srring
	 * @throws Exception
	 */
	public function __construct($file, $path = PATH_SNIPPET) {
		parent::__construct($file, $path);
	}

	/**
	 * Add a variable to be replaced.
	 *
	 * Note: If you pass in the same variable twice then it will overwrite the first.
	 *
	 * @access public
	 * @param $variable string
	 * @param $value string
	 * @return Core_Snippet
	 */
	public function addVariable($variable, $value) {
		$this->_variable[$variable] = $value;
		return $this;
	}

	/**
	 * Return the snippet with the variables replaced.
	 *
	 * If we can use a cached version of the file then we will, otherwise we
	 * will render the snippet fresh.
	 *
	 * @access public
	 * @return string
	 */
	public function render() {
		// Can we use a cached snippet?
		if ($this->cachedFileAvailable()) {
			// We can use a cached copy, mucho quick
			return $this->getCachedFile();
		}

		// Start object buffering
		ob_start();

		// Extract variables
		extract($this->_variable);

		// Include the snippet
		include PATH_SNIPPET . $this->_file;

		// Place the buffer contents into a string
		$content = ob_get_contents();
		ob_end_clean();

		// Do we want to save this to the cache
		if ($this->_enableCache) {
			$this->saveFileToCache($content);	
		}

		// Rendering complete
		return $content;
	}
}