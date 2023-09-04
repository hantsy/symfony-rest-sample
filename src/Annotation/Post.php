<?php

namespace App\Annotation;

use Attribute;
use Symfony\Component\Routing\Annotation\Route;

#[Attribute]
class Post extends Route
{
    public function getMethods(): array
    {
        return [HttpMethod::POST->name];
    }
}