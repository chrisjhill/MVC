<?php
include dirname(__FILE__) . '/../Library/autoloader.php';

// Start tests
class ValidateTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Testing the required test.
	 *
	 * @access public
	 */
	public function testRequiredInput() {
		// Input exists with a value
		$form = new Core\Validate(array(
			'foo' => array('value' => 'bar', 'tests' => array('required' => true))
		));
		$this->assertTrue($form->isValid());

		// Input exists, but with invalid values
		$form = new Core\Validate(array(
			'foo' => array('value' => '',    'tests' => array('required' => true))
		));
		$this->assertFalse($form->isValid());

		$form = new Core\Validate(array(
			'foo' => array('value' => null,  'tests' => array('required' => true))
		));
		$this->assertFalse($form->isValid());

		$form = new Core\Validate(array(
			'foo' => array('value' => false, 'tests' => array('required' => true))
		));
		$this->assertFalse($form->isValid());
	}

	/**
	 * Testing the required with test.
	 *
	 * @access public
	 */
	public function testRequiredWithInput() {
		// Passing tests
		// Required with single
		$form = new Core\Validate(array(
			'foo' => array('value' => 'bar', 'tests' => array('requiredWith' => array('bar'))),
			'bar' => array('value' => 'bar')
		));
		$this->assertTrue($form->isValid());

		// Required with double
		$form = new Core\Validate(array(
			'foo' => array('value' => 'bar', 'tests' => array('requiredWith' => array('bar', 'car'))),
			'bar' => array('value' => 'bar'),
			'car' => array('value' => 'bar')
		));
		$this->assertTrue($form->isValid());

		// Failing tests
		// Required with single
		$form = new Core\Validate(array(
			'foo' => array('value' => 'bar', 'tests' => array('requiredWith' => array('bar')))
		));
		$this->assertFalse($form->isValid());

		// Required with double
		$form = new Core\Validate(array(
			'foo' => array('value' => 'bar', 'tests' => array('requiredWith' => array('bar', 'car')))
		));
		$this->assertFalse($form->isValid());

		// Required with double, one valid
		$form = new Core\Validate(array(
			'foo' => array('value' => 'bar', 'tests' => array('requiredWith' => array('bar', 'car'))),
			'bar' => array('value' => 'bar')
		));
		$this->assertFalse($form->isValid());
	}

	/**
	 * Testing the inputs character length, but passing no min or max boundary
	 *
	 * @access public
	 * @expectedException Exception
	 */
	public function testLengthNoMinOrMaxBoundary() {
		// Required with double, one valid
		$form = new Core\Validate(array(
			'foo' => array('value' => 'bar', 'tests' => array('length' => true))
		));
	}

	/**
	 * Testing the inputs character length.
	 *
	 * @access public
	 */
	public function testLength() {
		// Passing tests
		// Greater than min
		$form = new Core\Validate(array(
			'foo' => array('value' => 'bar', 'tests' => array('length' => array('min' => 1)))
		));
		$this->assertTrue($form->isValid());

		// Less than max
		$form = new Core\Validate(array(
			'foo' => array('value' => 'bar', 'tests' => array('length' => array('max' => 10)))
		));
		$this->assertTrue($form->isValid());

		// Between min and max
		$form = new Core\Validate(array(
			'foo' => array('value' => 'bar', 'tests' => array('length' => array('min' => 2, 'max' => 4)))
		));
		$this->assertTrue($form->isValid());

		// Failing tests
		// Less than min
		$form = new Core\Validate(array(
			'foo' => array('value' => 'a', 'tests' => array('length' => array('min' => 2)))
		));
		$this->assertFalse($form->isValid());

		// More than max
		$form = new Core\Validate(array(
			'foo' => array('value' => 'abdefgh', 'tests' => array('length' => array('max' => 4)))
		));
		$this->assertFalse($form->isValid());

		// Not between min and max
		$form = new Core\Validate(array(
			'foo' => array('value' => 'abdefgh', 'tests' => array('length' => array('min' => 2, 'max' => 4)))
		));
		$this->assertFalse($form->isValid());
	}

	/**
	 * Testing the is type test.
	 *
	 * @access public
	 */
	public function testIsType() {
		// Passing tests
		// Boolean: true
		$form = new Core\Validate(array(
			'foo' => array('value' => true, 'tests' => array('is' => 'boolean'))
		));
		$this->assertTrue($form->isValid());
		// Boolean: false
		$form = new Core\Validate(array(
			'foo' => array('value' => false, 'tests' => array('is' => 'boolean'))
		));
		$this->assertTrue($form->isValid());

		// Email
		$form = new Core\Validate(array(
			'foo' => array('value' => 'cjhill@gmail.com', 'tests' => array('is' => 'email'))
		));
		$this->assertTrue($form->isValid());

		// Float
		$form = new Core\Validate(array(
			'foo' => array('value' => 1.5, 'tests' => array('is' => 'float'))
		));
		$this->assertTrue($form->isValid());

		// IPV4
		$form = new Core\Validate(array(
			'foo' => array('value' => '127.0.0.1', 'tests' => array('is' => 'ip'))
		));
		$this->assertTrue($form->isValid());

		// IPV6
		$form = new Core\Validate(array(
			'foo' => array('value' => '2001:0db8:85a3:0042:1000:8a2e:0370:7334', 'tests' => array('is' => 'ip'))
		));
		$this->assertTrue($form->isValid());

		// URL
		$form = new Core\Validate(array(
			'foo' => array('value' => 'http://www.google.com', 'tests' => array('is' => 'url'))
		));
		$this->assertTrue($form->isValid());

		// Failing tests
		// Boolean
		$form = new Core\Validate(array(
			'foo' => array('value' => 'bar', 'tests' => array('is' => 'boolean'))
		));
		$this->assertFalse($form->isValid());

		// Email
		$form = new Core\Validate(array(
			'foo' => array('value' => 'bar', 'tests' => array('is' => 'email'))
		));
		$this->assertFalse($form->isValid());

		// Float
		$form = new Core\Validate(array(
			'foo' => array('value' => 'bar', 'tests' => array('is' => 'float'))
		));
		$this->assertFalse($form->isValid());

		// IPV4
		$form = new Core\Validate(array(
			'foo' => array('value' => '127.0.0', 'tests' => array('is' => 'ip'))
		));
		$this->assertFalse($form->isValid());

		// URL
		$form = new Core\Validate(array(
			'foo' => array('value' => 'google.com', 'tests' => array('is' => 'url'))
		));
		$this->assertFalse($form->isValid());
	}

	/**
	 * Testing the exact test.
	 *
	 * @access public
	 */
	public function testExactly() {
		// Pass with a single option
		$form = new Core\Validate(array(
			'foo' => array('value' => 'bar', 'tests' => array('exactly' => array('bar')))
		));
		$this->assertTrue($form->isValid());

		// Pass with two options
		$form = new Core\Validate(array(
			'foo' => array('value' => 'bar', 'tests' => array('exactly' => array('bar', 'car')))
		));
		$this->assertTrue($form->isValid());

		// Fail with a single option
		$form = new Core\Validate(array(
			'foo' => array('value' => 'bar', 'tests' => array('exactly' => array('foo')))
		));
		$this->assertFalse($form->isValid());

		// Pass with two options
		$form = new Core\Validate(array(
			'foo' => array('value' => 'bar', 'tests' => array('exactly' => array('foo', 'car')))
		));
		$this->assertFalse($form->isValid());
	}

	/**
	 * Testing the between test, but with no max boundary.
	 *
	 * @access public
	 * @expectedException Exception
	 */
	public function testBetweenNoMaxBoundary() {
		$form = new Core\Validate(array(
			'foo' => array('value' => 5, 'tests' => array('between' => array('min' => 1)))
		));
	}

	/**
	 * Testing the between test, but with no mmin boundary.
	 *
	 * @access public
	 * @expectedException Exception
	 */
	public function testBetweenNoMinBoundary() {
		$form = new Core\Validate(array(
			'foo' => array('value' => 5, 'tests' => array('between' => array('max' => 10)))
		));
	}

	/**
	 * Testing the between test.
	 *
	 * @access public
	 */
	public function testBetween() {
		// Pass
		$form = new Core\Validate(array(
			'foo' => array('value' => 5, 'tests' => array('between' => array('min' => 1, 'max' => 10)))
		));
		$this->assertTrue($form->isValid());

		// Fail, not an int
		$form = new Core\Validate(array(
			'foo' => array('value' => 'bar', 'tests' => array('between' => array('min' => 1, 'max' => 10)))
		));
		$this->assertFalse($form->isValid());

		// Fail, too low
		$form = new Core\Validate(array(
			'foo' => array('value' => 2, 'tests' => array('between' => array('min' => 5, 'max' => 10)))
		));
		$this->assertFalse($form->isValid());

		// Fail, too high
		$form = new Core\Validate(array(
			'foo' => array('value' => 10, 'tests' => array('between' => array('min' => 1, 'max' => 5)))
		));
		$this->assertFalse($form->isValid());
	}
}