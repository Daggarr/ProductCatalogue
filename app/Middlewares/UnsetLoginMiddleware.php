<?php

namespace App\Middlewares;

class UnsetLoginMiddleware
{
    public function handler()
    {
        unset($_SESSION['authId']);
    }
}