<?php

namespace App\Controller;

use App\ArgumentResolver\Body;
use App\ArgumentResolver\QueryParam;
use App\Dto\CreateCommentDto;
use App\Dto\CreatePostDto;
use App\Dto\UpdatePostDto;
use App\Dto\UpdatePostStatusDto;
use App\Entity\Comment;
use App\Entity\PostFactory;
use App\Exception\PostNotFoundException;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

#[Route(path: "/posts", name: "posts_")]
class PostController extends AbstractController
{
    /**
     * @param PostRepository $posts
     * @param CommentRepository $comments
     * @param EntityManagerInterface $objectManager
     * @param SerializerInterface $serializer
     */
    public function __construct(private PostRepository         $posts,
                                private CommentRepository      $comments,
                                private EntityManagerInterface $objectManager,
                                private SerializerInterface    $serializer)
    {
    }

//    #[Route(path: "", name: "all", methods: ["GET"])]
//    function all(): Response
//    {
//        $data = $this->posts->findAll();
//        return $this->json($data);
//    }

    // function all(string $keyword, #[PositiveOrZero] int $offset = 0, #[Positive] int $limit = 20): Response
    // see: https://github.com/symfony/symfony/issues/43958
    #[Route(path: "", name: "get_all", methods: ["GET"])]
    function all(#[QueryParam] string $keyword,
                 #[QueryParam] int $offset = 0,
                 #[QueryParam] int $limit = 20): Response
    {
        $data = $this->posts->findByKeyword($keyword ?: '', $offset, $limit);
        return $this->json($data);
    }

    #[Route(path: "/{id}", name: "get", methods: ["GET"])]
    function getById(Uuid $id): Response
    {
        $data = $this->posts->findOneBy(["id" => $id]);
        if ($data) {
            return $this->json($data);
        } else {
            throw new PostNotFoundException($id);
            //return $this->json(["error" => "Post was not found by id:" . $id], 404);
        }
    }

    #[Route(path: "", name: "create", methods: ["POST"])]
    public function create(#[Body] CreatePostDto $data): Response
    {
        $entity = PostFactory::create($data->getTitle(), $data->getContent());
        $this->objectManager->persist($entity);
        $this->objectManager->flush();

        return $this->json([], 201, ["Location" => "/posts/" . $entity->getId()]);
    }

    #[Route(path: "/{id}", name: "update", methods: ["PUT"])]
    public function update(Uuid $id, #[Body] UpdatePostDto $data): Response
    {
        $entity = $this->posts->findOneBy(["id" => $id]);
        if (!$entity) {
            throw new PostNotFoundException($id);
            //return $this->json(["error" => "Post was not found by id:" . $id], 404);
        }
        $entity->setTitle($data->getTitle())
            ->setContent($data->getContent());
        $this->objectManager->merge($entity);
        $this->objectManager->flush();

        return $this->json([], 204);
    }

    #[Route(path: "/{id}/status", name: "update_status", methods: ["PUT"])]
    public function updateStatus(Uuid $id, #[Body] UpdatePostStatusDto $data): Response
    {
        $entity = $this->posts->findOneBy(["id" => $id]);
        if (!$entity) {
            throw new PostNotFoundException($id);
            //return $this->json(["error" => "Post was not found by id:" . $id], 404);
        }
        echo "update post status::::".PHP_EOL;
        var_export($data);
        $entity->setStatus($data->getStatus());
        $this->objectManager->merge($entity);
        $this->objectManager->flush();

        return $this->json([], 204);
    }

    #[Route(path: "/{id}", name: "delete", methods: ["DELETE"])]
    public function deleteById(Uuid $id): Response
    {
        $entity = $this->posts->findOneBy(["id" => $id]);
        if (!$entity) {
            throw new PostNotFoundException($id);
            //return $this->json(["error" => "Post was not found by id:" . $id], 404);
        }
        $this->objectManager->remove($entity);
        $this->objectManager->flush();

        return $this->json([], 204);
    }

    // comments sub resources.
    #[Route(path: "/{id}/comments", name: "commentByPostId", methods: ["GET"])]
    function getComments(Uuid $id): Response
    {
        $data = $this->posts->findOneBy(["id" => $id]);
        if ($data) {
            return $this->json($data->getComments());
        } else {
            throw new PostNotFoundException($id);
            //return $this->json(["error" => "Post was not found b}y id:" . $id], 404);
        }
    }

    #[Route(path: "/{id}/comments", name: "addComments", methods: ["POST"])]
    function addComment(Uuid $id, Request $request): Response
    {
        $data = $this->posts->findOneBy(["id" => $id]);
        if ($data) {
            $dto = $this->serializer->deserialize($request->getContent(), CreateCommentDto::class, 'json');
            $entity = Comment::of($dto->getContent());

            $this->objectManager->persist($entity->setPost($data));
            $this->objectManager->flush();
            return $this->json([], 201, ["Location" => "/comments/" . $entity->getId()]);
        } else {
            throw new PostNotFoundException($id);
            //return $this->json(["error" => "Post was not found b}y id:" . $id], 404);
        }
    }

}
