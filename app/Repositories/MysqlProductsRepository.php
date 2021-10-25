<?php
namespace App\Repositories;

use App\Models\Collections\ProductsCollection;
use App\Models\Product;
use PDO;
use PDOException;

class MysqlProductsRepository implements ProductsRepository
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

    public function save(Product $product): void
    {
        $sql = "INSERT INTO products (id, name, quantity, category, user_id) VALUES (?, ?, ?, ?, ?)";
        $statement = $this->conn->prepare($sql);
        $statement->execute([
            $product->getId(),
            $product->getName(),
            $product->getQuantity(),
            $product->getCategory(),
            $product->getUserId()
        ]);
    }

    public function getAll(): ProductsCollection
    {
        $collection = new ProductsCollection();

        $sql = "SELECT * FROM products WHERE user_id = ?";
        $statement = $this->conn->prepare($sql);
        $statement->execute([$_SESSION['authId']]);
        $products = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $product)
        {
            $collection->add(new Product(
                $product['id'],
                $product['name'],
                $product['quantity'],
                $product['category'],
                $product['user_id']
            ));
        }

        return $collection;
    }

    public function delete(Product $product): void
    {
        $sql = "DELETE FROM products WHERE id = ?";
        $statement = $this->conn->prepare($sql);
        $statement->execute([$product->getId()]);
    }

    public function getById(string $id): ?Product
    {
        $sql = "SELECT * FROM products WHERE id = ? AND user_id = ?";
        $statement = $this->conn->prepare($sql);
        $statement->execute([$id, $_SESSION['authId']]);
        $product = $statement->fetch();

        return new Product(
            $product['id'],
            $product['name'],
            $product['quantity'],
            $product['category'],
            $product['user_id'],
        );
    }

    public function getByCategory(string $category): ProductsCollection
    {
        $collection = new ProductsCollection();

        $sql = "SELECT * FROM products WHERE category = ? AND user_id = ?";
        $statement = $this->conn->prepare($sql);
        $statement->execute([$category,$_SESSION['authId']]);
        $products = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $product)
        {
            $collection->add(new Product(
                $product['id'],
                $product['name'],
                $product['quantity'],
                $product['category'],
                $product['user_id']
            ));
        }

        return $collection;
    }
}