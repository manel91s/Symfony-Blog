<?php

namespace App\Http\DTO;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ForgotPasswordRequest implements RequestDTO
{
    #[Assert\NotBlank]
    private ?string $email;

    public function __construct(Request $request)
    {
        $this->email = $request->request->get('email');
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}