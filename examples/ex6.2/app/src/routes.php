<?php
// app/src/routes.php

$router->group('/produtos', function(){
    // GET /produtos: retorna uma lista de produtos no formato json
    $this->get('', 'TutorialMvc\Controller\ProductController:fetch');
    // GET /produtos/{id}: retorna o produto cujo id é "id"
    $this->get('/{id}', 'TutorialMvc\Controller\ProductController:find');
    // POST /produtos: cria um novo produto
    $this->post('', 'TutorialMvc\Controller\ProductController:create');
    // PUT /produtos/{id}: atualiza o produto cujo id é "id"
    $this->put('/{id}', 'TutorialMvc\Controller\ProductController:update');
    // DELETE /produtos/{id}: remove o produto cujo id é "id"
    $this->delete('/{id}', 'TutorialMvc\Controller\ProductController:delete');    
});