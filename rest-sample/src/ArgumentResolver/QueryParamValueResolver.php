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
        $argumentName = $argument->getName();
        $this->logger->info("Found [QueryParam] annotation/attribute on argument '" . $argumentName . "', applying [QueryParamValueResolver]");
        $type = $argument->getType();
        $nullable = $argument->isNullable();
        $this->logger->debug("The method argument type: '" . $type . "' and nullable: '" . $nullable . "'");

        //read name property from QueryParam
        $attr = $argument->getAttributes(QueryParam::class)[0];// `QueryParam` is not repeatable
        $this->logger->debug("QueryParam:" . $attr);
        //if name property is not set in `QueryParam`, use the argument name instead.
        $name = $attr->getName() ?? $argumentName;
        $required = $attr->isRequired() ?? false;
        $this->logger->debug("Polished QueryParam values: name='" . $name . "', required='" . $required . "'");

        //fetch query name from request
        $value = $request->query->get($name);
        $this->logger->debug("The request query parameter value: '" . $value . "'");

        //if default value is set and query param value is not set, use default value instead.
        if (!$value && $argument->hasDefaultValue()) {
            $value = $argument->getDefaultValue();
            $this->logger->debug("After set default value: '" . $value . "'");
        }

        if ($required && !$value) {
            throw new \InvalidArgumentException("Request query parameter '" . $name . "' is required, but not set.");
        }

        $this->logger->debug("final resolved value: '" . $value . "'");
        
        //must return  a `yield` clause
        yield match ($type) {
            'int' => $value ? (int)$value : 0,
            'float' => $value ? (float)$value : .0,
            'bool' => (bool)$value,
            'string' => $value ? (string)$value : ($nullable ? null : ''),
            null => null
        };
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