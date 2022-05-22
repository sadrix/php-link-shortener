<?php

use Controllers\App\AppController;
use Controllers\App\LinksController;
use Controllers\App\ProfileController;
use Controllers\Auth\AuthController;

$router->get('/', [AppController::class, 'home']);

// link
$router->get('/([a-z0-9A-Z]{8})', [AppController::class, 'link']);

// auth
$router->mount('/auth', function() use ($router) {
    $router->post('/login',       [AuthController::class, 'login']);
    $router->post('/register',    [AuthController::class, 'register']);
    $router->post('/logout',      [AuthController::class, 'logout']);
});

// profile
$router->get('/profile',          [ProfileController::class, 'show']);
$router->post('/profile',         [ProfileController::class, 'update']);

// links
$router->mount('/links', function () use ($router) {
    $router->get('/',             [LinksController::class, 'all']);
    $router->post('/',            [LinksController::class, 'create']);
    $router->get('/{id}',         [LinksController::class, 'show']);
    $router->post('/{id}',        [LinksController::class, 'update']);
    $router->delete('/{id}',      [LinksController::class, 'delete']);
});

// 404 - url not found
$router->set404(function () {
    header('HTTP/1.1 404 Not Found');
    return response(404, 'Url not fount.');
});