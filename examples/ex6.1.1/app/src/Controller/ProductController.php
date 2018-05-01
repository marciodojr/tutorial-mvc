<?php
// app/src/Controller/ProductController.php

namespace TutorialMvc\Controller;

use TutorialMvc\Model\Product;

class ProductController
{
    private $prod;

    public function __construct(Product $p)
    {
        $this->prod = $p;
    }

    public function fetch($request, $response)
    {
        $data = $this->prod->fetch();
        return $response->withJson($data);
    }
}
