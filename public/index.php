<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Mvc\Router;

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader
$loader = new Loader();
$loader->registerDirs(
    [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
    ]
);

$loader->register();

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);



//$container->set(
//    'db',
//    function () {
//return new PdoMysql(
//            [
//                'host'     => 'localhost',
//                'username' => 'planet6l_db',
//                'password' => 'Wfbj5&20',
//                'dbname'   => 'planet6l_db',
//            ]
//        );
//    }
//);

$container->setShared(
    'session',
    function () {
        $session = new Manager();
        $files   = new Stream(
            [
                'savePath' => '/tmp',
            ]
        );
        $session->setAdapter($files);
        $session->start();
        return $session;
    }
);

$router = new Router(false);

$router->notFound(
   [
      'controller' => 'error',
      'action'     => 'notFound',
   ]
);

$router->add(
    '/',
    [
        'controller' => 'index',
        'action'     => 'index',
    ]
);

$router->add(
    '/ask',
    [
        'controller' => 'index',
        'action'     => 'ask',
    ]
);

$router->add(
    '/contacts',
    [
        'controller' => 'index',
        'action'     => 'contacts',
    ]
);

$router->add(
    '/transport',
    [
        'controller' => 'index',
        'action'     => 'transport',
    ]
);

$router->add(
    '/news',
    [
        'controller' => 'index',
        'action'     => 'news',
    ]
);

$router->add(
    '/tours',
    [
        'controller' => 'index',
        'action'     => 'tours',
    ]
);

$container->set('router', $router);

$application = new Application($container);

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
