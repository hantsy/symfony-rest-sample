<?php

namespace App\Controller\Dto;

class CreateCommentDto
{
    private string $content;

    static function of( string $content): CommentWithPostSummaryDto
    {
        $dto = new CommentWithPostSummaryDto();
        $dto->setContent($content);
        return $dto;
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
    function getContent(): string
    {
        return $this->content;
    }


}