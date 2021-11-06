<?php
namespace App\Services\Products;

use App\Models\Collections\ProductsCollection;
use App\Repositories\MysqlProductsRepository;

class GetProductsService
{
    private MysqlProductsRepository $productsRepository;
    private array $get;

    public function __construct(MysqlProductsRepository $productsRepository, array $get)
    {
        $this->productsRepository = $productsRepository;
        $this->get = $get;
    }

    public function execute(): ProductsCollection
    {
        $products = $this->productsRepository->getAll();

        if ($this->get['category'] !== "" && isset($this->get['category']))
        {
            $products = $this->productsRepository->getByCategory($this->get['category']);
        }

        if (!empty($this->get['tags']))
        {
            $filteredByTags = new ProductsCollection();

            foreach ($products->getProducts() as $product)
            {
                if (count(array_intersect($this->get['tags'],$product->getTags())) === count($this->get['tags']))
                {
                    $filteredByTags->add($product);
                }
            }

            $products = $filteredByTags;
        }

        return $products;

    }
}