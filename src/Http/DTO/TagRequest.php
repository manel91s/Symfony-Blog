<?php

namespace App\Http\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class TagRequest implements RequestWithAuthorizationDTO
{
    private ?int $id = null;

    #[Assert\NotBlank]
    private ?string $name = null;
    private ?string $authorizationHeader;
    
    public function __construct(Request $request)
    {
        $this->id = $request->attributes->get('id');
        $this->name = $request->request->get('name');
        $this->authorizationHeader = $request->headers->get('Authorization');
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
    
    public function authorizationHeader(): ?string
    {
        return $this->authorizationHeader;
    }
}