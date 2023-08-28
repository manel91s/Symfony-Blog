<?php

namespace App\Http\DTO;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class LoginRequest implements RequestDTO
{
    #[Assert\NotBlank]
    private ?string $email;
    #[Assert\NotBlank]
    private ?string $password;
    private ?string $authorizationHeader;

    public function __construct(Request $request)
    {
        $this->email = $request->request->get('email');
        $this->password = $request->request->get('password');
        $this->authorizationHeader = $request->headers->get('Authorization');
        
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function authorizationHeader(): ?string
    {
        return $this->authorizationHeader;
    }
}