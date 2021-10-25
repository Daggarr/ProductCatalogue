<?php
namespace App\Repositories;

use App\Models\User;

interface UsersRepository
{
    public function getByUsername(string $username):?User;
    public function save(User $user):void;
}