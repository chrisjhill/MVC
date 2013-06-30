<?php
namespace Core;

/**
 * Generates a standard HTML notice container.
 *
 * @copyright Copyright (c) 2012-2013 Christopher Hill
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author    Christopher Hill <cjhill@gmail.com>
 * @package   MVC
 */
class Notice
{
	/**
	 * What type of notice this is.
	 *
	 * @access private
	 * @var    string
	 */
	private $_status = 'info';

	/**
	 * The title of the notice.
	 *
	 * @access private
	 * @var    string
	 */
	private $_title;

	/**
	 * The introduction of the notice.
	 *
	 * @access private
	 * @var    string
	 */
	private $_intro;

	/**
	 * The bullet points for the notice.
	 *
	 * @access private
	 * @var    array
	 */
	private $_list;

	/**
	 * The CSS class to assign the notice.
	 *
	 * @access private
	 * @var    string
	 */
	private $_class;

	/**
	 * Set the title of the notice.
	 *
	 * Note: The notice can either be "success", "error", or "info".
	 *
	 * @access public
	 * @param  string      $status What type of notice this is.
	 * @return Core\Notice         For chainability.
	 */
	public function setStatus($status) {
		$this->_status = $status;
	}

	/**
	 * Set the title of the notice.
	 *
	 * @access public
	 * @param  string      $title What will be displayed at the top of the notice.
	 * @return Core\Notice        For chainability.
	 */
	public function setTitle($title) {
		$this->_title = $title;
	}

	/**
	 * Set the introduction of the notice.
	 *
	 * @access public
	 * @param  string      $intro What be displayed just below the title.
	 * @return Core\Notice        For chainability.
	 */
	public function setIntro($intro) {
		$this->_intro = $intro;
	}

	/**
	 * Add a bullet point list to the notice.
	 *
	 * @access public
	 * @param  string|array $message The items to place into a list.
	 * @return Core\Notice           For chainability.
	 * @recurvive
	 */
	public function setList($items) {
		// If an array then loop over each item and add it to the list
		if (is_array($items)) {
			foreach ($items as $item) {
				$this->setList($item);
			}
		}

		// A single item to add
		else {
			$this->_list[] = $item;
		}

		return $this;
	}

	/**
	 * Any classes to assign to the notice.
	 *
	 * @access public
	 * @param  string      $class CSS classes to assign to the notice.
	 * @return Core\Notice        For chainability.
	 */
	public function setClass($class) {
		$thi->_class = $class;
	}

	/**
	 * Generate the HTML notice.
	 *
	 * @access public
	 * @return string
	 */
	public function generate() {
		// Start to build the elements of the notice
		$status = $this->_status;
		$title  = $this->_title ? "<h3>{$this->_title}</h3>" : '';
		$intro  = $this->_intro ? "<p>{$this->_intro}</p>"   : '';
		$class  = $this->_class ? 'notice ' . $this->_class  : '';
		$list   = $this->_list  ? '<ul><li>' . implode('</li><li>', $this->_list) . '</li></ul>' : '';

		return '<div class="' . $class . '">' . $title . $intro . $list . '</div>';
	}
}