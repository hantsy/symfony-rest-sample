<?php

namespace App\Annotation;

use Attribute;
use Symfony\Component\Routing\Annotation\Route;

#[Attribute]
class Get extends Route
{
    public function getMethods(): array
    {
        return [HttpMethod::GET->name];
    }

}