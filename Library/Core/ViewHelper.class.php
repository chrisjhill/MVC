<?php
namespace Core;

/**
 * Provides common helper functions to the View to save more complex logic.
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       16/09/2012
 */
class ViewHelper
{
	/**
	 * The controller that we need to render.
	 *
	 * @access public
	 * @var    string
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
	 * @param  array  $param Parameters used to build the URL.
	 * @return string
	 */
	public function url($param = array()) {
		// Set some defaults
		$defaults = array(
			'controller'      => $this->controller,
			'action'          => '',
			'variables'       => isset($param['variable_retain']) && $param['variable_retain']
									? $_GET
									: array(),
			'variable_retain' => false
		);

		// However, we do not want the controller/action in the variable list
		unset($defaults['variables']['controller'], $defaults['variables']['action']);

		// Merge these in with the parameters
		// Parameters will take precedence
		$param = array_merge($defaults, $param);

		// Start to build URL
		// The controller
		$url = Config::get('path', 'root') . $param['controller'] . '/' . $param['action'];

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
	 * @param  string $string The string that we wish to make safe to output.
	 * @return string
	 */
	public function safe($string) {
		return Format::safeHtml($string);
	}

	/**
	 * Provides a nice interface to call view helpers.
	 *
	 * This is a magic function, so any calls to the view/view helper which do not
	 * exist will end up here. We only pass through the first parameter to make for
	 * a nicer implementation in each view helper. This is why it needs to be an array.
	 *
	 * @access public
	 * @param  string $helperName The View Helper that we wish to use.
	 * @param  array  $param      The parameters that need to be passed to the View Helper.
	 * @return string
	 */
	public function __call($helperName, $param) {
		// Try and instantiate the helper
		$viewHelperClassName = 'View_Helper_' . $helperName;
		$viewHelper = new $viewHelperClassName();

		// Call the init helper so they can set up any pre rendering settings
		if (method_exists($viewHelper, 'init')) {
			$param[0] = $viewHelper->init($param[0]);
		}

		// Render and return
		return $viewHelper->render($param[0]);
	}
}