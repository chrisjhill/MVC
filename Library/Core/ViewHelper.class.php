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
		$viewHelperClassName = Config::get('settings', 'project') . '\\View\\Helper\\' . $helperName;
		$viewHelper = new $viewHelperClassName();

		// Render and return
		return $viewHelper->render($param[0]);
	}
}