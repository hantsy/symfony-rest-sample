<?php

namespace App\Annotation;

use Attribute;
use Symfony\Component\Routing\Attribute\Route;

#[Attribute]
class Get extends Route
{
    public function getMethods(): array
    {
        return [HttpMethod::GET->name];
    }

}