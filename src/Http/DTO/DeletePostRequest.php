<?php

namespace App\Http\DTO;

use Symfony\Component\HttpFoundation\Request;

class DeletePostRequest implements RequestWithAuthorizationDTO
{
    private ?int $id;
    private ?string $authorizationHeader;

    public function __construct(Request $request)
    {
        $this->id = $request->attributes->get('id');
        $this->authorizationHeader = $request->headers->get('Authorization');
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function authorizationHeader() 
    {
        return $this->authorizationHeader;
    }
}
