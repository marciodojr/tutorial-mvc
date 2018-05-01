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

    public function find($id)
    {
        $stm = $this->conn->prepare('select * from products where id=?');
        if($stm->execute([$id])) {
            $product = $stm->fetch();
            if($product) {
                return $product;
            }
        }

        return [];
    }

    public function create($name)
    {
        $stm = $this->conn->prepare('insert into products(name) value(?)');
        if($stm->execute([$name])) {
            return $this->find($this->conn->lastInsertId());
        }

        return [];
    }

    public function update($id, $name)
    {
        $stm = $this->conn->prepare('update products set name=? where id=?');
        if($stm->execute([$name, $id])) {
            return $this->find($id);
        }

        return [];
    }

    public function delete($id)
    {
        $product = $this->find($id);

        if($product) {
            $stm = $this->conn->prepare('delete from products where id=?');
            if($stm->execute([$id])) {
                return $product;
            }
        }

        return [];
    }
}