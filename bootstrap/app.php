<?php

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
    return $view;
};

// Database
$container['db'] = function($container) {
    $pdo = new PDO("sqlite:" . __DIR__ . '/../database/todoapp.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;  
};

require_once __DIR__ . '/../routes/web.php';
