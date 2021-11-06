<?php

use App\Middlewares\AuthorizedMiddleware;
use App\Middlewares\GuestMiddleware;
use App\Middlewares\UnsetLoginMiddleware;
use App\Repositories\ProductsRepository;
use App\Repositories\UsersRepository;
use App\View;
use DI\Container;
use function DI\create;

session_start();
require_once 'vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', 'UsersController@login');
    $r->addRoute('POST', '/register', 'UsersController@store');
    $r->addRoute('POST', '/', 'UsersController@verify');
    $r->addRoute('GET', '/register', 'UsersController@register');

    $r->addRoute('GET', '/products', 'ProductsController@index');
    $r->addRoute('GET', '/products/create', 'ProductsController@create');
    $r->addRoute('GET', '/products/{id}', 'ProductsController@edit');
    $r->addRoute('POST', '/products/create', 'ProductsController@store');
    $r->addRoute('POST', '/products/delete/{id}', 'ProductsController@delete');

});

$middlewares = [
    "UsersController@login"=>[
        UnsetLoginMiddleware::class,
    ],
    "UsersController@register"=>[
        AuthorizedMiddleware::class
    ],
    "UsersController@store"=>[
        AuthorizedMiddleware::class
    ],
    "UsersController@verify"=>[
        AuthorizedMiddleware::class
    ],
    "ProductsController@index"=>[
        GuestMiddleware::class
    ],
    "ProductsController@create"=>[
        GuestMiddleware::class
    ],
    "ProductsController@edit"=>[
        GuestMiddleware::class
    ],
    "ProductsController@store"=>[
        GuestMiddleware::class
    ],
    "ProductsController@delete"=>[
        GuestMiddleware::class
    ],
];

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$loader = new \Twig\Loader\FilesystemLoader('app/Views');
$templateEngine = new \Twig\Environment($loader);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

$container = new Container();

//$container->set(ProductsRepository::class, create('MysqlProductsRepository'));
//$container->set(UsersRepository::class, create('MysqlUsersRepository'));

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        if (isset($middlewares[$handler]))
        {
            foreach ($middlewares[$handler] as $middleware)
            {
                $middle = new $middleware;
                $middle->handler();
            }
        }

        [$controller, $method] = explode('@',$handler);
        $controller ='App\Controllers\\'.$controller;

        $controller = $container->get($controller);

        //$controller = new $controller($dependencies);
        //$response = $controller->$method($vars);

        $response = $controller->$method($vars);

        if ($response instanceof View)
        {
            echo $templateEngine->render(
                $response->getTemplate(),
                $response->getArguments()
            );
        }
        break;
}