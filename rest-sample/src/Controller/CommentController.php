<?php

namespace App\Controller;


use App\Dto\CommentWithPostSummaryDto;
use App\Dto\PostSummaryDto;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('comments', name: 'comments')]
class CommentController extends AbstractController
{

    public function __construct(private CommentRepository $commentRepository)
    {
    }

    #[Route('{id}', name: "getById")]
    public function byId(string $id): Response
    {
        $data = $this->commentRepository->findOneBy(["id" => $id]);
        if ($data) {
            $dto = CommentWithPostSummaryDto::of(
                $data->getId(),
                $data->getContent(),
                PostSummaryDto::of($data->getPost()?->getId(), $data->getPost()?->getTitle())
            );
            return $this->json($dto);
        } else {
            return $this->json(["error" => "Comment was not found" . $id], 404);
        }
    }
}
