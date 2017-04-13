<?php

use App\Controllers\UserController;

$app->get('/', function($request, $response) {
    return $this->view->render($response, 'home.twig');
})->setName('home');

$app->get('/users', UserController::class . ':index')->setName('users.index');
