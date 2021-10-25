<?php

namespace App\Repositories;

use PDO;
use PDOException;

class MysqlProductTagRepository
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

    public function save(string $productId, string $tagId): void
    {
        $sql = "INSERT INTO product_tag (product_id, tag_id) VALUES (?, ?)";
        $statement = $this->conn->prepare($sql);
        $statement->execute([
            $productId,
            $tagId
        ]);
    }

    public function getTags(string $productId): array
    {
        $sql = "SELECT tag_id FROM product_tag WHERE product_id = ?";
        $statement = $this->conn->prepare($sql);
        $statement->execute([$productId]);
        $tagIds = $statement->fetchAll(PDO::FETCH_ASSOC);

        $tags = [];
        $tagsRepository = new MysqlTagsRepository();

        foreach ($tagIds as $id)
        {
            $tags[] = $tagsRepository->getTag($id['tag_id']);
        }

        return $tags;
    }
}