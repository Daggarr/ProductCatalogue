<?php
namespace App\Services\Products;

use App\Repositories\MysqlProductsRepository;

class DeleteProductsService
{
    private MysqlProductsRepository $productsRepository;
    private array $vars;

    public function __construct(MysqlProductsRepository $productsRepository, array $vars)
    {
        $this->productsRepository = $productsRepository;
        $this->vars = $vars;
    }

    public function execute()
    {
        $id = $this->vars['id'] ?? null;

        if ($id == null) header('Location: /products');

        $product = $this->productsRepository->getById($id);

        if ($product !== null)
        {
            $this->productsRepository->delete($product);
        }
    }
}
