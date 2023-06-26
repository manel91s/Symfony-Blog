<?php

namespace App\Http\DTO;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class PostRequest implements RequestDTO
{
    #[Assert\NotBlank]
    private ?string $title;
    #[Assert\NotBlank]
    private ?string $body;
    private ?string $slug;

    public function __construct(Request $request)
    {
        $this->title = $request->request->get('title');
        $this->body = $request->request->get('body');        
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

}