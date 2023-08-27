<?php

namespace App\Http\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class PostRequest implements RequestWithAuthorizationDTO
{
    #[Assert\NotBlank]
    private ?string $title;
    #[Assert\NotBlank]
    private ?string $body;
    private ?string $slug;
    
    private ?UploadedFile $image;
    #[Assert\NotBlank]
    private ?string $authorizationHeader;

    public function __construct(Request $request)
    {
        $this->title = $request->request->get('title');
        $this->body = $request->request->get('body');
        $this->authorizationHeader = $request->headers->get('Authorization');
        $this->image = $request->files->get('image');
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function authorizationHeader(): ?string
    {
        return $this->authorizationHeader;
    }
    public function getImage(): ?UploadedFile
    {
        return $this->image;
    }

}