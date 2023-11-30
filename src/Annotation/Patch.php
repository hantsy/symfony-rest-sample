<?php

namespace App\Annotation;

use Attribute;
use Symfony\Component\Routing\Annotation\Route;

#[Attribute]
class Patch extends Route
{
    public function getMethods():array
    {
        return [HttpMethod::PATCH->name];
    }
}