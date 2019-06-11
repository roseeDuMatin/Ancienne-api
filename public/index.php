<?php

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response; 
use Slim\Interfaces\RouteInterface;

require "../Composer/vendor/autoload.php";

session_start();

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);

require('../app/container.php');

$container = $app->getContainer();

// Middleware
// $app->add(new App\Middlewares\FlashMiddleware($container->view->getEnvironment()));

// $app->get('/', \App\Controllers\PagesController::class . ':home')->setName('home');
$app->post('/', \App\Controllers\PagesController::class . ':postHome');
// $app->get('/', \App\Controllers\PagesController::class . ':home')->setName('contact');


$app->post('/{table}/create',\App\Controllers\PagesController::class . ':create');

$app->put('/{table}/update',\App\Controllers\PagesController::class . ':update');

$app->delete('/{table}/delete', \App\Controllers\PagesController::class . ':delete');

$app->get('/{table}/{id}', \App\Controllers\PagesController::class . ':getById');

$app->get('/{table}', \App\Controllers\PagesController::class . ':getAll');


$app->run();