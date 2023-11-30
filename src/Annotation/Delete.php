<?php

namespace App\Annotation;

use Attribute;
use Symfony\Component\Routing\Attribute\Route;

#[Attribute]
class Delete extends Route
{
    public function getMethods(): array
    {
        return [HttpMethod::DELETE->name];
    }
}