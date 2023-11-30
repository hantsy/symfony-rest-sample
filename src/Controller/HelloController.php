<?php

namespace App\Controller;

use App\Annotation\Get;
use App\Dto\Greeting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HelloController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/hello', name: 'hello')]
    public function sayHello(Request $request): Response
    {
        $name = $request->get("name") ?? "Symfony";
        //$data = ['message' => 'Hello ' . $name];
        //return new JsonResponse($data, 200, [], true);
        $data = Greeting::of('Hello ' . $name);
        return $this->json($data);
    }
}
