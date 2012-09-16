<?php
/**
 * Provides common helper functions to the View to save more complex logic.
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       16/09/2012
 *
 * @todo Allow custom helpers. Catch all failed method calls and try and load from the View/Helper directory.
 */
class Core_ViewHelper
{
	/**
	 * The controller that we need to render.
	 *
	 * @access public
	 * @var string
	 */
	public $controller = 'index';

	/**
	 * The action that we need to render.
	 *
	 * @access public
	 * @var string
	 */
	public $action = 'index';

	/**
	 * Make a URL.
	 *
	 * By default we do not use the URL variables, but you can chose to do so.
	 *
	 * <code>
	 * array(
	 *     'controller'      => 'Index',
	 *     'action'          => 'Index',
	 *     'variables'       => array(
	 *         'foo' => 'bar',
	 *         'bar' => 'foobar'
	 *     )
	 *     'variable_retain' => false
	 * )
	 * </code>
	 *
	 * @access public
	 * @param $param array
	 * @return string
	 */
	public function url($param = array()) {
		// Set some defaults
		$defaults = array(
			'controller'      => $this->controller,
			'action'          => $this->action,
			'variables'       => isset($param['variable_retain']) && $param['variable_retain']
									? $_GET
									: array(),
			'variable_retain' => false
		);

		// However, we do not want the controller/action in the variable list
		unset($defaults['variables']['controller'], $defaults['variables']['action']);
		var_dump($defaults);

		// Merge these in with the parameters
		// Parameters will take precedence
		$param = array_merge($defaults, $param);

		// Start to build URL
		// The basics
		$url = '/' . $param['controller'] . '/' . $param['action'];

		// Any variables
		if ($param['variables']) {
			// Yes, there are variables to append, loop over them
			foreach ($param['variables'] as $variable => $value) {
				// If there is an odd amount of variables in the URL string
				// .. then we just set the last variable to true. This needs
				// .. to be the same in this case also.
				$url .= '/' . urlencode($variable) . '/' . ($value === true ? '' : $value);
			}
		}

		// URL has finished constructing, pass back
		return $url;
	}

	/**
	 * Ensure that a string is safe to be outputted to the browser.
	 *
	 * @access public
	 * @param $string string
	 * @return string
	 */
	public function safe($string) {
		return Core_Format::safeHtml($string);
	}
}