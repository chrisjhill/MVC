<h2>PHPUnit testing</h2>

<p>OK (55 tests, 177 assertions)</p>

<h2>How to use this MVC</h2>

<h3>Directory structure</h3>

<pre>
MVC
|-- Library
|   |
|   |-- Core
|   |
|   |-- MyProject
|   |   |-- Cache
|   |   |-- Controller
|   |   |-- Layout
|   |   |-- Model
|   |   |-- View
|   |   |   |-- Helper
|   |   |   |-- Partial
|   |   |   `-- Script
|   |   |
|   |   |-- EventListener.class.php
|   |   `-- config.ini
|   |
|   |-- autoloader.php
|   `-- global.php
|
`-- Web
    |-- assets
    |   |-- css
    |   |-- img
    |   `-- js
    |
    |-- .htaccess
    `-- index.php</pre>

<hr />

<h3>Setup</h3>

<p>Your project configuration files are in /Library/MyProject/config.ini</p>

<hr />

<h3>Routing</h3>

<p>We parse URI's such as <code>/index/hello/my/variables/go/here/foobar</code> and place it into the GET array. Dumping <code>$_GET</code> will give you:

<pre>array(
	'controller' => 'index',
	'action'     => 'hello',
	'my'         => 'variables',
	'go'         => 'here',
	'foobar'     => true
)</pre>

<p>This will forward the request onto the <code>index</code> controller and into the <code>hello</code> action.</p>

<h3>Advanced routing</h3>

<p>If you want to customise your URL's as such that the basic <code>/controller/action/my/variables/go/here</code> will not suffice, then you can use the built in <code>Router</code>.</p>

<h4>Example</h4>

<pre>&lt;?php
// Global configurations
include dirname(__FILE__) . '/../Library/global.php';

// Create new Router instance
$router = new Core\Router();
$router
    ->addRoute('Foo')
    ->setRoute('foo/:bar/:acme')
    ->setFormat(array(
        'bar'  => '\d+',
        'acme' => '[a-z0-9]+')
    )
    ->setEndpoint(array(
        'controller' => 'Foo',
        'action'     => 'bar')
    );

// Start the application
new Core\Front('MyProject', $router);
</pre>

<p>You can add as many routes as you like. Any variables that you do not specify in the <code>setFormat</code> method (which is optional) will use the regex of <code>[\w\-]+</code>. The <code>setEndpoint</code> method does not require an <code>action</code> parameter, but if none is declared then it will use the default <code>index</code> action of that controller.</p>

<p>This advanced routing system will use the first declared route that it finds matching the request URL.</p>

<h4>Reverse routing</h4>

<p>URL's will often change. Defining them in a single place (the router) will save you having to rewrite them in your View Helpers/Partials. It is also safer because URL encoding will be taken care for you. You can call the <code>Route</code> View Helper via:</p>

<pre>$this->view->route(array(
	'route'  => 'Foo',
	'params' => array(
		'bar'  => 1234,
		'acme' => 'foobar'
	)
);</pre>

<hr />

<h3>Controllers</h3>

<p>Controllers are created in <code>/Library/MyProject/Controller</code>, the file name begins with an uppercase letter and ends in a <code>.class.php</code> extension, so <code>index</code> would be called <code>Index.class.php</code>.</p>

<h4>Actions</h4>

<p>Actions are named the same as specified in the URI, are lowercase, and end in <code>Action</code>. So the <code>index</code> action will be named <code>indexAction()</code>.</p>

<h4>Caching</h4>

<p>Caching can be turned on or off from your projects configuration file (<code>/Library/MyProject/config.ini</code>), and you can set how long you want before the cache is invalidated.</p>

<pre>[cache]
    enable = true
    life   = 60</pre>

<h4>Forwarding</h4>

<p>You can forward to another action (or controller) via the <code>$this->forward('action', 'controller')</code> command in a controller.</p>

<h4>Layouts</h4>

<p>You can change the layout (layouts are stored in <code>/Library/MyProject/Layout</code>, are lowercase, and end with a <code>.phtml</code> extension) that will wrap the View by calling the <code>$this->setLayout('layout')</code> method in a controllers action.</p>

<h4>Example</h4>

<pre>&lt;?php
class Index extends Core\Controller
{
	public function indexAction() {
		$this->setLayout('new-layout');
		$this->forward('hello');
	}

	public function helloAction() {
		// This is the function that will be rendered to the browser
	}
}</pre>

<hr />

<h3>View Scripts</h3>

<p>Views are stored in the <code>/Library/MyProject/View/Script</code> directory, and each controller has their own directory. So the <code>Index</code> controller's views will be stored in <code>/Library/MyProject/View/Script/Index</code>. Each of the controllers actions have a separate view, so the <code>Index</code> controller's <code>hello</code> action will be stored in <code>/Library/MyProject/View/Script/Index/hello.phtml</code>.</p>

<h4>URL generation</h4>

<p>The view has a built in method to generate URL's. You can specify the controller, action and any variables. You can also state whether you want to retain the current pages URL variables (disabled by default). This is called via:</p>

<pre>echo $this->url(
	array(
		'controller'      => 'index',
		'action'          => 'hello',
		'variables'       => array('foo' => 'bar'),
		'variable_retain' => true
	)
);</pre>

<h4>Safe HTML</h4>

<p>You can output HTML to the browser safely by using the <code>$this->safe(array('string' => 'Evil string'))</code> method.</p>

<h3>View Helpers and View Partials</h3>

<p>Your View Scripts can easily direct logic away from themselves into View Helpers. View helpers can have their own template files, called Partials. For example, the <code>Test</code> View Helper:</p>

<pre>return $this->renderPartial('test', array(
	'testVar' => $params['testVar']
));</pre>

<hr />

<h4>Profiling</h4>

<p>A powerful in-built profiler will let you know exactly where your application is expending time and additional memory. Its waterfall display allows you to see which functions have been called by whom.</p>

<img src="https://raw.github.com/chrisjhill/MVC/master/Web/assets/img/profiler.png" />