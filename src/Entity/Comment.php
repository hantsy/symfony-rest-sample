<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use DateTime;

use DateTimeInterface;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[Id]
    //#[GeneratedValue(strategy: "UUID")]
    #[Column(type: "uuid", unique: true)]
    #[GeneratedValue(strategy: "CUSTOM")]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[Column(type: "string", length: 255)]
    private string $content;

    #[Column(name: "created_at", type: "datetime", nullable: true)]
    private DateTimeInterface|null $createdAt = null;

    #[ManyToOne(targetEntity: "Post", inversedBy: "comments")]
    #[JoinColumn(name: "post_id", referencedColumnName: "id")]
    #[Ignore]
    private Post $post;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public static function of(string $content): Comment
    {
        $comment = new Comment();
        $comment->setContent($content);
        return $comment;
    }

    public function getId(): ?Uuid
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

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface|null $createdAt
     * @return $this
     */
    public function setCreatedAt(?DateTimeInterface $createdAt): self
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
