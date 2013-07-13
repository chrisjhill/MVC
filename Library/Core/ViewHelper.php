<?php
namespace Core;

/**
 * Provides common functionality to View Helpers.
 *
 * @copyright Copyright (c) 2012-2013 Christopher Hill
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author    Christopher Hill <cjhill@gmail.com>
 * @package   MVC
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
	 * @param  mixed     $cacheName null to not cache, string otherwise.
	 * @return string               Converted template file into HTML.
	 */
	protected function renderPartial($template, $variables, $cacheName = null) {
		return self::$_view->parse(
			Config::get('path', 'base') . Config::get('path', 'project')
				. 'View/Partial/' . $template . '.phtml',
			$variables,
			$cacheName
		);
	}

	/**
	 * Returns the view.
	 *
	 * This function exists to provide a common interface for accessing the View.
	 *
	 * @access public
	 * @param  string $variableName The name of the variable to return.
	 * @return View
	 * @magic
	 */
	public function __get($variableName) {
		return self::$_view;
	}

	/**
	 * If the View Helper is echo'd then we need to render it.
	 *
	 * @access public
	 * @return string
	 * @magic
	 */
	public function __toString() {
		return $this->render();
	}
}