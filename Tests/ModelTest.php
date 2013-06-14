<?php
// Start tests
class ModelTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Testing SELECTing all fields.
	 *
	 * @access public
	 */
	public function testSelectAll() {
		// Create our test model object
		$user = new MyProject\Model\User();
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM `user`");
	}

	/**
	 * Testing SELECTing certain fields.
	 *
	 * @access public
	 */
	public function testSelectFields() {
		// Create our test model object
		$user = new MyProject\Model\User();
		$user->select('`user_id`');
		$this->assertEquals($this->format($user->build('select')), "SELECT `user_id` FROM `user`");
		$user->select('`name`');
		$this->assertEquals($this->format($user->build('select')), "SELECT `user_id`, `name` FROM `user`");
	}

	/**
	 * Testing SELECT AS.
	 *
	 * @access public
	 */
	public function testSelectAs() {
		// Create our test model object
		$user = new MyProject\Model\User();
		$user->select('`user_id`', 'user');
		$this->assertEquals($this->format($user->build('select')), "SELECT `user_id` AS 'user' FROM `user`");
	}

	/**
	 * Testing SELECTing fields using functions.
	 *
	 * @access public
	 */
	public function testSelectFieldsFunction() {
		// Create our test model object
		$user = new MyProject\Model\User();
		$user->select('DISTINCT(`email`)');
		$this->assertEquals($this->format($user->build('select')), "SELECT DISTINCT(`email`) FROM `user`");
	}

	/**
	 * Testing SELECTing fields using functions and setting them to AS.
	 *
	 * @access public
	 */
	public function testSelectFieldsFunctionAs() {
		// Create our test model object
		$user = new MyProject\Model\User();
		$user->select('DISTINCT(`email`)', 'email');
		$this->assertEquals($this->format($user->build('select')), "SELECT DISTINCT(`email`) AS 'email' FROM `user`");
	}

	/**
	 * Testing FROM.
	 *
	 * @access public
	 */
	public function testFrom() {
		// Create our test model object
		$user = new MyProject\Model\User();
		$user->from('user');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM `user`");
		$user->from('foo');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM `user`, `foo`");
	}

	/**
	 * Testing WHERE.
	 *
	 * @access public
	 */
	public function testWhere() {
		// Create our test model object
		$user = new MyProject\Model\User();
		$user->where('user_id', '=', 1);
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM `user` WHERE `user_id` = :__where_0");
		$user->where('name', '=', 'Chris', 'AND');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM `user` WHERE `user_id` = :__where_0 AND `name` = :__where_1");
	}

	/**
	 * Testing WHERE.
	 *
	 * @access public
	 */
	public function testWhereBraces() {
		// Create our test model object
		$user = new MyProject\Model\User();
		$user->brace('open');
			$user->where('user_id', '=', 1);
		$user->brace('close');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM `user` WHERE (`user_id` = :__where_1)");

		// Create our test model object
		$user = new MyProject\Model\User();
		$user->brace('open');
			$user->where('user_id', '=', 1);
			$user->where('name',    '=', 'Chris', 'AND');
		$user->brace('close');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM `user` WHERE (`user_id` = :__where_1 AND `name` = :__where_2)");

		// Create our test model object
		$user = new MyProject\Model\User();
		$user->brace('open');
			$user->where('user_id', '=', 1);
		$user->brace('close');
		$user->brace('open');
			$user->where('name',    '=', 'Chris', 'AND');
		$user->brace('close');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM `user` WHERE (`user_id` = :__where_1) AND (`name` = :__where_4)");

		// Create our test model object
		$user = new MyProject\Model\User();
		$user->brace('open');
			$user->brace('open');
				$user->where('user_id', '=', 1);
			$user->brace('close');
		$user->brace('close');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM `user` WHERE ((`user_id` = :__where_2))");
	}

	/**
	 * Strip all of the excess whitespace from the query
	 * @param  [type] $sql [description]
	 * @return [type]      [description]
	 */
	private function format($sql) {
		return str_replace(' , ', ', ', preg_replace('/\s+/', ' ', trim($sql)));
	}
}