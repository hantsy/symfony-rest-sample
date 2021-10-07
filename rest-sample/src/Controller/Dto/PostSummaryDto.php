<?php

namespace App\Controller\Dto;

class PostSummaryDto
{
    private string $id;
    private string $title;

    static function of(string $id, string $title): PostSummaryDto
    {
        $dto = new PostSummaryDto();
        $dto->setId($id)->setTitle($title);
        return $dto;
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
     * @return PostSummaryDto
     */
    public function setId(string $id): PostSummaryDto
    {
        $this->id = $id;
        return $this;
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
    public
    function getTitle(): string
    {
        return $this->title;
    }

}