<?php

use App\Controllers\UserController;
use App\Controllers\TodoController;

$app->get('/', function($request, $response) {
    return $this->view->render($response, 'home.twig');
})->setName('home');

$app->get('/users', UserController::class . ':index')->setName('users.index');
$app->post('/login', UserController::class . ':login')->setName('user.login');
$app->post('/register', UserController::class . ':createUser')->setName('user.create');
$app->get('/register', UserController::class . ':register')->setName('user.register');
$app->get('/todos', TodoController::class . ':index')->setName('todos.index');


