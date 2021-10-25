<?php

namespace App\Repositories;

use PDO;
use PDOException;

class MysqlTagsRepository
{
    private PDO $conn;

    public function __construct()
    {
        $config = parse_ini_file('config.ini');

        try {
            $this->conn = new PDO(
                "mysql:host={$config['serverName']};dbname={$config['dbName']}",
                $config['dbUser'],
                $config['dbPassword']);
            // set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function getTag(string $tagId): string
    {
        $sql = "SELECT name FROM tags WHERE id = ?";
        $statement = $this->conn->prepare($sql);
        $statement->execute([$tagId]);
        $tag = $statement->fetch();

        return $tag['name'];
    }
}