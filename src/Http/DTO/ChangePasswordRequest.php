<?php

namespace App\Http\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;


class ChangePasswordRequest implements RequestWithAuthorizationDTO
{
    #[Assert\NotBlank]
    private ?string $oldPassword;
    #[Assert\NotBlank]
    private ?string $newPassword;
    private ?string $authorizationHeader;
    public function __construct(Request $request)
    {
        $this->oldPassword = $request->request->get('oldPassword');
        $this->newPassword = $request->request->get('newPassword');
        $this->authorizationHeader = $request->headers->get('Authorization');
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function authorizationHeader(): ?string
    {
        return $this->authorizationHeader;
    }
}