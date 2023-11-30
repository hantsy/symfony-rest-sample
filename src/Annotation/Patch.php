<?php

namespace App\Annotation;

use Attribute;
use JetBrains\PhpStorm\Deprecated;
use Symfony\Component\Routing\Attribute\Route;

#[Attribute]
class Patch extends Route
{
    public function getMethods():array
    {
        return [HttpMethod::PATCH->name];
    }
}