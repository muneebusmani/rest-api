<?php

class Database
{
    /**
     * This class is used to handle connectivity with the database server.
     * @param string $host Hostname of the machine where the database is located
     * @param string $name Name of the database
     * @param string $user the name of the user you want to connect to
     * @param string $pass Password of the user you want to connect to
     */
    public function __construct(
        private string $host,
        private string $name,
        private string $user,
        private string $pass,
    ) {}

    /**
     * This method returns a pdo object
     *
     * This method returns an active connection php data object by which we can interact with out database
     * @return PDO
     */
    public function getConnection(): PDO
    {
        $charset = 'utf8';
        $dsn = "mysql:host=$this->host;dbname=$this->name;charset=$charset";
        $pdo = new PDO($dsn, $this->user, $this->pass, [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ]);
        return $pdo;
    }
}
