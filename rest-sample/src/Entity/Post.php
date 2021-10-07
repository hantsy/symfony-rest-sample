<?php

namespace App\Entity;

use App\Repository\PostRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints\Cascade;
use Symfony\Component\Validator\Mapping\CascadingStrategy;

#[Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[Id]
    #[GeneratedValue(strategy: "UUID")]
    #[Column(type: "string", unique: true)]
    private ?string $id = null;

    #[Column(type: "string", length: 255)]
    private string $title;

    #[Column(type: "string", length: 255)]
    private string $content;

    #[Column(type: "datetime", nullable: true)]
    private DateTime|null $createdAt = null;

    #[Column(type: "datetime", nullable: true)]
    private DateTime|null $publishedAt = null;

    #[OneToMany(mappedBy: "post", targetEntity: Comment::class, fetch: 'LAZY', orphanRemoval: true)]
    private Collection $comments;

    #[ManyToMany(targetEntity: Tag::class, mappedBy: "posts", cascade: ['persist','merge'], fetch: 'EAGER')]
    private Collection $tags;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getPublishedAt(): ?DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @param DateTime $publishedAt
     */
    public function setPublishedAt(DateTime $publishedAt): self
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }


    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addPost($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removePost($this);
        }

        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }


    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost(null);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            $comment->setPost(null);
        }

        return $this;
    }

    public function __toString(): string
    {
        return "Post: [ id =" . $this->getId()
            . ", title=" . $this->getTitle()
            . ", content=" . $this->getContent()
            . ", createdAt=" . $this->getCreatedAt()->getTimestamp()
            . ", publishedAt=" . $this->getPublishedAt()?->getTimestamp()
            . "]";
    }

}
