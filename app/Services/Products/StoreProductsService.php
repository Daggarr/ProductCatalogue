<?php
namespace App\Services\Products;

use App\Models\Product;
use App\Repositories\MysqlProductsRepository;
use App\Repositories\MysqlProductTagRepository;
use Ramsey\Uuid\Uuid;

class StoreProductsService
{
    private MysqlProductsRepository $productsRepository;
    private array $post;

    public function __construct(MysqlProductsRepository $productsRepository, array $post)
    {
        $this->productsRepository = $productsRepository;
        $this->post = $post;
    }

    public function execute(): void
    {
        $product = new Product(
            Uuid::uuid4(),
            $this->post['name'],
            $this->post['quantity'],
            $this->post['category'],
            $_SESSION['authId']
        );

        $productTagRepository = new MysqlProductTagRepository();

        foreach ($this->post['tags'] as $tagId)
        {
            $productTagRepository->save($product->getId(),$tagId);
        }

        $this->productsRepository->save($product);
    }
}