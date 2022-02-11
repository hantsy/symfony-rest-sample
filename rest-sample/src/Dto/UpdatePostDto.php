<?php

namespace App\Dto;

class UpdatePostDto
{
    private string $title;
    private string $content;

    static function of(string $title, string $content): UpdatePostStatusDto
    {
        $dto = new UpdatePostStatusDto();
        $dto->setTitle($title)->setContent($content);
        return $dto;
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
     * @param string $content
     */
    public
    function setContent(string $content): self
    {
        $this->content = $content;
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

    /**
     * @return string
     */
    public
    function getContent(): string
    {
        return $this->content;
    }


}