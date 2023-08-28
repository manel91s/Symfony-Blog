<?php

namespace App\Http\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;


class ProfileRequest implements RequestWithAuthorizationDTO
{
    #[Assert\NotBlank]
    private ?string $name;
    #[Assert\NotBlank]
    private ?string $surname;
    #[Assert\NotBlank]
    private ?string $email;
    private ?UploadedFile $file;
    private ?string $authorizationHeader;
    
    public function __construct(Request $request)
    {
        $this->name = $request->request->get('name');
        $this->surname = $request->request->get('surname');
        $this->email = $request->request->get('email');
        $this->file = $request->files->get('avatar');
        $this->authorizationHeader = $request->headers->get('Authorization');
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }
    
    public function authorizationHeader(): ?string
    {
        return $this->authorizationHeader;
    }
}