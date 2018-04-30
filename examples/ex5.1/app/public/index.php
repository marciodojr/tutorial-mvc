<?php
// app/public/index.php

require '../vendor/autoload.php';
$router = new Slim\App();
require '../src/routes.php';

$router->run();