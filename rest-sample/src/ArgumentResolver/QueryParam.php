<?php

namespace App\ArgumentResolver;


use Attribute;
use JetBrains\PhpStorm\Pure;

#[Attribute(Attribute::TARGET_PARAMETER)]
final class QueryParam
{
    private null|string $name;
    private bool $required;

    /**
     * @param string|null $name
     * @param bool $required
     */
    public function __construct(?string $name = null, bool $required = false)
    {
        $this->name = $name;
        $this->required = $required;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return QueryParam
     */
    public function setName(string $name): QueryParam
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     * @return QueryParam
     */
    public function setRequired(bool $required): QueryParam
    {
        $this->required = $required;
        return $this;
    }

    #[Pure] public function __toString(): string
    {
        return "QueryParam[name='" . $this->getName() . "', required='" . $this->isRequired() . "']";
    }
}