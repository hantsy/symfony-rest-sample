<?php

namespace App\Dto;

class CommentWithPostSummaryDto
{
    private string $id;

    private string $content;

    private PostSummaryDto $post;

    static function of(string $id, string $content, PostSummaryDto $post): CommentWithPostSummaryDto
    {
        $dto = new CommentWithPostSummaryDto();
        $dto->setId($id)->setContent($content)->setPost($post);
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
     * @return CommentWithPostSummaryDto
     */
    public function setId(string $id): self
    {
        $this->id = $id;
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
    function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return PostSummaryDto
     */
    public function getPost(): PostSummaryDto
    {
        return $this->post;
    }

    /**
     * @param PostSummaryDto $post
     * @return CommentWithPostSummaryDto
     */
    public function setPost(PostSummaryDto $post): self
    {
        $this->post = $post;
        return $this;
    }
}