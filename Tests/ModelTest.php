<?php
// Start tests
class ModelTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Testing the get and set magic methods.
	 *
	 * @access public
	 */
	public function testSetAndGet() {
		// Create our test model object
		$user = new MyProject\Model\User();
		$this->assertFalse($user->name);
		$user->name = 'Chris';
		$this->assertEquals($user->name, 'Chris');
	}

	/**
	 * Testing passing an array of data to the model.
	 *
	 * @access public
	 */
	public function testMassSettingOfData() {
		// Create our test model object
		$user = new MyProject\Model\User();
		$user->update(array('name' => 'Chris', 'email' => 'cjhill@gmail.com'));
		$this->assertEquals($user->name,  'Chris');
		$this->assertEquals($user->email, 'cjhill@gmail.com');
	}

	/**
	 * Testing SELECTing all fields.
	 *
	 * @access public
	 */
	public function testSelectAll() {
		// Create our test model object
		$user = new MyProject\Model\User();
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM user");
	}

	/**
	 * Testing SELECTing certain fields.
	 *
	 * @access public
	 */
	public function testSelectFields() {
		// Create our test model object
		$user = new MyProject\Model\User();
		$user->select('user_id');
		$this->assertEquals($this->format($user->build('select')), "SELECT user_id FROM user");
		$user->select('name');
		$this->assertEquals($this->format($user->build('select')), "SELECT user_id, name FROM user");
	}

	/**
	 * Testing SELECT AS.
	 *
	 * @access public
	 */
	public function testSelectAs() {
		// Create our test model object
		$user = new MyProject\Model\User();
		$user->select('user_id', 'user');
		$this->assertEquals($this->format($user->build('select')), "SELECT user_id AS 'user' FROM user");
	}

	/**
	 * Testing SELECTing fields using functions.
	 *
	 * @access public
	 */
	public function testSelectFieldsFunction() {
		// Create our test model object
		$user = new MyProject\Model\User();
		$user->select('DISTINCT(email)');
		$this->assertEquals($this->format($user->build('select')), "SELECT DISTINCT(email) FROM user");
	}

	/**
	 * Testing SELECTing fields using functions and setting them to AS.
	 *
	 * @access public
	 */
	public function testSelectFieldsFunctionAs() {
		// Create our test model object
		$user = new MyProject\Model\User();
		$user->select('DISTINCT(email)', 'email');
		$this->assertEquals($this->format($user->build('select')), "SELECT DISTINCT(email) AS 'email' FROM user");
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
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM user");
		$user->from('foo');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM user, foo");
	}

	/**
	 * Testing FROM joins.
	 *
	 * @access public
	 */
	public function testFromJoin() {
		// Create our test model object
		$user = new MyProject\Model\User();
		$user->from('user');
		$user->from('foo', 'LEFT', 'email', 'email');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM user, LEFT JOIN foo ON email = email");

		// Create our test model object
		$user = new MyProject\Model\User();
		$user->from('user');
		$user->from('foo', 'RIGHT', 'email', 'email');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM user, RIGHT JOIN foo ON email = email");

		// Create our test model object
		$user = new MyProject\Model\User();
		$user->from('user');
		$user->from('foo', 'INNER', 'email', 'email');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM user, INNER JOIN foo ON email = email");
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
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM user WHERE user_id = :__where_0");

		// Create our test model object
		$user = new MyProject\Model\User();
		$user->where('user_id', '=', 1, 'AND');
		$user->where('name', '=', 'Chris');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM user WHERE user_id = :__where_0 AND name = :__where_1");
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
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM user WHERE (user_id = :__where_1)");

		// Create our test model object
		$user = new MyProject\Model\User();
		$user->brace('open');
			$user->where('user_id', '=', 1, 'AND');
			$user->where('name',    '=', 'Chris');
		$user->brace('close');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM user WHERE (user_id = :__where_1 AND name = :__where_2)");

		// Create our test model object
		$user = new MyProject\Model\User();
		$user->brace('open');
			$user->where('user_id', '=', 1);
		$user->brace('close', 'AND');
		$user->brace('open');
			$user->where('name', '=', 'Chris');
		$user->brace('close');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM user WHERE (user_id = :__where_1) AND (name = :__where_4)");

		// Create our test model object
		$user = new MyProject\Model\User();
		$user->brace('open');
			$user->brace('open');
				$user->where('user_id', '=', 1);
			$user->brace('close');
		$user->brace('close');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM user WHERE ((user_id = :__where_2))");

		// Create our test model object
		$user = new MyProject\Model\User();
		$user->where('user_id', '=', 1, 'AND');
		$user->brace('open');
			$user->where('name', '=', 'Chris', 'OR');
			$user->where('name', '=', 'Christopher');
		$user->brace('close');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM user WHERE user_id = :__where_0 AND (name = :__where_2 OR name = :__where_3)");
	}

	/**
	 * Testing GROUP BY.
	 *
	 * @access public
	 */
	public function testGroupBy() {
		// Create our test model object
		$user = new MyProject\Model\User();
		$user->group('email');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM user GROUP BY email");
		$user->group('name');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM user GROUP BY email, name");
	}

	/**
	 * Testing ORDER BY.
	 *
	 * @access public
	 */
	public function testOrderBy() {
		// Create our test model object
		$user = new MyProject\Model\User();
		$user->order('user_id');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM user ORDER BY user_id ASC");

		// Create our test model object
		$user = new MyProject\Model\User();
		$user->order('user_id', 'DESC');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM user ORDER BY user_id DESC");

		// Create our test model object
		$user = new MyProject\Model\User();
		$user->order('user_id');
		$user->order('name');
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM user ORDER BY user_id ASC, name ASC");
	}

	/**
	 * Testing LIMIT.
	 *
	 * @access public
	 */
	public function testLimit() {
		// Create our test model object
		$user = new MyProject\Model\User();
		$user->limit(10);
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM user LIMIT 10");

		// Create our test model object
		$user = new MyProject\Model\User();
		$user->limit(10, 25);
		$this->assertEquals($this->format($user->build('select')), "SELECT * FROM user LIMIT 25, 10");
	}

	/**
	 * Testing UPDATE statements.
	 *
	 * @access public
	 */
	public function testUpdate() {
		// Create our test model object
		$user = new MyProject\Model\User();
		$user->name = 'Chris';
		$this->assertEquals($this->format($user->build('update')), "UPDATE user SET name = :name");
		$user->where('user_id', '=', 1);
		$this->assertEquals($this->format($user->build('update')), "UPDATE user SET name = :name WHERE user_id = :__where_0");
		$user->limit(1);
		$this->assertEquals($this->format($user->build('update')), "UPDATE user SET name = :name WHERE user_id = :__where_0 LIMIT 1");

		// Create our test model object
		$user = new MyProject\Model\User();
		$user->update(array('email' => 'cjhill@gmail.com', 'foo' => 'bar'));
		$this->assertEquals($this->format($user->build('update')), "UPDATE user SET email = :email, foo = :foo");
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