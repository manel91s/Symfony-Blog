<?php

namespace App\Http\DTO;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterRequest implements RequestDTO
{
    #[Assert\NotBlank]
    private ?string $name;
    #[Assert\NotBlank]
    private ?string $surname;
    #[Assert\NotBlank]
    private ?string $email;
    #[Assert\NotBlank]
    private ?string $password;
    public function __construct(Request $request)
    {
        $this->name = $request->request->get('name');
        $this->surname = $request->request->get('surname');
        $this->email = $request->request->get('email');
        $this->password = $request->request->get('password');
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
}