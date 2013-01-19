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
	 * The View, so all View Helpers can interact with it.
	 *
	 * @access public
	 * @var    View
	 */
	public static $_view;

	/**
	 * Parses a template file and returns the converted HTML.
	 *
	 * @access protected
	 * @param  string    $template  The name of the partial to render.
	 * @param  array     $variables An array of variables to replace.
	 * @param  mixed     $cacheName null to not cache, otherwise string.
	 * @return string               Converted template file into HTML.
	 */
	protected function parse($template, $variables, $cacheName = null) {
		return self::$_view->parse(
			Config::get('path', 'base') . Config::get('path', 'project')
				. 'View/Partial/' . $template . '.phtml',
			$variables,
			$cacheName
		);
	}

	/**
	 * A function to return the View in a nice way.
	 *
	 * @access public
	 * @param  string $variableName The name of the variable to return.
	 * @return View
	 */
	public function __get($variableName) {
		return self::$_view;
	}
}