<?php
// Start tests
class FormatTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Are we encoding correctly?
	 *
	 * @access public
	 */
	public function testGreaterThanEncoded() {
		$this->assertEquals(Core\Format::safeHtml('>foo'), '&gt;foo');
	}

	/**
	 * Are we encoding correctly?
	 *
	 * @access public
	 */
	public function testLessThanEncoded() {
		$this->assertEquals(Core\Format::safeHtml('<foo'), '&lt;foo');
	}

	/**
	 * Are we encoding correctly?
	 *
	 * @access public
	 */
	public function testDoubleQuoteEncoded() {
		$this->assertEquals(Core\Format::safeHtml('"foo'), '&quot;foo');
	}

	/**
	 * Are we encoding correctly?
	 *
	 * @access public
	 */
	public function testSingleQuoteEncoded() {
		$this->assertEquals(Core\Format::safeHtml('\'foo'), '&#039;foo');
	}

	/**
	 * Are we encoding correctly?
	 *
	 * @access public
	 */
	public function testAmpersandEncoded() {
		$this->assertEquals(Core\Format::safeHtml('&foo'), '&amp;foo');
	}

	/**
	 * Are we encoding correctly?
	 *
	 * @access public
	 */
	public function testForNonDoubleEncoded() {
		$this->assertEquals(Core\Format::safeHtml('&amp;foo'), '&amp;foo');
	}
}