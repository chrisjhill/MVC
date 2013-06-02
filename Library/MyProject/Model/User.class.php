<?php
namespace MyProject\Model;
use Core;

/**
 * Example of what your User Model might be.
 *
 * @copyright Copyright (c) 2012-2013 Christopher Hill
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author    Christopher Hill <cjhill@gmail.com>
 * @package   MVC
 */
class User extends Core\Model
{
	/**
	 * The name of the table where your users live.
	 *
	 * @access protected
	 * @var    string
	 */
	protected $_table = 'user';

	/**
	 * The primary key for the the user table.
	 *
	 * @access protected
	 * @var    string
	 */
	protected $_primaryKey = 'user_id';
}