<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToMany;
use JetBrains\PhpStorm\Pure;

#[Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[Id]
    #[GeneratedValue(strategy: "UUID")]
    #[Column(type: "string")]
    private ?string $id;

    #[Column(type: "string", length: 255)]
    private ?string $name;

    #[ManyToMany(targetEntity: Post::class, inversedBy: "tags")]
    private ArrayCollection $posts;

    #[Pure] public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        $this->posts->removeElement($post);

        return $this;
    }


}
