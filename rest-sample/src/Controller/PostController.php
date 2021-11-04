<?php

namespace App\Controller;

use App\Controller\Dto\CreateCommentDto;
use App\Controller\Dto\CreatePostDto;
use App\Entity\Comment;
use App\Entity\PostFactory;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Request\ParamConverter\Body;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

#[Route(path: "/posts", name: "posts_")]
class PostController extends AbstractController
{
    public function __construct(private PostRepository      $posts,
                                private CommentRepository   $comments,
                                private SerializerInterface $serializer)
    {
    }

    #[Route(path: "", name: "all", methods: ["GET"])]
    function all(): Response
    {
        $data = $this->posts->findAll();
        return $this->json($data);
    }

    #[Route(path: "/{id}", name: "byId", methods: ["GET"])]
    function getById(Uuid $id): Response
    {
        $data = $this->posts->findOneBy(["id" => $id]);
        if ($data) {
            return $this->json($data);
        } else {
            return $this->json(["error" => "Post was not found by id:" . $id], 404);
        }
    }

    #[Route(path: "", name: "create", methods: ["POST"])]
    public function create(Request $request): Response
    {
        $data = $this->serializer->deserialize($request->getContent(), CreatePostDto::class, 'json');
        $entity = PostFactory::create($data->getTitle(), $data->getContent());
        $this->posts->getEntityManager()->persist($entity);

        return $this->json([], 201, ["Location" => "/posts/" . $entity->getId()]);
    }

    #[Route(path: "/{id}", name: "delete", methods: ["DELETE"])]
    public function deleteById(Uuid $id): Response
    {
        $entity = $this->posts->findOneBy(["id" => $id]);
        if (!$entity) {
            return $this->json(["error" => "Post was not found by id:" . $id], 404);
        }
        $this->posts->getEntityManager()->remove($entity);

        return $this->json([], 202);
    }

    // comments sub resources.
    #[Route(path: "/{id}/comments", name: "commentByPostId", methods: ["GET"])]
    function getComments(Uuid $id): Response
    {
        $data = $this->posts->findOneBy(["id" => $id]);
        if ($data) {
            return $this->json($data->getComments());
        } else {
            return $this->json(["error" => "Post was not found b}y id:" . $id], 404);
        }
    }

    #[Route(path: "/{id}/comments", name: "addComments", methods: ["POST"])]
    function addComment(Uuid $id, Request $request): Response
    {
        $data = $this->posts->findOneBy(["id" => $id]);
        if ($data) {
            $dto = $this->serializer->deserialize($request->getContent(), CreateCommentDto::class, 'json');
            $entity = Comment::of($dto->getContent());
            $this->comments->getEntityManager()->persist($entity->setPost($data));
            //$data->addComment(Comment::of($dto->getContent()));
            return $this->json([], 201, ["Location" => "/comments/" . $entity->getId()]);
        } else {
            return $this->json(["error" => "Post was not found b}y id:" . $id], 404);
        }
    }

}