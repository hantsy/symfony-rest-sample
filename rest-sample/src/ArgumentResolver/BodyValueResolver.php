<?php

namespace App\ArgumentResolver;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;

// see: https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/converters.html#creating-a-converter
class BodyValueResolver implements ArgumentValueResolverInterface, LoggerAwareInterface
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    private LoggerInterface $logger;

    /**
     * @inheritDoc
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $className = $argument->getType();
        $format = $request->getRequestFormat();
        $this->logger->debug("request format:" . $format);

        //read request body
        $content = $request->getContent();
        $data = $this->serializer->deserialize($content, $className, $format);
        dump($data);
        yield $data;
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $attrs = $argument->getAttributes(Body::class);
        return count($attrs) > 0;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}