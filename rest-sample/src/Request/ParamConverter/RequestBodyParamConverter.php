<?php

namespace App\Request\ParamConverter;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

// see: https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/converters.html#creating-a-converter
class RequestBodyParamConverter implements ParamConverterInterface, LoggerAwareInterface
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    private LoggerInterface $logger;

    /**
     * @inheritDoc
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $param = $configuration->getName();
        $className = $configuration->getClass();
        $format = $request->getRequestFormat();
        $this->logger->debug("request format: {f}", ["f" => $format]);
        if ($format) {
            //read request body
            $content = $request->getContent();
            $bodyData = $this->serializer->deserialize($content, $className, $format);
            $request->attributes->set($param, $bodyData);
            return true;
        }

        return false;
        //
//        $violationList = $this->validator->validate($bodyObject);
//        $request->attributes->set('_violations', $violationList);
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     */
    public function supports(ParamConverter $configuration): bool
    {
        $className = $configuration->getClass();
        if ($className) {
            $reflector = new ReflectionClass($className);
            $attrs = $reflector->getAttributes(Body::class);

            //check if it is annotated with `Body`
            return sizeof($attrs) > 0;
        }

        return false;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}