<?php

class DatabaseConnector {

    private $dbConnection = null;

    public function __construct()
    {
        $host = 'db';
        $db   = 'resizer';
        $user = 'root';
        $pass = 'root';

        try {
            $this->dbConnection = new \PDO(
                "mysql:host=$host;dbname=$db",
                $user,
                $pass
            );
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->dbConnection;
    }
}