<?php
namespace Core;

/**
 * Handles all of the formatting onto pages, such as safe strings.
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       15/09/2012
 */
class Format
{
	/**
	 * Make sure that anything outputted to the browser is safe.
	 *
	 * @access public
	 * @param  string $string The string that we want to make safe to output.
	 * @return string
	 * @static
	 */
	public static function safeHtml($string) {	
		return htmlspecialchars($string, ENT_QUOTES, 'UTF-8', false);
	}
}