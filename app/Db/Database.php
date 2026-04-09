<?php

namespace App\Db;

use PDO;
use PDOException;

class Database
{
    private $connection;

    public function __construct($table = null)
    {
        $this->connection = new PDO(
            "mysql:host=localhost;
            dbname=restaurante;
            charset=utf8",
            "root",
            ""
        );

        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * INSERT
     */
    public function insert($values)
    {
        $fields = array_keys($values);
        $binds  = array_pad([], count($fields), '?');

        $query = "INSERT INTO pagamentos (" . implode(',', $fields) . ") VALUES (" . implode(',', $binds) . ")";

        $stmt = $this->connection->prepare($query);
        $stmt->execute(array_values($values));

        return $this->connection->lastInsertId();
    }
}
