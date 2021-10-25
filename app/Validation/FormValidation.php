<?php
namespace App\Validation;

use App\Validation\Exceptions\FormException;

class FormValidation
{
    private array $errors = [];

    public function __construct()
    {
        $this->errors = [];
    }

    /**
     * @throws FormException
     */
    public function registerFormValidator(array $data)
    {
        $this->errors = [];

        if (empty($_POST['email']))
        {
            $this->errors[] = "Email can't be empty";
        }

        if (empty($_POST['username']))
        {
            $this->errors[] = "Username can't be empty";
        }

        if (empty($_POST['password']))
        {
            $this->errors[] = "Password can't be empty";
        }

        if (count($this->errors)>0)
        {
            throw new FormException();
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}