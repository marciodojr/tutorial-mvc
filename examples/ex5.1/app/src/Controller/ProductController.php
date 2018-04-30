<?php
// app/src/Controller/ProductController.php

namespace TutorialMvc\Controller;

class ProductController
{
    public function __construct()
    {
    }

    public function fetch($request, $response)
    {
        $data = [
            ['id' =>1, 'name' => 'batom'],
            ['id' =>2, 'name' => 'perfume'],
            ['id' =>3, 'name' => 'bolacha'],
            ['id' =>4, 'name' => 'Tomate'],
            ['id' =>5, 'name' => 'Felicidade'],
            ['id' =>6, 'name' => 'Conhecimento']
        ];

        return $response->withJson($data);
    }
}
