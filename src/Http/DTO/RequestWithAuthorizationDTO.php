<?php

namespace App\Http\DTO;

use Symfony\Component\HttpFoundation\Request;

interface RequestWithAuthorizationDTO
{
    public function __construct(Request $request);

    public function authorizationHeader();
}