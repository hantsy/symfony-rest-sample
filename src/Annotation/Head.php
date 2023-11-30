<?php

namespace App\Annotation;

use Attribute;
use JetBrains\PhpStorm\Deprecated;
use Symfony\Component\Routing\Attribute\Route;

#[Attribute]
class Head extends Route
{
    public function getMethods(): array
    {
        return [HttpMethod::HEAD->name];
    }

}