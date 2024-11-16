<?php

class Database
{
    private $pdo;

    public function __construct()
    {



        $config = require 'config.php';
        $db = $config['database'];

        // Create a DSN string
        $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset={$db['charset']}";

        try {
            // Initialize PDO connection
            $this->pdo = new PDO($dsn, $db['username'], $db['password']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}
