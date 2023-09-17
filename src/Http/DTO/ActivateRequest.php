<?php

namespace App\Http\DTO;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ActivateRequest implements RequestDTO
{
    #[Assert\NotBlank]
    private ?string $token;

    public function __construct(Request $request)
    {
        $this->token = substr($request->attributes->get('token'), 6);
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
