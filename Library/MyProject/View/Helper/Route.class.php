<?php
namespace MyProject\View\Helper;
use Core;

/**
 * Create a link from a pre-defined route.
 *
 * @copyright   2013 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       09/04/2013
 */
class Route
{
	/**
	 * Create a link from a pre-defined route.
	 *
	 * <code>
	 * array(
	 *     'route'  => 'Foo',
	 *     'params' => array(
	 *         'bar'  => 'Hello',
	 *         'amce' => 'World'
	 *     )
	 * )
	 * </code>
	 *
	 * @access public
	 * @param  array  $param Parameters used to build the URL.
	 * @return string
	 */
	public function render($param = array()) {
		return Core\Config::get('path', 'root') . Core\Router::reverse(
			$param['route'],
			$param['params']
		);
	}
}