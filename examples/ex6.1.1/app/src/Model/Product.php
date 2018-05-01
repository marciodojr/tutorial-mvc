<?php
// app/src/Model/Product.php

namespace TutorialMvc\Model;

use PDO;

class Product
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function fetch()
    {
        $stm = $this->conn->query('select * from products');

        if($stm) {
            return $stm->fetchAll();
        }

        return [];
    }
}