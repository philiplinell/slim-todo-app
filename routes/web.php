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

// ------ Not Logged in resources ----
$app->group('', function() {
    $this->get('/auth/signup', AuthController::class . ':getSignUp')->setName('auth.signup');
    $this->post('/auth/signup', AuthController::class . ':postSignUp');

    $this->get('/auth/signin', AuthController::class . ':getSignIn')->setName('auth.signin');
    $this->post('/auth/signin', AuthController::class . ':postSignIn');
})->add(new GuestMiddleware($container));

// ------ Logged in resources ----
$app->group('', function() {
    $this->get('/auth/signout', AuthController::class . ':getSignOut')->setName('auth.signout');
    $this->get('/auth/password/change', PasswordController::class . ':getChangePassword')->setName('auth.password.change');
    $this->post('/auth/password/change', PasswordController::class . ':postChangePassword');

    $this->get('/profile', UserController::class . ':profile')->setName('user.profile');
    
    $this->get('/todos', TodoController::class . ':index')->setName('todos.index');
    $this->get('/todos/setdone/{id}', TodoController::class . ':done')->setName('todos.setdone');
    $this->get('/todos/setundone/{id}', TodoController::class . ':undone')->setName('todos.setundone');
    $this->post('/todos/create', TodoController::class . ':create')->setName('todos.create');
})->add(new AuthMiddleware($container));



