<?php
namespace MyProject\View\Helper;
use Core;

/**
 * Make a URL that links to a controller/action/variables.
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       18/01/2012
 */
class Url
{
	/**
	 * Make a URL that links to a controller/action/variables.
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
	public function render($param = array()) {
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
		$url = Core\Config::get('path', 'root') . $param['controller'] . '/' . $param['action'];

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
}