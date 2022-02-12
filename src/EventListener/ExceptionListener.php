<?php

namespace App\EventListener;

use App\Exception\PostNotFoundException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener implements LoggerAwareInterface
{
    private LoggerInterface $logger;

    public function __construct()
    {
    }

    public function onKernelException(ExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();
        $data = ["error" => $exception->getMessage()];

        // Customize your response object to display the exception details
        $response = new JsonResponse($data);

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details

        if ($exception instanceof PostNotFoundException) {
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
        } else if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // sends the modified response object to the event
        $event->setResponse($response);
    }

    public function setLogger(LoggerInterface $logger):void
    {
        $this->logger = $logger;
    }
}