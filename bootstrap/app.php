<?php

use Respect\Validation\Validator as v;

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;
$app = new \Slim\App(["settings" => $config]);

// Get container
$container = $app->getContainer();

// Add slim/flash
$container['flash'] = function ($container) {
    return new \Slim\Flash\Messages();
};

// Authentication
$container['auth'] = function ($container) {
    return new \App\Auth\Auth($container['db']);
};

// Register component on container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [
        'cache' => false
    ]);
    
    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

    // Add flash message to all views
    $view->getEnvironment()->addGlobal('flash', $container['flash']);

    // Add auth to all view
    $view->getEnvironment()->addGlobal('auth', [
        'check' => $container->auth->check(),
        'user' => $container->auth->user(),
    ]);
    
    return $view;
};

// Database
$container['db'] = function($container) {
    $pdo = new PDO("sqlite:" . __DIR__ . '/../database/todoapp.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;  
};

// Validator
$container['validator'] = function ($container) {
    return new App\Validation\Validator;
};

// CSRF
$container['csrf'] = function ($container) {
    return new \Slim\Csrf\Guard;
};



// Middleware
$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\CSRFViewMiddleware($container));

// Specify in which folder our Validation rules are
v::with('App\\Validation\\Rules\\');

require_once __DIR__ . '/../routes/web.php';
