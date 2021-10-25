<?php
namespace App\Models;

class User
{
    private string $id;
    private string $email;
    private string $username;
    private string $password;

    public function __construct(string $id, string $email, string $username, string $password)
    {
        $this->id = $id;
        $this->email = $email;
        $this->username = $username;
        //password is saved as hash
        $this->password = $password;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}