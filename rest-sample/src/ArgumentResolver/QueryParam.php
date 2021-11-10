<?php

namespace App\ArgumentResolver;


use Attribute;
use JetBrains\PhpStorm\Pure;

#[Attribute(Attribute::TARGET_PARAMETER)]
final class QueryParam
{
    private ?string $name = '';
    private ?bool $required = false;

    /**
     * @return string
     */
    public function getName(): string
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
        return "QueryParam[name=".$this->getName().", required=".$this->isRequired().']';
    }
}