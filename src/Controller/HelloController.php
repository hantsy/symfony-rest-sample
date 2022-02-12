<?php

namespace App\Controller;

use App\Annotation\Get;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HelloController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     */
    #[Get('/hello', name: 'hello')]
    public function sayHello(Request $request): Response
    {
        $name = $request->get("name") ?? "Symfony";
        $data = ['message' => 'Hello ' . $name];

        //return new JsonResponse($data, 200, [], true);
        return $this->json($data);
    }
}
