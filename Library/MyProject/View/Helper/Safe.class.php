<?php
namespace MyProject\View\Helper;
use Core;

class Safe
{
	/**
	 * Ensure that a string is safe to be outputted to the browser.
	 *
	 * @access public
	 * @param  string $string The string that we wish to make safe to output.
	 * @return string
	 */
	public function render($string) {
		return Core\Format::safeHtml($string);
	}
}