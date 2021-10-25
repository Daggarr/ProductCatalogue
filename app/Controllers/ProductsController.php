<?php
namespace App\Controllers;

use App\Models\Collections\ProductsCollection;
use App\Models\Product;
use App\Repositories\MysqlProductsRepository;
use App\Repositories\MysqlProductTagRepository;
use App\View;
use Ramsey\Uuid\Uuid;

class ProductsController
{
    private MysqlProductsRepository $productsRepository;

    public function __construct()
    {
        $this->productsRepository = new MysqlProductsRepository();
    }

    public function index(): ?View
    {
            $products = $this->productsRepository->getAll();

            if ($_GET['category'] !== "" && isset($_GET['category']))
            {
                $products = $this->productsRepository->getByCategory($_GET['category']);
            }

            if (!empty($_GET['tags']))
            {
                $filteredByTags = new ProductsCollection();

                foreach ($products->getProducts() as $product)
                {
                    if (count(array_intersect($_GET['tags'],$product->getTags())) === count($_GET['tags']))
                    {
                        $filteredByTags->add($product);
                    }
                }

                $products = $filteredByTags;
            }

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
            $id = $vars['id'] ?? null;

            if ($id == null) header('Location: /products');

            $product = $this->productsRepository->getById($id);

            if ($product !== null)
            {
                $this->productsRepository->delete($product);
            }

            header('Location: /products');
    }

    public function store()
    {
            $product = new Product(
                Uuid::uuid4(),
                $_POST['name'],
                $_POST['quantity'],
                $_POST['category'],
                $_SESSION['authId']
            );

            $productTagRepository = new MysqlProductTagRepository();

            foreach ($_POST['tags'] as $tagId)
            {
                $productTagRepository->save($product->getId(),$tagId);
            }

            $this->productsRepository->save($product);

            header('Location: /products');
    }
}