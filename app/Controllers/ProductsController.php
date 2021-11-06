<?php
namespace App\Controllers;

use App\Repositories\MysqlProductsRepository;
use App\Services\Products\DeleteProductsService;
use App\Services\Products\GetProductsService;
use App\Services\Products\StoreProductsService;
use App\View;

class ProductsController
{
    private MysqlProductsRepository $productsRepository;

    public function __construct(MysqlProductsRepository $productsRepository)
    {
        $this->productsRepository = $productsRepository;
    }

    public function index(): ?View
    {
        $service = new GetProductsService($this->productsRepository, $_GET);
        $products = $service->execute();

        return new View('products.twig',['products' => $products]);
    }

    public function create(): ?View
    {
        return new View('productCreate.twig',[]);
    }

    public function edit(array $vars): ?View
    {
        return new View('productEdit.twig',['vars' => $vars]);
    }

    public function delete(array $vars)
    {
        $service = new DeleteProductsService($this->productsRepository, $vars);
        $service->execute();

        header('Location: /products');
    }

    public function store()
    {
        $service = new StoreProductsService($this->productsRepository, $_POST);
        $service->execute();

        header('Location: /products');
    }
}