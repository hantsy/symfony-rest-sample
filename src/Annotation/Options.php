<?php

namespace App\Annotation;

use Attribute;
use Symfony\Component\Routing\Attribute\Route;

#[Attribute]
class Options extends Route
{
    public function getMethods(): array
    {
        return [HttpMethod::OPTIONS->name];
    }

}