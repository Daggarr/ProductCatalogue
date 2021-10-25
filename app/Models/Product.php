<?php
namespace App\Models;

use App\Repositories\MysqlProductTagRepository;

class Product
{
    private string $id;
    private string $name;
    private int $quantity;
    private string $category;
    private string $userId;

    public function __construct(string $id, string $name, int $quantity, string $category, string $userId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->category = $category;
        $this->userId = $userId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getTags(): array
    {
        $productTagRepository = new MysqlProductTagRepository();

        return $productTagRepository->getTags($this->id);
    }
}