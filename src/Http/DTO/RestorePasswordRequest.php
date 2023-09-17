<?php

namespace App\Http\DTO;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class RestorePasswordRequest implements RequestDTO
{
    #[Assert\NotBlank]
    private ?string $token;
    
    #[Assert\NotBlank]
    private ?string $password;

    public function __construct(Request $request)
    {
        $this->token = substr($request->attributes->get('token'), 6);
        $this->password = $request->request->get('password');
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
