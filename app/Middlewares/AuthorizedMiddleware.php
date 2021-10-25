<?php
namespace App\Middlewares;

class AuthorizedMiddleware
{
    public function handler()
    {
        if (isset($_SESSION['authId']))
        {
            header('Location: /products');
            exit;
        }
    }
}