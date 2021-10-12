<?php

namespace App\Request\ParamConverter;

use ReflectionClass;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestBodyParamConverter implements ParamConverterInterface
{
    public function __construct(private SerializerInterface $serializer,
                                private ValidatorInterface  $validator)
    {
    }

    /**
     * @inheritDoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $className = $configuration->getClass();
        $reflector = new ReflectionClass($className);
        $requestBody = $reflector->getAttributes(RequestBody::class)[0];
        $format = $requestBody->getArguments()[0] ?? "json";

        //read request body
        $body = $request->getContent();
        $bodyObject = $this->serializer->deserialize($body, $className, $format);
        $violationList = $this->validator->validate($bodyObject);

        $request->attributes->set('_violations', $violationList);
        $request->attributes->set($configuration->getName(), $bodyObject);
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     */
    public function supports(ParamConverter $configuration)
    {
        $className = $configuration->getClass();
        $reflector = new ReflectionClass($className);
        $attrs = $reflector->getAttributes(RequestBody::class);

        //check if it is annotated with `RequestBody`
        return sizeof($attrs) > 0;
    }
}