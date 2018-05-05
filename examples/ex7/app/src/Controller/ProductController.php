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

    public function create($request, $response)
    {
        $name = $request->getParam('name');
        $product = $this->prod->create($name);
        return $response->withJson($product);
    }

    public function find($request, $response, $args)
    {
        $product = $this->prod->find($args['id']);
        return $response->withJson($product);
    }

    public function update($request, $response, $args)
    {
        $name = $request->getParam('name');
        $product = $this->prod->update($args['id'], $name);
        return $response->withJson($product);
    }

    public function delete($request, $response, $args)
    {
        $product = $this->prod->delete($args['id']);
        return $response->withJson($product);
    }
}
