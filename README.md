# Welcome to MVC

## What is this project?
There are plenty of excellent MVC's out there. So why the need for yet another? Simply because they are big and complex. Why use a sledgehammer to crack a nut?

## The projects aims
To provide a compact codebase providing the basics for any application. It's designed to be fast. *Really fast.* With the built in profiler you can easily see how fast each fragment of your site is, how much memory it consumes, and how each fragment was initiated.

## What does this project have to offer?
We provide the basics of the MVC, everything from the router, dispatcher, controllers, views, and view helpers. We even have a few components you can make use of, including:

 * Model ORM
 * Cache
 * Event listeners
 * Formatting of data
 * Notice messages
 * Data validation
 * Store (APC, Cookie, Memcache(d), Request, and Session)

## Testing
This project contains a PHPUnit test suite with 64 tests and 194 assertions (all passing).

---

## Installation
Checkout a copy of the source files and head over to your app's config in `/Library/MyProject/config.ini` and update the `path`'s to your specific directory structure. You can rename the `MyProject` in the `Library` directory, just make sure you also update the namespace definition in each of your `.php` files.

---

## Features and Documentation

### Routing (Basic)
We parse URI's such as `/index/hello/my/variables/go/here/foobar` and place it into the GET array. Dumping the  will give you:

    array(
    	'controller' => 'index',
    	'action'     => 'hello',
    	'my'         => 'variables',
    	'go'         => 'here',
    	'foobar'     => true
    )

This will forward the request onto the `Index` controller and into the `hello` action.

### Routing (Advanced)

If you want to customise your URL's as such that the basic `/controller/action/my/variables/go/here` will not suffice, then you can use the built in `Router`.

#### Example

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

    // Start the application, passing in the Router
    new Core\Front('MyProject', $router);

You can add as many routes as you like. Any variables that you do not specify in the `setFormat` method (which is optional) will use the regex of `[\w\-]+`. The `setEndpoint` method does not require an `action` parameter, but if none is declared then it will use the default `index` action of that controller.

This advanced routing system will use the first declared route that it finds matching the request URL.

### Reverse routing

URL's will often change. Defining them in a single place (the router) will save you having to rewrite them in your View Helpers/Partials. It is also safer because URL encoding will be taken care for you. You can call the `Route` View Helper via:

    $this->view->route(array(
    	'route'  => 'Foo',
    	'params' => array(
    		'bar'  => 1234,
    		'acme' => 'foobar'
    	)
    );

---

### Controllers

Controllers are created in `/Library/MyProject/Controller`, the file name begins with an uppercase letter and ends in a `.php` extension, so `index` would be called `Index.php`.

### Actions

Actions are named the same as specified in the URI, are lowercase, and end in `Action`. So the `index` action will be named `indexAction()`.

### Caching

Caching can be turned on or off from your projects configuration file (`/Library/MyProject/config.ini`), and you can set how long you want before the cache is invalidated.

    [cache]
        enable = true
        life   = 60

### Forwarding

You can forward to another action (or controller) via the `return $this->forward('action', 'controller')` command in a controller.

### Layouts

You can change the layout (layouts are stored in `/Library/MyProject/Layout`, are lowercase, and end with a `.phtml` extension) that will wrap the View by calling the `$this->setLayout('layout')` method in a controllers action.

#### Example

    class Index extends Core\Controller
    {
    	public function indexAction() {
    		$this->setLayout('new-layout');
    	}
    }

* * *

### View Scripts

Views are stored in the `/Library/MyProject/View/Script` directory, and each controller has their own directory. So the `Index` controller's views will be stored in `/Library/MyProject/View/Script/Index`. Each of the controllers actions have a separate view, so the `Index` controller's `hello` action will be stored in `/Library/MyProject/View/Script/Index/hello.phtml`.

#### URL generation

The view has a built in method to generate URL's. You can specify the controller, action and any variables. You can also state whether you want to retain the current pages URL variables (disabled by default). This is called via:

    echo $this->url(
    	array(
    		'controller'      => 'index',
    		'action'          => 'hello',
    		'variables'       => array('foo' => 'bar'),
    		'variable_retain' => true
    	)
    );

#### Safe HTML

You can output HTML to the browser safely by using the `$this->safe(array('string' => 'Evil string'))` method.

### View Helpers and View Partials

Your View Scripts can easily direct logic away from themselves into View Helpers. View helpers can have their own template files, called Partials. For example, the `Test` View Helper:

    return $this->renderPartial('test', array(
    	'testVar' => $params['testVar']
    ));

---

## Profiling
A powerful in-built profiler will let you know exactly where your application is expending time and additional memory. Its waterfall display allows you to see which functions have been called by whom.

![](https://raw.github.com/chrisjhill/MVC/master/Web/assets/img/profiler.png)