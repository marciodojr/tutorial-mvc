<?php
// app/src/routes.php

// GET /produtos: retorna uma lista de produtos em json
$klein->respond('GET', '/produtos', function ($request, $response) {
    $response->header('Content-Type', 'application/json');
    
    $data = json_encode([
        ['id' =>1, 'name' => 'batom'],
        ['id' =>2, 'name' => 'perfume'],
        ['id' =>3, 'name' => 'bolacha'],
        ['id' =>4, 'name' => 'Tomate'],
        ['id' =>5, 'name' => 'Felicidade'],
        ['id' =>6, 'name' => 'Conhecimento']
    ]);

    $response->body($data);
    $response->send();
});