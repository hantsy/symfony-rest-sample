<?php

namespace App\Request\ParamConverter;

use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

class UuidParamConverter implements ParamConverterInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }


    /**
     * @inheritDoc
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {

        $param = $configuration->getName();

        if (!$request->attributes->has($param)) {
            return false;
        }

        $value = $request->attributes->get($param);
        $this->logger->info("parameter value:" . $value);
        if (!$value && $configuration->isOptional()) {
            $request->attributes->set($param, null);

            return true;
        }

        $data = Uuid::fromString($value);
        $request->attributes->set($param, $data);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function supports(ParamConverter $configuration): bool
    {
        $className = $configuration->getClass();
        $this->logger->info("converting to UUID :{c}", ["c" => $className]);
        return $className && $className == Uuid::class;
    }
}