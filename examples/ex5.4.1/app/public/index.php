<?php
// app/public/index.php

require '../vendor/autoload.php';

$router = new Slim\App();

require __DIR__ . '/../src/dependencies.php';
require __DIR__ . '/../src/routes.php';

$router->run();