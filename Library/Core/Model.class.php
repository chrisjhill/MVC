<?php
namespace Core;

/**
 * Provides a simple interface to gather data from a database.
 *
 * Inserting
 * ---------
 * <code>
 * // 1. Object-orientated inserting:
 * $user = new Model\User();
 * $user->name  = 'Chris';
 * $user->email = 'cjhill@gmail.com';
 * $user->save();
 *
 * // 2. Pass in an array of data:
 * $user = new Model\User();
 * $user->insert(array('name' => 'Chris', 'email' => 'cjhill@gmail.com'));
 * </code>
 *
 * Selecting
 * ---------
 * <code>
 * // 1. Select a single user very quickly:
 * $user = new Model\User(1);
 *
 * // 2. Finds the users with ID's 1, 2, 3:
 * $users = new Model\User(array(1, 2, 3));
 *
 * // 3. Advanced query selecting:
 * $users = new Model\User();
 * $users->where('active', '=', 1)->where('name', '=', 'Dave')->limit(10)->find();
 *
 * // 4. How many users the query found:
 * echo 'I found ' . $users->rowCount() . ' users.';
 *
 * // 5. Loop over the found users:
 * while ($user = $users->fetch()) {
 *     echo 'Hello, ' . $user['name'];
 * }
 * </code>
 *
 * Updating
 * --------
 * TBC.
 *
 * Deleting
 * --------
 * <code>
 * $user = new Model\User();
 * $user->where('id', '=', 1)->limit(1)->delete();
 *
 * Running your own queries
 * ------------------------
 * <code>
 * // 1. In your User Model, for instance:
 * $this->run('SELECT * FROM `user` WHERE `name` = :name', array(':name' => 'Chris'));
 * $user = $this->fetch();
 * echo 'Hello, ' . $user['name'];
 * </code>
 *
 * @copyright Copyright (c) 2012-2013 Christopher Hill
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author    Christopher Hill <cjhill@gmail.com>
 * @package   MVC
 *
 * @todo      SUM, AVG, etc.
 * @todo      Table joins.
 * @todo      WHERE OR
 * @todo      HAVING
 * @todo      Some kind of config schema.
 */
class Model
{
	/**
	 * The connection to the database.
	 *
	 * @access private
	 * @var    \PDO
	 */
	private $_connection;

	/**
	 * The query that we have just run.
	 *
	 * @access private
	 * @var    \PDOStatement
	 */
	private $_statement;

	/**
	 * The primary key for the table.
	 *
	 * This can (and should) be overridden by the extending class.
	 *
	 * @access protected
	 * @var    string
	 */
	protected $_primaryKey = 'id';

	/**
	 * Which columns we want to select.
	 *
	 * To mitigate SQL errors we always append the table name to the start of
	 * the field name, whether or not one is supplied. If no table name is
	 * passed in then we use the default table the the extended class declared.
	 *
	 * @access private
	 * @var    array
	 */
	private $_select = array();

	/**
	 * The tables that we wish to select data from.
	 *
	 * @access private
	 * @var    array
	 */
	private $_from = array();

	/**
	 * The where conditions to apply to the query.
	 *
	 * @access private
	 * @var    array
	 */
	private $_where = array();

	/**
	 * How we should order the returned rows.
	 *
	 * @access private
	 * @var    array
	 */
	private $_order = array();

	/**
	 * How we should limit the returned rows.
	 *
	 * @access private
	 * @var    array
	 */
	private $_limit = array();

	/**
	 * Data that has been passed to the row to insert/update.
	 *
	 * @access private
	 * @var    array
	 */
	private $_store = array();

	/**
	 * Setup the model.
	 *
	 * If you want to load a row automatically then you can pass an int to this
	 * function, or to load multiple rows then you can pass an array or ints.
	 *
	 * <code>
	 * // Load a single user row
	 * $user = new MyProject\Model\User(1);
	 *
	 * // Load x user rows
	 * $user = new MyProject\Model\User(array(1, 2, 3, 4, 5));
	 * </code>
	 *
	 * @access public
	 * @param  mixed  $id The ID's to load automatically.
	 */
	public function __construct($id = null) {
		if ($id) {
			$this->where($this->_primaryKey, '=', $id)
				 ->limit(count($id))
				 ->find();
		}
	}

	/**
	 * Connect to the database if we have not already.
	 *
	 * @access private
	 * @todo   Move this into a Core Database class.
	 */
	private function connect() {
		// Get the connection details
		$host     = Config::get('db', 'host');
		$database = Config::get('db', 'database');
		$username = Config::get('db', 'username');
		$password = Config::get('db', 'password');

		try {
			// Connect to the database
			$this->_connection = new \PDO(
				"mysql:host={$host};dbname={$database};charset=utf8",
				$username,
				$password
			);

			// Work with arrays, not objects
			$this->_connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
		} catch(\PDOException $e) {
			if (Core::get('settings', 'environment') == 'Dev') {
				var_dump($e);
			}

			die('<p>Sorry, we were unable to complete your request.</p>');
		}
	}

	/**
	 * Execute an SQL statement on the database.
	 *
	 * @access public
	 * @param  string  $sql  The SQL statement to run.
	 * @param  array   $data The data to pass into the prepared statement.
	 * @return boolean       Whether the query was successful.
	 */
	public function run($sql, $data = array()) {
		// If we do not have a connection then establish one
		if (! $this->_connection) {
			$this->connect();
		}

		// Prepare and execute the statement
		$this->_statement = $this->_connection->prepare($sql);
		return $this->_statement->execute($data);
	}

	/**
	 * Add a row to the SELECT section.
	 *
	 * Note: The table name will always be prefixed to the field name to try and
	 * mitigate errors. If none is supplied then we assume you are using the
	 * table name that is declared in the extending class.
	 *
	 * @access public
	 * @param  string $field The field name.
	 * @param  string $table The table this field lives.
	 * @param  string $as    The name of the field that is supplied to you.
	 * @return Model         For chainability.
	 */
	public function select($field, $table = null, $as = null) {
		$this->_select[] = array('field' => $field, 'table' => $table ?: $this->_table, 'as' => $as);
		return $this;
	}

	/**
	 * Add a table to the query.
	 *
	 * Note: If no table is supplied then we will use the table defined in the
	 * extending class. If you are not joining other tables then you do not need
	 * to call this function.
	 *
	 * @access public
	 * @param  string $table A table that is part of the statement.
	 * @return Model         For chainability.
	 */
	public function from($table) {
		$this->_from[] = $table;
		return $this;
	}

	/**
	 * Add a where condition to the statement.
	 *
	 * <code>
	 * // 1. A simple condition:
	 * ->where('name', '=', 'Chris')
	 *
	 * // 2. An IN condition:
	 * ->where('name', 'IN', array('Chris', 'John', 'Smith'));
	 *
	 * // You can also use the equals operator for this!
	 * ->where('name', '=',  array('Chris', 'John', 'Smith'));
	 *
	 * // 3. Multiple where's:
	 * ->where('name', '=', 'Chris')->where('email', '=', 'cjhill@gmail.com')
	 * </code>
	 *
	 * @access public
	 * @param  string       $field    The field we wish to test.
	 * @param  string       $operator How we wish to test the field (=, >, etc.)
	 * @param  string|array $value    The value to test the field against.
	 * @return Model                  For chainability.
	 */
	public function where($field, $operator, $value) {
		$this->_where[] = array('field' => $field, 'operator' => $operator, 'value' => $value);
		return $this;
	}

	/**
	 * How to order the returned results.
	 *
	 * @access public
	 * @param  string $field     The field we wish to order by.
	 * @param  string $direction Either 'ASC' or 'DESC'. 'ASC' by default.
	 * @return Model             For chainability.
	 */
	public function order($field, $direction = 'ASC') {
		$this->_order[] = array('field' => $field, 'direction' => $direction);
		return $this;
	}

	/**
	 * How to limit the returned results.
	 *
	 * @access public
	 * @param  int    $limit How many results to return.
	 * @param  int    $start The offset to start the results from.
	 * @return Model      For chainability.
	 */
	public function limit($limit, $start = null) {
		$this->_limit = array('limit' => $limit, 'start' => $start);
		return $this;
	}

	/**
	 * Insert a row into the table.
	 *
	 * @access public
	 * @param  array  $data The data to insert into the table.
	 * @return Model        For chainability.
	 */
	public function insert($data = array()) {
		// If we have been supplied from data then add it to the store.
		foreach ($data as $field => $value) {
			$this->$field = $value;
		}

		// If the insert was successful then add the primary key to the store
		if ($this->run($this->build('insert'), $this->_store)) {
			$this->_store[$this->_primaryKey] = $this->_connection->lastInsertId();
		}
	}

	/**
	 * Select some records from a table.
	 *
	 * @access public
	 */
	public function find() {
		$this->run($this->build('select'), $this->_store);
	}

	/**
	 * Shorthand for the insert and update functions.
	 *
	 * @access public
	 */
	public function save() {
		$this->{$this->_primaryKey}
			? $this->update()
			: $this->insert();
	}

	/**
	 * Piece together all of the sections of the query.
	 *
	 * @access private
	 * @param  string  $type What type of query we wish to build.
	 * @return string        The SQL that has been generated.
	 */
	private function build($type) {
		switch ($type) {
			case 'insert' : $sql = $this->buildInsert(); break;
			case 'select' : $sql = $this->buildSelect(); break;
			case 'update' : $sql = $this->buildUpdate(); break;
			case 'delete' : $sql = $this->buildDelete(); break;
		}

		return $sql;
	}

	/**
	 * Build an insert statement.
	 *
	 * @access private
	 * @return string
	 */
	private function buildInsert() {
		$keys   = array_keys($this->_store);
		$fields = implode('`, `', $keys);
		$values = implode(', :',  $keys);

		return "INSERT INTO `{$this->_table}` (`{$fields}`) VALUES (:{$values})";
	}

	/**
	 * Build a select statement.
	 *
	 * @access private
	 * @return string
	 */
	private function buildSelect() {
		return "SELECT {$this->buildFragmentSelect()}
			    FROM   {$this->buildFragmentFrom()}
			           {$this->buildFragmentWhere()}
			           {$this->buildFragmentOrder()}
			           {$this->buildFragmentLimit()}";
	}

	/**
	 * Build an update statement.
	 *
	 * @access private
	 * @return string
	 */
	private function buildUpdate() {
		// @todo
		return "";
	}

	/**
	 * Build a delete statement.
	 *
	 * @access private
	 * @return string
	 */
	private function buildDelete() {
		return "DELETE FROM {$this->buildFragmentFrom()}
			                {$this->buildFragmentWhere()}
			                {$this->buildFragmentLimit()}";
	}

	/**
	 * Build the SELECT portion of the statement.
	 *
	 * @access private
	 * @return string
	 */
	private function buildFragmentSelect() {
		// If there are no fields to select from then just return them all
		if (empty($this->_select)) {
			return '*';
		}

		// Container for the fields we wish to select
		$fields = array();

		// Loop over each field that we want to return and build it's SQL
		foreach ($this->_select as $select) {
			$as = $select['as']
				? " AS '{$select['as']}'"
				: '';

			$fields[] = "`{$select['table']}`.`{$select['field']}` {$as}";
		}

		return implode(', ', $fields);
	}

	/**
	 * Build the FROM portion of the statement.
	 *
	 * @access private
	 * @return string
	 */
	private function buildFragmentFrom() {
		return empty($this->_from)
			? $this->_table
			: '`' . implode('`, `', $this->_from) . '`';
	}

	/**
	 * Build the WHERE portion of the statement.
	 *
	 * Note: So we do not interfere with any field names we label our prepared
	 * variables prefixed with "__where_".
	 *
	 * @access private
	 * @return string
	 * @todo   Allow for OR's.
	 */
	private function buildFragmentWhere() {
		// If there are no conditions then return nothing
		if (empty($this->_where)) {
			return '';
		}

		// Container for the where conditions
		$conditions = array();

		// Loop over each where condition and build its SQL
		foreach ($this->_where as $whereIndex => $where) {
			// The basic perpared variable name
			$variableName = "__where_{$whereIndex}";

			// We are dealing with an IN
			if (is_array($where['value'])) {
				// We need to create the condition as :a, :b, :c
				$ins = array();

				// Loop over each value in the array
				foreach ($where['value'] as $inIndex => $in) {
					$ins[]      = "{$variableName}_{$inIndex}";
					$this->_store["{$variableName}_{$inIndex}"] = $in;
				}

				// The SQL for this IN
				$sql = "`{$where['field']}` IN (" . implode(', ', $ins) . ")";
			}

			// A simple where condition
			else {
				$sql = "`{$where['field']}` {$where['operator']} :{$variableName}}";
				$this->_store[$variableName] = $where['value'];
			}

			// Add this where clause to the SQL
			$conditions[] = $sql;
		}

		return 'WHERE ' . implode(', AND', $conditions);
	}

	/**
	 * Build the ORDER BY portion of the statement.
	 *
	 * @access private
	 * @return string
	 */
	private function buildFragmentOrder() {
		// If there are no order by's then return nothing
		if (empty($this->_order)) {
			return '';
		}

		// Container for the order by's
		$orders = array();

		// Loop over each order by and build it's SQL
		foreach ($this->_order as $order) {
			$orders[] = "{$order['field']} {$order['direction']}";
		}

		return 'ORDER BY ' . implode(', ', $orders);
	}

	/**
	 * Build the LIMIT portion of the statement.
	 *
	 * @access private
	 * @return string
	 */
	private function buildFragmentLimit() {
		// If there is no limit then return nothing
		if (empty($this->_limit)) {
			return '';
		}

		// If there is a start then add that as well...
		if (! is_null($this->_limit['start'])) {
			return "LIMIT {$this->_limit['start']}, {$this->_limit['limit']}";
		}

		// ... otherwise just return the simple limit
		return "LIMIT {$this->_limit['limit']}";
	}

	/**
	 * Get how many rows the statement located.
	 *
	 * @access public
	 * @return int|boolean int if statement was successful, boolean false otherwise.
	 */
	public function rowCount() {
		return $this->_statement
			? $this->_statement->rowCount()
			: false;
	}

	/**
	 * Get the next row of the located results.
	 *
	 * @access public
	 * @return int|array Array if statement was successful, boolean false otherwise.
	 */
	public function fetch() {
		return $this->_statement
			? $this->_statement->fetch()
			: false;
	}

	/**
	 * Set a variable for the row.
	 *
	 * Note: This is only used for inserting and updating statements. It will
	 * also update any previous value the field had.
	 *
	 * @access public
	 * @param  string $field The field to manipulate.
	 * @param  mixed  $value The field's value.
	 * @magic
	 */
	public function __set($variable, $value) {
		$this->_store[$variable] = $value;
	}

	/**
	 * Get a field value.
	 *
	 * Note: This is only used for inserting and updating statements. For all
	 * other statements you can use the fetch() function.
	 *
	 * @access public
	 * @param  string         $field The name of the field.
	 * @return string|boolean        String if exists, boolean false otherwise.
	 */
	public function __get($field) {
		return isset($this->_store[$field])
			? $this->_store[$field]
			: false;
	}
}