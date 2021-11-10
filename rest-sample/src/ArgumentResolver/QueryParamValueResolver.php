<?php

namespace App\ArgumentResolver;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class QueryParamValueResolver implements ArgumentValueResolverInterface, LoggerAwareInterface
{
    public function __construct()
    {
    }

    private LoggerInterface $logger;

    /**
     * @inheritDoc
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $this->logger->debug("Applying QueryParamValueResolver...");
        $argumentName = $argument->getName();
        $this->logger->debug("The method argument name: " . $argumentName);

        //read name property from QueryParam
        $attrs = $argument->getAttributes(QueryParam::class);

        //if name property is not set in `QueryParam`, use the argument name instead.
        $queryParamAttribute = $attrs[0];
        //$this->logger->debug("QueryParam:" . $queryParamAttribute);
        $name = $queryParamAttribute->getName();
        $required = $queryParamAttribute->isRequired();
        $this->logger->debug("QueryParam values: name= " . $name . ", required =" . $required);
        $name = $name ?? $argumentName;

        //fetch query name from request
        $value = $request->query->get($name);
        $this->logger->debug("request query parameter value: " . $value);

        //if default value is set and query param value is not set, use default value instead.
        if (!$value && $required) {
            if ($argument->hasDefaultValue()) {
                $value = $argument->getDefaultValue();
            } else {
                throw new \InvalidArgumentException("Request query parameter `" . $name . "` is required, but not set.");
            }
        }
        $this->logger->debug("final resolved value: " . $value);
        yield $value;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $attrs = $argument->getAttributes(QueryParam::class);
        return count($attrs) > 0;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}