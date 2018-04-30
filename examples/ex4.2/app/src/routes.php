<?php
// app/src/routes.php

// GET /produtos: retorna uma lista de produtos no formato json
$router->get('/produtos', function ($request, $response) {    
    
    $data = [
        ['id' =>1, 'name' => 'batom'],
        ['id' =>2, 'name' => 'perfume'],
        ['id' =>3, 'name' => 'bolacha'],
        ['id' =>4, 'name' => 'Tomate'],
        ['id' =>5, 'name' => 'Felicidade'],
        ['id' =>6, 'name' => 'Conhecimento']
    ];

    return $response->withJson($data);
});