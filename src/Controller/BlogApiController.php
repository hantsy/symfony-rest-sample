<?php
// src/Controller/BlogApiController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogApiController extends AbstractController
{
    //{parameter_name?default_value}
    //#[Route('/blog/{page<\d+>?1}', name: 'blog_list')]
    // #[Route('/blog/{page<\d+>}', name: 'blog_list')]
    #[Route('/blog/{page}', name: 'blog_list', requirements: ['page' => '\d+'])]
    public function list(int $page = 1)
    {
        // ...
    }

    #[Route('/blog/{slug}', name: 'blog_show')]
    public function show($slug)
    {
        // ...
    }

    #[Route('/api/posts/{id}', methods: ['PUT'])]
    public function edit(int $id)
    {
        // ... edit a post
    }
}
