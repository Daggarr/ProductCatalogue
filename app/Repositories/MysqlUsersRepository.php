<?php
namespace App\Repositories;

use App\Models\User;
use PDO;
use PDOException;

class MysqlUsersRepository implements UsersRepository
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

    public function getByUsername(string $username): ?User
    {
        $sql = "SELECT * FROM users WHERE username = ?";
        $statement = $this->conn->prepare($sql);
        $statement->execute([$username]);
        $user = $statement->fetch();

        if (empty($user)) return null;

        return new User(
            $user['id'],
            $user['email'],
            $user['username'],
            $user['password']
        );
    }

    public function save(User $user): void
    {
        $sql = "INSERT INTO users (id, email, username, password) VALUES (?, ?, ?, ?)";
        $statement = $this->conn->prepare($sql);
        $statement->execute([
            $user->getId(),
            $user->getEmail(),
            $user->getUsername(),
            $user->getPassword()
        ]);
    }
}