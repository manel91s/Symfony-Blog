<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "posts")]
    private User $user;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $body = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_registration = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_update = null;

    #[ORM\Column(nullable: true)]
    private ?bool $public = null;

    public function __construct(string $title, string $body)
    {
        $this->title = $title;
        $this->body = $body;
        $this->date_registration = new \DateTimeImmutable();
        $this->date_update = new \DateTimeImmutable();
        $this->public = true;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDateRegistration(): ?\DateTimeInterface
    {
        return $this->date_registration;
    }

    public function setDateRegistration(?\DateTimeInterface $date_registration): self
    {
        $this->date_registration = $date_registration;

        return $this;
    }

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->date_update;
    }

    public function setDateUpdate(?\DateTimeInterface $date_update): self
    {
        $this->date_update = $date_update;

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(?bool $public): self
    {
        $this->public = $public;

        return $this;
    }
}
