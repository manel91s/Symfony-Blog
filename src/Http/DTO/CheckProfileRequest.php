<?php

namespace App\Http\DTO;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class CheckProfileRequest implements RequestWithAuthorizationDTO
{
    private ?string $authorizationHeader;

    public function __construct(Request $request)
    {
        $this->authorizationHeader = $request->headers->get('Authorization');
    }

    public function authorizationHeader(): ?string
    {
        return $this->authorizationHeader;
    }
}