<?php
// app/src/Model/ProductEntity.php

namespace TutorialMvc\Model;

use PDO;

class ProductEntity
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function fetch()
    {
        $data = [
            ['id' =>1, 'name' => 'batom'],
            ['id' =>2, 'name' => 'perfume'],
            ['id' =>3, 'name' => 'bolacha'],
            ['id' =>4, 'name' => 'Tomate'],
            ['id' =>5, 'name' => 'Felicidade'],
            ['id' =>6, 'name' => 'Conhecimento']
        ];

        return $data;
    }
}