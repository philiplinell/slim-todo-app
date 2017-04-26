<?php

use App\Controllers\UserController;
use App\Controllers\TodoController;
use App\Controllers\AuthController;
use App\Controllers\PasswordController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

$app->get('/', function($request, $response) {
    return $this->view->render($response, 'home.twig');
})->setName('home');

$app->get('/users', UserController::class . ':index')->setName('users.index');

$app->group('', function() {
    $this->get('/auth/signup', AuthController::class . ':getSignUp')->setName('auth.signup');
    $this->post('/auth/signup', AuthController::class . ':postSignUp');

    $this->get('/auth/signin', AuthController::class . ':getSignIn')->setName('auth.signin');
    $this->post('/auth/signin', AuthController::class . ':postSignIn');
})->add(new GuestMiddleware($container));

$app->group('', function() {
    $this->get('/auth/signout', AuthController::class . ':getSignOut')->setName('auth.signout');
    $this->get('/auth/password/change', PasswordController::class . ':getChangePassword')->setName('auth.password.change');
    $this->post('/auth/password/change', PasswordController::class . ':postChangePassword');

    $this->get('/todos', TodoController::class . ':index')->setName('todos.index');
})->add(new AuthMiddleware($container));



