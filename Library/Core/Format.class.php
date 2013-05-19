<?php
namespace Core;

/**
 * Handles all of the formatting for pages.
 *
 * @copyright Copyright (c) 2012-2013 Christopher Hill
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author    Christopher Hill <cjhill@gmail.com>
 * @package   MVC
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

	/**
	 * Strips all invalid characters out of a URL.
	 *
	 * @access public
	 * @param  string $url The URL we need to parse.
	 * @return string
	 * @static
	 */
	public static function parseUrl($url) {
		return preg_replace('/[^a-z0-9-]/', '',
			strtolower(str_replace(' ', '-', $url))
		);
	}
}