<?php
namespace MyProject\View\Helper;
use Core;

/**
 * Ensure that a string is safe to be outputted to the browser.
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       18/01/2012
 */
class Safe
{
	/**
	 * Ensure that a string is safe to be outputted to the browser.
	 *
	 * <code>
	 * array(
	 *     'string' => 'Evil string'
	 * )
	 * </code>
	 *
	 * @access public
	 * @param  array  $params The string that we wish to make safe to output.
	 * @return string
	 */
	public function render($params) {
		return Core\Format::safeHtml($params['string']);
	}
}