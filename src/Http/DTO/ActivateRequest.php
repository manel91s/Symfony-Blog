<?php

namespace App\Http\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterRequest implements RequestDTO
{
    #[Assert\NotBlank]
    private string $token;
    
    public function __construct(Request $request)
    {
        $this->token = $request->request->get('token');
    }

    public function getToken(): string
    {
        return $this->token;
    }

}