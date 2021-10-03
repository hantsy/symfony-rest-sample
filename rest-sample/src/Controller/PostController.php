<?php

namespace App\Controller;

use App\Controller\Dto\CreatePostDto;
use App\Entity\PostFactory;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: "/posts", name: "posts_")]
class PostController extends AbstractController
{
    public function __construct(private PostRepository $posts)
    {
    }

    #[Route(path: "", name: "all", methods: ["GET"])]
    function all(): Response
    {
        /*        $post1 = PostFactory::create("test title", "test content");
                $post1->setId("1");

                $post2 = PostFactory::create("test title", "test content");
                $post2->setId("2");
                $data = [$post1->asArray(), $post2->asArray()];
                //return new JsonResponse($data, 200, ["Content-Type" => "application/json"]);
                return $this->json($data, 200, ["Content-Type" => "application/json"]);*/

        $data = $this->posts->findAll();
        return $this->json($data, 200, ["Content-Type" => "application/json"]);
    }

    #[Route(path: "/{id}", name: "byId", methods: ["GET"])]
    function getById(string $id): Response
    {
        /*
            $post1 = PostFactory::create("test title", "test content");
            $post1->setId("1");

            $post2 = PostFactory::create("test title", "test content");
            $post2->setId("2");
            $data = [$post1, $post2];
            $result = array_filter($data, function ($a) use ($id) {
                echo "\$id:" . $id . PHP_EOL;
                return $a->getId() === $id;
            });
            if (empty($result)) {
                return new JsonResponse(["error" => "Post was not found by id"], 404, ["Content-Type" => "application/json"]);
            } else {
                return new JsonResponse($result[0]->asArray(), 200, ["Content-Type" => "application/json"]);
            }*/

        $data = $this->posts->findOneBy(["id" => $id]);
        if ($data) {
            return $this->json($data, 200, ["Content-Type" => "application/json"]);
        } else {
            return $this->json(["error" => "Post was not found by id:" . $id], 404, ["Content-Type" => "application/json"]);
        }
    }

    #[Route(path: "", name: "create", methods: ["POST"])]
    public function create(CreatePostDto $data): Response
    {
        $entity = PostFactory::create($data->getTitle(), $data->getContent());
        $this->posts->getEntityManager()->persist($entity);

        return $this->json([], 201, ["Location" => "/posts/" . $entity->getId()]);
    }

    #[Route(path: "/{id}", name: "delete", methods: ["DELETE"])]
    public function deleteById(string $id): Response
    {
        $entity = $this->posts->findOneBy(["id" => $id]);
        if (!$entity) {
            return $this->json(["error" => "Post was not found by id:" . $id], 404, ["Content-Type" => "application/json"]);
        }
        $this->posts->getEntityManager()->remove($entity);

        return $this->json([], 202);
    }
}