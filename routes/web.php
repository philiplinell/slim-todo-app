<?php

use App\Controllers\UserController;
use App\Controllers\TodoController;
use App\Controllers\AuthController;

$app->get('/', function($request, $response) {
    return $this->view->render($response, 'home.twig');
})->setName('home');

$app->get('/users', UserController::class . ':index')->setName('users.index');

$app->get('/auth/signup', AuthController::class . ':getSignUp')->setName('auth.signup');
$app->post('/auth/signup', AuthController::class . ':postSignUp');

$app->get('/auth/signin', AuthController::class . ':getSignIn')->setName('auth.signin');
$app->post('/auth/signin', AuthController::class . ':postSignIn');

$app->get('/auth/signout', AuthController::class . ':getSignOut')->setName('auth.signout');

$app->get('/todos', TodoController::class . ':index')->setName('todos.index');


