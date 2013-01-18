<?php
namespace MyProject\View\Helper;
use Core;

/**
 * A test View Helper.
 *
 * Just outputs a very simple partial.
 *
 * This only exists for PHPUnit tests. Delete it as and when you want.
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       18/01/2012
 */
class Test extends Core\ViewHelper
{
	/**
	 * Render a View Helper.
	 *
	 * @access public
	 * @param  array  $params A collection of variables that has been passed to us.
	 * @return string         A rendered View Helper Partial template file.
	 */
	public function render($params = array()) {
		return $this->parse('test', array(
			'testVar' => $params['testVar']
		));
	}
}