<?php
namespace Core;

/**
 * Validates user input.
 *
 * This class can be run in "single" mode or "mass" mode. In both situations it
 * is designed to be human-readable, and provide a simple API to basic validation.
 *
 * Single mode example
 * -------------------
 *
 * <code>
 * if (Core\Validate::is('cjhill@gmail.com', 'email')) {
 *     // Houston, we have a valid email address
 * }
 * </code>
 *
 * Mass mode example
 * -----------------
 *
 * <code>
 * $form = new Validate(array(
 *     'Email address' => array(
 *         'value' => 'cjhill@gmail.com',
 *         'tests' => array(
 *             'required' => true,
 *             'is'       => 'email'
 *         )
 *     ),
 *     'Age' => array(
 *         'value' => 20,
 *         'tests' => array(
 *             'between' => array('min' => 13, 'max' => 99)
 *         )
 *     ),
 *     'Password' => array(
 *         'value' => 's3cr37',
 *         'tests' => array(
 *             'required'      => true,
 *             'required_with' => array('Email address')
 *             'length'        => array('min' => 8)
 *         )
 *     )
 * ));
 *
 * if (! $form->isValid()) {
 *     // Errors
 *     var_dump($validate->getErrors());
 * }
 * </code>
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       21/04/2013
 *
 * @todo        Integrate with a Core\Notice class.
 * @todo        Import data from a request and "bolt" on tests.
 * @todo        Regex testing.
 */
class Validate
{
	/**
	 * The inputs that need to be validated.
	 *
	 * List of possible tests to perform:
	 *
	 * <ul>
	 *     <li>'required'      => true</li>
	 *     <li>'required_with' => array('foo', 'bar')</li>
	 *     <li>
	 *         You can explicitly set both min and max, or just one:
	 *         <ul>
	 *             <li>'length' => array('min' => 5, 'max' => 10)</li>
	 *             <li>'length' => array('min' => 5)</li>
	 *             <li>'length' => array('max' => 10)</li>
	 *         </ul>
	 *     </li>
	 *     <li>'is'      => 'boolean|email|float|int|ip|url'</li>
	 *     <li>'exactly' => array('foobar', 'acme')/li>
	 *     <li>'between' => array('min' => 10, 'max' => 100)</li>
	 * </ul>
	 *
	 * @access private
	 * @var    array
	 */
	private $_input = array();

	/**
	 * Whether we have run the validation tests on the inputs.
	 *
	 * @access private
	 * @var    boolean
	 */
	private $_validated = false;

	/**
	 * A collection of errors that have occurred with the inputs and their tests.
	 *
	 * @access private
	 * @var    array
	 */
	private $_error = array();

	/**
	 * Set the inputs that need to be validated.
	 *
	 * @access public
	 * @param  array   $input   The inputs that need to be validated.
	 * @param  boolean $autorun Whether we should run the validation automatically.
	 */
	public function __construct($input, $autorun = true) {
		// Set the inputs
		$this->_input = $input;

		// Run the tests automatically without further instruction
		if ($autorun) {
			$this->validate();
		}
	}

	/**
	 * Run the validation on each input that has been passed in.
	 *
	 * @access public
	 * @return boolean Whether the validation was successful or not.
	 */
	public function validate() {
		// We only ever need to run once
		if ($this->_validated) {
			return $this->getIsValid();
		}

		// Loop through each input
		foreach ($this->_input as $inputName => $inputInfo) {
			// Sanity check that we have tests to perform
			if (! isset($inputInfo['tests'])) {
				continue;
			}

			// Loop over each test that we need to run
			foreach ($inputInfo['tests'] as $testName => $testParams) {
				// Run the test
				// Note: The function to test might not exist, so we can catch with __call.
				if (! self::$testName($inputInfo['value'], $testParams)) {
					// The test was unsuccessful, report error
					$this->addError($inputName, $testName);
				}
			}
		}

		// We have run the validation tests, so do not run again
		$this->_validated = true;

		// Pass back whether it was successful or not
		return $this->isValid();
	}

	/**
	 * Test to see if the input actually has a value.
	 *
	 * @access private
	 * @param  string  $inputValue The value of the input.
	 * @param  array   $testParams The parameters for this test.
	 * @return boolean
	 */
	private function required($inputValue, $testParams = array()) {
		return ! empty($inputValue);
	}

	/**
	 * Test to see if the input's other required fields are valid and exist.
	 *
	 * We only test to see if the required inputs have been defined. Any further
	 * validating should be done on the required input.
	 *
	 * @access private
	 * @param  string  $inputValue The value of the input.
	 * @param  array   $testParams The parameters for this test.
	 * @return boolean
	 */
	private function requiredWith($inputValue, $testParams) {
		// Loop over each of the required other fields
		foreach ($testParams as $inputRequired) {
			// Do we even have a reference to this input?
			if (! isset($this->_input[$inputRequired])) {
				return false;
			}

			// Does the parameter actually pass the required test?
			else if (! $this->required($this->_input[$inputRequired]['value'])) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Test to see if the input's length is with bounds.
	 *
	 * Note: This function can deal with a min, a max, or a min and a max boundary.
	 *
	 * @access public
	 * @param  string|array $inputValue The value of the input.
	 * @param  array        $testParams The parameters for this test.
	 * @return boolean
	 * @static
	 * @throws \Exception   If neither a min or max boundary is specified.
	 */
	public static function length($inputValue, $testParams) {
		// We need to have at least one boundary
		if (! isset($testParams['min']) && ! isset($testParams['max'])) {
			throw new \Exception('Length test requires at least a min or max boundary.');
		}

		// Get the string length of the inputs value
		// Note: We could have been given a string or array
		$length = is_array($inputValue)
			? count($inputValue)
			: strlen($inputValue);

		// Length is less than the minimum
		if (isset($testParams['min']) && $length < $testParams['min']) {
			return false;
		}

		// Length is more than the maximum
		else if (isset($testParams['max']) && $length > $testParams['max']) {
			return false;
		}

		return true;
	}

	/**
	 * Test to see if the input is a specific type.
	 *
	 * @access public
	 * @param  string  $inputValue The value of the input.
	 * @param  array   $testParams The parameters for this test.
	 * @return boolean
	 * @static
	 */
	public static function is($inputValue, $testParams) {
		// Due to a bug in filter_var we handle booleans differently
		// @see https://bugs.php.net/bug.php?id=49510
		if ($testParams == 'boolean') {
			return is_bool($inputValue);
		}

		// Work out which filter we should use
		switch ($testParams) {
			case 'email'   : $filter = \FILTER_VALIDATE_EMAIL;   break;
			case 'float'   : $filter = \FILTER_VALIDATE_FLOAT;   break;
			case 'int'     : $filter = \FILTER_VALIDATE_INT;     break;
			case 'ip'      : $filter = \FILTER_VALIDATE_IP;      break;
			case 'url'     : $filter = \FILTER_VALIDATE_URL;     break;
			default        : return false;
		}

		// And try to filer the input with this type
		return filter_var($inputValue, $filter);
	}

	/**
	 * Test to see if the input matches exactly another value.
	 *
	 * Note: We also check for same data types, so "1" and 1 will fail.
	 *
	 * @access public
	 * @param  string  $inputValue The value of the input.
	 * @param  array   $testParams The parameters for this test.
	 * @return boolean
	 * @static
	 */
	public static function exactly($inputValue, $testParams) {
		return in_array($inputValue, $testParams, true);
	}

	/**
	 * Test to see if the input is between two numbers.
	 *
	 * Note: We check the datatype of the input first to make sure it is an int.
	 *
	 * @access public
	 * @param  string     $inputValue The value of the input.
	 * @param  array      $testParams The parameters for this test.
	 * @return boolean
	 * @static
	 * @thorws \Exception If the user has supplied no min and max boundary.
	 */
	public static function between($inputValue, $testParams) {
		// User has supplied both boundaries?
		if (! isset($testParams['min']) || ! isset($testParams['max'])) {
			throw new \Exception('Between expects a min and max boundary.');
		}

		// Make sure the user has given us an int
		else if (! self::is($inputValue, 'int')) {
			return false;
		}

		// Check to see if the input value is between the two boundaries
		else if ($inputValue < $testParams['min'] || $inputValue > $testParams['max']) {
			return false;
		}

		return true;
	}

	/**
	 * Add a human readable error message.
	 *
	 * @access private
	 * @param  string  $inputValue The value of the input.
	 * @param  array   $testName   The parameters for this test.
	 * @return boolean
	 */
	private function addError($inputName, $testName) {
		// Work out the best response for the error message
		switch ($testName) {
			case 'required'     : $error = ' is required.';            break;
			case 'requiredWith' : $error = ' has not been completed.'; break;
			case 'length'       : $error = ' length is incorrect.';    break;
			case 'is'           : $error = ' type is incorrect.';      break;
			case 'exactly'      : $error = ' is invalid.';             break;
			case 'between'      : $error = ' is invalid.';             break;
			default             : $error = ' is invalid.';             break;
		}

		// Add to the error array
		$this->_error[$inputName][] = $inputName . $error;
	}

	/**
	 * Return the outcome of the validation.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isValid() {
		return count($this->_error) <= 0;
	}

	/**
	 * Return the errors that we found with the inputs.
	 *
	 * @access public
	 * @return array
	 */
	public function getErrors() {
		return $this->_error;
	}

	/**
	 * Return if a single input had an error.
	 *
	 * @access public
	 * @param  string $inputName The name of the input.
	 * @return boolean
	 */
	public function hadError($inputName) {
		return isset($this->_error[$inputName]);
	}

	/**
	 * The user has passed in a validating test that we do not know of.
	 *
	 * @access public
	 * @param  string     $functionName The name of the method the user called.
	 * @param  array      $params       The parameters that the user passed in.
	 * @magic
	 * @throws \Exception If called then the user has entered an invalid test name.
	 */
	public function __call($testName, $params) {
		throw new \Exception($testName . ' is not a valid test.');
	}
}