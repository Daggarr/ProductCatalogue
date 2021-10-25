<?php
namespace App\Controllers;

use App\Models\User;
use App\Repositories\MysqlUsersRepository;
use App\Validation\Exceptions\FormException;
use App\Validation\FormValidation;
use App\View;
use Ramsey\Uuid\Uuid;

class UsersController
{
    private MysqlUsersRepository $usersRepository;

    public function __construct()
    {
        $this->usersRepository = new MysqlUsersRepository();
    }

    public function login(): View
    {
        unset($_SESSION['authId']);
        return new View('login.twig',[]);
    }

    public function register(): View
    {
        return new View('register.twig',[]);
    }

    public function verify(): void
    {
        $user = $this->usersRepository->getByUsername($_POST['username']);

        if (isset($user))
        {
            if (password_verify($_POST['password'],$user->getPassword()))
            {
                $_SESSION['authId'] = $user->getId();
            }
        }

        header('Location: /products');
    }

    public function store(): void
    {
        try {
            $validator = new FormValidation();
            $validator->registerFormValidator($_POST);

            if ($_POST['password'] === $_POST['repeatPassword'])
            {
                $user = new User(
                    Uuid::uuid4(),
                    $_POST['email'],
                    $_POST['username'],
                    password_hash($_POST['password'], PASSWORD_DEFAULT)
                );

                $this->usersRepository->save($user);
            }

            header('Location: /');
        }
        catch (FormException $exception)
        {
            foreach ($validator->getErrors() as $error)
            {
                echo $error;
            }
            exit;
        }

    }
}