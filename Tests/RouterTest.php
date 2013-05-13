<?php
include dirname(__FILE__) . '/../Library/autoloader.php';

// Start tests
class RouterTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Match a plain URL.
	 *
	 * @access public
	 */
	public function testBasicRouteUrl() {
		// Set up the reflection class
		$reflection = new ReflectionMethod('Core\Router', 'routeTest');
		$reflection->setAccessible(true);

		// Simple routes
		// One directory
		$route = new Core\Route('Foo');
		$route->setRoute('foo')->setEndpoint(array('controller' => 'Foo'));
		$this->assertTrue( $reflection->invoke(new Core\Router(), array('foo'),        $route));
		$this->assertTrue( $reflection->invoke(new Core\Router(), array('foo', 'bar'), $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('bar'),        $route));

		// Two directories
		$route = new Core\Route('Foo');
		$route->setRoute('foo/bar')->setEndpoint(array('controller' => 'Foo'));
		// Passes
		$this->assertTrue( $reflection->invoke(new Core\Router(), array('foo', 'bar'),         $route));
		$this->assertTrue( $reflection->invoke(new Core\Router(), array('foo', 'bar', 'acme'), $route));
		// Fails
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo'),                $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('bar'),                $route));

		// Five directories
		$route = new Core\Route('Foo');
		$route->setRoute('foo/bar/acme/boop/beep')->setEndpoint(array('controller' => 'Foo'));
		// Passes
		$this->assertTrue( $reflection->invoke(new Core\Router(), array('foo', 'bar', 'acme', 'boop', 'beep'),          $route));
		$this->assertTrue( $reflection->invoke(new Core\Router(), array('foo', 'bar', 'acme', 'boop', 'beep', 'hello'), $route));
		// Fails
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo'),                                         $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', 'bar'),                                  $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', 'bar'),                                  $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', 'bar', 'acme'),                          $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', 'bar', 'acme', 'boop'),                  $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('hello'),                                       $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('hello', 'world'),                              $route));
	}

	/**
	 * Match a plain URL.
	 *
	 * @access public
	 */
	public function testComplexRoutes() {
		// Set up the reflection class
		$reflection = new ReflectionMethod('Core\Router', 'routeTest');
		$reflection->setAccessible(true);

		// Here be variable routes
		// One directory, one variable
		$route = new Core\Route('Foo');
		$route->setRoute('foo/:bar')->setFormat(array('bar' => '\d+'))->setEndpoint(array('controller' => 'Foo'));
		// Passes
		$this->assertTrue( $reflection->invoke(new Core\Router(), array('foo', '1'),          $route));
		$this->assertTrue( $reflection->invoke(new Core\Router(), array('foo', '12'),         $route));
		$this->assertTrue( $reflection->invoke(new Core\Router(), array('foo', '123'),        $route));
		$this->assertTrue( $reflection->invoke(new Core\Router(), array('foo', '123', 'foo'), $route));
		// Fails
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo'),               $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo/:bar'),          $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo/bar'),           $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo/a123'),          $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo/1a23'),          $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo/12a3'),          $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo/a123a'),         $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo/12-3'),          $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo/12_3'),          $route));

		// Two directories, two variables
		$route = new Core\Route('Foo');
		$route->setRoute('foo/:bar/hello/:world')->setFormat(array('bar' => '\d+'))->setEndpoint(array('controller' => 'Foo'));
		// Passes
		$this->assertTrue( $reflection->invoke(new Core\Router(), array('foo', '1', 'hello', 'world'),        $route));
		$this->assertTrue( $reflection->invoke(new Core\Router(), array('foo', '123', 'hello', 'abc123'),     $route));
		$this->assertTrue( $reflection->invoke(new Core\Router(), array('foo', '123', 'hello', 'ab-c12-3'),   $route));
		$this->assertTrue( $reflection->invoke(new Core\Router(), array('foo', '123', 'hello', 'ab_c12_3'),   $route));
		$this->assertTrue( $reflection->invoke(new Core\Router(), array('foo', '1', 'hello', 'world', 'foo'), $route));
		// Fails
		// Missing parameters
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo'),                               $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', '1'),                          $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', '1', 'hello'),                 $route));
		// Wrong basic parameters
		$this->assertFalse($reflection->invoke(new Core\Router(), array('bar', '1', 'hello', 'world'),        $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', '1', 'bar', 'world'),          $route));
		// Wrong number type
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', 'bar', 'hello', 'world'),      $route));
		// Incorrect default data types, not matching [\w\-]+
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', '1', 'hello', 'wo"rld'),       $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', '1', 'hello', 'wo\'rld'),      $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', '1', 'hello', 'wo<rld'),       $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', '1', 'hello', 'wo>rld'),       $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', '1', 'hello', 'wo@rld'),       $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', '1', 'hello', 'wo!rld'),       $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', '1', 'hello', 'wo£rld'),       $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', '1', 'hello', 'wo$rld'),       $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', '1', 'hello', 'wo%rld'),       $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', '1', 'hello', 'wo&rld'),       $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', '1', 'hello', 'wo+rld'),       $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', '1', 'hello', 'wo.rld'),       $route));
		$this->assertFalse($reflection->invoke(new Core\Router(), array('foo', '1', 'hello', 'wo rld'),       $route));
	}
}