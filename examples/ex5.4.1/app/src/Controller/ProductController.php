<?php
// app/src/Controller/ProductController.php

namespace TutorialMvc\Controller;

use TutorialMvc\Model\ProductEntity;

class ProductController
{
    private $prodEnt;

    public function __construct(ProductEntity $pe)
    {
        $this->prodEnt = $pe;
    }

    public function fetch($request, $response)
    {
        $data = $this->prodEnt->fetch();
        return $response->withJson($data);
    }
}
