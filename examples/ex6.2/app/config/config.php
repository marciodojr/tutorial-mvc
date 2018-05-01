<?php
// app/config/config.php

$config = [
    'database' => [
        'driver' => 'pdo_mysql',
        'charset' => 'utf8',
    ],
];

return array_merge_recursive(
    $config,
    require_once __DIR__ . '/config.local.php'
);
