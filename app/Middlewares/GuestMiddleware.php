<?php
namespace App\Middlewares;

class GuestMiddleware
{
    public function handler()
    {
        if (!isset($_SESSION['authId']))
        {
            header('Location: /');
        }
    }
}