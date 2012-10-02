<?php
/**
 * Handles all of the URL functions within this system
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       15/09/2012
 */
class Core_Url
{
	/**
	 * Return a breakdown of the URL into their sections.
	 *
	 * @access public
	 * @return array
	 */
	public function getUrlBreakdown() {
		// Set the URL
		// We do not want the start and the end slash, explode on separators, and filter
		$urlBreakdown = trim($_SERVER['REQUEST_URI'], '/');
		$urlBreakdown = explode('/', $urlBreakdown);
		$urlBreakdown = array_filter($urlBreakdown);

		// Start to piece back together and create a nice, usable, array
		$url = array(
			'controller' => isset($urlBreakdown[0]) ? $urlBreakdown[0] : 'Index',
			'action'     => isset($urlBreakdown[1]) ? $urlBreakdown[1] : 'index'
		);

		// Chunk them into variable->value
		$urlBreakdown = array_chunk($urlBreakdown, 2);

		// The first index will be the controller/action
		// We have already set this so just ignore
		unset($urlBreakdown[0]);

		// Loop over the remaining array, these are GET variables
		foreach ($urlBreakdown as $urlSegment) {
			// If there is no value, set it to true
			$url[$urlSegment[0]] = isset($urlSegment[1])
				? $urlSegment[1]
				: true;
		}

		// Put this into the GET string so we no never have to call this method again
		$_GET = array_merge($_GET, $url);
	}
}