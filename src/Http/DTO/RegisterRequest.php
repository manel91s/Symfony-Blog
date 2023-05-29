<?php

namespace App\Http\DTO;

use Symfony\Component\HttpFoundation\Request;

class RegisterRequest implements RequestDTO
{
    private ?string $name;
    private ?string $email;
    private ?string $password;
    public function __construct(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $this->name = $data['name'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->password = $data['password'] ?? null;
    }

    public function getName(): ?string
    {
        return $this->name;
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