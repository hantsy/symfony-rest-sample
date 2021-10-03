<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[Id]
    #[GeneratedValue(strategy: "UUID")]
    #[Column(type: "string", unique: true)]
    //    #[GeneratedValue(strategy: "CUSTOM")]
    //    #[CustomIdGenerator(class:UuidGenerator::class)]
    private ?string $id = null;

    #[Column(type: "string", length: 255)]
    private string $content;

    #[Column(name: "created_at", type: "datetime")]
    private \DateTime|null $createdAt;

    #[ManyToOne(targetEntity: "Post", inversedBy: "comments")]
    #[JoinColumn(name: "post_id", referencedColumnName: "id")]
    private Post $post;

    public function __construct()
    {
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(Post $post): self
    {
        $this->post = $post;
        return $this;
    }
}
