<?php

namespace App\Annotation;

use Attribute;
use Symfony\Component\Routing\Attribute\Route;

#[Attribute]
class Put extends Route
{
    public function getMethods(): array
    {
        return [HttpMethod::PUT->name];
    }
}