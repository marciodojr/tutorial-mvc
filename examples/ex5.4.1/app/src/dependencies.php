<?php
// app/src/dependencies.php

use TutorialMvc\Controller\ProductController;
use TutorialMvc\Model\ProductEntity;

$container = $router->getContainer();

$config = require __DIR__ . '/../config/config.php';
$container['config'] = $config;

$container[PDO::class] = function($c) {
    $config = $c->get('config')['database'];
    return new PDO(
        'mysql:host='.$config['host'].';dbname='.$config['db_name'].';charset=' . $config['charset'],
        $config['db_user'],
        $config['db_pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
};

$container[ProductEntity::class] = function($c) {
    $pdo = $c->get(PDO::class);
    return new ProductEntity($pdo);
};

$container[ProductController::class] = function($c) {
    $pe = $c->get(ProductEntity::class);
    return new ProductController($pe);
};