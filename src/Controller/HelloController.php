<?php

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    #[Route("/hello", name: "hello")]
    public function index(): Response
    {
        return $this->render('hello/index.html.twig', [
            'name' => 'Symfony 5',
            'ts' => new DateTime()
        ]);
    }
}
