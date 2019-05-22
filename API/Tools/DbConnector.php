<?php

class DbConnector
{
    private $pdo;

    public function __construct($host, $user, $pass, $port=3306)
    {

        $dsn = "mysql:host=$host;dbname=helloprint;port=$port";
        try {
            $this->pdo = new PDO($dsn, $user, $pass);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function getUserByUsername($username)
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE username=:username');
        $statement->execute([':username' => $username]);
        return $statement->fetchObject(User::class);
    }
}