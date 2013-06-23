<?php
namespace Core;

/**
 * Handles the connection and query runners for databases.
 *
 * @copyright Copyright (c) 2012-2013 Christopher Hill
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author    Christopher Hill <cjhill@gmail.com>
 * @package   MVC
 */
class Database
{
	/**
	 * The connection to the database.
	 *
	 * @access protected
	 * @var    \PDO
	 */
	protected $_connection;

	/**
	 * The query that we have just run.
	 *
	 * @access protected
	 * @var    \PDOStatement
	 */
	protected $_statement;

	/**
	 * Connect to the database if we have not already.
	 *
	 * @access private
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
		} catch(\PDOException $e) {
			if (Config::get('settings', 'environment') == 'Dev') {
				var_dump($e);
			}

			die('<p>Sorry, we were unable to complete your request.</p>');
		}
	}

	/**
	 * Execute an SQL statement on the database.
	 *
	 * @access protected
	 * @param  string    $sql  The SQL statement to run.
	 * @param  array     $data The data to pass into the prepared statement.
	 * @return boolean
	 */
	protected function run($sql, $data = array()) {
		// If we do not have a connection then establish one
		if (! $this->_connection) {
			$this->connect();
		}

		// Prepare, execute, reset, and return the outcome
		$this->_statement = $this->_connection->prepare($sql);
		$result = $this->_statement->execute($data);
		$this->reset();
		return $result;
	}
}