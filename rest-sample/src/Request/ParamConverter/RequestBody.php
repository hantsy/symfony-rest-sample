<?php

namespace App\Request\ParamConverter;


use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
final class RequestBody
{
    // format is json or xml
    private string $format = "json" ;

    /**
     * @param string $format
     */
    public function __construct(string $format)
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     * @return RequestBody
     */
    public function setFormat(string $format): RequestBody
    {
        $this->format = $format;
        return $this;
    }

}